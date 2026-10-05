@extends('admin.layout')

@section('title', 'Permissions')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Access control</p><h1>Permission matrix</h1><p class="page-description">Review effective role capabilities. Change assignments from the Roles page.</p></div><a class="button button-secondary" href="{{ route('admin.roles') }}">Manage roles</a></div>
    <section class="panel table-panel">
        <div class="table-scroll"><table class="table-list permission-matrix">
            <thead><tr><th>Capability</th>@foreach($roles as $role)<th>{{ $role->name }}</th>@endforeach</tr></thead>
            <tbody>
                @foreach($permissions as $permission)
                    <tr><td><strong>{{ str($permission->name)->replace('_', ' ')->title() }}</strong><small class="table-subtitle">{{ $permission->name }}</small></td>
                        @foreach($roles as $role)
                            <td>@if($role->name === 'Super Admin' || $role->permissions->contains('id', $permission->id))<span class="permission-yes" aria-label="Granted">Yes</span>@else<span class="muted" aria-label="Not granted">—</span>@endif</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    </section>
@endsection
