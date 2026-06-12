@extends('admin.layouts.app')
@section('title', 'Page Sections Settings')
@push('styles')
    <style>
        .table td {
            vertical-align: middle;
            padding: 5px;
        }
    </style>    
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Page Sections Settings</h3>
            {{-- <div>
                <button class="btn btn-outline-primary btn-sm">Export CSV</button>
                <button class="btn btn-primary btn-sm">Generate Report</button>
            </div> --}}
        </div>
        @php
            $faqSection = $sections->where('section_type', 'faq')->first();
            $faqData = $faqSection ? $faqSection->data : [];
            $faqs = $faqData['faqs'] ?? [];

            $howItWorkSection = $sections->where('section_type', 'how_it_work')->first();
            $howItWorkData = $howItWorkSection ? $howItWorkSection->data : [];
        @endphp

        <ul class="nav nav-pills mb-3 border-0" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-organisation-category-tab" data-bs-toggle="pill" data-bs-target="#pills-organisation-category" type="button" role="tab" aria-controls="pills-organisation-category" aria-selected="false">Organisation Category</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-faq-tab" data-bs-toggle="pill" data-bs-target="#pills-faq" type="button" role="tab" aria-controls="pills-faq" aria-selected="true">FAQs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-home-contact-section-tab" data-bs-toggle="pill" data-bs-target="#pills-home-contact-section" type="button" role="tab" aria-controls="pills-home-contact-section" aria-selected="false">Home Contact Section</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-how-it-work-tab" data-bs-toggle="pill" data-bs-target="#pills-how-it-work" type="button" role="tab" aria-controls="pills-how-it-work" aria-selected="false">How It Works</button>
            </li>
            
            
        </ul>
        <div class="tab-content p-0 border-0" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-organisation-category" role="tabpanel" aria-labelledby="pills-organisation-category-tab" tabindex="0">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.page-sections.store') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="section_type" value="faq">

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Heading</label>
                                    <input type="text" name="heading" value="{{ $faqData['heading'] ?? '' }}" class="form-control rounded" placeholder="Enter FAQ Heading">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control rounded" rows="3" placeholder="Enter FAQ Description">{{ $faqData['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <h3>Organisation Categories</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        @foreach($orgCategories as $index => $category)
                                            <tr class="faq-row">
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ ucfirst($category->name ?? '') }} </td>
                                                <td class="d-flex align-items-center justify-content-between gap-2">
                                                    @if($category->categorySetting?->image)
                                                        <img src="{{ asset($category->categorySetting?->image) }}" alt="{{ $category->name }}" width="100" height="100">
                                                    @else
                                                        <img src="https://placehold.co/600x400" alt="{{ $category->name }}" width="100" height="100">
                                                    @endif
                                                    <input type="file" name="image" class="w-50 form-control rounded mt-2" accept="image/png, image/jpeg, image/jpg">
                                                </td>
                                                <td class="text-center"> 
                                                    <select name="status" class="form-select rounded text-dark">
                                                        <option value="active" {{ $category->categorySetting?->status == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $category->categorySetting?->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary rounded save-category" data-category-id="{{ $category->id }}">
                                                        <i class="fa fa-save"></i> Save
                                                    </button>
                                                </td>  
                                            </tr>
                                        @endforeach
                                         
                                    </tbody>
                                </table>                           
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-faq" role="tabpanel" aria-labelledby="pills-faq-tab" tabindex="0">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.page-sections.store') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="section_type" value="faq">

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">FAQ Heading</label>
                                    <input type="text" name="heading" value="{{ $faqData['heading'] ?? '' }}" class="form-control rounded" placeholder="Enter FAQ Heading">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label">FAQ Description</label>
                                    <textarea name="description" class="form-control rounded" rows="3" placeholder="Enter FAQ Description">{{ $faqData['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <h3>FAQs</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Question</th>
                                            <th>Answer</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="faq-wrapper">
                                        @if(count($faqs) > 0)
                                            @foreach($faqs as $index => $faq)
                                                <tr class="faq-row">
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td><input type="text" name="faqs[{{ $index }}][question]" class="form-control rounded" placeholder="Enter Question" value="{{ $faq['question'] ?? '' }}"></td>
                                                    <td><input type="text" name="faqs[{{ $index }}][answer]" class="form-control rounded" placeholder="Enter Answer" value="{{ $faq['answer'] ?? '' }}"></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger remove-faq rounded btn-sm">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="faq-row">
                                                <td class="text-center">1</td>
                                                <td><input type="text" name="faqs[0][question]" class="form-control rounded" placeholder="Enter Question"></td>
                                                <td><input type="text" name="faqs[0][answer]" class="form-control rounded" placeholder="Enter Answer"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger remove-faq rounded btn-sm">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>                           
                            </div>
                        <div class="my-3 text-end">
                                <button type="button" class="btn btn-primary mb-4 rounded btn-sm" id="add-faq-btn"><i class="fa fa-plus"></i> Add More FAQ</button>
                        </div>
                        <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-success rounded bt n-sm">Save FAQ Section</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-home-contact-section" role="tabpanel" aria-labelledby="pills-home-contact-section-tab" tabindex="0">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.page-sections.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="section_type" value="home_contact_section">

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Heading</label>
                                    <input type="text" name="heading" value="{{ $homeContactSection->data['heading'] ?? '' }}" class="form-control rounded" placeholder="Enter Heading">
                                </div>

                                <div class="mb-4 col-md-4">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" value="" class="form-control rounded" rows="3" placeholder="Enter Description">{{ $homeContactSection->data['description'] ?? '' }}</textarea>
                                </div>
                                <div class="mb-4 col-md-4">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" name="button_text" value="{{ $homeContactSection->data['button_text'] ?? '' }}" class="form-control rounded" placeholder="Enter Button Text">
                                </div>
                            </div>
                             
                            <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-success rounded bt n-sm">Save FAQ Section</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-how-it-work" role="tabpanel" aria-labelledby="pills-how-it-work-tab" tabindex="0">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.page-sections.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="section_type" value="how_it_work">

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Heading</label>
                                    <input type="text" name="heading" value="{{ $howItWorkData['heading'] ?? '' }}" class="form-control rounded" placeholder="Enter How It Work Heading">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" value="" class="form-control rounded" rows="3" placeholder="Enter How It Work Description">{{ $howItWorkData['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <h4>Step 1</h4> 
                            <div class="row mb-3">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Heading</label>
                                    <input type="text" name="steps[0][heading]" value="{{ $howItWorkData['steps'][0]['heading'] ?? '' }}" class="form-control rounded" placeholder="Enter How It Work Heading">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label">Description</label>
                                    <textarea name="steps[0][description]" value="" class="form-control rounded" rows="3" placeholder="Enter How It Work Description">{{ $howItWorkData['steps'][0]['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="my-3">
                            <h4>Step 2</h4> 
                            <div class="row mb-3">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Heading</label>
                                    <input type="text" name="steps[1][heading]" value="{{ $howItWorkData['steps'][1]['heading'] ?? '' }}" class="form-control rounded" placeholder="Enter How It Work Heading">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label">Description</label>
                                    <textarea name="steps[1][description]" value="" class="form-control rounded" rows="3" placeholder="Enter How It Work Description">{{ $howItWorkData['steps'][1]['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="my-3">
                            <h4>Step 3</h4> 
                            <div class="row mb-3">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Heading</label>
                                    <input type="text" name="steps[2][heading]" value="{{ $howItWorkData['steps'][2]['heading'] ?? '' }}" class="form-control rounded" placeholder="Enter How It Work Heading">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label">Description</label>
                                    <textarea name="steps[2][description]" value="" class="form-control rounded" rows="3" placeholder="Enter How It Work Description">{{ $howItWorkData['steps'][2]['description'] ?? '' }}</textarea>
                                </div>
                            </div> 
                            <div class="mb-3 text-end">
                                    <button type="submit" class="btn btn-success rounded bt n-sm">Save FAQ Section</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>          
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let faqIndex = 1;
        $('#add-faq-btn').on('click', function () {
            let rowCount = $('#faq-wrapper .faq-row').length + 1;
            let faqRow = `
                <tr class="faq-row">
                    <td class="faq-number">${rowCount}</td>
                    <td>
                        <input type="text" name="faqs[${rowCount}][question]" class="form-control rounded" placeholder="Enter Question">
                    </td>
                    <td>
                        <textarea name="faqs[${rowCount}][answer]" class="form-control rounded" rows="2" placeholder="Enter Answer"></textarea>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger rounded btn-sm remove-faq">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#faq-wrapper').append(faqRow);
            faqIndex++;
        });

        $(document).on('click', '.remove-faq', function () {
            if ($('#faq-wrapper .faq-row').length == 1) {
                alert('At least one FAQ is required.');
                return;
            }
            $(this).closest('tr').remove();
            updateFaqNumbers();
        });

        function updateFaqNumbers() {
            $('#faq-wrapper .faq-row').each(function(index) {
                $(this).find('.faq-number').text(index + 1);
            });
        }


        // category save scripts
        $(document).on('click', '.save-category', function() {

            let categoryId = $(this).data('category-id');
            let row = $(this).closest('tr');
            let imageInput = row.find(`input[name="image"]`)[0];
            let statusSelect = row.find(`select[name="status"]`);
            let formData = new FormData();
            formData.append('category_id', categoryId);
            if (imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            }
            formData.append('status', statusSelect.val());

            $.ajax({
                url: '{{ route("admin.organisation-category.update", ["category" => ":category"]) }}'.replace(':category', categoryId),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    row.find('.save-category').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving');
                    swal.fire({
                        title: 'Saving...',
                        text: 'Please wait while the category is being updated.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    swal.close();
                   if(response.success) {
                        swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        });
                        window.location.reload();
                    } else {
                        swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'An error occurred while updating the category.',
                        });
                    }
                    row.find('.save-category').prop('disabled', false).html('<i class="fa fa-save"></i> Save');
                },

                error: function(xhr) {
                    swal.close();

                    let errorMessage = 'An error occurred while updating the category.';

                    // Laravel validation errors (422)
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join('\n');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage
                    });

                    row.find('.save-category')
                        .prop('disabled', false)
                        .html('<i class="fa fa-save"></i> Save');
                }
            });
        });
    </script>
@endpush