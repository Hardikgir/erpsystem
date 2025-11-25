@extends('university_admin.layouts.app')

@section('title', 'Fee Package')
@section('page-title', 'Fee Package')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Fee Package</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Form Card -->
        <div class="card">
            <div class="card-header" style="background-color: #FF8C00; color: white;">
                <h3 class="card-title" style="color: white;">Add Fee Package (University)</h3>
            </div>
            <div class="card-body">
                <form action="{{ isset($feePackage) ? route('university.admin.fee.package.update', $feePackage->id) : route('university.admin.fee.package.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="package_name">Package Name:<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('package_name') is-invalid @enderror" 
                                       id="package_name" 
                                       name="package_name" 
                                       value="{{ old('package_name', isset($feePackage) ? $feePackage->package_name : '') }}" 
                                       placeholder="Text Box"
                                       required>
                                @error('package_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <input type="text" 
                                       class="form-control @error('description') is-invalid @enderror" 
                                       id="description" 
                                       name="description" 
                                       value="{{ old('description', isset($feePackage) ? $feePackage->description : '') }}" 
                                       placeholder="Text Box">
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Fee Elements Table -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Select Fee Elements:</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Select To Save</th>
                                        <th>Element Name</th>
                                        <th>Pattern Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feeElements as $element)
                                        <tr>
                                            <td>
                                                <input type="checkbox" 
                                                       name="selected_elements[]" 
                                                       value="{{ $element->id }}"
                                                       class="element-checkbox"
                                                       {{ (isset($feePackage) && $feePackage->items->contains('element_id', $element->id)) || (is_array(old('selected_elements')) && in_array($element->id, old('selected_elements'))) ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $element->element_name }}</td>
                                            <td>
                                                <select name="patterns[{{ $element->id }}]" class="form-control pattern-select" style="width: 100%;">
                                                    <option value="Annual" {{ (isset($feePackage) && $feePackage->items->where('element_id', $element->id)->first() && $feePackage->items->where('element_id', $element->id)->first()->pattern == 'Annual') || old('patterns.'.$element->id) == 'Annual' ? 'selected' : '' }}>Annual</option>
                                                    <option value="Semester" {{ (isset($feePackage) && $feePackage->items->where('element_id', $element->id)->first() && $feePackage->items->where('element_id', $element->id)->first()->pattern == 'Semester') || old('patterns.'.$element->id) == 'Semester' ? 'selected' : '' }}>Semester</option>
                                                    <option value="Quarter" {{ (isset($feePackage) && $feePackage->items->where('element_id', $element->id)->first() && $feePackage->items->where('element_id', $element->id)->first()->pattern == 'Quarter') || old('patterns.'.$element->id) == 'Quarter' ? 'selected' : '' }}>Quarter</option>
                                                    <option value="Monthly" {{ (isset($feePackage) && $feePackage->items->where('element_id', $element->id)->first() && $feePackage->items->where('element_id', $element->id)->first()->pattern == 'Monthly') || old('patterns.'.$element->id) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                    <option value="One Time" {{ (isset($feePackage) && $feePackage->items->where('element_id', $element->id)->first() && $feePackage->items->where('element_id', $element->id)->first()->pattern == 'One Time') || old('patterns.'.$element->id) == 'One Time' ? 'selected' : '' }}>One Time</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @error('selected_elements')
                                <span class="text-danger"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            @if(isset($feePackage))
                                <button type="submit" class="btn btn-primary" style="background-color: #1F8BFF;">Update</button>
                                <a href="{{ route('university.admin.fee.package') }}" class="btn btn-secondary">Cancel</a>
                            @else
                                <button type="submit" name="action" value="new_package" class="btn btn-primary" style="background-color: #1F8BFF;">Save and Proceed for New Package</button>
                                <button type="submit" name="action" value="proceed_to_fee_plan" class="btn btn-primary" style="background-color: #1F8BFF;">Save and Proceed for Fee Plan</button>
                                <a href="{{ route('university.admin.fee.package') }}" class="btn btn-secondary">Cancel</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Sr. no</th>
                            <th>Package name</th>
                            <th>Selected Elements</th>
                            <th>Status</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feePackages as $index => $package)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $package->package_name }}</td>
                                <td>
                                    @foreach($package->items as $item)
                                        {{ $item->element->element_name }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </td>
                                <td>{{ $package->status ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <a href="{{ route('university.admin.fee.package.edit', $package->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No fee packages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Enable/disable pattern select based on checkbox
    document.querySelectorAll('.element-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const patternSelect = this.closest('tr').querySelector('.pattern-select');
            patternSelect.disabled = !this.checked;
        });
        
        // Initialize disabled state
        const patternSelect = checkbox.closest('tr').querySelector('.pattern-select');
        patternSelect.disabled = !checkbox.checked;
    });
</script>
@endsection

