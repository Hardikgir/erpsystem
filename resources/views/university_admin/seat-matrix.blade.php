@extends('university_admin.layouts.app')

@section('title', 'Seat Matrix')
@section('page-title', 'Seat Matrix')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Seat Matrix</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Seats Matrix</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('university.admin.seat.matrix.store') }}" method="POST" id="seatMatrixForm">
                    @csrf
                    
                    <!-- Two Column Dropdown Layout -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="program_id">Program<span class="text-danger">*</span></label>
                                <select class="form-control @error('program_id') is-invalid @enderror" 
                                        id="program_id" name="program_id" required>
                                    <option value="">All</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->program_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="course_id">Courses<span class="text-danger">*</span></label>
                                <select class="form-control @error('course_id') is-invalid @enderror" 
                                        id="course_id" name="course_id" required>
                                    <option value="">All</option>
                                    @if(old('program_id') && isset($courses))
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('course_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="college_id">College<span class="text-danger">*</span></label>
                                <select class="form-control @error('college_id') is-invalid @enderror" 
                                        id="college_id" name="college_id" required>
                                    <option value="">--Select--</option>
                                    @foreach($colleges as $college)
                                        <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>
                                            {{ $college->college_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('college_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="academic_session_id">Academic Session<span class="text-danger">*</span></label>
                                <select class="form-control @error('academic_session_id') is-invalid @enderror" 
                                        id="academic_session_id" name="academic_session_id" required>
                                    <option value="">--Select--</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->session_label }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <a href="{{ route('university.admin.session.master') }}" target="_blank">[Define Session]</a>
                                </small>
                                @error('academic_session_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="admission_session_id">Admission Session<span class="text-danger">*</span></label>
                                <select class="form-control @error('admission_session_id') is-invalid @enderror" 
                                        id="admission_session_id" name="admission_session_id" required>
                                    <option value="">Select</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('admission_session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->session_label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('admission_session_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Mode Selection -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Mode:<span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('mode') is-invalid @enderror" 
                                           type="checkbox" name="mode[]" id="mode_direct" value="direct" 
                                           {{ (is_array(old('mode')) && in_array('direct', old('mode'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mode_direct">Direct</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('mode') is-invalid @enderror" 
                                           type="checkbox" name="mode[]" id="mode_counselling" value="counselling"
                                           {{ (is_array(old('mode')) && in_array('counselling', old('mode'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mode_counselling">Counselling</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('mode') is-invalid @enderror" 
                                           type="checkbox" name="mode[]" id="mode_merit" value="merit"
                                           {{ (is_array(old('mode')) && in_array('merit', old('mode'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mode_merit">Merit</label>
                                </div>
                                <small class="form-text text-muted">Note: Choose the mode of Admission for this course</small>
                                @error('mode')
                                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Admission Session Dates -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 style="color: #1F8BFF; margin-bottom: 15px;">Select Admission Session date</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Start Date<span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ old('start_date') }}" 
                                       required>
                                @error('start_date')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date<span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="{{ old('end_date') }}" 
                                       required>
                                @error('end_date')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Publish Mode -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Publish Mode:<span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('publish_mode') is-invalid @enderror" 
                                           type="radio" name="publish_mode" id="publish_public" value="public" 
                                           {{ old('publish_mode', 'public') == 'public' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="publish_public">Public</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('publish_mode') is-invalid @enderror" 
                                           type="radio" name="publish_mode" id="publish_private" value="private"
                                           {{ old('publish_mode') == 'private' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="publish_private">Private</label>
                                </div>
                                <small class="form-text text-muted">Note: Select private if you want to take admissions through offline mode, or select public to publish online.</small>
                                @error('publish_mode')
                                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Total Seats -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total_seats">Total no. of Seats<span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('total_seats') is-invalid @enderror" 
                                       id="total_seats" 
                                       name="total_seats" 
                                       value="{{ old('total_seats') }}" 
                                       min="0"
                                       required>
                                <small class="form-text text-muted">Note: Don't mention seats if intake capacity is not defined. After defining intake capacity, you can't take more students than defined no. of seats.</small>
                                @error('total_seats')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Define Category -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Do you want to define seats by category:<span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="radio" name="define_category" id="define_category_yes" value="yes" 
                                           {{ old('define_category', 'no') == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="define_category_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="radio" name="define_category" id="define_category_no" value="no"
                                           {{ old('define_category', 'no') == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="define_category_no">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Table (shown when Yes is selected) -->
                    <div class="row" id="categoryTableRow" style="display: {{ old('define_category') == 'yes' ? 'block' : 'none' }};">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered" id="categoryTable">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Direct</th>
                                                <th>Counselling</th>
                                                <th>Merit</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $categories = ['GENERAL', 'OBC', 'SC', 'ST'];
                                                $oldCategories = old('categories', []);
                                            @endphp
                                            @foreach($categories as $index => $category)
                                                <tr>
                                                    <td><strong>{{ $category }}</strong></td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control category-input" 
                                                               name="categories[{{ $index }}][direct_seats]" 
                                                               data-category="{{ $category }}" 
                                                               data-type="direct"
                                                               value="{{ $oldCategories[$index]['direct_seats'] ?? 0 }}" 
                                                               min="0">
                                                        <input type="hidden" name="categories[{{ $index }}][category_name]" value="{{ $category }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control category-input" 
                                                               name="categories[{{ $index }}][counselling_seats]" 
                                                               data-category="{{ $category }}" 
                                                               data-type="counselling"
                                                               value="{{ $oldCategories[$index]['counselling_seats'] ?? 0 }}" 
                                                               min="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control category-input" 
                                                               name="categories[{{ $index }}][merit_seats]" 
                                                               data-category="{{ $category }}" 
                                                               data-type="merit"
                                                               value="{{ $oldCategories[$index]['merit_seats'] ?? 0 }}" 
                                                               min="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                               class="form-control category-total" 
                                                               name="categories[{{ $index }}][total_seats]" 
                                                               data-category="{{ $category }}" 
                                                               value="{{ $oldCategories[$index]['total_seats'] ?? 0 }}" 
                                                               readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary" style="background-color: #1F8BFF;">
                                Go To Summary
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Program change - load courses
    const programSelect = document.getElementById('program_id');
    const courseSelect = document.getElementById('course_id');
    
    programSelect.addEventListener('change', function() {
        const programId = this.value;
        courseSelect.innerHTML = '<option value="">All</option>';
        
        if (programId) {
            fetch(`/university-admin/get-courses/${programId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.course_name;
                        courseSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading courses:', error);
                });
        }
    });

    // Show/hide category table based on radio selection
    const defineCategoryYes = document.getElementById('define_category_yes');
    const defineCategoryNo = document.getElementById('define_category_no');
    const categoryTableRow = document.getElementById('categoryTableRow');
    
    function toggleCategoryTable() {
        if (defineCategoryYes.checked) {
            categoryTableRow.style.display = 'block';
        } else {
            categoryTableRow.style.display = 'none';
        }
    }
    
    defineCategoryYes.addEventListener('change', toggleCategoryTable);
    defineCategoryNo.addEventListener('change', toggleCategoryTable);

    // Calculate category totals
    const categoryInputs = document.querySelectorAll('.category-input');
    categoryInputs.forEach(input => {
        input.addEventListener('input', function() {
            const category = this.dataset.category;
            const row = this.closest('tr');
            const directInput = row.querySelector('input[data-type="direct"]');
            const counsellingInput = row.querySelector('input[data-type="counselling"]');
            const meritInput = row.querySelector('input[data-type="merit"]');
            const totalInput = row.querySelector('.category-total');
            
            const direct = parseInt(directInput.value) || 0;
            const counselling = parseInt(counsellingInput.value) || 0;
            const merit = parseInt(meritInput.value) || 0;
            const total = direct + counselling + merit;
            
            totalInput.value = total;
        });
    });

    // Trigger initial calculation
    categoryInputs.forEach(input => {
        if (input.value) {
            input.dispatchEvent(new Event('input'));
        }
    });
});
</script>
@endsection

