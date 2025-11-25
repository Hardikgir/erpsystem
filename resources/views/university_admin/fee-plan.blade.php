@extends('university_admin.layouts.app')

@section('title', 'Fee Plan')
@section('page-title', 'Fee Plan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Fee Plan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Form Card -->
        <div class="card">
            <div class="card-header" style="background-color: #FF8C00; color: white;">
                <h3 class="card-title" style="color: white;">Add Fee Plan (University)</h3>
                <h4 class="card-title" style="color: white; font-size: 14px;">Add / Edit Fee Plan</h4>
            </div>
            <div class="card-body">
                <form action="{{ isset($feePlan) ? route('university.admin.fee.plan.update', $feePlan->id) : route('university.admin.fee.plan.store') }}" method="POST" id="feePlanForm">
                    @csrf
                    
                    @if(isset($feePlan))
                        <!-- Edit Mode: Single Course and Category -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_id">Course:<span class="text-danger">*</span></label>
                                    <select class="form-control @error('course_id') is-invalid @enderror" 
                                            id="course_id" 
                                            name="course_id" 
                                            required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                    {{ (isset($feePlan) && $feePlan->course_id == $course->id) || old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Category:<span class="text-danger">*</span></label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" 
                                                    {{ (isset($feePlan) && $feePlan->category == $cat) || old('category') == $cat ? 'selected' : '' }}>
                                                {{ $cat }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Create Mode: Multiple Courses and Categories -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_type">Course Type:</label>
                                    <select class="form-control" id="course_type" name="course_type">
                                        <option value="">All</option>
                                        <option value="Semester">Semester</option>
                                        <option value="Year">Year</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="select_all_courses" {{ (is_array(old('course_ids')) && count(old('course_ids')) == count($courses)) ? 'checked' : '' }}>
                                        <strong>All Courses</strong>
                                    </label>
                                    <div class="mt-2" style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                        @foreach($courses as $course)
                                            <div class="form-check">
                                                <input class="form-check-input course-checkbox" 
                                                       type="checkbox" 
                                                       name="course_ids[]" 
                                                       value="{{ $course->id }}"
                                                       id="course_{{ $course->id }}"
                                                       {{ (is_array(old('course_ids')) && in_array($course->id, old('course_ids'))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="course_{{ $course->id }}">
                                                    {{ $course->course_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('course_ids')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Category List -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="select_all_categories" {{ (is_array(old('categories')) && count(old('categories')) == count($categories)) ? 'checked' : '' }}>
                                        <strong>All Categories</strong>
                                    </label>
                                    <div class="mt-2">
                                        @foreach($categories as $category)
                                            <div class="form-check">
                                                <input class="form-check-input category-checkbox" 
                                                       type="checkbox" 
                                                       name="categories[]" 
                                                       value="{{ $category }}"
                                                       id="category_{{ $loop->index }}"
                                                       {{ (is_array(old('categories')) && in_array($category, old('categories'))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="category_{{ $loop->index }}">
                                                    {{ $category }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('categories')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Fee Package Category -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="package_id">Fee Package Category:<span class="text-danger">*</span></label>
                                <select class="form-control @error('package_id') is-invalid @enderror" 
                                        id="package_id" 
                                        name="package_id" 
                                        required>
                                    <option value="">Select Fee Package</option>
                                    @foreach($feePackages as $package)
                                        <option value="{{ $package->id }}" 
                                                {{ (isset($feePlan) && $feePlan->package_id == $package->id) || old('package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->package_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('package_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Fee Details Table -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Fee Details:</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="feeItemsTable">
                                    <thead>
                                        <tr>
                                            <th>Inst. No.</th>
                                            @php
                                                $selectedPackage = null;
                                                $packageElements = [];
                                                
                                                if (isset($feePlan) && $feePlan->package_id) {
                                                    $selectedPackage = $feePackages->find($feePlan->package_id);
                                                } elseif (old('package_id')) {
                                                    $selectedPackage = $feePackages->find(old('package_id'));
                                                } elseif (count($feePackages) > 0) {
                                                    $selectedPackage = $feePackages->first();
                                                }
                                                
                                                if ($selectedPackage && $selectedPackage->items) {
                                                    $packageElements = $selectedPackage->items->map(function($item) {
                                                        return $item->element ? $item->element->element_name : null;
                                                    })->filter()->toArray();
                                                }
                                            @endphp
                                            @if(count($packageElements) > 0)
                                                @foreach($packageElements as $elementName)
                                                    <th>{{ $elementName }}</th>
                                                @endforeach
                                            @else
                                                <th colspan="0" class="text-center">Please select a Fee Package</th>
                                            @endif
                                            <th>Total</th>
                                            <th>Semester</th>
                                            <th>Delete Installment</th>
                                        </tr>
                                    </thead>
                                    <tbody id="feeItemsBody">
                                        @if(count($packageElements) > 0)
                                            @if(isset($feePlan) && $feePlan->items->count() > 0)
                                                @php
                                                    $installmentNo = 0;
                                                    $groupedItems = $feePlan->items->groupBy('installment_no');
                                                @endphp
                                                @foreach($groupedItems as $installment => $items)
                                                    <tr class="installment-row" data-installment="{{ $installmentNo }}">
                                                        <td>{{ $installmentNo }}</td>
                                                        @foreach($packageElements as $elementName)
                                                            @php
                                                                $item = $items->first(function($i) use ($elementName) {
                                                                    return $i->element && $i->element->element_name == $elementName;
                                                                });
                                                                $packageItem = $selectedPackage ? $selectedPackage->items->first(function($item) use ($elementName) {
                                                                    return $item->element && $item->element->element_name == $elementName;
                                                                }) : null;
                                                            @endphp
                                                            <td>
                                                                <input type="hidden" name="fee_items[{{ $installmentNo }}][element_id]" value="{{ $item ? $item->element_id : ($packageItem ? $packageItem->element_id : '') }}">
                                                                <input type="number" 
                                                                       class="form-control amount-input" 
                                                                       name="fee_items[{{ $installmentNo }}][amount]" 
                                                                       value="{{ $item ? $item->amount : 0 }}" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       data-element="{{ $elementName }}">
                                                            </td>
                                                        @endforeach
                                                        <td class="row-total">0</td>
                                                        <td>
                                                            <select class="form-control" name="fee_items[{{ $installmentNo }}][semester_no]">
                                                                <option value="">Select</option>
                                                                @for($i = 1; $i <= 10; $i++)
                                                                    <option value="{{ $i }}" {{ $item && $item->semester_no == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" class="delete-installment" name="delete_installments[]" value="{{ $installmentNo }}">
                                                        </td>
                                                    </tr>
                                                    @php $installmentNo++; @endphp
                                                @endforeach
                                            @else
                                                <tr class="installment-row" data-installment="0">
                                                    <td>0</td>
                                                    @if(isset($selectedPackage) && $selectedPackage)
                                                        @foreach($packageElements as $index => $elementName)
                                                            @php
                                                                $packageItem = $selectedPackage->items->first(function($item) use ($elementName) {
                                                                    return $item->element && $item->element->element_name == $elementName;
                                                                });
                                                                $element = $packageItem ? $packageItem->element : null;
                                                            @endphp
                                                            <td>
                                                                <input type="hidden" name="fee_items[0][element_id]" value="{{ $element ? $element->id : '' }}">
                                                                <input type="number" 
                                                                       class="form-control amount-input" 
                                                                       name="fee_items[0][amount]" 
                                                                       value="{{ old('fee_items.0.amount', 0) }}" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       data-element="{{ $elementName }}">
                                                            </td>
                                                        @endforeach
                                                    @endif
                                                    <td class="row-total">0</td>
                                                    <td>
                                                        <select class="form-control" name="fee_items[0][semester_no]">
                                                            <option value="">Select</option>
                                                            @for($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" class="delete-installment">
                                                    </td>
                                                </tr>
                                            @endif
                                        @else
                                            <tr>
                                                <td colspan="100%" class="text-center">Please select a Fee Package to see fee elements</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Grand Total</th>
                                            @if(isset($packageElements))
                                                @foreach($packageElements as $elementName)
                                                    <th class="column-total" data-element="{{ $elementName }}">0</th>
                                                @endforeach
                                            @endif
                                            <th id="grandTotal">0</th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="addInstallment">Add Installment</button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary" style="background-color: #1F8BFF;">Save</button>
                            @if(isset($feePlan))
                                <a href="{{ route('university.admin.fee.plan') }}" class="btn btn-secondary">Cancel</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all courses checkbox
    const selectAllCourses = document.getElementById('select_all_courses');
    const courseCheckboxes = document.querySelectorAll('.course-checkbox');
    
    selectAllCourses.addEventListener('change', function() {
        courseCheckboxes.forEach(cb => cb.checked = this.checked);
    });

    // Select all categories checkbox
    const selectAllCategories = document.getElementById('select_all_categories');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    
    selectAllCategories.addEventListener('change', function() {
        categoryCheckboxes.forEach(cb => cb.checked = this.checked);
    });

    // Package change - reload fee elements
    const packageSelect = document.getElementById('package_id');
    if (packageSelect) {
        packageSelect.addEventListener('change', function() {
            // Reload page with selected package to show correct elements
            const form = document.getElementById('feePlanForm');
            if (form && !form.querySelector('input[name="_method"]')) {
                // Only reload if not in edit mode
                const url = new URL(window.location.href);
                url.searchParams.set('package_id', this.value);
                window.location.href = url.toString();
            }
        });
    }

    // Calculate totals
    function calculateTotals() {
        const rows = document.querySelectorAll('.installment-row');
        const columnTotals = {};
        let grandTotal = 0;

        rows.forEach(row => {
            const inputs = row.querySelectorAll('.amount-input');
            let rowTotal = 0;
            
            inputs.forEach(input => {
                const amount = parseFloat(input.value) || 0;
                rowTotal += amount;
                
                const elementName = input.getAttribute('data-element');
                if (!columnTotals[elementName]) {
                    columnTotals[elementName] = 0;
                }
                columnTotals[elementName] += amount;
            });
            
            row.querySelector('.row-total').textContent = rowTotal.toFixed(2);
            grandTotal += rowTotal;
        });

        // Update column totals
        Object.keys(columnTotals).forEach(elementName => {
            const totalCell = document.querySelector(`.column-total[data-element="${elementName}"]`);
            if (totalCell) {
                totalCell.textContent = columnTotals[elementName].toFixed(2);
            }
        });

        // Update grand total
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
    }

    // Add event listeners for amount inputs
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('amount-input')) {
            calculateTotals();
        }
    });

    // Add installment
    let installmentCounter = {{ isset($feePlan) && $feePlan->items->count() > 0 ? $feePlan->items->groupBy('installment_no')->count() : 1 }};
    document.getElementById('addInstallment').addEventListener('click', function() {
        const tbody = document.getElementById('feeItemsBody');
        const firstRow = tbody.querySelector('.installment-row');
        if (!firstRow) return;
        
        const newRow = firstRow.cloneNode(true);
        newRow.setAttribute('data-installment', installmentCounter);
        
        // Update input names
        newRow.querySelectorAll('input, select').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${installmentCounter}]`);
            }
            if (input.value && input.type === 'number') {
                input.value = 0;
            }
        });
        
        // Update installment number display
        newRow.querySelector('td:first-child').textContent = installmentCounter;
        
        tbody.appendChild(newRow);
        installmentCounter++;
        calculateTotals();
    });

    // Delete installment
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('delete-installment') && e.target.checked) {
            e.target.closest('.installment-row').remove();
            calculateTotals();
        }
    });

    // Initial calculation
    calculateTotals();
});
</script>
@endsection

