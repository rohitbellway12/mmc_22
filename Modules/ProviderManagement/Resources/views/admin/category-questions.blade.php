@extends('adminmodule::layouts.master')

@section('title', translate('Manage Global Category Questions'))

@push('css_or_js')
    <style>
        .question-card {
            transition: all 0.3s ease;
        }

        .question-card:hover {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{ translate('Global_Category_Questions') }}</h2>
                        <p class="text-muted">
                            {{ translate('These questions will be shown to all users booking services in the selected categories, regardless of the provider.') }}
                        </p>
                    </div>

                    <!-- Add New Question Card -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="material-icons">add_circle</span>
                                {{ translate('Add_New_Global_Question') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="question-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>{{ translate('Question_Text') }} <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="question_text" id="question_text" class="form-control" rows="2"
                                                placeholder="e.g., Please enter your tyre size" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ translate('Category') }} <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="" disabled selected>{{ translate('Select_Category') }}
                                                </option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ translate('Question_Type') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="question_type" id="question_type" class="form-control" required>
                                                <option value="text">{{ translate('Text_Answer') }}</option>
                                                <option value="yes_no">{{ translate('Yes/No') }}</option>
                                                <option value="select">{{ translate('Select/Dropdown') }}</option>
                                                <option value="file">{{ translate('File/Image Upload') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ translate('Required') }}?</label>
                                            <div class="custom-control custom-checkbox mt-2">
                                                <input type="checkbox" class="custom-control-input" id="is_required"
                                                    name="is_required" value="1" checked>
                                                <label class="custom-control-label" for="is_required"></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3 d-none" id="options_section">
                                        <div class="form-group">
                                            <label>{{ translate('Options') }} ({{ translate('Comma separated') }}) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="options" id="options" class="form-control"
                                                placeholder="e.g., Option 1, Option 2, Option 3">
                                        </div>
                                    </div>

                                    <div class="col-12 text-right mt-3">
                                        <button type="submit" class="btn btn--primary px-4">
                                            {{ translate('Save_Question') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Questions List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ translate('Standardized_Category_Questions') }}</h5>
                        </div>
                        <div class="card-body">
                            @forelse($questions as $question)
                                <div class="question-card card mb-3">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <h6 class="mb-1">{{ $question->question_text }}</h6>
                                                <div class="mt-2">
                                                    <span class="badge badge-soft-info">
                                                        <span class="material-icons"
                                                            style="font-size: 14px; vertical-align: middle;">
                                                            @if ($question->question_type == 'yes_no')
                                                                check_circle
                                                            @elseif($question->question_type == 'select')
                                                                list
                                                            @elseif($question->question_type == 'file')
                                                                image
                                                            @else
                                                                text_fields
                                                            @endif
                                                        </span>
                                                        {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                                    </span>
                                                    @if ($question->is_required)
                                                        <span
                                                            class="badge badge-soft-danger">{{ translate('Required') }}</span>
                                                    @else
                                                        <span
                                                            class="badge badge-soft-secondary">{{ translate('Optional') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <small class="text-muted d-block">{{ translate('Category') }}</small>
                                                <span class="badge badge-light border"
                                                    style="color: white !important;">{{ $question->category->name ?? translate('General') }}</span>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <small class="text-muted d-block">{{ translate('Display Order') }}</small>
                                                <span class="badge badge-primary">#{{ $question->display_order }}</span>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <small class="text-muted d-block">{{ translate('Status') }}</small>
                                                <label class="switcher mx-auto">
                                                    <input type="checkbox" class="switcher_input status-toggle"
                                                        data-id="{{ $question->id }}"
                                                        {{ $question->is_active ? 'checked' : '' }}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-1 text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-question"
                                                    data-id="{{ $question->id }}">
                                                    <span class="material-icons">delete</span>
                                                </button>
                                            </div>
                                            @if ($question->question_type == 'select' && $question->options)
                                                <div class="col-12 mt-2">
                                                    <small class="text-muted"><strong>{{ translate('Options') }}:</strong>
                                                        {{ $question->options }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <p class="text-muted">{{ translate('No global questions added yet') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if ($questions->count() > 0)
                        <div class="d-flex justify-content-end mt-3">
                            {{ $questions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";

        // Show/hide options section based on type
        $('#question_type').on('change', function() {
            if ($(this).val() === 'select') {
                $('#options_section').removeClass('d-none');
                $('#options').attr('required', true);
            } else {
                $('#options_section').addClass('d-none');
                $('#options').attr('required', false);
            }
        });

        // Add new question
        $('#question-form').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            if (!$('#is_required').is(':checked')) {
                formData.set('is_required', '0');
            }

            $.ajax({
                url: '{{ route('admin.provider.category_questions.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success('{{ translate('Global question added successfully') }}');
                    location.reload();
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('{{ translate('Something went wrong') }}');
                    }
                }
            });
        });

        // Toggle status
        $('.status-toggle').on('change', function() {
            let questionId = $(this).data('id');
            let status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ route('admin.provider.category_questions.update_status') }}',
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: questionId,
                    status: status
                },
                success: function(response) {
                    toastr.success('{{ translate('Status updated successfully') }}');
                },
                error: function() {
                    toastr.error('{{ translate('Failed to update status') }}');
                    location.reload();
                }
            });
        });

        // Delete question
        $('.delete-question').on('click', function() {
            let questionId = $(this).data('id');

            Swal.fire({
                title: '{{ translate('Are you sure') }}?',
                text: "{{ translate('Standardized questions for this category will be removed') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ translate('Yes, delete it') }}!',
                cancelButtonText: '{{ translate('Cancel') }}'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('admin.provider.category_questions.destroy', ':id') }}'
                            .replace(':id', questionId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success('{{ translate('Question deleted successfully') }}');
                            location.reload();
                        },
                        error: function() {
                            toastr.error('{{ translate('Failed to delete question') }}');
                        }
                    });
                }
            });
        });
    </script>
@endpush
