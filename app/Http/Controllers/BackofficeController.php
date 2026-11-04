<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use Illuminate\Contracts\View\View;

/**
 * Sales team and admin screens. Access is checked in the browser
 * (data-require-role on the layout) because the site is static.
 */
class BackofficeController extends Controller
{
    /** The demo salesperson, Marko Babić. */
    private const DEMO_SALESMAN_ID = 2;

    public function salesDashboard(): View
    {
        $mine = Catalog::cars()->where('salesman_id', self::DEMO_SALESMAN_ID);
        $sales = Catalog::sales()->where('salesman_id', self::DEMO_SALESMAN_ID);

        return view('backoffice.dashboard', [
            'area' => 'sales',
            'stats' => [
                ['label' => 'My cars for sale', 'value' => $mine->where('status', 'available')->count()],
                ['label' => 'Reserved', 'value' => $mine->where('status', 'reserved')->count()],
                ['label' => 'Open orders', 'value' => $sales->where('status', 'requested')->count()],
                ['label' => 'Sold value', 'value' => euro($sales->where('payment_status', 'paid')->sum('total_price'))],
            ],
            'sales' => $sales->take(5),
            'cars' => $mine->sortByDesc('created_at')->take(5),
        ]);
    }

    public function salesCars(): View
    {
        return view('backoffice.cars', [
            'area' => 'sales',
            'cars' => Catalog::cars()->where('salesman_id', self::DEMO_SALESMAN_ID)->values(),
        ]);
    }

    public function salesAddCar(): View
    {
        return $this->carForm('sales');
    }

    public function salesOrders(): View
    {
        return view('backoffice.sales', [
            'area' => 'sales',
            'sales' => Catalog::sales()->where('salesman_id', self::DEMO_SALESMAN_ID)->values(),
        ]);
    }

    public function adminDashboard(): View
    {
        $cars = Catalog::cars();
        $sales = Catalog::sales();

        return view('backoffice.dashboard', [
            'area' => 'admin',
            'stats' => [
                ['label' => 'Cars for sale', 'value' => $cars->where('status', 'available')->count()],
                ['label' => 'Reserved', 'value' => $cars->where('status', 'reserved')->count()],
                ['label' => 'Revenue this quarter', 'value' => euro($sales->where('payment_status', 'paid')->sum('total_price'))],
                ['label' => 'Customers', 'value' => Catalog::users()->where('role', 'customer')->count()],
            ],
            'sales' => $sales->take(5),
            'cars' => $cars->take(5),
        ]);
    }

    public function adminCars(): View
    {
        return view('backoffice.cars', ['area' => 'admin', 'cars' => Catalog::cars()]);
    }

    public function adminAddCar(): View
    {
        return $this->carForm('admin');
    }

    public function adminSales(): View
    {
        return view('backoffice.sales', ['area' => 'admin', 'sales' => Catalog::sales()]);
    }

    public function adminUsers(): View
    {
        return view('backoffice.users', [
            'area' => 'admin',
            'users' => Catalog::users(),
            'roles' => Catalog::roles(),
        ]);
    }

    public function adminExtras(): View
    {
        return view('backoffice.extras', ['area' => 'admin', 'additions' => Catalog::additions()]);
    }

    private function carForm(string $area): View
    {
        return view('backoffice.car-form', [
            'area' => $area,
            'team' => Catalog::salesTeam(),
            'additions' => Catalog::additions()->where('is_active', true),
        ]);
    }
}
