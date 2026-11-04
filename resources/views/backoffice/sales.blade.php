<x-layouts.backoffice :area="$area" nav="sales" :title="$area === 'admin' ? 'Sales' : 'My sales'">
    <div class="bo-head">
        <div class="stack" style="--stack: var(--space-1)">
            <h1 class="heading">{{ $area === 'admin' ? 'Sales' : 'My sales' }}</h1>
            <p class="muted">Orders placed online, with the extras chosen and the payment status.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Order</th>
                    <th scope="col">Car</th>
                    <th scope="col">Buyer</th>
                    <th scope="col">Extras</th>
                    @if ($area === 'admin')<th scope="col">Salesperson</th>@endif
                    <th scope="col" class="right">Total</th>
                    <th scope="col">Status</th>
                    <th scope="col">Payment</th>
                </tr>
            </thead>
            <tbody data-sales-body>
                @forelse ($sales as $sale)
                    <tr>
                        <td class="nowrap"><span class="mono">{{ $sale['id'] }}</span><br><span class="caption">{{ $sale['requested_label'] }}</span></td>
                        <td>
                            <div class="cell-car" style="min-width: 200px">
                                <img src="{{ $sale['car']['image'] }}" alt="" loading="lazy" width="144" height="90">
                                <span><strong>{{ $sale['car']['name'] }}</strong><span class="caption">{{ euro($sale['base_car_price']) }}</span></span>
                            </div>
                        </td>
                        <td class="nowrap">{{ $sale['buyer']['name'] }}<br><span class="caption">{{ $sale['buyer']['phone'] }}</span></td>
                        <td>{{ $sale['addition_names'] ? implode(', ', $sale['addition_names']) : 'None' }}</td>
                        @if ($area === 'admin')<td class="nowrap">{{ $sale['salesman']['name'] }}</td>@endif
                        <td class="right num nowrap" style="font-weight: 700">{{ euro($sale['total_price']) }}</td>
                        <td><x-status-badge :status="$sale['status']" /></td>
                        <td><x-status-badge :status="$sale['payment_status']" /><br><span class="mono caption">{{ $sale['payment_reference'] }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="muted">No sales yet. They appear here as soon as a customer pays.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="caption">Orders placed in this browser during the demo are added at the top.</p>
</x-layouts.backoffice>
