@extends('admin.layout')

@section('title', 'Users')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Access control</p><h1>People</h1><p class="page-description">Manage newsroom accounts, assignments, and access status.</p></div></div>
    <section class="panel">
        <h2>Invite a newsroom user</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" class="form-grid form-grid-wide">
            @csrf
            <label>Full name <input name="name" value="{{ old('name') }}" maxlength="255" required></label>
            <label>Username <input name="username" value="{{ old('username') }}" maxlength="191" placeholder="generated-from-name"></label>
            <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
            <label>Temporary password <input type="password" name="password" minlength="12" autocomplete="new-password" required><small class="field-hint">At least 12 characters. Share it securely and ask the user to change it.</small></label>
            <label>Initial role
                <select name="role"><option value="">Reader account</option>@foreach($roles as $role)<option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ $role->name }}</option>@endforeach</select>
            </label>
            <div class="form-span-2"><button class="button button-primary" type="submit">Create user</button></div>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-toolbar"><div><h2>Accounts</h2><span class="muted">{{ $users->total() }} users</span></div></div>
        <div class="table-scroll">
            <table class="table-list">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Manage</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong><small class="table-subtitle">{{ $user->username ? '@'.$user->username : 'No username' }}</small></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('name')->join(', ') ?: 'Reader' }}</td>
                            <td><span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">{{ $user->is_active ? 'Active' : 'Deactivated' }}</span></td>
                            <td class="row-actions">
                                @if($user->id !== auth()->id())
                                    <details class="row-edit"><summary>Edit access</summary>
                                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="form-grid row-edit-form">
                                            @csrf @method('PATCH')
                                            <label>Name <input name="name" value="{{ $user->name }}" required></label>
                                            <label>Username <input name="username" value="{{ $user->username }}"></label>
                                            <label>Email <input type="email" name="email" value="{{ $user->email }}" required></label>
                                            <label>Role <select name="role" required>@foreach($roles as $role)<option value="{{ $role->name }}" @selected($user->roles->contains('name', $role->name))>{{ $role->name }}</option>@endforeach</select></label>
                                            <label class="check-list"><input type="checkbox" name="is_active" value="1" @checked($user->is_active)> Account active</label>
                                            <button class="button button-primary" type="submit">Save access</button>
                                        </form>
                                    </details>
                                    @if($user->is_active)
                                        <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" onsubmit="return confirm('Deactivate {{ addslashes($user->name) }}? They will be denied on their next request.')">@csrf<button class="link-button" type="submit">Deactivate</button></form>
                                    @endif
                                @else
                                    <span class="muted">You</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state"><strong>No accounts</strong><p>Create a newsroom account above.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $users->links() }}</div>
    </section>
@endsection
