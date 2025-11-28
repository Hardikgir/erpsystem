@extends('university_admin.layouts.app')

@section('title', 'Course Document')
@section('page-title', 'Course Document')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Course Document</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Course Document</h3>
            </div>
            <div class="card-body">
                <!-- Form Section -->
                <form id="searchForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="program_id">Program<span class="text-danger">*</span></label>
                                <select class="form-control" id="program_id" name="program_id" required>
                                    <option value="">Select</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}">
                                            {{ $program->program_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="course_id">Course<span class="text-danger">*</span></label>
                                <select class="form-control" id="course_id" name="course_id" required>
                                    <option value="">Select</option>
                                    @if(isset($courses) && $courses->count() > 0)
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="session_id">Session<span class="text-danger">*</span></label>
                                <select class="form-control" id="session_id" name="session_id" required>
                                    <option value="">Select</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}">
                                            {{ $session->session_label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="domicile">Domicile<span class="text-danger">*</span></label>
                                <select class="form-control" id="domicile" name="domicile" required>
                                    <option value="">Select</option>
                                    <option value="All India">All India</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Add New Documents Section -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h5>Add New Documents</h5>
                            <div class="form-group">
                                <label for="document_name">Document Name<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="document_name" 
                                           name="document_name" 
                                           placeholder="Enter document name"
                                           required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary" id="saveDocumentBtn">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="searchBtn" style="background-color: #1F8BFF;">
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Documents Table -->
                <div id="documentsTableContainer" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="documentsTable">
                            <thead style="background-color: #E3F2FD;">
                                <tr>
                                    <th style="width: 10%">S.No.</th>
                                    <th style="width: 70%">Document Name</th>
                                    <th style="width: 20%">
                                        <input type="checkbox" id="select_all_docs" title="Select All">
                                        Select All
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="documentsTableBody">
                                <!-- Documents will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-right">
                            @if(auth()->user()->can('university.admin.course.document.map'))
                                <button type="button" id="submitMappingBtn" class="btn btn-primary" style="background-color: #1F8BFF;">
                                    Submit
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Program change - load courses
document.getElementById('program_id').addEventListener('change', function() {
    const programId = this.value;
    const courseSelect = document.getElementById('course_id');
    
    courseSelect.innerHTML = '<option value="">Select</option>';
    
    if (programId) {
        fetch(`/university-admin/ajax/courses/${programId}`)
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

// Search button click
document.getElementById('searchBtn').addEventListener('click', function() {
    const programId = document.getElementById('program_id').value;
    const courseId = document.getElementById('course_id').value;
    const sessionId = document.getElementById('session_id').value;
    const domicile = document.getElementById('domicile').value;
    
    if (!programId || !courseId || !sessionId || !domicile) {
        alert('Please select all required fields.');
        return;
    }
    
    // Show loading
    const tableContainer = document.getElementById('documentsTableContainer');
    const tableBody = document.getElementById('documentsTableBody');
    tableBody.innerHTML = '<tr><td colspan="2" class="text-center">Loading...</td></tr>';
    tableContainer.style.display = 'block';
    
    // AJAX search
    fetch('{{ route("university.admin.course.document.search") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            program_id: programId,
            course_id: courseId,
            session_id: sessionId,
            domicile: domicile
        })
    })
    .then(response => response.json())
    .then(data => {
        tableBody.innerHTML = '';
        
        if (data.success && data.documents.length > 0) {
            const mappedIds = data.mapped_document_ids || [];
            let serialNo = 1;
            
            data.documents.forEach(doc => {
                const row = document.createElement('tr');
                const isChecked = mappedIds.includes(doc.id) ? 'checked' : '';
                row.innerHTML = `
                    <td>${serialNo}</td>
                    <td>${doc.document_name}</td>
                    <td>
                        <input type="checkbox" 
                               class="doc-checkbox" 
                               name="document_ids[]" 
                               value="${doc.id}" 
                               ${isChecked}>
                    </td>
                `;
                tableBody.appendChild(row);
                serialNo++;
            });
            
            // Update select all checkbox state
            updateSelectAllState();
        } else {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No documents found.</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error searching documents:', error);
        tableBody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Error loading documents.</td></tr>';
    });
});

// Save document button click
document.getElementById('saveDocumentBtn').addEventListener('click', function() {
    const programId = document.getElementById('program_id').value;
    const courseId = document.getElementById('course_id').value;
    const sessionId = document.getElementById('session_id').value;
    const domicile = document.getElementById('domicile').value;
    const documentName = document.getElementById('document_name').value.trim();
    
    if (!programId || !courseId || !sessionId || !domicile) {
        alert('Please select Program, Course, Session, and Domicile first.');
        return;
    }
    
    if (!documentName) {
        alert('Please enter a document name.');
        return;
    }
    
    // Disable button
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Saving...';
    
    // AJAX save
    fetch('{{ route("university.admin.course.document.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            program_id: programId,
            course_id: courseId,
            session_id: sessionId,
            domicile: domicile,
            document_name: documentName
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Save';
        
        if (data.success) {
            alert('Document saved successfully!');
            document.getElementById('document_name').value = '';
            // Refresh the table if it's visible
            if (document.getElementById('documentsTableContainer').style.display !== 'none') {
                document.getElementById('searchBtn').click();
            }
        } else {
            alert(data.message || 'Failed to save document.');
        }
    })
    .catch(error => {
        console.error('Error saving document:', error);
        btn.disabled = false;
        btn.textContent = 'Save';
        alert('Error saving document. Please try again.');
    });
});

// Delete document (event delegation)
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-document-btn')) {
        if (!confirm('Are you sure you want to delete this document?')) {
            return;
        }
        
        const documentId = e.target.getAttribute('data-id');
        const btn = e.target;
        btn.disabled = true;
        btn.textContent = 'Deleting...';
        
        fetch(`/university-admin/course-document/${documentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.closest('tr').remove();
                // If no rows left, show message
                const tableBody = document.getElementById('documentsTableBody');
                if (tableBody.children.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="2" class="text-center">No documents found.</td></tr>';
                }
            } else {
                alert(data.message || 'Failed to delete document.');
                btn.disabled = false;
                btn.textContent = 'Delete';
            }
        })
        .catch(error => {
            console.error('Error deleting document:', error);
            alert('Error deleting document. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Delete';
        });
    }
});

// Select All checkbox functionality
function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.doc-checkbox');
    const selectAll = document.getElementById('select_all_docs');
    
    if (checkboxes.length === 0) {
        selectAll.checked = false;
        return;
    }
    
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    selectAll.checked = allChecked;
}

document.addEventListener('change', function(e) {
    if (e.target.id === 'select_all_docs') {
        const checkboxes = document.querySelectorAll('.doc-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = e.target.checked;
        });
    } else if (e.target.classList.contains('doc-checkbox')) {
        updateSelectAllState();
    }
});

// Submit mapping button click
@if(auth()->user()->can('university.admin.course.document.map'))
document.getElementById('submitMappingBtn').addEventListener('click', function() {
    const programId = document.getElementById('program_id').value;
    const courseId = document.getElementById('course_id').value;
    const sessionId = document.getElementById('session_id').value;
    const domicile = document.getElementById('domicile').value;
    
    if (!programId || !courseId || !sessionId || !domicile) {
        alert('Please select Program, Course, Session, and Domicile first.');
        return;
    }
    
    // Get selected document IDs
    const selectedDocs = Array.from(document.querySelectorAll('.doc-checkbox:checked'))
        .map(cb => parseInt(cb.value));
    
    if (selectedDocs.length === 0) {
        alert('Please select at least one document to map.');
        return;
    }
    
    // Disable button
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Submitting...';
    
    // AJAX submit
    fetch('{{ route("university.admin.course.document.submit") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            program_id: programId,
            course_id: courseId,
            session_id: sessionId,
            domicile: domicile,
            document_ids: selectedDocs
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Submit';
        
        if (data.success) {
            alert('Documents successfully mapped!');
        } else {
            alert(data.message || 'Failed to map documents.');
        }
    })
    .catch(error => {
        console.error('Error submitting mapping:', error);
        btn.disabled = false;
        btn.textContent = 'Submit';
        alert('Error submitting mapping. Please try again.');
    });
});
@endif
</script>
@endsection

