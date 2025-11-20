@extends('admin.layouts.app')

@section('title', 'Sub Module Management')
@section('page-title', 'Sub Module Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Sub Modules</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sub Modules List</h3>
        <div class="card-tools">
            <a href="{{ route('admin.sub-modules.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Sub Module
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sub Module Name</th>
                    <th>Module</th>
                    <th>Route</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subModules as $subModule)
                    <tr>
                        <td>{{ $subModule->id }}</td>
                        <td>{{ $subModule->sub_module_name }}</td>
                        <td>{{ $subModule->module->module_name }}</td>
                        <td><code>{{ $subModule->route }}</code></td>
                        <td>
                            @if($subModule->status)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $subModule->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.sub-modules.edit', $subModule) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.sub-modules.destroy', $subModule) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No sub modules found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $subModules->links() }}
    </div>
</div>
@endsection

