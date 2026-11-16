<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiFormat;
use App\Support\Catalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * POST /api/v1/sales: reserve a car with extras and a (simulated) card
 * payment. The same rules as the checkout on the website. Payments use
 * Stripe-style test cards and nothing is charged. The project has no
 * database, so the sale is returned but not stored.
 */
class SaleController extends Controller
{
    private const DECLINED_CARD = '4000000000000002';

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'car' => ['required', 'string'],
            'additions' => ['sometimes', 'array'],
            'additions.*' => ['integer'],
            'buyer' => ['required', 'array'],
            'buyer.first_name' => ['required', 'string', 'max:60'],
            'buyer.last_name' => ['required', 'string', 'max:60'],
            'buyer.email' => ['required', 'email'],
            'buyer.phone' => ['required', 'string', 'min:8', 'max:30'],
            'payment' => ['required', 'array'],
            'payment.card_number' => ['required', 'string'],
            'payment.exp_month' => ['required', 'integer', 'between:1,12'],
            'payment.exp_year' => ['required', 'integer', 'between:2000,2100'],
            'payment.cvc' => ['required', 'digits_between:3,4'],
        ], [], [
            'buyer.first_name' => 'first name',
            'buyer.last_name' => 'last name',
            'buyer.email' => 'email',
            'buyer.phone' => 'phone',
            'payment.card_number' => 'card number',
            'payment.exp_month' => 'expiry month',
            'payment.exp_year' => 'expiry year',
            'payment.cvc' => 'security code',
        ]);

        $card = preg_replace('/\D/', '', (string) $request->input('payment.card_number'));
        $validator->after(function ($v) use ($card, $request) {
            if ($card !== '' && (strlen($card) !== 16 || ! $this->luhn($card))) {
                $v->errors()->add('payment.card_number', 'The card number is not valid.');
            }
            $year = (int) $request->input('payment.exp_year');
            $month = (int) $request->input('payment.exp_month');
            if ($year && $month && ($year < (int) date('Y') || ($year === (int) date('Y') && $month < (int) date('n')))) {
                $v->errors()->add('payment.exp_year', 'The card has expired.');
            }
        });

        if ($validator->fails()) {
            return ApiFormat::error(422, 'validation_failed', 'Some fields are missing or not valid.', $validator->errors()->toArray());
        }

        $car = Catalog::cars()->firstWhere('slug', $request->input('car'));
        if (! $car) {
            return ApiFormat::error(404, 'car_not_found', 'No car with the slug "'.$request->input('car').'".');
        }
        if ($car['status'] !== 'available') {
            return ApiFormat::error(409, 'car_unavailable', $car['status'] === 'sold'
                ? "The {$car['title']} has already been sold."
                : "The {$car['title']} is already reserved by another customer.");
        }

        $offered = Catalog::additionsFor($car)->keyBy('id');
        $chosen = collect($request->input('additions', []))->unique()->values();
        $notOffered = $chosen->reject(fn ($id) => $offered->has($id))->values();
        if ($notOffered->isNotEmpty()) {
            return ApiFormat::error(422, 'addition_not_offered', 'Some extras are not offered for this car.', [
                'additions' => $notOffered->all(),
                'offered' => $offered->keys()->all(),
            ]);
        }

        if ($card === self::DECLINED_CARD) {
            return ApiFormat::error(402, 'payment_declined', 'The card was declined and nothing was charged.');
        }

        $lines = $chosen->map(fn ($id) => [
            'addition_id' => $id,
            'name' => $offered[$id]['name'],
            'price_at_purchase' => $offered[$id]['price'],
        ]);
        $total = $car['price'] + $lines->sum('price_at_purchase');
        $vatRate = config('riva.vat_rate');
        $id = 'RM-'.date('Y').'-'.str_pad((string) random_int(500, 99999), 5, '0', STR_PAD_LEFT);

        return response()->json([
            'data' => [
                'id' => $id,
                'status' => 'requested',
                'payment_status' => 'paid',
                'payment_reference' => 'pi_'.Str::lower(Str::random(14)),
                'requested_at' => now()->toAtomString(),
                'car' => ApiFormat::carSummary($car),
                'buyer' => $request->only(['buyer.first_name', 'buyer.last_name', 'buyer.email', 'buyer.phone'])['buyer'],
                'salesperson' => $car['salesman'] ? ['id' => $car['salesman']['id'], 'name' => $car['salesman']['name']] : null,
                'base_car_price' => $car['price'],
                'additions' => $lines->all(),
                'total_price' => $total,
                'vat_included' => round($total - $total / (1 + $vatRate / 100), 2),
                'currency' => 'EUR',
                'card' => ['brand' => str_starts_with($card, '4') ? 'visa' : 'card', 'last4' => substr($card, -4)],
                'stored' => false,
            ],
        ], 201, ['Location' => ApiFormat::url('api/v1/sales/'.$id)]);
    }

    private function luhn(string $digits): bool
    {
        $sum = 0;
        foreach (array_reverse(str_split($digits)) as $i => $d) {
            $d = (int) $d * ($i % 2 ? 2 : 1);
            $sum += $d > 9 ? $d - 9 : $d;
        }

        return $sum % 10 === 0;
    }
}
