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
                <button class="nav-link active" id="pills-faq-tab" data-bs-toggle="pill" data-bs-target="#pills-faq" type="button" role="tab" aria-controls="pills-faq" aria-selected="true">FAQs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-how-it-work-tab" data-bs-toggle="pill" data-bs-target="#pills-how-it-work" type="button" role="tab" aria-controls="pills-how-it-work" aria-selected="false">How It Works</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</button>
            </li>
        </ul>
        <div class="tab-content p-0 border-0" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-faq" role="tabpanel" aria-labelledby="pills-faq-tab" tabindex="0">
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
            <div class="tab-pane fade" id="pills-how-it-work" role="tabpanel" aria-labelledby="pills-how-it-work-tab" tabindex="0">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.page-sections.store') }}"
                            method="POST">
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
            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">...</div>
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
    </script>
@endpush