@extends('admin.layouts.app')

@section('title', 'Module Management')
@section('page-title', 'Module Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Modules</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modules List</h3>
        <div class="card-tools">
            <a href="{{ route('admin.modules.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Module
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Module Name</th>
                    <th>Icon</th>
                    <th>Sub Modules</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($modules as $module)
                    <tr>
                        <td>{{ $module->id }}</td>
                        <td>{{ $module->module_name }}</td>
                        <td><i class="{{ $module->icon ?? 'fas fa-circle' }}"></i></td>
                        <td>{{ $module->subModules->count() }}</td>
                        <td>
                            @if($module->status)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $module->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" class="d-inline">
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
                        <td colspan="7" class="text-center">No modules found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $modules->links() }}
    </div>
</div>
@endsection

