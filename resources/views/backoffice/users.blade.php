<x-layouts.backoffice area="admin" nav="users" title="Users">
    <div class="bo-head">
        <div class="stack" style="--stack: var(--space-1)">
            <h1 class="heading">Users</h1>
            <p class="muted">Customers, the sales team and admins. Change a role to give or take away access.</p>
        </div>
    </div>

    <div class="bo-filters">
        <div class="search-input">
            <x-icon name="search" :size="18" />
            <label class="sr-only" for="user-search">Search users</label>
            <input class="input" id="user-search" type="search" placeholder="Search name or email" data-table-search>
        </div>
        <div class="chip-row" role="group" aria-label="Role">
            <button type="button" class="chip" data-status-chip="" aria-pressed="true">All <span class="count">{{ $users->count() }}</span></button>
            @foreach ($roles as $key => $label)
                <button type="button" class="chip" data-status-chip="{{ $key }}" aria-pressed="false">{{ $label }} <span class="count">{{ $users->where('role', $key)->count() }}</span></button>
            @endforeach
        </div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Phone</th><th scope="col">Joined</th><th scope="col">Role</th></tr>
            </thead>
            <tbody data-table-body>
                @foreach ($users as $user)
                    <tr data-row data-status="{{ $user['role'] }}" data-search="{{ strtolower($user['name'].' '.$user['email']) }}">
                        <td>
                            <div class="row" style="--row: var(--space-3); flex-wrap: nowrap">
                                <span class="avatar" aria-hidden="true">{{ $user['initials'] }}</span>
                                <strong class="nowrap">{{ $user['name'] }}</strong>
                            </div>
                        </td>
                        <td>{{ $user['email'] }}</td>
                        <td class="nowrap num">{{ $user['phone'] }}</td>
                        <td class="nowrap">{{ \App\Support\Catalog::date($user['created_at'], false) }}</td>
                        <td>
                            <label class="sr-only" for="role-{{ $user['id'] }}">Role of {{ $user['name'] }}</label>
                            <select class="select" id="role-{{ $user['id'] }}" style="height: var(--control-sm); width: 150px; font-size: 13px" data-role-select="{{ $user['name'] }}">
                                @foreach ($roles as $key => $label)
                                    <option value="{{ $key }}" @selected($user['role'] === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="caption" data-table-count></p>
    <div class="empty" data-table-empty hidden>
        <h2 class="title">No users match</h2>
        <p class="muted">Try another name, email or role.</p>
    </div>
</x-layouts.backoffice>
