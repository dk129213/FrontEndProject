@props(['makes', 'bodies', 'count'])
<form {{ $attributes->class(['hero-search']) }} action="{{ page('cars.index') }}" method="get" role="search" aria-label="Search cars">
    <div class="field">
        <label for="qs-brand">Make</label>
        <select class="select" id="qs-brand" name="brand">
            <option value="">Any make</option>
            @foreach ($makes as $brand => $makeCount)
                <option value="{{ $brand }}">{{ $brand }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="qs-body">Body type</label>
        <select class="select" id="qs-body" name="body">
            <option value="">Any type</option>
            @foreach ($bodies as $body)
                <option value="{{ $body['key'] }}">{{ $body['label'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="qs-fuel">Fuel</label>
        <select class="select" id="qs-fuel" name="fuel">
            <option value="">Any fuel</option>
            @foreach (\App\Support\Catalog::FUEL as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="qs-max">Max price</label>
        <select class="select" id="qs-max" name="max">
            <option value="">No limit</option>
            <option value="20000">€20,000</option>
            <option value="25000">€25,000</option>
            <option value="30000">€30,000</option>
            <option value="40000">€40,000</option>
        </select>
    </div>
    <button type="submit" class="btn btn--primary btn--lg">
        <x-icon name="search" /> Show {{ $count }} cars
    </button>
</form>
