<x-layouts.backoffice area="admin" nav="extras" title="Extras">
    <div class="bo-head">
        <div class="stack" style="--stack: var(--space-1)">
            <h1 class="heading">Extras</h1>
            <p class="muted">Additions customers can choose at checkout. Hidden extras are not offered on any car.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th scope="col">Extra</th><th scope="col">Description</th><th scope="col" class="right">Price</th><th scope="col">Offered</th></tr>
            </thead>
            <tbody>
                @foreach ($additions as $addition)
                    <tr>
                        <td>
                            <div class="row" style="--row: var(--space-3); flex-wrap: nowrap">
                                <span class="icon-tile" style="width: 40px; height: 40px"><x-icon :name="$addition['icon']" /></span>
                                <strong>{{ $addition['name'] }}</strong>
                            </div>
                        </td>
                        <td class="muted">{{ $addition['description'] }}</td>
                        <td class="right num nowrap" style="font-weight: 700">{{ euro($addition['price']) }}</td>
                        <td>
                            <label class="check">
                                <input type="checkbox" @checked($addition['is_active']) data-extra-toggle="{{ $addition['name'] }}">
                                <span class="sr-only">Offer {{ $addition['name'] }}</span>
                                <x-status-badge :status="$addition['is_active'] ? 'active' : 'hidden'" data-extra-badge>{{ $addition['is_active'] ? 'Offered' : 'Hidden' }}</x-status-badge>
                            </label>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.backoffice>
