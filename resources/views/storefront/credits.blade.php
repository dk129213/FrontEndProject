<x-layouts.app title="Photo credits" page="credits">
    <div class="container">
        <div class="page-head">
            <h1 class="heading">Photo credits</h1>
            <p class="muted prose">Car photos come from Wikimedia Commons and are used under the licences listed. They show the same models as our stock but are not photos of the exact cars for sale.</p>
        </div>
        <div class="table-wrap" style="margin-bottom: var(--space-7)">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Photo</th>
                        <th scope="col">Used for</th>
                        <th scope="col">Author</th>
                        <th scope="col">Licence</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($credits as $credit)
                        <tr>
                            <td>
                                <div class="cell-car">
                                    <img src="{{ img($credit['file']) }}" alt="" loading="lazy" width="144" height="90">
                                    <a href="{{ $credit['source'] }}" target="_blank" rel="noopener">{{ $credit['title'] }}</a>
                                </div>
                            </td>
                            <td>{{ $cars[$credit['slug']]['title'] ?? $credit['slug'] }}</td>
                            <td>{{ $credit['author'] }}</td>
                            <td class="nowrap">
                                @if ($credit['license_url'])
                                    <a href="{{ $credit['license_url'] }}" target="_blank" rel="noopener">{{ $credit['license'] }}</a>
                                @else
                                    {{ $credit['license'] }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Credits are generated when the photos are downloaded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
