@extends('admin.layout')

@section('title', 'Roles')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Access control</p><h1>Roles</h1><p class="page-description">Assign capabilities by newsroom responsibility. Super Admin is protected.</p></div></div>
    <section class="panel">
        <h2>Create role</h2>
        <form method="POST" action="{{ route('admin.roles.store') }}" class="form-grid">
            @csrf
            <label>Role name <input type="text" name="name" maxlength="100" required></label>
            <fieldset class="permission-fieldset"><legend>Initial permissions</legend>
                @foreach($permissions as $permission)
                    <label><input type="checkbox" name="permissions[]" value="{{ $permission->id }}"> {{ str($permission->name)->replace('_', ' ')->title() }}</label>
                @endforeach
            </fieldset>
            <button class="button button-primary" type="submit">Create role</button>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-toolbar"><div><h2>Role assignments</h2><span class="muted">{{ $roles->count() }} roles configured</span></div></div>
        <div class="table-scroll"><table class="table-list">
            <thead><tr><th>Role</th><th>Guard</th><th>Permissions</th><th>Manage</th></tr></thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td><strong>{{ $role->name }}</strong></td><td>{{ $role->guard_name }}</td>
                        <td>{{ $role->permissions->count() }} capabilities</td>
                        <td>
                            @if($role->name === 'Super Admin')
                                <span class="muted">Protected</span>
                            @else
                                <details class="row-edit"><summary>Configure permissions</summary>
                                    <form method="POST" action="{{ route('admin.roles.permissions', $role) }}" class="form-grid row-edit-form">
                                        @csrf @method('PUT')
                                        <fieldset class="permission-fieldset"><legend>{{ $role->name }} permissions</legend>
                                            @foreach($permissions as $permission)
                                                <label><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked($role->permissions->contains('id', $permission->id))> {{ str($permission->name)->replace('_', ' ')->title() }}</label>
                                            @endforeach
                                        </fieldset>
                                        <button class="button button-primary" type="submit">Save permissions</button>
                                    </form>
                                </details>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    </section>
@endsection
