@extends('admin.layouts.app')

@section('title', 'Role Management')
@section('page-title', 'Role Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Roles</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Roles List</h3>
        <div class="card-tools">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Role
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->role_name }}</td>
                        <td>{{ $role->description ?? 'N/A' }}</td>
                        <td>{{ $role->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if($role->role_name !== 'Super Admin')
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No roles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $roles->links() }}
    </div>
</div>
@endsection

