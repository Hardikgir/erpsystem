@extends('admin.layouts.app')

@section('title', 'University Master')
@section('page-title', 'University Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">University Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">University Master</h3>
                    <a href="{{ route('university.master.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add University
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%">Sr. No.</th>
                                <th style="width: 20%">University Code</th>
                                <th style="width: 35%">University Name</th>
                                <th style="width: 15%">Status</th>
                                <th style="width: 25%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($universities as $index => $university)
                                <tr>
                                    <td>{{ ($universities->currentPage() - 1) * $universities->perPage() + $index + 1 }}</td>
                                    <td><strong>{{ $university->university_code }}</strong></td>
                                    <td>{{ $university->university_name }}</td>
                                    <td>
                                        @if($university->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('university.master.view', $university->id) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('university.master.edit', $university->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('university.master.destroy', $university->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete('{{ addslashes($university->university_name) }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No universities found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($universities->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $universities->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(universityName) {
        return confirm('Are you sure you want to delete "' + universityName + '"?\n\nThis action cannot be undone.');
    }
</script>
@endsection

