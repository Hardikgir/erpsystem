@extends('university_admin.layouts.app')

@section('title', 'Eligibility Criteria')
@section('page-title', 'Eligibility Criteria')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Eligibility Criteria</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title" style="color: white;">Eligibility Criteria</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="text-danger"></span>
                    </div>
                </div>
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

                <!-- Top Tab Row -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="nav nav-tabs" role="tablist">
                            <a class="nav-item nav-link active" href="#" data-toggle="tab">Add Master</a>
                            <a class="nav-item nav-link" href="#" data-toggle="tab">Program and Course <i class="fas fa-plus"></i></a>
                            <a class="nav-item nav-link" href="#" data-toggle="tab">Student Category <i class="fas fa-plus"></i></a>
                            <a class="nav-item nav-link" href="#" data-toggle="tab">Add PreRequisites <i class="fas fa-plus"></i></a>
                            <a class="nav-item nav-link" href="#" data-toggle="tab">Add Board <i class="fas fa-plus"></i></a>
                            <a class="nav-item nav-link" href="#" data-toggle="tab">Map Previous Academic <i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <form action="{{ route('university.admin.eligibility.criteria.store') }}" method="POST" id="eligibilityForm">
                    @csrf
                    
                    <!-- Program and Course Details Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="program_id">Program<span class="text-danger">*</span></label>
                                <select class="form-control @error('program_id') is-invalid @enderror" 
                                        id="program_id" name="program_id" required>
                                    <option value="">Select</option>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="course_id">Course<span class="text-danger">*</span></label>
                                <select class="form-control @error('course_id') is-invalid @enderror" 
                                        id="course_id" name="course_id" required>
                                    <option value="">Select</option>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="semester_year">Semester/Year<span class="text-danger">*</span></label>
                                <select class="form-control @error('semester_year') is-invalid @enderror" 
                                        id="semester_year" name="semester_year" required>
                                    <option value="">Select</option>
                                </select>
                                @error('semester_year')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id">
                                    <option value="">Select</option>
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Eligibility Criteria for Gender and Age -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label>Eligibility Criteria for:</label>
                                    <div class="form-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('gender') is-invalid @enderror" 
                                                   type="radio" 
                                                   name="gender" 
                                                   id="gender_male" 
                                                   value="male" 
                                                   {{ old('gender', 'male') == 'male' ? 'checked' : '' }}
                                                   required>
                                            <label class="form-check-label" for="gender_male">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('gender') is-invalid @enderror" 
                                                   type="radio" 
                                                   name="gender" 
                                                   id="gender_female" 
                                                   value="female" 
                                                   {{ old('gender') == 'female' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_female">Female</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('gender') is-invalid @enderror" 
                                                   type="radio" 
                                                   name="gender" 
                                                   id="gender_both" 
                                                   value="both" 
                                                   {{ old('gender') == 'both' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_both">Both</label>
                                        </div>
                                    </div>
                                    @error('gender')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="min_age">Min. Age</label>
                                        <input type="number" 
                                               class="form-control @error('min_age') is-invalid @enderror" 
                                               id="min_age" 
                                               name="min_age" 
                                               value="{{ old('min_age') }}" 
                                               min="0" 
                                               max="100">
                                        @error('min_age')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="max_age">Max. Age</label>
                                        <input type="number" 
                                               class="form-control @error('max_age') is-invalid @enderror" 
                                               id="max_age" 
                                               name="max_age" 
                                               value="{{ old('max_age') }}" 
                                               min="0" 
                                               max="100">
                                        @error('max_age')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Minimum Qualification Details Table -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="eligibilityTable">
                                    <thead style="background-color: #E3F2FD;">
                                        <tr>
                                            <th>Minimum Qualification<span class="text-danger">*</span></th>
                                            <th>Minimum Marks(%)<span class="text-danger">*</span></th>
                                            <th>Board</th>
                                            <th>Compt. Exam</th>
                                            <th>Minimum Percentile</th>
                                            <th>Add /Edit</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="eligibilityTableBody">
                                        <tr class="eligibility-row">
                                            <td>
                                                <select class="form-control min-qualification" name="items[0][min_qualification_id]">
                                                    <option value="">Select</option>
                                                    @foreach($qualifications as $qualification)
                                                        <option value="{{ $qualification->id }}">
                                                            {{ $qualification->qualification_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control min-marks" 
                                                       name="items[0][min_marks]" 
                                                       step="0.01" 
                                                       min="0" 
                                                       max="100">
                                            </td>
                                            <td>
                                                <select class="form-control board-select" name="items[0][board_id]">
                                                    <option value="">Select</option>
                                                    @foreach($boards as $board)
                                                        <option value="{{ $board->id }}">
                                                            {{ $board->board_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control exam-select" name="items[0][exam_id]">
                                                    <option value="">Select</option>
                                                    @foreach($exams as $exam)
                                                        <option value="{{ $exam->id }}">
                                                            {{ $exam->exam_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control min-percentile" 
                                                       name="items[0][min_percentile]" 
                                                       step="0.01" 
                                                       min="0" 
                                                       max="100">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-link btn-sm add-row-btn" style="color: #1F8BFF;">
                                                    Add
                                                </button>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" style="background-color: #1F8BFF;">
                                Save Eligibility Criteria
                            </button>
                            <a href="{{ route('university.admin.eligibility.criteria.view') }}" class="btn btn-secondary">
                                View Eligibility Criteria
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let rowIndex = 1;

// Program change - load courses
document.getElementById('program_id').addEventListener('change', function() {
    const programId = this.value;
    const courseSelect = document.getElementById('course_id');
    const semesterSelect = document.getElementById('semester_year');
    const categorySelect = document.getElementById('category_id');
    
    courseSelect.innerHTML = '<option value="">Select</option>';
    semesterSelect.innerHTML = '<option value="">Select</option>';
    
    if (programId) {
        // Load courses
        fetch(`/university-admin/ajax/courses/${programId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.course_name;
                    courseSelect.appendChild(option);
                });
            });
        
        // Load categories
        fetch(`/university-admin/ajax/categories/${programId}`)
            .then(response => response.json())
            .then(data => {
                categorySelect.innerHTML = '<option value="">Select</option>';
                data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.value;
                    option.textContent = category.label;
                    categorySelect.appendChild(option);
                });
            });
    }
});

// Course change - load semesters/years
document.getElementById('course_id').addEventListener('change', function() {
    const courseId = this.value;
    const semesterSelect = document.getElementById('semester_year');
    
    semesterSelect.innerHTML = '<option value="">Select</option>';
    
    if (courseId) {
        fetch(`/university-admin/ajax/semesters/${courseId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(semester => {
                    const option = document.createElement('option');
                    option.value = semester.value;
                    option.textContent = semester.label;
                    semesterSelect.appendChild(option);
                });
            });
    }
});

// Add row functionality
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-row-btn')) {
        e.preventDefault();
        const tbody = document.getElementById('eligibilityTableBody');
        const lastRow = tbody.lastElementChild;
        const newRow = lastRow.cloneNode(true);
        
        // Update input names with new index
        newRow.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${rowIndex}]`));
            }
            // Clear values
            if (input.tagName === 'INPUT') {
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
        
        // Update Add button to remove button
        const addBtn = newRow.querySelector('.add-row-btn');
        addBtn.textContent = 'Add';
        addBtn.classList.remove('add-row-btn');
        addBtn.classList.add('add-row-btn');
        
        // Add remove button
        const actionCell = newRow.querySelector('td:last-child');
        actionCell.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-row-btn">Remove</button>';
        
        tbody.appendChild(newRow);
        rowIndex++;
    }
    
    // Remove row functionality
    if (e.target.classList.contains('remove-row-btn')) {
        e.preventDefault();
        const row = e.target.closest('tr');
        if (document.getElementById('eligibilityTableBody').children.length > 1) {
            row.remove();
        } else {
            alert('At least one row is required.');
        }
    }
});

</script>
@endsection

