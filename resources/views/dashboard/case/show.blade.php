@section('css')
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    {{-- <script src="{{ asset('js/sweetalert2.min.js') }}"></script> --}}

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .info-box {
            min-height: 100px;
            background: #fff;
            width: 100%;
            margin-bottom: 20px;
            padding: 15px;
        }

        .info-box small {
            font-size: 14px;
        }

        .info-box .progress {
            background: rgba(0, 0, 0, .2);
            margin: 5px -10px 5px 0;
            height: 2px;
        }

        .info-box .progress,
        .info-box .progress .progress-bar {
            border-radius: 0;
        }

        .info-box .progress .progress-bar {
            background: #fff;
        }

        .info-box-icon {
            float: left;
            height: 70px;
            width: 70px;
            text-align: center;
            font-size: 30px;
            line-height: 74px;
            background: rgba(0, 0, 0, .2);
            border-radius: 100%
        }

        .info-box-icon.push-bottom {
            margin-top: 20px;
        }

        .info-box-icon>img {
            max-width: 100%
        }

        .info-box-content {
            padding: 10px 10px 10px 0;
            margin-left: 90px;
        }

        .info-box-number {
            font-weight: 300;
            font-size: 21px;
        }

        a.a_admin,
        a.a_admin:hover,
        a.a_admin:active,
        a.a_admin:focus {
            color: #f9b115;
        }

        a.a_sales,
        a.a_sales:hover,
        a.a_sales:active,
        a.a_sales:focus {
            color: #e55353;
        }

        a.a_lawyer,
        a.a_lawyer:hover,
        a.a_lawyer:active,
        a.a_lawyer:focus {
            color: #4638c2;
        }

        a.a_clerk,
        a.a_clerk:hover,
        a.a_clerk:active,
        a.a_clerk:focus {
            color: #2ca8ff;
        }

        a.a_account,
        a.a_account:hover,
        a.a_account:active,
        a.a_account:focus {
            color: #2eb85c;
        }

        .bg-done {
            background-color: #46be8a !important;
            color: white !important;
        }

        .bg-overdue {
            background-color: #e55353 !important;
            color: white !important;
        }

        .info-box-text,
        .progress-description {
            display: block;
            font-size: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;

            font-weight: 400;
        }

        .checklist_name {
            font-size: 12px;
        }

        .done .checklist_name {
            color: #33cabb;
        }

        .color-test {
            background-color: red;
        }

        @media print {
            .color-test {
                background-color: red;
            }
        }


        .fonticon-wrap {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .active-folder {
            margin-top: 20px;
            margin-bottom: 20px;
        }




        *.hidden {
            display: none !important;
        }

        div.loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(16, 16, 16, 0.5);
        }

        @-webkit-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-webkit-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-moz-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-ms-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-moz-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-webkit-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-o-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .uil-ring-css {
            margin: auto;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 200px;
            height: 200px;
        }

        .uil-ring-css>div {
            position: absolute;
            display: block;
            width: 160px;
            height: 160px;
            top: 20px;
            left: 20px;
            border-radius: 80px;
            box-shadow: 0 6px 0 0 #ffffff;
            -ms-animation: uil-ring-anim 1s linear infinite;
            -moz-animation: uil-ring-anim 1s linear infinite;
            -webkit-animation: uil-ring-anim 1s linear infinite;
            -o-animation: uil-ring-anim 1s linear infinite;
            animation: uil-ring-anim 1s linear infinite;
        }

        @media all {
            .page-break {
                display: block;
                background-color: gray;
                color: white;
                height: 50px;
                padding-top: 15px;
                width: 100%
            }

            .page-break-print {
                display: none;
            }
        }

        @media print {
            .page-break-print {
                display: block;
                page-break-after: always;
                ;
            }

            html,
            body {
                height: 100%;
                margin: 0px;
            }

            .page-break {
                display: none;
            }

            footer:after {
                content: "Footer Text";
                display: block;
                text-align: center;
            }


            /* div.divFooter {
                                                                        position: fixed;
                                                                        bottom: 0;
                                                                    } */

            div.divFooter {
                bottom: 0;
                color: #707070;
                height: 2em;
                left: 0;
                position: fixed;
                font-size: small;
                width: 100%;
                display: table-footer-group;
            }

            div.divFooter:after {
                counter-increment: page;
                content: counter(page);
            }
        }
    </style>

    <link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

    {{-- <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script> --}}
    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
@endsection

    <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
@extends('dashboard.base')
@section('content')
    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">

                <input class="form-control" type="hidden" value="" id="selected_bill_id" name="selected_bill_id">
                <input class="form-control" type="hidden" value="" id="selected_referral" name="selected_referral">
                
            <input id="current_bill_tab" type="hidden" value="voucher" />
                <input class="form-control" type="hidden" id="main_case_id" name="main_case_id"
                    value="{{ $case->id }}">
                {{-- <div class="col-xl-8 col-lg-8">
                    @include('dashboard.case.section.d-case-summary')
                </div> --}}


                <div id="div_case_summary" class="col-xl-8 col-lg-8">
                    @include('dashboard.case.section.d-case-summary')
                </div>
                <div class="col-xl-4 col-lg-4">
                    <div class="row">
                        <div id="div_case_client" class="col-12">
                            @include('dashboard.case.section.d-case-client')
                        </div>

                        <div id="div_case_team" class="col-12">
                            @include('dashboard.case.section.d-case-team')
                        </div>
                    </div>
                </div>



                {{-- <div id="div_case_summary" class="col-xl-8 col-lg-8">
                    @include('dashboard.case.section.d-case-summary')
                </div>

                <div id="div_case_client" class="col-xl-4 col-lg-4">
                    @include('dashboard.case.section.d-case-client')
                </div>

                <div id="div_case_team" class="col-xl-4 col-lg-4">
                    @include('dashboard.case.section.d-case-team')
                </div> --}}

            </div>

            <div class="nav-tabs-custom nav-tabs-custom-ctr">
                <ul class="nav nav-tabs scrollable-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link @if (!in_array($current_user->menuroles, ['account', 'maker'])) active @endif" data-toggle="tab" href="#notes"
                            role="tab" aria-controls="log" aria-selected="true">Notes<span style="margin-left:10px"
                                class="label bg-danger">{{ count($LoanCaseKIVNotes) }}</span></a>
                    </li>

                    <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#home-1" role="tab"
                            aria-controls="home" aria-selected="false">Checklist</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#profile-1" role="tab"
                            aria-controls="profile" aria-selected="false">Master List</a></li>

                    <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#trust" role="tab"
                            aria-controls="trust" aria-selected="true">Trust</a></li>

                    <li class="nav-item"><a class="nav-link @if (in_array($current_user->menuroles, ['account', 'maker'])) active @endif"
                            data-toggle="tab" href="#messages-1" role="tab" aria-controls="messages"
                            aria-selected="true">Bills</a></li>

                    @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::MarketingNotePermission()) == true)
                        <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#account_notes" role="tab"
                                aria-controls="trust" aria-selected="true">Marketing Notes<span style="margin-left:10px"
                                    class="label bg-danger">{{ count($LoanCaseNotes) }}</span></a></li>
                    @endif

                    @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::LedgerPermission()) == true)
                        <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#ledger" role="tab"
                                aria-controls="trust" aria-selected="true">Ledger</a></li>
                    @endif

                    @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::MarketingBillPermission()) == true)
                        <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#marketing-bill" role="tab"
                                aria-controls="trust" aria-selected="true">Account</a></li>
                    @endif

                    <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#attachment" role="tab"
                            aria-controls="trust" aria-selected="true">Attachment</a></li>

                    <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#documents" role="tab"
                            aria-controls="document" aria-selected="true">Documents</a></li>

                    @if ($current_user->management == 1)
                        <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#notes-pnc" role="tab"
                                aria-controls="log" aria-selected="true">P&C Notes<span style="margin-left:10px"
                                    class="label bg-danger">{{ count($LoanCasePNCNotes) }}</span></a></li>
                    @endif

                    @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ClaimsPermission()) == true)
                        <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#claims" role="tab"
                                aria-controls="home" aria-selected="false">Claims</a></li>
                    @endif

                </ul>
                <div class="tab-content">

                    <div class="tab-pane @if (!in_array($current_user->menuroles, ['account', 'maker'])) active @endif" id="notes" role="tabpanel">
                        @include('dashboard.case.tabs.tab-notes-all')
                    </div>

                    <div class="tab-pane " id="home-1" role="tabpanel">
                        {{-- @include('dashboard.case.tabs.tab-case2') --}}
                        @include('dashboard.case.tabs.tab-case3')
                    </div>

                    <div class="tab-pane" id="profile-1" role="tabpanel">
                        @include('dashboard.case.tabs.tab-master-list')
                    </div>

                    <div class="tab-pane " id="trust" role="tabpanel">
                        @include('dashboard.case.tabs.tab-trust')
                    </div>

                    <div class="tab-pane @if (in_array($current_user->menuroles, ['account', 'maker'])) active @endif" id="messages-1" role="tabpanel"
                        style="width:100%;overflow-x:auto">
                        @include('dashboard.case.tabs.tab-bill')
                    </div>

                    @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::MarketingNotePermission()) == true)
                        <div class="tab-pane " id="account_notes" role="tabpanel">
                            @include('dashboard.case.tabs.tab-notes')
                        </div>
                    @endif

                    @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::LedgerPermission()) == true)
                        <div class="tab-pane " id="ledger" role="tabpanel">
                            @include('dashboard.case.tabs.tab-ledger')
                        </div>
                    @endif

                    @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::MarketingBillPermission()) == true)
                        <div class="tab-pane " id="marketing-bill" role="tabpanel">
                            @include('dashboard.case.tabs.tab-marketing-bill')
                        </div>
                    @endif

                    <div class="tab-pane " id="documents" role="tabpanel">
                        @include('dashboard.case.tabs.tab-documents')
                    </div>

                    @if ($current_user->management == 1)
                        <div class="tab-pane " id="notes-pnc" role="tabpanel">
                            @include('dashboard.case.tabs.tab-notes-pnc')
                        </div>
                    @endif

                    <div class="tab-pane " id="attachment" role="tabpanel">
                        @include('dashboard.case.tabs.tab-attachment')
                    </div>

                    @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ClaimsPermission()) == true)
                        <div class="tab-pane " id="claims" role="tabpanel">
                            @include('dashboard.case.tabs.tab-claims')
                        </div>
                    @endif


                </div>
            </div>

            {{-- @include('dashboard.case.voucher', [
                'customer' => $customer,
                'case' => $case,
                'parameters' => $parameters,
            ]) --}}

            @include('dashboard.case.d-action')
            @include('dashboard.case.d-notes')
            @include('dashboard.case.d-file')
            @include('dashboard.case.d-voucher')
            @include('dashboard.case.d-dispatch')
            @include('dashboard.case.d-trust')
            @include('dashboard.case.d-trust-edit')
            @include('dashboard.case.d-bill')
            @include('dashboard.case.d-bill-list')
            @include('dashboard.case.d-bill-create')
            @include('dashboard.case.d-edit-quotation')
            {{-- @include('dashboard.case.d-bill-entry') --}}
            @include('dashboard.case.section.d-billv2')
            @include('dashboard.case.d-file-template-list')
            <div id="div-print-inv">
                {{-- @include('dashboard.case.d-invoice-print') --}}
            </div>

            <div id="div-print-bill">
                {{-- @include('dashboard.case.d-quotation-print') --}}
            </div>

            @include('dashboard.case.d-upload-marketing-bill')

            @include('dashboard.case.modal.modal-close-file')
            @include('dashboard.case.modal.modal-receipt')

            @include('dashboard.case.modal.modal-date-close-file')
            @include('dashboard.case.modal.modal-create-bill')
            @include('dashboard.case.modal.modal-upload')
            @include('dashboard.case.modal.modal-request-voucher')
            @include('dashboard.case.modal.modal-receive-bill')
            @include('dashboard.case.modal.modal-masterlist-shortcut')
            @include('dashboard.case.modal.modal-case-summary')
            @include('dashboard.case.modal.modal-invoice-date')
            @include('dashboard.case.modal.modal-sst-rate')
            @include('dashboard.case.modal.modal-client')
            @include('dashboard.case.modal.modal-account-summary-input')
            @include('dashboard.case.modal.modal-referral', ['newcase' => 0])

            @include('dashboard.case.modal.modal-move-disb')
            @include('dashboard.case.modal.modal-trust')
            @include('dashboard.case.modal.modal-add-billto')
            @include('dashboard.case.modal.modal-add-billto-info')
        </div>
    </div>
@endsection

@section('javascript')
    {{-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> --}}
    <script src="{{ asset('js/paperfish/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/paperfish/jquery.bootstrap.wizard.js') }}"></script>

    <script src="{{ asset('js/paperfish/paper-bootstrap-wizard.js') }}"></script>
    <script src="{{ asset('js/paperfish/jquery.validate.min.js') }}"></script>
    <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/PrintArea.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
    <script src="{{ asset('js/jquery.print.js?0001') }}"></script>
    <script src="{{ asset('js/jquery.toast.min.js') }}"></script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script>
         
        Dropzone.autoDiscover = false;
        var drop = document.getElementById('form_file');
        var dropModal = document.getElementById('form_file_modal');
        // var drop = document.getElementsByClassName('dropzone');

        var myDropzone = new Dropzone(drop, {
            url: "/CaseFileUpload",
            addRemoveLinks: true,
            autoProcessQueue: false,
            maxFilesize: 70, // MB
            maxFiles: 5,
            uploadMultiple: true,
            parallelUploads: 10,
            sending: function(file, xhr, formData) {
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("type", $("#type_list").val());
                formData.append("case_id", $("#main_case_id").val());
                formData.append("file_type", $("#file_type").val());
                formData.append("file_remark", $("#file_remark").val());
            },
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    this.removeFile(file);
                    // showAlert("File Limit exceeded!", "error");
                });
                this.on("addedfile", function() {
                    if ($("#file_type").val() == 1) {
                        $(".dz-preview:last").append(`<br>
                            <select class="form-control" id="type_list" name="type[]">
                                    @foreach ($attachment_type as $index => $type)
                                        @if ($type->parameter_value_3 == 1)
                                            @if (in_array($current_user->menuroles, ['admin', 'account', 'maker']) || in_array($current_user->id, [51]))
                                            <option value="{{ $type->parameter_value_2 }}" @if ($type->parameter_value_2 == 6) selected @endif >{{ $type->parameter_value_1 }}</option>
                                            @endif
                                            
                                        @else
                                            <option value="{{ $type->parameter_value_2 }}" @if ($type->parameter_value_2 == 4) selected @endif>
                                                {{ $type->parameter_value_1 }}</option>
                                        @endif
                                    @endforeach
                            </select> <br>
                            <div class="col">
                                                    <label>Remarks</label>
                                                    <textarea class="form-control" id="file_remark" name="remark[]" rows="3"></textarea>
                                                </div>
                            `);
                    } else {
                        $(".dz-preview:last").append(`<br>
                            <select class="form-control" id="type_list" name="type[]" style="display:none">
                                <option value="5"  selected  >Account</option>
                            </select> <br>
                            <div class="col">
                                                    <label>Remarks</label>
                                                    <textarea class="form-control" id="file_remark" name="remark[]" rows="3"></textarea>
                                                </div>
                            `);
                    }

                });
            },
            success: function(file, response) {
                // console.log(response);
                $.each(myDropzone.files, function(i, file) {
                    file.status = Dropzone.QUEUED
                });

                if (response.status == 1) {
                    $("#div_full_screen_loading").hide();
                    // Swal.fire('Success!', response.message, 'success');

                    viewMode();
                    toastController('Attachment Uploaded');
                    this.removeAllFiles(true);
                    $('#div_case_attachment').html(response.LoanAttachment);
                    $('#div_case_marketing_attachment').html(response.LoanAttachmentMarketing);

                    iniAttachmentTable()

                    document.getElementById("form_file").reset();

                    this.removeAllFiles(true);
                } else {

                }
            },
            error: function(file, response) {
                $.each(myDropzone.files, function(i, file) {
                    file.status = Dropzone.QUEUED
                });
                $("#div_full_screen_loading").hide();
            }


        });

        var myDropzoneModal = new Dropzone(dropModal, {
            url: "/CaseFileUpload",
            addRemoveLinks: true,
            autoProcessQueue: false,
            maxFilesize: 70, // MB
            maxFiles: 5,
            uploadMultiple: true,
            parallelUploads: 10,
            sending: function(file, xhr, formData) {
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("type", $("#type_list_modal").val());
                formData.append("case_id", $("#main_case_id").val());
                formData.append("checklist_id", $("#checklist_id").val());
                formData.append("file_type", $("#file_type_modal").val());
                formData.append("file_remark", $("#file_remark_modal").val());
            },
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    this.removeFile(file);
                    // showAlert("File Limit exceeded!", "error");
                });
                this.on("addedfile", function() {
                    if ($("#file_type").val() == 1) {
                        $(".dz-preview:last").append(`<br>
                            <select class="form-control" id="type_list_modal" name="type[]"  style="display:none">
                                    @foreach ($attachment_type as $index => $type)
                                        @if ($type->parameter_value_2 == 9)
                                            <option value="{{ $type->parameter_value_2 }}" @if ($type->parameter_value_2 == 4) selected @endif>
                                                {{ $type->parameter_value_1 }}</option>
                                        @endif
                                    @endforeach
                            </select> <br>
                            <div class="col" style="display:none">
                                                    <label>Remarks</label>
                                                    <textarea class="form-control" id="file_remark_modal" name="remark[]" rows="3"></textarea>
                                                </div>
                            `);
                    } else {
                        $(".dz-preview:last").append(`<br>
                            <select class="form-control" id="type_list" name="type[]" style="display:none">
                                <option value="5"  selected  >Account</option>
                            </select> <br>
                            <div class="col">
                                                    <label>Remarks</label>
                                                    <textarea class="form-control" id="file_remark" name="remark[]" rows="3"></textarea>
                                                </div>
                            `);
                    }

                });
            },
            success: function(file, response) {
                // console.log(response);
                $.each(myDropzoneModal.files, function(i, file) {
                    file.status = Dropzone.QUEUED
                });

                if (response.status == 1) {
                    $("#div_full_screen_loading").hide();
                    // Swal.fire('Success!', response.message, 'success');

                    closeUniversalModal();
                    toastController('Attachment Uploaded');
                    this.removeAllFiles(true);
                    $('#div_case_attachment').html(response.LoanAttachment);
                    $('#div_case_marketing_attachment').html(response.LoanAttachmentMarketing);
                    $('#home-1').html(response.CheckListMain);

                    iniAttachmentTable()

                    document.getElementById("form_file_modal").reset();

                    this.removeAllFiles(true);
                } else {

                }
            },
            error: function(file, response) {
                $.each(myDropzoneModal.files, function(i, file) {
                    file.status = Dropzone.QUEUED
                });
                $("#div_full_screen_loading").hide();
            }


        });

        function newUpload() {

            if (myDropzone.getAcceptedFiles().length <= 0) {
                Swal.fire('Notice!', 'No file selected', 'warning');
                return;
            }

            $("#div_full_screen_loading").show();
            myDropzone.processQueue();
        }

        function newUploadModal() {
            if (myDropzoneModal.getAcceptedFiles().length <= 0) {
                Swal.fire('Notice!', 'No file selected', 'warning');
                return;
            }

            $("#div_full_screen_loading").show();
            myDropzoneModal.processQueue();
        }


        CKEDITOR.replace('summary-ckeditor');
        CKEDITOR.config.height = 300;
        CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
        CKEDITOR.config.removeButtons = 'Image';
        // CKEDITOR.config.removeButtons = 'Source,JustifyCenter';

        if (document.getElementById("ddlPrintFormal") != null) {
            document.getElementById("ddlPrintFormal").onchange = function() {

                var isFormal = $("#ddlPrintFormal").val();

                if (isFormal == 1) {
                    $(".print-formal").removeClass("no-print");
                } else {
                    $(".print-formal").addClass("no-print");
                }
            }
        }

        // if (document.getElementById("ddl_party") != null) {
        //     document.getElementById("ddl_party").onchange = function() {

        //         var billto = $("#ddl_party").val();

        //         $("#p-quo-client-name").html(billto);
        //     }
        // }

        // if (document.getElementById("ddl_party_inv") != null) {
        //     document.getElementById("ddl_party_inv").onchange = function() {

        //         var billto = $("#ddl_party_inv").val();

        //         $("#p-quo-client-name-inv").html(billto);
        //     }
        // }

        // $(document).ready(function() {
        //   setTimeout(function() {
        //     stepController(4);
        //     $( "#checklist_2081")[0].scrollIntoView();
        //   }, 5000);
        // });

        function exportTableToExcelbak() {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById('tbl-ledger-data');
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

            filename = 'invoice_report' + Date.now();

            // Specify file name
            filename = filename ? filename + '.xls' : 'excel_data.xls';

            // Create download link element
            downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            if (navigator.msSaveOrOpenBlob) {
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

                // Setting the file name
                downloadLink.download = filename;

                //triggering the function
                downloadLink.click();
            }
        }

        function exportTableToExcel() {
            var tab_text = "<table border='2px'>";
            var textRange;
            var j = 0;
            tab = document.getElementById('tbl-ledger-data'); // id of table

            for (j = 0; j < tab.rows.length; j++) {
                if (j == 2) {
                    tab_text = "<tr style='background-color:black;color:white'>" + tab_text + tab.rows[j].innerHTML +
                        "</tr>";
                } else {
                    tab_text = "<tr >" + tab_text + tab.rows[j].innerHTML + "</tr>";
                }

                //tab_text=tab_text+"</tr>" ;
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
            {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
            } else //other browser not tested on IE 11
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

            return (sa);
        }

        function exportTableToExcelDisb2() {
            var tab_text = "<table border='2px'>";
            var textRange;
            var j = 0;
            tab = document.getElementById('tbl-disb-case'); // id of table

            for (j = 0; j < tab.rows.length; j++) {
                if (j == 2) {
                    tab_text = "<tr style='background-color:black;color:white'>" + tab_text + tab.rows[j].innerHTML +
                        "</tr>";
                } else {
                    tab_text = "<tr >" + tab_text + tab.rows[j].innerHTML + "</tr>";
                }

                //tab_text=tab_text+"</tr>" ;
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
            {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
            } else //other browser not tested on IE 11
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

            return (sa);
        }

        function exportTableToExcelDisb() {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var textRange;
            var j = 0;
            tab = document.getElementById('tbl-disb-case'); // id of table
            var cloneTable = tab.cloneNode(true);
            jQuery(cloneTable).find('.remove-this').remove();

            tab = cloneTable;

            for (j = 0; j < tab.rows.length; j++) {
                tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
                //tab_text=tab_text+"</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replaceAll(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
            tab_text = tab_text.replaceAll(/<br\s*\/?>/ig, '<br style="mso-data-placement:same-cell;" />');


            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
            {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, "file.xlsx");
            } else //other browser not tested on IE 11
            {
                var a = document.createElement('a');
                //getting data from our div that contains the HTML table
                var data_type = 'data:application/vnd.ms-excel';
                var table_div = document.getElementById('tbl-disb-case');
                var table_html = table_div.outerHTML.replace(/ /g, '%20');
                a.href = data_type + ', ' + encodeURIComponent(tab_text);
                //setting the file name
                a.download = 'disbursement_report_' + Date.now() + '.xls';
                //triggering the function
                a.click();
                //just in case, prevent default behaviour
                e.preventDefault();
            }

            return (sa);
        }

        function printlo() {
            if ($("#p-quo-client-name").html() == '') {
                Swal.fire('Notice!', 'Please select party before print', 'warning');
                return;
            }

            $("#dQuotationInvoice-p").print({
                addGlobalStyles: true,
                stylesheet: true,
                rejectWindow: true,
                noPrintSelector: ".no-print",
                iframe: false,
                append: null,
                prepend: null
            });
        }

        function printloinv() {
            if ($("#p-inv-client-name").html() == '') {
                Swal.fire('Notice!', 'Please select party before print', 'warning');
                return;
            }

            $("#dInvoice-p").print({
                addGlobalStyles: true,
                stylesheet: true,
                rejectWindow: true,
                noPrintSelector: ".no-print",
                iframe: false,
                append: null,
                prepend: null
            });
        }

        function printloReceipt() {


            $("#dReceipt-p").print({
                addGlobalStyles: true,
                stylesheet: true,
                rejectWindow: true,
                noPrintSelector: ".no-print",
                iframe: false,
                title: $("#receipt_name").val(),
                append: null,
                prepend: null
            });
        }

        function printSummary() {
            // window.print();

            // $("#dVoucherInvoice").print();

            // jQuery.print();

            $("#dSummaryReport").print({
                addGlobalStyles: true,
                stylesheet: true,
                rejectWindow: true,
                noPrintSelector: ".no-print",
                iframe: false,
                append: null,
                prepend: null
            });
        }

        $(".file-item-action").click(function() {
            // alert(this.id);
            $(".tab-pane-folder").hide();
            $(".tab-pane-" + this.id).show();
        });


        if (document.getElementById("template_cat") != null) {
            document.getElementById("template_cat").onchange = function() {

                var cat_id = $("#template_cat").val();

                $(".all_cat").hide();
                $(".temp_cat_" + cat_id).show();

                $("#template").val(0)

                if (cat_id != 0) {
                    $('#template').attr('disabled', false);
                } else {
                    $('#template').attr('disabled', true);
                }

            }
        } else {
            document.getElementById("ddl_payment_type").onchange = function() {

                // $(".dPaymentType").hide();
                // $(".dChequeNo").hide();
                // $(".dBankTransfer").hide();

                // if ($("#ddl_payment_type").val() == "2") {
                //   $(".dChequeNo").show();
                // } else if ($("#ddl_payment_type").val() == "3") {
                //   $(".dBankTransfer").show();
                // } else if ($("#ddl_payment_type").val() == "4") {
                //   $(".dCreditCard").show();
                // } else {
                //   $(".dChequeNo").hide();
                //   $(".dBankTransfer").hide();
                // }
            }


            // $(".ddl_payment_type").change(function() {

            //   $(".dPaymentType").hide();

            //   if (this.value == "2") {
            //     $(".dChequeNo").show();
            //   } else if (this.value == "3") {
            //     $(".dBankTransfer").show();
            //   } else if (this.value == "4") {
            //     $(".dCreditCard").show();
            //   } else {
            //     $(".dChequeNo").hide();
            //     $(".dBankTransfer").hide();
            //   }
            // });
        }





        function actionMode(id, caseId) {
            $(".nav-tabs-custom-ctr").hide();
            $("#div_action").show();

            $("#action").val($("#act_" + id).val());
            $("#remarks").html($("#remark_" + id).val());
            $("#check_list_status").val($("#status_" + id).val());
            $("#selected_id").val(id);
            $("#case_id_action").val(caseId);


            if ($("#file_" + id).val() == "0") {
                $("#field_file").hide();
            } else {
                $("#field_file").show();
            }

        }

        function getAccountTemplate(template_id) {
            var form_data = new FormData();

            $.ajax({
                type: 'GET',
                url: '/getBillTemplate/' + template_id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        location.reload();
                    }

                }
            });
        }

        function voucherMode(id, case_id) {

            var available_amt = $("#amt_" + id).html();
            if (available_amt == 0) {
                Swal.fire('Notice!', 'No more available amount for this item', 'error');
                return;
            }

            $(".nav-tabs-custom-ctr").hide();
            $("#dVoucher").show();

            $("#item").val($("#item_" + id).html());
            $("#td_item_name").html($("#item_" + id).html());

            $("#account_details_id").val(id);
            $("#available_amt").val($("#amt_" + id).html());
            $("#amt").val(0);

        }

        function notesMode(type) {
            $("#note_type").val(type);
            $(".nav-tabs-custom-ctr").hide();
            $("#note_edit_mode").val(0);
            $("#dNotes").show();

            CKEDITOR.instances['summary-ckeditor'].setData('');
        }

        function notesEditMarketingMode(id) {
            $("#note_type").val(1);
            $(".nav-tabs-custom-ctr").hide();
            $("#note_edit_mode").val(1);
            $("#note_edit_id").val(id);
            $("#dNotes").show();


            CKEDITOR.instances['summary-ckeditor'].setData($("#notes_marketing_" + id).html());
        }

        function notesEditMode(id) {
            $("#note_type").val(2);
            $(".nav-tabs-custom-ctr").hide();
            $("#note_edit_mode").val(1);
            $("#note_edit_id").val(id);
            $("#dNotes").show();


            CKEDITOR.instances['summary-ckeditor'].setData($("#notes_" + id).html());
        }

        function notesEditPncMode(id) {
            $("#note_type").val(3);
            $(".nav-tabs-custom-ctr").hide();
            $("#note_edit_mode").val(1);
            $("#note_edit_id").val(id);
            $("#dNotes").show();


            CKEDITOR.instances['summary-ckeditor'].setData($("#notes_pnc_" + id).html());
        }

        function fileMode(id, case_id) {
            $(".nav-tabs-custom-ctr").hide();
            $("#case_id").val(case_id);
            $("#selected_id").val(id);
            $("#file_type").val(1);
            $("#div_attachment_type").show();

            // $(".need-remark").hide();

            $("#dFile").show();
        }

        function marketingBillMode(id, case_id) {
            $(".nav-tabs-custom-ctr").hide();
            // $("#case_id").val(case_id);
            // $("#selected_id").val(id);
            $("#div_attachment_type").hide();
            $(".need-remark").show();
            $("#file_type").val(2);
            $("#dFile").show();
        }

        function fileTemplateListMode(id, case_id) {
            $(".nav-tabs-custom-ctr").hide();
            $("#case_id").val(case_id);
            $("#selected_id").val(id);
            $("#dFileTemplateList").show();
        }

        function dispatchMode(id, case_id) {
            $(".nav-tabs-custom-ctr").hide();
            $("#case_id_dispatch").val(case_id);
            $("#selected_id").val(id);
            $("#dDispatch").show();
        }

        function trustModeV2($mode) {
            $("#header-trust-entry").html('Receiving Trust');
            $(".mode-trust-request").hide();
            $(".mode-trust-receive").hide();
            $(".mode-trust-edit").hide();

            var form = $("#form_trust");


            var transaction_id = form.find('[name=transaction_id]').val();

            if ($mode == 'receive') {
                document.getElementById("form_trust").reset();
                form.find('[name=amount]').prop('readonly', false);
                $(".mode-trust-receive").show();
            } else if ($mode == 'request') {
                $(".mode-trust-request").show();
                document.getElementById("form_trust").reset();
                form.find('[name=amount]').prop('readonly', false);
            } else if ($mode == 'edit') {
                
                $(".mode-trust-edit").show();
                if ($("#trust_is_recon").val() == 1) { 
                    $("#btnUpdateTrustReceive").hide();
                    $("#div_recon_text_trust").show();
                } else {
                    if ($("#account_approval").val() == 1 || transaction_id != "")
                    {
                        @if (!in_array($current_user->menuroles, ['admin', 'account', 'maker']))
                        $("#btnUpdateTrustReceive").hide();
                        $("#div_recon_text_trust").hide();
                        @endif
                    }
                    else
                    {
                        $("#btnUpdateTrustReceive").show();
                        $("#div_recon_text_trust").hide();
                    }
                    
                }
            }

            $(".div_office_bank_account").hide();
            @if (in_array($current_user->menuroles, ['account', 'admin', 'management']))
                $(".div_office_bank_account").show();
            @endif


        }

        function trustEditMode(id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/get_trust_value/' + id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status == 1) {

                        var form = $("#form_trust");

                        form.find('[name=payee]').val(data.data.payee);
                        form.find('[name=remark]').val(data.data.remark);
                        form.find('[name=payment_type]').val(data.data.payment_type);
                        form.find('[name=payment_date]').val(data.data.payment_date);
                        form.find('[name=office_account_id]').val(data.data.office_account_id);
                        form.find('[name=amount]').val(data.data.total_amount);
                        form.find('[name=transaction_id]').val(data.data.transaction_id);
                        form.find('[name=cheque_no]').val(data.data.cheque_no);
                        form.find('[name=bank_account]').val(data.data.bank_account);
                        form.find('[name=bank_id]').val(data.data.bank_id);
                        form.find('[name=credit_card_no]').val(data.data.credit_card_no);
                        form.find('[name=adjudication_no]').val(data.data.adjudication_no);
                        form.find('[name=email]').val(data.data.email);
                        form.find('[name=trust_id]').val(data.data.id);
                        console.log(data.data.transaction_id);

                        if(data.data.voucher_type == 3 || data.data.voucher_type == 2)
                        {
                            if (data.data.transaction_id == '' || data.data.transaction_id == null) {
                                form.find('[name=amount]').prop('readonly', false);
                            } else {
                                form.find('[name=amount]').prop('readonly', true);
                            }
                        }
                        else
                        {
                            if (data.data.transaction_id == '' || data.data.transaction_id == null) {
                                form.find('[name=amount]').prop('readonly', false);
                            } else {
                                form.find('[name=amount]').prop('readonly', true);
                            }
                        }
                        
                        $("#trust_is_recon").val(data.data.is_recon);
                        $("#account_approval").val(data.data.account_approval);
                        $("#trust_id").val(id);

                        trustModeV2('edit');
                    }

                }

            });


        }

        function billReceiveEditMode(id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/get_bill_receive_value/' + id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {

                        var form = $("#form_bill_receive");

                        form.find('[name=payee_name]').val(data.data.payee);
                        form.find('[name=payment_desc]').val(data.data.remark);
                        form.find('[name=payment_type]').val(data.data.payment_type);
                        form.find('[name=payment_date]').val(data.data.payment_date);
                        form.find('[name=office_account_id]').val(data.data.office_account_id);
                        form.find('[name=amount]').val(data.data.amount);
                        form.find('[name=transaction_id]').val(data.data.transaction_id);
                        form.find('[name=cheque_no]').val(data.data.cheque_no);
                        form.find('[name=bank_account]').val(data.data.bank_account);
                        form.find('[name=bank_id]').val(data.data.bank_id);
                        form.find('[name=credit_card_no]').val(data.data.credit_card_no);
                        form.find('[name=trust_id]').val(data.data.id);

                        if (data.data.is_recon == 1) {
                            $("#btnUpdateBillReceive").hide();
                            $("#div_recon_text").show();
                        } else {
                            $("#btnUpdateBillReceive").show();
                            $("#div_recon_text").hide();
                        }

                        // trustmode = 3;

                        $("#div-bill-receive").hide();
                        $("#bill_receive_id").val(id);

                        $("#dBillReceiveV2").show();

                        // $("#dTrustEdit").show();
                    }

                }

            });
        }

        function billReceiveMainEditMode(id) {
            $sum = 0;
            $count = 0;
            $trx_id = '';
            $payment_desc = '';
            $payee = '';
            $voucher_no = '';

            $.each($("input[name='receive_list']:checked"), function() {
                // $sum += Number($(this).val());
                // $count += 1;

                var itemID = $(this).val();

                $sum += Number($("#recv_amount_" + itemID).val());

                if ($count > 0) {
                    $trx_id += ',';
                    $payment_desc += ',';
                    $payee += ',';
                    $voucher_no += ',';
                }

                $trx_id = $trx_id + $("#recv_trx_id_" + itemID).val();
                $voucher_no = $voucher_no + $("#recv_voucher_no_" + itemID).val();
                $payment_desc = $payment_desc + $("#recv_remarks_" + itemID).val();
                $payee = $payee + $("#recv_payee_" + itemID).val();


                $count += 1;
                // var status = 0;
                // var notApplicable = 0;

                // $("#ori_remark_" + itemID).html($("#edited_remarks_" + itemID).val());
                // $("#remark_" + itemID).val($("#edited_remarks_" + itemID).val());

                // $("#checklist_" + itemID).removeClass("bg-done ion-checkmark ion-person bg-aqua ion-clock bg-overdue ion-clock bg-info");

            });

            if ($count == 0) {

                Swal.fire('notice!', 'No payment selected', 'warning');
                return;
            }
            $("#div-bill-receive").hide();
            $("#bill_receive_id").val(id);

            var form = $("#form_bill_main_info");

            form.find('[name=amount]').val($sum.toFixed(2));
            form.find('[name=transaction_id]').val($trx_id);
            form.find('[name=payment_desc]').val($payment_desc);
            form.find('[name=payee_name]').val($payee);
            form.find('[name=voucher_no]').val($voucher_no);

            $("#dBillMainPrint").show();

        }

        function trustReceiveMainEditMode(id) {
            $sum = 0;
            $count = 0;
            $trx_id = '';
            $payment_desc = '';
            $payee = '';

            // $.each($("input[name='receive_list']:checked"), function() {
            //   // $sum += Number($(this).val());
            //   // $count += 1;

            //   var itemID = $(this).val();

            //   $sum += Number($("#recv_amount_" + itemID).val());

            //   if ($count > 0) {
            //     $trx_id += ',';
            //     $payment_desc += ',';
            //     $payee += ',';
            //   }

            //   $trx_id = $trx_id + $("#recv_trx_id_" + itemID).val();
            //   $payment_desc = $payment_desc + $("#recv_remarks_" + itemID).val();
            //   $payee = $payee + $("#recv_payee_" + itemID).val();


            //   $count += 1;
            //   // var status = 0;
            //   // var notApplicable = 0;

            //   // $("#ori_remark_" + itemID).html($("#edited_remarks_" + itemID).val());
            //   // $("#remark_" + itemID).val($("#edited_remarks_" + itemID).val());

            //   // $("#checklist_" + itemID).removeClass("bg-done ion-checkmark ion-person bg-aqua ion-clock bg-overdue ion-clock bg-info");

            // });

            // if ($count == 0) {

            //   Swal.fire('notice!', 'No payment selected', 'warning');
            //   return;
            // }
            // $("#div-bill-receive").hide();
            // $("#bill_receive_id").val(id);

            // var form = $("#form_bill_main_info");

            // form.find('[name=amount]').val($sum.toFixed(2));
            // form.find('[name=transaction_id]').val($trx_id);
            // form.find('[name=payment_desc]').val($payment_desc);
            // form.find('[name=payee_name]').val($payee);

            $("#form_trust_main").show();

        }

        function cancelBillReceiveEdit() {
            $("#div-bill-receive").show();
            // $("#case_id_trust").val(case_id);

            $("#dBillReceiveV2").hide();
            $("#dBillMainPrint").hide();
        }



        function billEntryMode(case_id) {
            $(".nav-tabs-custom-ctr").hide();
            $(".d_operation").hide();

            // $("#case_id_trust").val(case_id);
            // $("#selected_id").val(id);
            $("#dBillEntry").show();
        }

        function billMode(case_id) {
            $(".nav-tabs-custom-ctr").hide();
            $("#case_id_bill").val(case_id);
            // $("#selected_id").val(id);
            $("#dBill").show();
        }

        function billCreateMode(case_id) {
            $(".nav-tabs-custom-ctr").hide();
            $("#case_id_bill").val(case_id);
            // $("#selected_id").val(id);
            $("#dBillCreate").show();
        }

        function exitBillCreateMode() {

            $("#dBillList").show();
            $("#dBillv2").hide();
            // $("#dBillv2").show();
        }

        function billListMode(bill_id, bill_name) {
            $('#current_bill_tab').val('voucher');
            $(".tab-bill").removeClass("active");
            $(".tab-bill_link").removeClass("active");
            $(".tab-bill-voucher").addClass("active");
            $(".nav-tabs-custom-ctr").hide();
            $("#case_id_bill").val(bill_id);
            $("#selected_bill_id").val(bill_id);
            // $("#selected_id").val(id);
            $("#dBillList").show();
            $("#bill_name").html(bill_name);
            loadCaseBill(bill_id);
        }

        function quotationPrintMode() {
            $("#dBillList").hide();
            $("#dQuotationInvoice-p").show();
        }



        function cancelQuotationPrintMode() {
            $("#dBillList").show();
            $("#dQuotationInvoice-p").hide();
        }

        // function invoicePrintMode() {
        //     $("#dBillList").hide();
        //     $("#dInvoice-p").show();
        // }

        function invoicePrintMode(party_id) {
            $("#dBillList").hide();
            $("#dInvoice-p").show();

            if(party_id == null)
            {
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/loadBillToInvWIthInvoice/' + party_id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        $(".party_invoice_info").html(data.view);
                        $("#inv_no_print").html(data.inv_no);
                        
                    }

                },
                complete: function() {
                    blnAjax == true;
                    $("#div_full_screen_loading").hide();
                },
                error: function(data) {
                    toastController('Some error occur, please try again or contact developer', 'warning');
                    $("#div_full_screen_loading").hide();

                }
            });
        }



        function cancelInvoicePrintMode() {
            $("#dBillList").show();
            $("#dInvoice-p").hide();
        }



        function trustReceiptPrintMode(transaction_id) {
            $("#dTrustList").hide();
            $("#dTrustReceipt-p").show();
        }

        function cancelTrustReceiptPrintMode() {
            $("#dTrustList").show();
            $("#dTrustReceipt-p").hide();

        }

        function billModeGroup() {
            var favorite = [];
            var strRows = '';
            var tbl_content = '';
            var count = 0;

            $.each($("input[name='case_bill']:checked"), function() {

                itemID = $(this).val();
                itemName = $("#item_" + $(this).val()).html();
                availableAmount = $("#amt_" + $(this).val()).html();

                count += 1;

                strRows += '<tr  ><td>' + itemName + '</td><td class="text-center">' + availableAmount +
                    '</td><td class="text-center"><input class="form-control" type="text" id="request_amt_' + $(
                        this).val() + '" value="0" ></td></tr>'

                tbl_content += `
            <tr id="row_bill_` + itemID + `">
            <td class="text-center">` + count + `</td>
            <td class="row_id hide">` + itemID + `</td>
            <td class="item_name">` + itemName + `</td>
            <td class="text-left available_number">RM ` + availableAmount + `<input id="txt_available_num_` + itemID +
                    `" class="form-control txt_available_num_" type="hidden" id="txt_available_num_` + $(this)
                    .val() + `" value="` + availableAmount +
                    `" ></td>
            <td name="user_amount" ><input onclick="clearErrorHighlight()" onchange="calculateVoucherSum('#txt_request_amt_` +
                    itemID +
                    `')" class="form-control input_field" type="number" id="txt_request_amt_` + itemID +
                    `" value="0" ><span class="span_error_text" id="txt_error_span_` + $(this).val() + `" style="display:none;" class="text-danger"></span></td>
          </tr>
      `;

            });

            console.log(strRows.length);

            if (strRows.length > 0) {
                var now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                $("#voucher_payment_time").val(now.toISOString().slice(0, 16));

                $("#tbl-bill-group").html(tbl_content);

                $(".nav-tabs-custom-ctr").hide();
                $("#dBillList").hide();
                $("#case_id_bill").val(case_id);
                // $("#selected_id").val(id);
                $("#dBillv2").show();
            } else {
                Swal.fire('notice!', 'No bill selected', 'warning');
            }

        }

        function RequestVoucherModal() {

            var count = 0;
            var strRows = '';
            var tbl_content = '';

            $.each($("input[name='case_bill']:checked"), function() {

                itemID = $(this).val();
                itemName = $("#item_" + $(this).val()).html();
                availableAmount = $("#amt_" + $(this).val()).html();

                count += 1;

                strRows += '<tr  ><td>' + itemName + '</td><td class="text-center">' + availableAmount +
                    '</td><td class="text-center"><input class="form-control" type="text" id="request_amt_' + $(
                        this).val() + '" value="0" ></td></tr>'

                tbl_content += `
                <tr id="row_bill_` + itemID + `">
                <td class="text-center">` + count + `</td>
                <td class="row_id hide">` + itemID + `</td>
                <td class="item_name">` + itemName + `</td>
                <td class="text-left available_number">RM ` + availableAmount + `<input id="txt_available_num_` +
                    itemID +
                    `" class="form-control txt_available_num_" type="hidden" id="txt_available_num_` + $(this)
                    .val() + `" value="` + availableAmount +
                    `" ></td>
                <td name="user_amount" ><input onclick="clearErrorHighlight()" onchange="calculateVoucherSum('#txt_request_amt_` +
                    itemID +
                    `')" class="form-control input_field" type="number" id="txt_request_amt_` + itemID +
                    `" value="0" ><span class="span_error_text" id="txt_error_span_` + $(this).val() + `" style="display:none;" class="text-danger"></span></td>
                </tr>
                `;

            });

            $("#btnSubmitVoucherRequest").hide();

            if (strRows.length > 0) {
                var now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                $("#voucher_payment_time").val(now.toISOString().slice(0, 16));

                $("#tbl-voucher-request").html(tbl_content);
                $("#case_id_bill").val(case_id);
                $("#btnSubmitVoucherRequest").show();
            } else {
                Swal.fire('notice!', 'No account item selected', 'warning');
            }

        }
        

        function submitVoucherRequestV2() {
            var errorCount = 0;
            var available_amount = 0;
            var request_amount = 0;
            var Total_amount = 0;
            var voucher_list = [];
            var voucher = {};

            var form = $("#form_request_voucher");

            if (form.find('[name=ddl_payment_type]').val() == '' || form.find('[name=txt_payee]').val() == '') {
                Swal.fire('warning', 'Please make sure all mandatory field are fill', 'warning');
                return;
            }

            $.each($("input[name='case_bill']:checked"), function() {

                itemID = $(this).val();

                console.log($("#txt_available_num_" + itemID).val());

                available_amount = $("#txt_available_num_" + itemID).val();
                available_amount = available_amount.replaceAll(',', '');
                available_amount = parseFloat(available_amount);
                request_amount = $("#txt_request_amt_" + itemID).val();

                request_amount = parseFloat($("#txt_request_amt_" + itemID).val());
                itemName = $("#item_" + itemID).html();

                if (request_amount > available_amount) {
                    errorCount += 1;
                    $("#txt_request_amt_" + itemID).addClass("error-input-box");
                    $("#txt_error_span_" + itemID).html('The available amount not sufficient: RM ' +
                        available_amount);
                    $("#txt_error_span_" + itemID).show();
                }

                if (request_amount == 0) {
                    errorCount += 1;
                    $("#txt_request_amt_" + itemID).addClass("error-input-box");
                    $("#txt_error_span_" + itemID).html("Request amount cannot be zero");
                    $("#txt_error_span_" + itemID).show();
                }

                voucher = {
                    account_details_id: itemID,
                    amount: request_amount
                };

                voucher_list.push(voucher);

            });

            if (errorCount > 0) {
                Swal.fire('Notice!', 'The available amount not sufficient');
                return;
            }

            var form_data = new FormData();

            var files = $('#attachment_file')[0].files;

            form_data.append("payment_type", $("#ddl_payment_type").val());
            form_data.append("payment_date", $("#voucher_payment_time").val());
            form_data.append("voucher_no", $("#txt_voucher_no").val());
            form_data.append("cheque_no", $("#txt_cheque_no").val());
            form_data.append("payee", $("#txt_payee").val());
            form_data.append("credit_card_no", $("#txt_card_no").val());
            form_data.append("bank_id", $("#txt_bank_name").val());
            form_data.append("remark", $("#voucher_remark").val());
            form_data.append("email", $("#payee_email_voucher").val());
            form_data.append("bank_account", $("#txt_bank_account").val());
            form_data.append("bill_main_id", $("#selected_bill_id").val());
            form_data.append("adjudication_no", $("#adjudication_no").val());
            form_data.append("OfficeBankAccount", $("#OfficeBankAccount_id").val());
            form_data.append("voucher_list", JSON.stringify(voucher_list));
            if (files.length > 0) {
                form_data.append('attachment_file', files[0]);

            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/request_voucher/{{ $case->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        toastController(data.message);
                        // location.reload();
                        closeUniversalModal();
                        loadCaseBill($("#selected_bill_id").val());
                        $('#form_request_voucher')[0].reset();
                        
                    }

                },
                complete: function() {
                    blnAjax == true;
                    $("#div_full_screen_loading").hide();
                },
                error: function(data) {
                    toastController('Some error occur, please try again or contact developer', 'warning');
                    $("#div_full_screen_loading").hide();

                }
            });

        }

        function loadMasterListPrint() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/loadMasterlistPrintInfo/{{ $case->id }}',
                data: null,
                success: function(data) {
                    $(".div-info-print-section").html(data.view);
                    $(".ddl-party").html(data.ddl); 
                    // alert(data.file_ref.value);
                    
                    if (data.file_ref != null) {
                        $(".div_bank_ref_no").show();
                        $("#purchaser_file_ref").html(data.file_ref.value); 
                    }
                    else
                    {
                        $(".div_bank_ref_no").hide();
                    }

                }
            });

        }

        function clearErrorHighlight() {
            $(".input_field").removeClass('error-input-box');
            $(".span_error_text").hide();
        }

        function billModeGroupBack() {
            $(".nav-tabs-custom-ctr").hide();
            $("#dVoucherInvoice").hide();
            $("#dBillv2").show();
        }

        function generateVoucher(case_id) {

            var itemID = '';
            var itemName = '';
            var availableAmount = '';
            var count = 0;
            var errorCount = 0;
            var tbl_content = '';
            var available_amount = 0;
            var request_amount = 0;
            var Total_amount = 0;



            $(".p_cheque").hide();
            $(".p_bank").hide();

            // check main selection
            if ($("#ddl_payment_type").val() == '') {
                Swal.fire('Notice!', 'Please select payment type');
                return;
            }

            if ($("#txt_payee").val() == '') {
                Swal.fire('Notice!', 'Please key in payee');
                return;
            }

            // check if cheque no selection is empty (If cheque selected)
            if ($("#ddl_payment_type").val() == '2') {
                // if ($("#txt_cheque_no").val() == '') {
                //   Swal.fire('Notice!', 'Please enter the cheque no');
                //   return;
                // }

                $("#div_payment_details").html('<b>Cheque No: </b><span >' + $("#txt_cheque_no").val() + '</span>');
                $("#div_payment_details").show();
            }

            // check if bank required fields is empty (If bank selected)a
            if ($("#ddl_payment_type").val() == '3') {
                if ($("#txt_bank_name").val() == '' || $("#txt_bank_account").val() == '') {
                    // Swal.fire('Notice!', 'Please enter the bank details');
                    // return;
                }
                $("#div_payment_details").html('<b>Bank Name: </b><span >' + $("#txt_bank_name  option:selected").text() +
                    '</span><br/><b>Bank Account: </b><span >' + $("#txt_bank_account").val() + '</span>');
                $("#div_payment_details").show();
            }

            $("#span_payment_type").html($("#ddl_payment_type option:selected").text());
            $("#span_payment_date").html($("#voucher_payment_time").val().replace('T', ' '));
            $("#payment_type").val($("#ddl_payment_type").val());
            $("#cheque_no").val($("#txt_cheque_no").val());
            $("#payee").val($("#txt_payee").val());
            $("#payee_voucher_name").html($("#txt_payee").val());
            // payee_voucher_name

            $.each($("input[name='case_bill']:checked"), function() {

                itemID = $(this).val();

                console.log($("#txt_available_num_" + itemID).val());

                available_amount = $("#txt_available_num_" + itemID).val();
                available_amount = available_amount.replaceAll(',', '');
                available_amount = parseFloat(available_amount);
                request_amount = $("#txt_request_amt_" + itemID).val();
                request_amount = parseFloat($("#txt_request_amt_" + itemID).val());
                itemName = $("#item_" + itemID).html();

                console.log(available_amount);
                console.log(request_amount);

                // available_amount = available_amount.replace(',','');

                if (request_amount > available_amount) {
                    errorCount += 1;
                    $("#txt_request_amt_" + itemID).addClass("error-input-box");
                    $("#txt_error_span_" + itemID).html('The available amount not sufficient: RM ' +
                        available_amount);
                    $("#txt_error_span_" + itemID).show();
                }
                // console.log(request_amount);
                // console.log(available_amount);

                if (request_amount == 0) {
                    errorCount += 1;
                    $("#txt_request_amt_" + itemID).addClass("error-input-box");
                    $("#txt_error_span_" + itemID).html("Request amount cannot be zero");
                    $("#txt_error_span_" + itemID).show();
                }

                Total_amount += request_amount;


                count += 1;

                tbl_content += `
        <tr id="row_` + itemID + `">
              <td>` + count + `</td>
              <td name="item_name">` + itemName + `</td>
              <td name="item_id" class="hide">` + itemID + `</td>
              <td class="text-right td_item_price hide" >` + request_amount + `</td>
              <td class="text-right hide">1</td>
              <td class="text-right td_item_price hide">` + request_amount + `</td>
              <td class="text-right td_item_price hide">RM ` + request_amount + `</td>
              <td class="text-right td_item_price">RM ` + request_amount + `</td>
              <td name="item_account_id" class="hide">` + itemID + `</td>
            </tr>
        `;

                //         tbl_content += `
            // <tr id="row_` + itemID + `">
            //       <td>` + count + `</td>
            //       <td name="item_name">` + itemName + `</td>
            //       <td name="item_id" class="hide">` + itemID + `</td>
            //       <td class="text-right td_item_price hide" >` + request_amount + `</td>
            //       <td class="text-right">1</td>
            //       <td class="text-right td_item_price hide">` + request_amount + `</td>
            //       <td class="text-right td_item_price">RM ` + request_amount + `</td>
            //       <td class="text-right td_item_price">RM ` + request_amount + `</td>
            //       <td name="item_account_id" class="hide">` + itemID + `</td>
            //     </tr>
            // `;

            });

            // var voucher_list = [];

            // $("#tbl-bill-group tr").each(function() {
            //   var self = $(this);

            //   var item_id = self.find("td:eq(2)").text().trim();
            //   var request_amount = self.find("td:eq(3)").text().trim();

            //   voucher = {
            //     account_details_id: item_id,
            //     amount: request_amount
            //   };

            //   voucher_list.push(voucher);
            // })

            // console.log(voucher_list);
            // return;

            if (errorCount <= 0) {
                $("#tbl-submit-voucher").html(tbl_content);

                viewMode();
                $(".nav-tabs-custom-ctr").hide();
                $("#dVoucherInvoice").show();

                $("#span_total_amount").html("RM " + numberWithCommas(Total_amount));
            } else {
                Swal.fire('Notice!', 'The available amount not sufficient');
                return;
            }

            return;

            $.each($("input[name='bill']:checked"), function() {

                itemID = $(this).val();

                console.log(itemID);

                itemName = $("#item_" + $(this).val()).html();
                availableAmount = $("#request_amt_" + $(this).val()).val();

                count += 1;

                tbl_content += `
       <tr id="row_` + itemID + `">
            <td>` + count + `</td>
            <td name="item_name">` + itemName + `</td>
            <td>12345678912514</td>
            <td class="text-right">1</td>
            <td class="text-right td_item_price">RM ` + availableAmount + `</td>
            <td class="text-right td_item_price">RM ` + availableAmount + `</td>
          </tr>
      `;

            });

            $("#tbl-submit-voucher").html(tbl_content);

            viewMode();
            $(".nav-tabs-custom-ctr").hide();
            $("#dVoucherInvoice").show();

            return;

            $(".td_item_price").html("RM " + voucher_amt);

            if (voucher_amt == 0) {
                return;
            }

            if (voucher_amt > available_amt) {
                Swal.fire('Notice!', 'The available amount not sufficient: RM ' + available_amt, 'error');
                return;
            }

            viewMode();
            $(".nav-tabs-custom-ctr").hide();
            $("#dVoucherInvoice").show();

        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        var blnAjax = true;

        function submitVoucher(case_id) {
            var voucher_amt = parseFloat($("#amt").val());
            var available_amt = parseFloat($("#available_amt").val());
            var voucher_list = [];
            var voucher = {};

            if (blnAjax == false) return;

            blnAjax = false;

            $("#tbl-submit-voucher tr").each(function() {
                var self = $(this);

                var item_id = self.find("td:eq(2)").text().trim();
                var request_amount = self.find("td:eq(3)").text().trim();

                voucher = {
                    account_details_id: item_id,
                    amount: request_amount
                };

                voucher_list.push(voucher);
            })


            var form_data = new FormData();

            var files = $('#voucher_file')[0].files;

            form_data.append("payment_type", $("#ddl_payment_type").val());
            form_data.append("payment_date", $("#voucher_payment_time").val());
            form_data.append("voucher_no", $("#txt_voucher_no").val());
            form_data.append("cheque_no", $("#txt_cheque_no").val());
            form_data.append("payee", $("#txt_payee").val());
            form_data.append("credit_card_no", $("#txt_card_no").val());
            form_data.append("bank_id", $("#txt_bank_name").val());
            form_data.append("remark", $("#voucher_remark").val());
            form_data.append("email", $("#payee_email_voucher").val());
            form_data.append("bank_account", $("#txt_bank_account").val());
            form_data.append("bill_main_id", $("#selected_bill_id").val());
            form_data.append("adjudication_no", $("#adjudication_no").val());
            form_data.append("OfficeBankAccount", $("#OfficeBankAccount_id").val());
            form_data.append("voucher_list", JSON.stringify(voucher_list));
            if (files.length > 0) {
                form_data.append('voucher_file', files[0]);

            }

            form_data.append('_token', '{{ csrf_token() }}');

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/request_voucher/' + case_id,
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        toastController(data.message);
                        location.reload();
                    }

                },
                complete: function() {
                    blnAjax == true;
                    $("#div_full_screen_loading").hide();
                }
            });

            return;

            if (voucher_amt == 0) {
                return;
            }

            if (voucher_amt > available_amt) {
                Swal.fire('Notice!', 'The available amount not sufficient: RM ' + available_amt, 'error');
                return;
            }

            if ($("#ddl_payment_type").val() == '') {
                Swal.fire('Notice!', 'Please select payment type');
                return;
            }

            // if ($("#ddl_payment_type").val() == '2') {
            //   if ($("#txt_cheque_no").val() == '') {
            //     Swal.fire('Notice!', 'Please enter the cheque no');
            //     return;
            //   }
            // }

            $("#payment_type").val($("#ddl_payment_type").val());
            $("#cheque_no").val($("#txt_cheque_no").val());

            $.ajax({
                type: 'POST',
                url: '/request_voucher/' + case_id,
                data: $('#form_voucher').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        location.reload();
                    }

                }
            });
        }

        function timelineCOntroller(timelineID) {
            var isExpanded = document.getElementsByClassName('#main_checklist_' + timelineID + ' ion-arrow-up-b');
            var div = document.querySelector('#main_checklist_' + timelineID);

            var isExpanded = div.classList.contains('ion-arrow-up-b');

            if (isExpanded == true) {
                $("#main_checklist_" + timelineID).removeClass('ion-arrow-up-b');
                $("#main_checklist_" + timelineID).addClass('ion-arrow-down-b');
                $(".li-item-" + timelineID).slideUp('fast');
            } else {
                $("#main_checklist_" + timelineID).removeClass('ion-arrow-down-b');
                $("#main_checklist_" + timelineID).addClass('ion-arrow-up-b');
                $(".li-item-" + timelineID).slideDown('fast');
            }
        }

        function viewMode() {
            $(".nav-tabs-custom-ctr").show();
            $(".d_operation").hide();
            myDropzone.removeAllFiles(true);
        }

        function updateChecklist($id) {
            $("#span_update").hide();
            $(".overlay").show();

            console.log($('#form_action').serialize());

            $.ajax({
                type: 'POST',
                url: '/update_checklist',
                data: $('#form_action').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            'Checklist Updated',
                            'success'
                        )
                        $('#form_action')[0].reset();
                        $("#span_update").show();
                        $(".overlay").hide();
                        // $('#home-1').html(data.view);

                        viewMode();


                        // location.reload();
                    }



                }
            });

        }

        function closeCase($checklist_id, $case_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Close this case?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/close_case/' + $case_id + "/" + $checklist_id,
                        data: $('#form_action').serialize(),
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })


        }

        function createDispatch($id) {
            $("#span_update_dispatch").hide();
            $(".overlay").show();

            $.ajax({
                type: 'POST',
                url: '/create_dispatch',
                data: $('#form_dispatch').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        // $("#body_" + $("#selected_id").val()).append($("#remarks").val());

                        // $("#checklist_" + $("#selected_id").val()).removeClass("ion-person bg-aqua");
                        // $("#checklist_" + $("#selected_id").val()).addClass(" ion-checkmark bg-done ");

                        // $("#date_" + $("#selected_id").val()).html(data.data);

                        // $("#remarks").val('');

                        $("#span_update").show();
                        $(".overlay").hide();

                        viewMode();
                    }

                    Swal.fire(
                        'Success!',
                        'Dispatch created',
                        'success'
                    )

                    location.reload();

                }
            });

        }

        function requestTrustDisbusement() {

            var form = $("#form_trust");

            if (form.find('[name=payee]').val() == '' || form.find('[name=amount]').val() == '' || form.find(
                    '[name=remark]').val() == '') {
                Swal.fire('warning', 'Please make sure all mandatory field are fill', 'warning');
                return;
            }

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/requestTrustDisbusement/{{ $case->id }}',
                data: new FormData($("#form_trust")[0]),
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);

                    $("#div_full_screen_loading").hide();
                    if (data.status == 1) {
                        toastController(data.message);
                        $('#tbl-trust-disb').html(data.view);
                        $('#d-trust-summary').html(data.summary);
                        closeUniversalModal();
                    } else {
                        toastController(data.message, 'warning');
                    }
                },
                error: function(data) {
                    toastController('Error occur', 'warning');
                    $("#div_full_screen_loading").hide();

                }
            });
        }

        function receiveTrustDisbusement() {
            var form = $("#form_trust");

            if (form.find('[name=payee]').val() == '' || form.find('[name=amount]').val() == '' || form.find(
                    '[name=remark]').val() == '' || form.find('[name=transaction_id]').val() == '' || form.find(
                    '[name=office_account_id]').val() == '' || form.find('[name=payment_date]').val() == '') {
                Swal.fire('warning', 'Please make sure all mandatory field are fill', 'warning');
                return;
            }

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/receiveTrustDisbusement/{{ $case->id }}',
                data: new FormData($("#form_trust")[0]),
                processData: false,
                contentType: false,
                success: function(data) {

                    toastController(data.message);
                    $('#tbl-trust-recv').html(data.view);
                    $('#tbl-trust-disb').html(data.view2);
                    $('#d-trust-summary').html(data.summary);
                    closeUniversalModal();

                    $("#div_full_screen_loading").hide();
                },
                error: function(data) {
                    toastController('Error occur', 'warning');
                    $("#div_full_screen_loading").hide();

                }
            });
        }

        function uploadFile(type) {
            $("#span_update").hide();
            $(".overlay").show();

            var formData = new FormData();

            var file_type = $("#file_type").val();

            var files = $('#case_file')[0].files;
            console.log(files[0]);
            formData.append('case_file', files[0]);
            formData.append('file_type', file_type);
            formData.append('case_id', '{{ $case->id }}');
            if (file_type == 1) {
                formData.append('selected_id', $("#selected_id").val());
                formData.append('attachment_type', $("#attachment_type").val());
                formData.append('remark', $("#file_remark").val());
            } else {
                formData.append('selected_id', 0);
                formData.append('remark', $("#file_remark").val());
                formData.append('attachment_type', 5);
            }


            formData.append('case_ref_no', '{{ $case->case_ref_no }}');

            // formData.append('_token','<?php echo csrf_token(); ?>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/upload_file',
                // data: $('#form_action').serialize(), 
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    $("#span_update").hide();
                    $(".overlay").hide();
                    if (data.status == 1) {
                        viewMode();
                        toastController('Attachment Uploaded');
                        $('#div_case_attachment').html(data.LoanAttachment);
                        $('#div_case_marketing_attachment').html(data.LoanAttachmentMarketing);

                        document.getElementById("form_file").reset();
                    }
                }
            });
        }

        function submitMasterList(cat_id, case_id) {

            $("#span_update_" + cat_id).hide();
            $(".overlay_" + cat_id).show();
            var form = $('#form_master_' + cat_id).serialize();
            // form.append("case_id", case_id);
            console.log(form);

            // return ;

            $.ajax({
                type: 'POST',
                url: '/update_masterlist/' + case_id,
                data: form,
                success: function(data) {
                    console.log(data);

                    if (data.status == 1) {
                        toastController('Masterlist Updated');

                        $("#span_update_" + cat_id).show();
                        $(".overlay_" + cat_id).hide();
                        $('#profile-1').html(data.view);

                        loadMasterListPrint();
                        $("#div_case_summary").html(data.summary);
                    } else {
                        Swal.fire('Notice!', data.message, 'error');
                    }


                }
            });
        }

        function submitMasterListLetterHead() {

            var form = $('#formMasterListLetterHead').serialize();
            // form.append("case_id", case_id);
            console.log(form);

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/update_masterlist/{{ $case->id }}',
                data: form,
                success: function(data) {
                    console.log(data);

                    $("#div_full_screen_loading").hide();

                    if (data.status == 1) {
                        toastController('Masterlist Updated');

                        closeUniversalModal();
                        loadMasterListPrint();
                    } else {
                        Swal.fire('Notice!', data.message, 'error');
                    }
                },
                error: function(data) {
                    toastController('Error occur', 'warning');
                    $("#div_full_screen_loading").hide();

                }
            });
        }

        function acceptCase($id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if ($("#template").val() == 0) {
                Swal.fire('notice', 'Please select template', 'warning');

                return;
            }
            $(".btn").attr("disabled", true);
            $.ajax({
                type: 'POST',
                url: '/accept_case/' + $id,
                data: $('#form_accept_case').serialize(),
                success: function(data) {
                    $(".btn").attr("disabled", false);

                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire('success', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('success', data.message, 'warning');
                    }

                },
                error: function(data) {

                    $(".btn").attr("disabled", false);
                    console.log(data);

                }
            });

        }

        function setKIV($id) {

            if ($("#reason").val() == "") {
                Swal.fire('notice', 'Please provide reason', 'warning')
                return;
            }

            var formData = new FormData();
            formData.append('reason', $("#reason").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/set_kiv/' + $id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    Swal.fire('success', 'Status changed', 'success')
                    location.reload();
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function loadCaseTemplate() {


            var formData = new FormData();
            formData.append('template_id', $("#ddl_case_template").val());

            $.ajax({
                type: 'POST',
                url: 'load_case_template',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    $('#tbl-data').html(data.view);
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function LoadUserQuotationTemplate(case_id) {

        }

        function loadQuotationTemplate(case_id) {

            if ($("#ddlQuotationCheck").is(':checked')) {

                if ($("#my_quotation_template").val() == 0) {
                    Swal.fire('Notice!', 'Please select an template', 'warning');
                    return;
                }

                $url = '/loadMyQuotationTemplate/' + $("#my_quotation_template").val();
            } else {
                if ($("#quotation_template").val() == 0) {
                    Swal.fire('Notice!', 'Please select an template', 'warning');
                    return;
                }

                $url = '/load_quotation_template/' + $("#quotation_template").val();
            }



            if ($("#ddl_party_quo").val() == 0) {
                Swal.fire('Notice!', 'Please select party to bill to', 'warning');
                return;
            }

            var formData = new FormData();
            formData.append('case_id', case_id);
            formData.append('sst', $("#ddl_sst").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                // url: '/load_quotation_template/' + $("#quotation_template").val(),
                url: $url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    // $('#tbl-bill-create').html(data.view);
                    $('#tbl-quotation-create').html(data.view);
                    quotationItemCheckAllController(1);
                    updateAllQuotationAmount();
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function loadMyQuotationTemplate(case_id) {

            if ($("#quotation_template").val() == 0) {
                Swal.fire('Notice!', 'Please select an template', 'warning');
                return;
            }

            if ($("#ddl_party_quo").val() == 0) {
                Swal.fire('Notice!', 'Please select party to bill to', 'warning');
                return;
            }

            var formData = new FormData();
            formData.append('case_id', case_id);
            formData.append('sst', $("#ddl_sst").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/load_quotation_template/' + $("#quotation_template").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    // $('#tbl-bill-create').html(data.view);
                    $('#tbl-quotation-create').html(data.view);
                    quotationItemCheckAllController(1);
                    updateAllQuotationAmount();
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function onBillTabActive($id) {
            $("#current_bill_tab").val($id);
        }

        function loadCaseBill(bill_id) {

            var formData = new FormData();
            formData.append('case_id', '{{ $case->id }}');
            formData.append('current_bill_tab', $('#current_bill_tab').val());

            $("#dQuotationPrint").show();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/load_case_bill/' + bill_id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    // console.log(data.InvoiceSummary);
                    $('#tbl-print-quotation').html(data.view2);
                    $('#tbl-disb-case').html(data.disburse);
                    $('#tbl-bill-receive').html(data.receive);
                    $('#summaryReport').html(data.summary);
                    $('#tabBillCase').html(data.tab);
                    $('#billInvoice2').html(data.invoiceView);
                    $('#billVoucher').html(data.billView);
                    $('#div-print-inv').html(data.invoicePrint);
                    $('#div-print-bill').html(data.billPrint);
                    $('#div-bill-summary-details').html(data.billSummary);
                    $('#div-invoice-summary-details').html(data.InvoiceSummary);
                    $('#tbl-case-bill').html(data.view);
                    loadMasterListPrint();

                    try {

                        $("#ddl_sst_modal").val(parseInt(data.LoanCaseBillMain.sst_rate));
                        $(".lbl_sst_rate").html(parseInt(data.LoanCaseBillMain.sst_rate));

                        let currentDateInv = new Date(data.LoanCaseBillMain.invoice_date);
                        var dateInv = currentDateInv.getDate();
                        var monthInv = currentDateInv.getMonth(); //Be careful! January is 0 not 1
                        var yearInv = currentDateInv.getFullYear();


                        var day = ("0" + dateInv).slice(-2);
                        var month = ("0" + (monthInv + 1)).slice(-2);

                        $("#input_invoice_date").val(yearInv + "-" + month + "-" + day);
                    } catch (e) {

                    }

                    var strHtml = '';

                    if (data.QuotationTemplate.length > 0) {
                        strHtml += '<option value="0">-- Select account --</option>';

                        for (var i = 0; i < data.QuotationTemplate.length; i++) {
                            strHtml += '<option value="' + data.QuotationTemplate[i].default_amt +
                                '" class="cat_all cat_' + data.QuotationTemplate[i].account_cat_id + '" id="' +
                                data.QuotationTemplate[i].account_item_id + '">' + data.QuotationTemplate[i]
                                .account_name + '</option>';
                        }
                        console.log(strHtml);

                        $("#ddlAccountItem").html(strHtml);

                    }

                    if (data.invoiceTemplate.length > 0) {
                        strHtml += '<option value="0">-- Select account --</option>';

                        for (var i = 0; i < data.invoiceTemplate.length; i++) {
                            strHtml += '<option value="' + data.invoiceTemplate[i].default_amt +
                                '" class="cat_all cat_invoice_' + data.invoiceTemplate[i].account_cat_id +
                                '" id="' + data.invoiceTemplate[i].account_item_id + '">' + data
                                .invoiceTemplate[i].account_name + '</option>';
                        }

                        strHtml +=
                            '<option value="0" class="cat_all cat_invoice_1" id="129">Admin fees</option>';
                        $("#ddlAccountItemInvoice").html(strHtml);

                    }

                    
                        $("#lbl_bill_to_party").html(data.InvBillto);

                    $("#div_full_screen_loading").hide();
                }
            });
        }

        function loadCaseDisb() {



            var formData = new FormData();
            formData.append('bill_id', $("#selected_bill_id").val());
            formData.append('case_id', {{ $case->id }});

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/loadBillDisb',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    $('#tbl-disb-move').html(data.disburse);
                    $('#ddl_move_bill').html(data.bill);
                }
            });
        }

        function MoveBill(case_id) {

            var bill_list = [];
            var bill = {};

            if ($("#ddl_move_bill").val() == 0) {
                Swal.fire('Notice!', 'Please select destination bill', 'warning');
                return;
            }

            $.each($("input[name='move_id']:checked"), function() {
                itemID = $(this).val();
                bill_list.push(itemID);
            });

            if (bill_list.length <= 0) {
                Swal.fire('Notice!', 'No voucher item selected', 'warning');
                return;
            }

            var formData = new FormData();
            formData.append('case_id', {{ $case->id }});
            formData.append("movelist", JSON.stringify(bill_list));
            formData.append('current_bill', $("#selected_bill_id").val());
            formData.append('target_bill', $("#ddl_move_bill").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#div_full_screen_loading").show();
            $('.btn-submit').attr('disabled', true);

            $.ajax({
                type: 'POST',
                url: '/MoveBill',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    if (data.status == 1) {
                        toastController(data.message);
                        closeUniversalModal();
                        setTimeout(() => {
                            location.reload();
                        }, 3000)
                    } else {
                        $("#div_full_screen_loading").hide();
                        toastController(data.message, 'warning');
                        $('.btn-submit').attr('disabled', false);

                        $(".text_voucher_all").removeClass('text-red');

                        if (data.error_check.length > 0) {
                            for (var i = 0; i < data.error_check.length; i++) {
                                $(".text_voucher_" + data.error_check[i]).addClass('text-red');
                            }
                        }
                    }

                }
            });
        }

        function manageVoucherDetails($voucher_main_id, $voucher_detail_id) {
            $('.check_voucher_' + $voucher_main_id).prop('checked', $('#chk_' + $voucher_detail_id).is(':checked'));

        }

        function checkAllController() {
            $('.move_all').prop('checked', $('#checkall').is(':checked'));
        }

        function loadCaseBillBak(bill_id) {

            var formData = new FormData();
            formData.append('case_id', '{{ $case->id }}');

            $("#dQuotationPrint").show();

            var balance_amt = $("#txt_hd_collcted_amt_" + bill_id).val() - $("#txt_hd_spent_amt_" + bill_id).val();


            $("#lb_bill_collected").html("RM " + $("#txt_hd_collcted_amt_" + bill_id).val());
            $("#lb_bill_spent").html("RM " + $("#txt_hd_spent_amt_" + bill_id).val());
            $("#lb_bill_balance").html("RM " + balance_amt.toFixed(2));

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/load_case_bill/' + bill_id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data.invoice);
                    $('#tbl-case-bill').html(data.view);
                    $('#tbl-print-quotation').html(data.view2);
                    $('#tbl-print-invoice').html(data.view3);
                    // $('#tbl-invoice-bill').html(data.view4);
                    $('#tbl-bill-disburse').html(data.disburse);
                    $('#tbl-bill-receive').html(data.receive);
                    $('#summaryReport').html(data.summary);
                    $('#billInvoice').html(data.invoice);

                    $("#span_total_disb").html(numberWithCommas(data.bill_disburse_count));

                    console.log(data.invoice[2].account_details.length);

                    // if (data.invoice[0].account_details.length > 0) {

                    //     $invoice_date = null;

                    //     if (data.invoice[0].account_details.length > 0)
                    //     {
                    //         $invoice_date = data.invoice[0].account_details[0].created_at;
                    //         alert(1);
                    //     }

                    //     if (data.invoice[1].account_details.length > 0)
                    //     {
                    //         $invoice_date = data.invoice[1].account_details[0].created_at;
                    //         alert(2);
                    //     }

                    //     if (data.invoice[2].account_details.length > 0)
                    //     {
                    //         alert(3);
                    //         alert(data.invoice[2].account_details[0].created_at);
                    //         $invoice_date = data.invoice[2].account_details[0].created_at;

                    //     }

                    //     alert($invoice_date);

                    //     let currentDate = new Date($invoice_date);

                    //     var date = currentDate.getDate();
                    //     var month = currentDate.getMonth(); //Be careful! January is 0 not 1
                    //     var year = currentDate.getFullYear();




                    //     $("#print_payment_date_inv").html(date + "-" + (month + 1) + "-" + year);
                    // } else {}

                    try {
                        $invoice_date = null;

                        if (data.invoice[0].account_details.length > 0) {
                            $invoice_date = data.invoice[0].account_details[0].created_at;
                        }

                        if (data.invoice[1].account_details.length > 0) {
                            $invoice_date = data.invoice[1].account_details[0].created_at;
                        }

                        if (data.invoice[2].account_details.length > 0) {
                            $invoice_date = data.invoice[2].account_details[0].created_at;

                        }


                        // let currentDate = new Date(data.invoice[2].account_details[0].created_at);

                        // var date = currentDate.getDate();
                        // var month = currentDate.getMonth(); //Be careful! January is 0 not 1
                        // var year = currentDate.getFullYear();

                        let currentDateInv = new Date(data.LoanCaseBillMain.invoice_date);
                        $("#lbl_invoice_no").html(data.LoanCaseBillMain.invoice_no);

                        $("#ddl_sst_modal").val(parseInt(data.LoanCaseBillMain.sst_rate));
                        $(".lbl_sst_rate").html(parseInt(data.LoanCaseBillMain.sst_rate));
                        // alert(data.LoanCaseBillMain.sst_rate);

                        var dateInv = currentDateInv.getDate();
                        var monthInv = currentDateInv.getMonth(); //Be careful! January is 0 not 1
                        var yearInv = currentDateInv.getFullYear();

                        $("#print_payment_date_inv").html(dateInv + "-" + (monthInv + 1) + "-" + yearInv);

                        $("#lbl_invoice_date").html(dateInv + "-" + (monthInv + 1) + "-" + yearInv);

                        if (data.LoanCaseBillMain.transferred_pfee_amt > 0) {
                            $("#lbl_invoice_status").html(
                                '<span style="color:red">Pfee transffered for this invoice, edit featured disabled</span>'
                            );
                        }



                        // alert(yearInv + "-" + (monthInv + 1) + "-" + dateInv);

                        var day = ("0" + dateInv).slice(-2);
                        var month = ("0" + (monthInv + 1)).slice(-2);

                        $("#input_invoice_date").val(yearInv + "-" + month + "-" + day);
                    } catch (e) {

                    }








                    $("#quotation_no").html(data.LoanCaseBillMain.bill_no);
                    $("#invoice_no").html(data.LoanCaseBillMain.invoice_no);

                    var uncollected = 0;
                    var collection_amount = 0;
                    var marketing = 0;

                    if (data.LoanCaseBillMain.uncollected != null) {
                        uncollected = data.LoanCaseBillMain.uncollected;
                        collection_amount = data.LoanCaseBillMain.collection_amount;
                    }

                    if (data.LoanCaseBillMain.marketing != null) {
                        marketing = data.LoanCaseBillMain.marketing;
                    }

                    $("#sum_invoice_no").html(data.LoanCaseBillMain.bill_no);
                    // $("#sum_pfee").html("RM " + numberWithCommas(data.LoanCaseBillMain.pfee_recv));
                    $("#sum_pfee1").html("RM " + numberWithCommas(data.LoanCaseBillMain.pfee1));
                    $("#sum_pfee2").html("RM " + numberWithCommas(data.LoanCaseBillMain.pfee2));
                    $("#sum_disb").html("RM " + numberWithCommas(data.LoanCaseBillMain.disb));
                    $("#sum_sst").html("RM " + numberWithCommas(data.LoanCaseBillMain.sst));
                    $("#sum_total_invoice").html("RM " + numberWithCommas(data.LoanCaseBillMain.total_amt));
                    $("#sum_outstanding").html("RM " + (data.LoanCaseBillMain.collected_amt - data
                        .LoanCaseBillMain.total_amt).toFixed(2));
                    $("#sum_uncollected").html("RM " + (numberWithCommas(uncollected)));
                    $("#sum_marketing").html("RM " + numberWithCommas(marketing));
                    $("#sum_referral_a1").val(data.LoanCaseBillMain.referral_a1);
                    $("#sum_referral_a2").val(data.LoanCaseBillMain.referral_a2);
                    $("#sum_referral_a3").val(data.LoanCaseBillMain.referral_a3);
                    $("#sum_referral_a4").val(data.LoanCaseBillMain.referral_a4);
                    // $("#sum_marketing").val(data.LoanCaseBillMain.marketing);
                    // $("#sum_uncollected").val(data.LoanCaseBillMain.uncollected);

                    $("#lb_bill_target").html("RM " + numberWithCommas(data.LoanCaseBillMain.total_amt));
                    $("#lb_bill_collected").html("RM " + numberWithCommas(data.LoanCaseBillMain.collected_amt));
                    $("#lb_bill_spent").html("RM " + numberWithCommas(data.LoanCaseBillMain.used_amt));

                    balance_amt = data.LoanCaseBillMain.collected_amt - data.LoanCaseBillMain.used_amt;

                    $("#lb_bill_balance").html("RM " + numberWithCommas(balance_amt.toFixed(2)));
                    // target_bill_case
                    if (data.LoanCaseBillMain.bln_invoice == 1) {
                        $(".quotation").hide();
                        $(".btninvoice").show();
                        $("#li_invoice").show();

                        $('.bill_link').width('20%');
                        // $(".quotation-colspan").attr('colspan', 3);
                        // $(".quotation-sst-colspan").attr('colspan', 1);
                        // $(".quotation-total-colspan").attr('colspan', 2);
                    } else {

                        $(".quotation").show();
                        $(".btninvoice").hide();
                        $('.bill_link').width('25%');
                        // $(".quotation-colspan").attr('colspan', 5);
                        // $(".quotation-sst-colspan").attr('colspan', 3);
                        // $(".quotation-total-colspan").attr('colspan', 2);
                    }
                    if (data.LoanCaseBillMain.bln_sst == 1) {
                        $("#btnToSST").hide();
                    }

                    // console.log(data.LoanCaseBillMain.bill_to);
                    // if (data.LoanCaseBillMain.bill_to != null  && data.LoanCaseBillMain.bill_to != '' ) {
                    //     if(data.LoanCaseBillMain.id != 4680)
                    //     {
                    //         $(".div_quo_bill_to").hide();
                    //         $(".div_inv_bill_to").hide();
                    //     }

                    // }
                    // else
                    // {
                    //     $(".div_quo_bill_to").show();
                    //     $(".div_inv_bill_to").show();
                    // }

                    $(".div_quo_bill_to").show();
                    $(".div_inv_bill_to").show();

                    if (data.LoanCaseBillMain.bln_sst != 1 && data.LoanCaseBillMain.transferred_pfee_amt <= 0) {
                        $("#btn_revert_invoice").show();
                    } else {
                        $("#btn_revert_invoice").hide();
                    }

                    $("#p-quo-client-name").html(data.LoanCaseBillMain.bill_to);
                    $("#p-quo-client-name-inv").html(data.LoanCaseBillMain.invoice_to);
                    // $("#p-quo-client-name-inv").html(data.client.name);
                    $("#p-quo-client-address").html(data.client.address);
                    $("#p-quo-client-phone_no").html(data.client.phone_no);
                    $("#p-quo-client-email").html(data.client.email);


                    var form = $("#form_bill_main_info");

                    form.find('[name=payee_name]').val(data.LoanCaseBillMain.payee);
                    form.find('[name=payment_desc]').val(data.LoanCaseBillMain.remark);
                    // form.find('input[name=payment_movement]').val(data.data.movement_type);
                    form.find('[name=payment_type]').val(data.LoanCaseBillMain.payment_type);
                    form.find('[name=payment_date]').val(data.LoanCaseBillMain.payment_date);
                    form.find('[name=office_account_id]').val(data.LoanCaseBillMain.office_account_id);
                    form.find('[name=amount]').val(data.LoanCaseBillMain.amount);
                    form.find('[name=transaction_id]').val(data.LoanCaseBillMain.transaction_id);
                    form.find('[name=cheque_no]').val(data.LoanCaseBillMain.cheque_no);
                    form.find('[name=bank_account]').val(data.LoanCaseBillMain.bank_account);
                    form.find('[name=bank_id]').val(data.LoanCaseBillMain.bank_id);
                    form.find('[name=credit_card_no]').val(data.LoanCaseBillMain.credit_card_no);
                    form.find('[name=bill_id]').val(data.LoanCaseBillMain.id);

                    var form = $("#form_bill_summary");

                    console.log(data.LoanCaseBillMain.referral_a1_id);

                    form.find('[name=referral_name_1]').val(data.LoanCaseBillMain.referral_a1_id);
                    form.find('[name=referral_id_1]').val(data.LoanCaseBillMain.referral_a1_ref_id);

                    if (data.LoanCaseBillMain.referral_a1_id != '' && data.LoanCaseBillMain.referral_a1_id !=
                        0 && data.LoanCaseBillMain.referral_a1_id != null) {
                        $("#referral_1_table").html('<b>' + data.LoanCaseBillMain.referral_a1_id + '</b>');
                    } else {
                        $("#referral_1_table").html('Referral(A1)');
                    }

                    if (data.LoanCaseBillMain.referral_a1 != null) {
                        // marketing = data.LoanCaseBillMain.marketing;
                        $("#sum_referral_a1").html("RM " + numberWithCommas(data.LoanCaseBillMain.referral_a1));
                    }


                    form.find('[name=ref_a1_payment_date]').val(data.LoanCaseBillMain.referral_a1_payment_date);
                    form.find('[name=ref_a1_payment_trx_id]').val(data.LoanCaseBillMain.referral_a1_trx_id);

                    if (data.LoanCaseBillMain.referral_a1_trx_id != "" && data.LoanCaseBillMain
                        .referral_a1_trx_id != null) {
                        form.find('[name=ref_a1_amt]').prop('readonly', true);
                        form.find('[name=referral_name_1_clear_btn]').prop('disabled', true);

                    } else {
                        form.find('[name=ref_a1_amt]').prop('readonly', false);
                        form.find('[name=referral_name_1_clear_btn]').prop('disabled', false);
                    }

                    form.find('[name=ref_a1_amt]').val(data.LoanCaseBillMain.referral_a1);

                    form.find('[name=referral_bank_1]').val(data.LoanCaseBillMain.r1_bank);
                    form.find('[name=referral_account_no_1]').val(data.LoanCaseBillMain.r1_bank_account);

                    form.find('[name=referral_name_2]').val(data.LoanCaseBillMain.referral_a2_id);
                    form.find('[name=referral_id_2]').val(data.LoanCaseBillMain.referral_a2_ref_id);

                    console.log(data.LoanCaseBillMain.referral_a2_id);

                    if (data.LoanCaseBillMain.referral_a2_id != '' && data.LoanCaseBillMain.referral_a2_id !=
                        0 && data.LoanCaseBillMain.referral_a2_id != null) {
                        $("#referral_2_table").html('<b>' + data.LoanCaseBillMain.referral_a2_id + '</b>');
                    } else {
                        $("#referral_2_table").html('Referral(A1)');
                    }

                    if (data.LoanCaseBillMain.referral_a2 != null) {
                        // marketing = data.LoanCaseBillMain.marketing;
                        $("#sum_referral_a2").html("RM " + numberWithCommas(data.LoanCaseBillMain.referral_a2));
                    }

                    if (data.LoanCaseBillMain.referral_a2_trx_id != "" && data.LoanCaseBillMain
                        .referral_a2_trx_id != null) {
                        form.find('[name=ref_a2_amt]').prop('readonly', true);
                        form.find('[name=referral_name_2_clear_btn]').prop('disabled', true);
                    } else {
                        form.find('[name=ref_a2_amt]').prop('readonly', false);
                        form.find('[name=referral_name_2_clear_btn]').prop('disabled', false);
                    }



                    form.find('[name=ref_a2_payment_date]').val(data.LoanCaseBillMain.referral_a2_payment_date);
                    form.find('[name=ref_a2_payment_trx_id]').val(data.LoanCaseBillMain.referral_a2_trx_id);
                    form.find('[name=ref_a2_amt]').val(data.LoanCaseBillMain.referral_a2);
                    form.find('[name=referral_bank_2]').val(data.LoanCaseBillMain.r2_bank);
                    form.find('[name=referral_account_no_2]').val(data.LoanCaseBillMain.r2_bank_account);

                    form.find('[name=referral_name_3]').val(data.LoanCaseBillMain.referral_a3_id);
                    form.find('[name=referral_id_3]').val(data.LoanCaseBillMain.referral_a3_ref_id);

                    if (data.LoanCaseBillMain.referral_a3_id != '' && data.LoanCaseBillMain.referral_a3_id !=
                        0 && data.LoanCaseBillMain.referral_a3_id != null) {
                        $("#referral_3_table").html('<b>' + data.LoanCaseBillMain.referral_a3_id + '</b>');
                    } else {
                        $("#referral_3_table").html('Referral(A3)');
                    }

                    if (data.LoanCaseBillMain.referral_a3 != null) {
                        $("#sum_referral_a3").html("RM " + numberWithCommas(data.LoanCaseBillMain.referral_a3));
                    }

                    if (data.LoanCaseBillMain.referral_a3_trx_id != "" && data.LoanCaseBillMain
                        .referral_a3_trx_id != null) {
                        form.find('[name=ref_a3_amt]').prop('readonly', true);
                        form.find('[name=referral_name_3_clear_btn]').prop('disabled', true);
                    } else {
                        form.find('[name=ref_a3_amt]').prop('readonly', false);
                        form.find('[name=referral_name_3_clear_btn]').prop('disabled', false);
                    }

                    form.find('[name=ref_a3_payment_date]').val(data.LoanCaseBillMain.referral_a3_payment_date);
                    form.find('[name=ref_a3_payment_trx_id]').val(data.LoanCaseBillMain.referral_a3_trx_id);
                    form.find('[name=ref_a3_amt]').val(data.LoanCaseBillMain.referral_a3);
                    form.find('[name=referral_bank_3]').val(data.LoanCaseBillMain.r3_bank);
                    form.find('[name=referral_account_no_3]').val(data.LoanCaseBillMain.r3_bank_account);

                    form.find('[name=referral_name_4]').val(data.LoanCaseBillMain.referral_a4_id);
                    form.find('[name=referral_id_4]').val(data.LoanCaseBillMain.referral_a4_ref_id);

                    if (data.LoanCaseBillMain.referral_a4_id != '' && data.LoanCaseBillMain.referral_a4_id !=
                        0 && data.LoanCaseBillMain.referral_a4_id != null) {
                        $("#referral_4_table").html('<b>' + data.LoanCaseBillMain.referral_a4_id + '</b>');
                    } else {
                        $("#referral_4_table").html('Referral(A4)');
                    }

                    if (data.LoanCaseBillMain.referral_a4 != null) {
                        $("#sum_referral_a4").html("RM " + numberWithCommas(data.LoanCaseBillMain.referral_a4));
                    }

                    if (data.LoanCaseBillMain.referral_a4_trx_id != "" && data.LoanCaseBillMain
                        .referral_a4_trx_id != null) {
                        form.find('[name=ref_a4_amt]').prop('readonly', true);
                        form.find('[name=referral_name_4_clear_btn]').prop('disabled', true);
                    } else {
                        form.find('[name=ref_a4_amt]').prop('readonly', false);
                        form.find('[name=referral_name_4_clear_btn]').prop('disabled', false);
                    }

                    form.find('[name=ref_a4_payment_date]').val(data.LoanCaseBillMain.referral_a4_payment_date);
                    form.find('[name=ref_a4_payment_trx_id]').val(data.LoanCaseBillMain.referral_a4_trx_id);
                    form.find('[name=ref_a4_amt]').val(data.LoanCaseBillMain.referral_a4);
                    form.find('[name=referral_bank_4]').val(data.LoanCaseBillMain.r4_bank);
                    form.find('[name=referral_account_no_4]').val(data.LoanCaseBillMain.r4_bank_account);

                    form.find('[name=sales_payment_date]').val(data.LoanCaseBillMain.marketing_payment_date);
                    form.find('[name=sales_payment_trx_id]').val(data.LoanCaseBillMain.marketing_trx_id);
                    form.find('[name=marketing_amt]').val(data.LoanCaseBillMain.marketing);
                    form.find('[name=uncollected_amt]').val(data.LoanCaseBillMain.uncollected);
                    form.find('[name=collection_amount]').val(data.LoanCaseBillMain.collection_amount);

                    if (data.LoanCaseBillMain.marketing_trx_id != '' && data.LoanCaseBillMain
                        .marketing_trx_id != null) {
                        $('#marketing_amt').prop('readonly', true);
                    }

                    form.find('[name=sst_payment_date]').val(data.LoanCaseBillMain.sst_payment_date);
                    form.find('[name=sst_payment_trx_id]').val(data.LoanCaseBillMain.sst_trx_id);

                    form.find('[name=pfee1_receipt_date]').val(data.LoanCaseBillMain.pfee1_receipt_date);
                    form.find('[name=pfee1_receipt_trx_id]').val(data.LoanCaseBillMain.pfee1_receipt_trx_id);

                    form.find('[name=pfee2_receipt_date]').val(data.LoanCaseBillMain.pfee2_receipt_date);
                    form.find('[name=pfee2_receipt_trx_id]').val(data.LoanCaseBillMain.pfee2_receipt_trx_id);

                    form.find('[name=financed_fee]').val(data.LoanCaseBillMain.financed_fee);
                    form.find('[name=financed_sum]').val(data.LoanCaseBillMain.financed_sum);
                    form.find('[name=financed_payment_date]').val(data.LoanCaseBillMain.payment_date);

                    form.find('[name=disb_name]').val(data.LoanCaseBillMain.disb_name);
                    form.find('[name=disb_amt_manual]').val(data.LoanCaseBillMain.disb_amt_manual);
                    form.find('[name=disb_trx_id]').val(data.LoanCaseBillMain.disb_trx_id);
                    form.find('[name=disb_payment_date]').val(data.LoanCaseBillMain.disb_payment_date);


                    let currentDate = new Date(data.LoanCaseBillMain.created_at);

                    var date = currentDate.getDate();
                    var month = currentDate.getMonth(); //Be careful! January is 0 not 1
                    var year = currentDate.getFullYear();

                    $("#print_payment_date").html(date + "-" + (month + 1) + "-" + year);

                    var strHtml = '';

                    if (data.QuotationTemplate.length > 0) {
                        strHtml += '<option value="0">-- Select account --</option>';

                        for (var i = 0; i < data.QuotationTemplate.length; i++) {
                            strHtml += '<option value="' + data.QuotationTemplate[i].default_amt +
                                '" class="cat_all cat_' + data.QuotationTemplate[i].account_cat_id + '" id="' +
                                data.QuotationTemplate[i].account_item_id + '">' + data.QuotationTemplate[i]
                                .account_name + '</option>';
                        }
                        console.log(strHtml);
                        $("#ddlAccountItem").html(strHtml);

                    }

                    if (data.invoiceTemplate.length > 0) {
                        strHtml += '<option value="0">-- Select account --</option>';

                        for (var i = 0; i < data.invoiceTemplate.length; i++) {
                            strHtml += '<option value="' + data.invoiceTemplate[i].default_amt +
                                '" class="cat_all cat_invoice_' + data.invoiceTemplate[i].account_cat_id +
                                '" id="' + data.invoiceTemplate[i].account_item_id + '">' + data
                                .invoiceTemplate[i].account_name + '</option>';
                        }
                        console.log(strHtml);

                        strHtml +=
                            '<option value="0" class="cat_all cat_invoice_1" id="129">Admin fees</option>';
                        $("#ddlAccountItemInvoice").html(strHtml);

                    }


                    // form.find('[name=payment_desc]').val(data.LoanCaseBillMain.remark);
                }
            });
        }


        function clearReferral(id) {
            $("#referral_name_" + id).val('');
        }

        function updateBillSummary() {
            var formData = new FormData();
            formData.append('referral_a1', $("#sum_referral_a1").val());
            formData.append('referral_a2', $("#sum_referral_a2").val());
            formData.append('referral_a3', $("#sum_referral_a3").val());
            formData.append('referral_a4', $("#sum_referral_a4").val());
            formData.append('marketing', $("#sum_marketing").val());
            formData.append('uncollected', $("#sum_uncollected").val());

            $.ajax({
                type: 'POST',
                url: '/updateBillSummary/' + $("#selected_bill_id").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        loadCaseBill($("#selected_bill_id").val());
                        // location.reload();
                    }

                }
            });
        }

        function loadQuotationIntoInvoice() {

            $.ajax({
                type: 'POST',
                url: '/loadQuotationToInvoice/' + $("#selected_bill_id").val(),
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )


                        $('#tbl-invoice-bill').html(data.view4);

                        // location.reload();
                    }

                }
            });
        }



        function CreateBill(case_id) {



            var bill_list = [];
            var bill = {};
            var count = 0;
            var min_hit_count = 0;
            var max_hit_count = 0;

            var template_name = $("#quotation_template option:selected").text();

            if ($("#ddlQuotationCheck").is(':checked')) {

                if ($("#my_quotation_template").val() == "0") {
                    Swal.fire('notice!', 'Please select quotation template', 'warning');
                    return;
                }
            } else {
                if ($("#quotation_template").val() == 0) {
                    Swal.fire('Notice!', 'Please select an template', 'warning');
                    return;
                }

            }



            $.each($("input[name='quotation']:checked"), function() {
                count += 1;
            });


            if (count == 0) {
                Swal.fire('notice!', 'Please load selected quotation template', 'warning');
                return;
            }

            // $('.btn-submit').attr('disabled', true);

            template_name = template_name.replace('Quotation', '').trim();
            template_name = template_name.replace('-', '').trim();

            $.each($("input[name='quotation']:checked"), function() {

                itemID = $(this).val();

                account_item_id = parseFloat($("#account_item_id_" + itemID).val());
                need_approval = parseFloat($("#need_approval_" + itemID).val());
                amount = parseFloat($("#quo_amt_" + itemID).val());
                cat_id = parseFloat($("#cat_" + itemID).val());
                min = parseFloat($("#min_" + itemID).val());
                max = parseFloat($("#max_" + itemID).val());
                desc = $("#desc_span_" + itemID).val();
                console.log(desc);

                if (min != 0) {
                    if (amount < min) {
                        min_hit_count += 1;
                        $("#quo_amt_" + itemID).addClass('error-input-box');
                    } else {
                        $("#quo_amt_" + itemID).removeClass('error-input-box');
                    }
                }

                if (max != 0) {
                    if (amount > max) {
                        max_hit_count += 1;
                        $("#quo_amt_" + itemID).addClass('error-input-box');
                    } else {
                        $("#quo_amt_" + itemID).removeClass('error-input-box');
                    }
                }

                bill = {
                    account_item_id: account_item_id,
                    need_approval: need_approval,
                    cat_id: cat_id,
                    amount: amount,
                    desc: desc,
                    min: min,
                    max: max
                };

                bill_list.push(bill);

            });

            if (min_hit_count > 0) {
                @if (in_array($current_user->branch_id, [1]))
                    @if (!in_array($current_user->menuroles, ['account', 'admin']) && !in_array($current_user->id, [122,158,165,167,177,176]))
                        Swal.fire('notice!', 'Please make sure all item not lower than min value', 'warning');
                        $('.btn-submit').attr('disabled', false);
                        return;
                    @endif
                @endif
                
                
            }

            if (max_hit_count > 0) {
                @if (in_array($current_user->branch_id, [1]))
                    @if (!in_array($current_user->menuroles, ['account', 'admin']) && !in_array($current_user->id, [122,158,165,167,177,176]))
                        Swal.fire('notice!', 'Please make sure all item not higher than max value', 'warning');
                        $('.btn-submit').attr('disabled', false);
                        return;
                    @endif
                @endif
                
            }

            var form_data = new FormData();
            form_data.append("bill_list", JSON.stringify(bill_list));
            form_data.append("quotation_template_id", $("#quotation_template").val());
            form_data.append("name", template_name);
            form_data.append("hidden_quotation_template_id", $("#hidden_quotation_template_id").val());
            form_data.append("bill_to", $("#ddl_party_quo").val());
            form_data.append("sst_rate", $("#ddl_sst").val());

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/create_bill/' + case_id,
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    $("#div_full_screen_loading").hide();
                    console.log(data);
                    if (data.status == 1) {
                        toastController(data.message);
                        // location.reload();
                        closeUniversalModal();
                        loadMainBillTable();
                        $('#tbl-quotation-create').html('');
                        // $("#div-bill-list").html(data.view);
                    } else {
                        $('.btn-submit').attr('disabled', false);
                    }

                },
                error: function(xhr, status, error) {
                    $('.btn-submit').attr('disabled', false);
                    $("#div_full_screen_loading").hide();
                }
            });
        }

        function loadMainBillTable() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/loadMainBillTable/{{ $case->id }}',
                // data: $('#form_files').serialize(),
                data: null,
                success: function(data) {
                    $("#div-bill-list").html(data.view);
                    $("#div-bill-summary").html(data.total_sum);
                }
            });
        }

        function generateFileFromTemplate($id) {
            var formData = new FormData();
            var template_id = [];

            $.each($("input[name='files']:checked"), function() {

                itemID = $(this).val();
                template_id.push(itemID);

            });

            formData.append('template_id', template_id);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/generate_file_from_template/' + $id,
                // data: $('#form_files').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    $("#div_full_screen_loading").hide();

                    toastController('File generated');
                    location.reload();
                },
                error: function(file, response) {
                    $("#div_full_screen_loading").hide();
                    toastController('Error occurs', 'warning');
                }
            });
        }

        function generateReceipt($id) {
            var formData = new FormData();
            // formData.append('template_id', $("#ddl_case_template").val());

            var template_id = [];

            $.each($("input[name='files']:checked"), function() {

                itemID = $(this).val();
                template_id.push(itemID);

            });

            formData.append('template_id', template_id);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/generate_receipt/' + $id + '/{{ $case->id }}',
                // data: $('#form_files').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data.data);

                    var formData = new FormData();

                    var link = document.createElement("a");
                    link.download = name;
                    link.href = '/' + data.data;
                    link.click();

                    formData.append('delete_path', data.data);

                    $.ajax({
                        type: 'POST',
                        url: '/delete_receipt_file/',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {

                            // location.reload();
                        }
                    });

                    Swal.fire('success', 'Receipt generated', 'success')
                    // location.reload();
                }
            });
        }

        function generateBillReceipt($id) {
            var formData = new FormData();
            // formData.append('template_id', $("#ddl_case_template").val());

            var template_id = [];

            // $.each($("input[name='files']:checked"), function() {

            //   itemID = $(this).val();
            //   template_id.push(itemID);

            // });

            // formData.append('template_id', template_id);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/generateBillReceipt/' + $id + '/{{ $case->id }}',
                // data: $('#form_files').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data.data);

                    var formData = new FormData();

                    var link = document.createElement("a");
                    link.download = name;
                    link.href = '/' + data.data;
                    link.click();

                    formData.append('delete_path', data.data);

                    $.ajax({
                        type: 'POST',
                        url: '/delete_receipt_file/',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {}
                    });

                    Swal.fire('success', 'Receipt generated', 'success')
                }
            });
        }


        function generateTrustdReceipt($id) {
            var formData = new FormData();
            // formData.append('template_id', $("#ddl_case_template").val());

            var template_id = [];

            // $.each($("input[name='files']:checked"), function() {

            //   itemID = $(this).val();
            //   template_id.push(itemID);

            // });

            formData.append('template_id', template_id);
            formData.append('sum_amount', $("#sum_amount").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/generate_trust_receipt/{{ $case->id }}',
                // data: $('#form_files').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data.data);

                    var formData = new FormData();

                    var link = document.createElement("a");
                    link.download = name;
                    link.href = '/' + data.data;
                    link.click();

                    formData.append('delete_path', data.data);

                    $.ajax({
                        type: 'POST',
                        url: '/delete_receipt_file/',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {

                            // location.reload();
                        }
                    });

                    Swal.fire('success', 'Receipt generated', 'success')
                    // location.reload();
                }
            });
        }

        function generateBillLumdReceipt($id) {
            var formData = new FormData();
            // formData.append('template_id', $("#ddl_case_template").val());

            var template_id = [];

            // $.each($("input[name='files']:checked"), function() {

            //   itemID = $(this).val();
            //   template_id.push(itemID);

            // });

            formData.append('template_id', template_id);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/generateBillLumdReceipt/' + $("#bill_id").val() + '/{{ $case->id }}',
                data: $('#form_bill_main_info').serialize(),
                // processData: false,
                // contentType: false,
                success: function(data) {

                    console.log(data.data);

                    $("#dReceipt-p").html(data.view);

                    // var formData = new FormData();

                    // var link = document.createElement("a");
                    // link.download = name;
                    // link.href = '/' + data.data;
                    // link.click();

                    // formData.append('delete_path', data.data);

                    // $.ajax({
                    //     type: 'POST',
                    //     url: '/delete_receipt_file/',
                    //     data: formData,
                    //     processData: false,
                    //     contentType: false,
                    //     success: function(data) {

                    //         // location.reload();
                    //     }
                    // });

                    // Swal.fire('success', 'Receipt generated', 'success')
                    // location.reload();
                }
            });
        }

        function generateReceiptController($id, $type) {
            var formData = new FormData();
            // formData.append('template_id', $("#ddl_case_template").val());

            var template_id = [];

            // $.each($("input[name='files']:checked"), function() {

            //   itemID = $(this).val();
            //   template_id.push(itemID);

            // });

            formData.append('template_id', template_id);
            formData.append('type', $type);
            formData.append('obj_id', $id);
            formData.append('sum_amount', $("#sum_amount").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/generateReceiptController/{{ $case->id }}',
                // data: $('#form_files').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    // console.log(data.view);

                    $("#dReceipt-p").html(data.view);

                    // var formData = new FormData();



                    // Swal.fire('success', 'Receipt generated', 'success')
                    // location.reload();
                }
            });
        }

        function submitBillEntry() {
            var formData = new FormData();

            var form = $("#formBillReceive");

            if (form.find('[name=OfficeBankAccount_id]').val() == '' || form.find('[name=transaction_id]').val() == '' ||
                form.find('[name=payment_name]').val() == '' || form.find('[name=amount]').val() == '' ||
                form.find('[name=payment_date]').val() == '') {
                Swal.fire('notice', 'Please make sure mandatory fields are fill', 'warning');
                return;
            }

            form.find('[name=bill_main_id]').val($("#selected_bill_id").val());

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                // url: '/receive_bill_payment/' + '{{ $case->id }}/' + $("#selected_bill_id").val(),
                url: '/receive_bill_payment/' + '{{ $case->id }}',
                data: $('#formBillReceive').serialize(),
                // processData: false,
                // contentType: false,
                success: function(data) {

                    $("#div_full_screen_loading").hide();

                    closeUniversalModal();
                    toastController('Payment updated');
                    loadCaseBill($("#selected_bill_id").val());
                    loadMainBillTable();

                    document.getElementById("formBillReceive").reset();
                },
                error: function(file, response) {
                    $("#div_full_screen_loading").hide();
                }
            });
        }

        function submitNotes() {
            // submitNotesV1();

            if ($("#note_edit_mode").val() == 0) {
                submitNotesV1();
            } else if ($("#note_edit_mode").val() == 1) {
                submitEditNotes();
            }

        }

        function submitNotesV1() {
            var formData = new FormData();

            var desc = CKEDITOR.instances['summary-ckeditor'].getData();

            if ($("#desc").val() == "") {
                // Swal.fire('Notice!', '', 'warning');
                return
            }

            console.log(desc);

            formData.append('notes_msg', desc);
            formData.append('note_type', $("#note_type").val());


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/submitNotes/' + '{{ $case->id }}',
                // data: $('#form_notes').serialize(), 
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);

                    if ($("#note_type").val() == 1) {
                        $('#account_notes').html(data.view);
                    } else if ($("#note_type").val() == 2) {
                        $('#notes').html(data.view);

                    } else {
                        $('#notes-pnc').html(data.view);

                    }

                    viewMode();
                    toastController('Note submitted');

                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#note_box_" + data.return_id).offset().top
                    }, 1000);
                }
            });
        }


        function submitEditNotes() {
            var formData = new FormData();
            var url = 'submitEditNotes';

            var desc = CKEDITOR.instances['summary-ckeditor'].getData();

            if ($("#desc").val() == "") {
                // Swal.fire('Notice!', '', 'warning');
                return
            }

            console.log(desc);



            formData.append('notes_msg', desc);
            formData.append('note_type', $("#note_type").val());

            if ($("#note_type").val() == 3) {
                url = 'submitEditPncNotes';
            } else if ($("#note_type").val() == 1) {
                url = 'submitEditMarketingNotes';
            }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/' + url + '/' + $("#note_edit_id").val(),
                // data: $('#form_notes').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    if (data.status == 1) {
                        if ($("#note_type").val() == 3) {
                            $('#notes-pnc').html(data.view);
                        } else if ($("#note_type").val() == 1) {
                            $('#account_notes').html(data.view);
                        } else {
                            $('#notes').html(data.view);
                        }

                        viewMode();
                        toastController('Note edited');

                        if ($("#note_type").val() == 3) {
                            $([document.documentElement, document.body]).animate({
                                scrollTop: $("#note_pnc_box_" + data.return_id).offset().top
                            }, 1000);
                        } else if ($("#note_type").val() == 1) {
                            $([document.documentElement, document.body]).animate({
                                scrollTop: $("#note_pnc_box_" + data.return_id).offset().top
                            }, 1000);
                        } else {
                            $([document.documentElement, document.body]).animate({
                                scrollTop: $("#note_box_" + data.return_id).offset().top
                            }, 1000);
                        }



                    } else {
                        toastController(data.message, 'danger');
                    }


                }
            });
        }

        function submitEditPncNotes() {
            var formData = new FormData();

            var desc = CKEDITOR.instances['summary-ckeditor'].getData();

            if ($("#desc").val() == "") {
                // Swal.fire('Notice!', '', 'warning');
                return
            }


            formData.append('notes_msg', desc);
            formData.append('note_type', $("#note_type").val());


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/submitEditPncNotes/' + $("#note_edit_id").val(),
                // data: $('#form_notes').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    if (data.status == 1) {
                        $('#notes-pnc').html(data.view);
                        viewMode();
                        toastController('Note edited');

                        $([document.documentElement, document.body]).animate({
                            scrollTop: $("#note_pnc_box_" + data.return_id).offset().top
                        }, 1000);
                    } else {
                        toastController(data.message, 'danger');
                    }


                }
            });
        }

        function deleteNotes($id) {
            var formData = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                icon: 'warning',
                title: 'Delete this note?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteNotes/' + $id,
                        data: null,
                        // processData: false,
                        // contentType: false,
                        success: function(data) {

                            if (data.status == 1) {
                                $('#notes').html(data.view);
                                viewMode();
                                toastController('Note deleted');

                                $([document.documentElement, document.body]).animate({
                                    scrollTop: $("#note_box_" + data.return_id).offset().top
                                }, 1000);
                            } else {
                                toastController(data.message, 'danger');
                            }



                        }
                    });
                }
            })

        }

        function deletePncNotes($id) {
            var formData = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                icon: 'warning',
                title: 'Delete this note?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deletePncNotes/' + $id,
                        data: null,
                        // processData: false,
                        // contentType: false,
                        success: function(data) {

                            if (data.status == 1) {
                                $('#notes-pnc').html(data.view);
                                viewMode();
                                toastController('Note deleted');

                                $([document.documentElement, document.body]).animate({
                                    scrollTop: $("#note_box_" + data.return_id).offset().top
                                }, 1000);
                            } else {
                                toastController(data.message, 'danger');
                            }



                        }
                    });
                }
            })

        }

        function clearReferral($field_type) {
            Swal.fire({
                icon: 'warning',
                title: 'Confirmation',
                text: 'Remove current referral?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {


                    var formData = new FormData();
                    formData.append('field_type', $field_type);
                    $.ajax({
                        type: 'POST',
                        url: '/clearReferral/' + $("#selected_bill_id").val(),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {

                            if (data.status == 1) {
                                loadCaseBill($("#selected_bill_id").val());
                                toastController(data.message);
                            } else {
                                toastController(data.message, 'danger');
                            }



                        }
                    });
                }
            })
        }

        function deleteMarketingNotes($id) {
            var formData = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Delete this note?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteMarketingNotes/' + $id,
                        data: null,
                        // processData: false,
                        // contentType: false,
                        success: function(data) {

                            if (data.status == 1) {
                                $('#account_notes').html(data.view);
                                viewMode();
                                toastController('Note deleted');

                                $([document.documentElement, document.body]).animate({
                                    scrollTop: $("#note_box_" + data.return_id).offset().top
                                }, 1000);
                            } else {
                                toastController(data.message, 'danger');
                            }



                        }
                    });
                }
            })

        }


        function stepController(step_id) {
            $(".li_check_point").removeClass('current');
            $("#li_check_point_" + step_id).addClass('current');


            $(".sCheckpoint").hide();
            $("#s_check_point_" + step_id).show();
        }

        function stepControllerV2(step_id) {
            $(".li_check_pointv2").removeClass('current');
            $("#li_check_point_" + step_id + '_v2').addClass('current');


            $(".sCheckpointv2").hide();
            $("#s_check_point_" + step_id + '_v2').show();
        }

        function deleteFile($id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Delete this template?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/delete_case_file/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {


                                Swal.fire('Success!', data.message, 'success');
                                // iniFileTable();
                                table.ajax.reload();
                                // location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        function deleteMarketingBill($id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                icon: 'warning',
                title: 'Delete this file?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteMarketingBill/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                viewMode();
                                toastController('Attachment deleted');
                                $('#div_case_attachment').html(data.LoanAttachment);
                                $('#div_case_marketing_attachment').html(data.LoanAttachmentMarketing);
                                $('#home-1').html(data.CheckListMain);

                                iniAttachmentTable();


                                // Swal.fire('Success!', data.message, 'success');
                                // // iniFileTable();
                                // table.ajax.reload();
                                // location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        function calculateVoucherSum(object) {
            var voucherSum = 0;
            convertDecimal(object);
            $.each($("input[name='case_bill']:checked"), function() {

                itemID = $(this).val();

                console.log($("#txt_available_num_" + itemID).val());

                available_amount = parseFloat($("#txt_available_num_" + itemID).val());
                available_amount = $("#txt_available_num_" + itemID).val();
                request_amount = parseFloat($("#txt_request_amt_" + itemID).val());
                voucherSum += request_amount;

            });

            $("#voucherSum").html(voucherSum);

        }


        function convertDecimal(object) {
            var Value = $(object).val();

            if (Value == "") {
                Value = 0;
            }

            $(object).val(parseFloat(Value).toFixed(2));
        }

        function updateVoucher() {
            var formData = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/updateVoucher/' + $("#trust_id").val(),
                data: $('#form_trust').serialize(),
                // processData: false,
                // contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {

                        if (data.type == 'bill') {
                            // $('#tbl-bill-receive').html(data.receive);
                            // $('#tbl-disb-case').html(data.disburse);
                            // $('#div-bill-summary-details').html(data.billSummary);
                            loadCaseBill($("#selected_bill_id").val());
                        } else {
                            $('#tbl-trust-recv').html(data.view);
                            $('#tbl-trust-disb').html(data.view2);
                            $('#d-trust-summary').html(data.summary);
                        }
                        closeUniversalModal();
                        toastController(data.message);
                    } else {
                        Swal.fire('notice!', data.message, 'warning');
                    }

                    $("#div_full_screen_loading").hide();
                },
                error: function(file, response) {
                    $("#div_full_screen_loading").hide();
                }
            });
        }

        function updateBillReceive() {
            var formData = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                type: 'POST',
                url: '/update_bill_receove_value/' + $("#bill_receive_id").val() + '/' + $("#selected_bill_id")
                    .val(),
                data: $('#form_bill_receive').serialize(),
                // processData: false,
                // contentType: false,
                success: function(data) {

                    if (data.status == 1) {

                        Swal.fire('success', 'Bill details updated', 'success');
                        location.reload();
                    } else {
                        Swal.fire('notice!', data.message, 'warning');
                    }

                    console.log(data);

                    // 
                }
            });
        }

        function updateBillPrintDetails() {
            var formData = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/update_bill_print_details/' + $("#bill_id").val(),
                data: $('#form_bill_main_info').serialize(),
                // processData: false,
                // contentType: false,
                success: function(data) {

                    console.log(data);



                    Swal.fire('success', 'Trust details updated', 'success')
                    location.reload();
                }
            });
        }

        function updateLoanCaseTrustMain() {
            var formData = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/update_loan_case_trust_main/{{ $case->id }}',
                data: $('#form_trust_main').serialize(),
                // processData: false,
                // contentType: false,
                success: function(data) {

                    console.log(data);
                    Swal.fire('success', 'Trust details updated', 'success')
                    // location.reload();
                }
            });
        }

        function uploadMarketingBill() {
            $("#span_update").hide();
            $(".overlay").show();

            var formData = new FormData();

            var files = $('#marketing_bill_file')[0].files;
            console.log(files[0]);
            formData.append('marketing_bill_file', files[0]);
            formData.append('case_id', '{{ $case->id }}');
            // formData.append('selected_id', $("#selected_id").val());
            formData.append('case_ref_no', '{{ $case->case_ref_no }}');

            // formData.append('_token','<?php echo csrf_token(); ?>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/uploadMarketingBill',
                // data: $('#form_action').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            'File Uploaded',
                            'success'
                        )
                        location.reload();
                    }



                }
            });
        }

        function revertToQuotation() {
            Swal.fire({
                icon: 'warning',
                text: 'Revert back to quotation? This action will clear current invoice records',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $("#div_full_screen_loading").show();

                    var form_data = new FormData();

                    form_data.append("is_reserve_runningno", 0);

                    $.ajax({
                        type: 'POST',
                        url: '/RevertInvoiceBacktoQuotation/' + $("#selected_bill_id").val(),
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            $("#div_full_screen_loading").hide();
                            console.log(data);
                            if (data.status == 1) {

                                toastController(data.message);
                                location.reload();
                            } else {
                                toastController(data.message);
                            }

                        },
                        error: function(file, response) {
                            $("#div_full_screen_loading").hide();
                        }
                    });
                }
            })
        }

        function revertToQuotationWithReserveINVNo() {
            Swal.fire({
                icon: 'warning',
                text: 'Revert back to quotation and reserve current running no? This action will clear current invoice records',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $("#div_full_screen_loading").show();

                    var form_data = new FormData();

                    form_data.append("is_reserve_runningno", 1);

                    $.ajax({
                        type: 'POST',
                        url: '/RevertInvoiceBacktoQuotation/' + $("#selected_bill_id").val(),
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            $("#div_full_screen_loading").hide();
                            console.log(data);
                            if (data.status == 1) {

                                toastController(data.message);
                                location.reload();
                            } else {
                                toastController(data.message);
                            }

                        },
                        error: function(file, response) {
                            $("#div_full_screen_loading").hide();
                        }
                    });
                }
            })
        }

        function convertToInvoice() {
            Swal.fire({
                icon: 'warning',
                text: 'Convert this quotation to Invoice? After convert you won\'t be able to update figure',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $("#div_full_screen_loading").show();
                    $.ajax({
                        type: 'POST',
                        url: '/convertToInvoice/' + $("#selected_bill_id").val(),
                        data: $('#form_action').serialize(),
                        success: function(data) {
                            console.log(data);
                            $("#div_full_screen_loading").hide();
                            if (data.status == 1) {

                                // Swal.fire('Success!', data.message, 'success');
                                toastController(data.message);
                                location.reload();
                            } else {
                                // Swal.fire('notice!', data.message, 'warning');
                                toastController(data.message);
                            }

                        },
                        error: function(file, response) {
                            $("#div_full_screen_loading").hide();
                        }
                    });
                }
            })
        }

        function convertToSST() {
            Swal.fire({
                title: 'Convert this invoice to SST? After convert you won\'t be able to update figure',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/convertToSST/' + $("#selected_bill_id").val(),
                        data: $('#form_action').serialize(),
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                // Swal.fire('Success!', data.message, 'success');
                                toastController(data.message);
                                location.reload();
                            } else {
                                // Swal.fire('notice!', data.message, 'warning');
                                toastController(data.message);
                            }

                        }
                    });
                }
            })
        }


        function modifiedCheck(id) {
            blnEdited = false;


            convertDecimal("#quo_amount_" + id);
            convertDecimal("#edit_quo_amount_" + id);

            // convertDecimal("#quo_amt_" + id);
            // convertDecimal("#order_no" + id);


            if ($("#quo_amount_" + id).val() != $("#edit_quo_amount_" + id).val()) {
                blnEdited = true;
            }

            var sum = 0;
            var total = 0;

            $.each($("input[name='" + $("#edit_quo_amount_" + id).attr('name') + "']"), function() {

                value = $(this).val();

                sum += parseFloat(value);
                console.log(sum);

            });

            $('.totalprice').each(function() {
                total += parseFloat($(this).val()); // Or this.innerHTML, this.innerText
            });

            $("#" + $("#edit_quo_amount_" + id).attr('name')).html("RM " + numberWithCommas(sum.toFixed(2)));
            $("#total_edit").html("RM " + numberWithCommas(total.toFixed(2)));


            if (blnEdited == true) {

                $("#bln_modified_" + id).val(1);
                // $("#modified_" + id).html(1);
                $("#ic_modified_" + id).show();

            } else {
                $("#bln_modified_" + id).val(0);
                $("#ic_modified_" + id).hide();
            }


        }

        function saveAllQuoValue() {
            var bill_list = [];
            var bill = {};
            var intCountModified = 0;
            var amount = 0;
            var min = 0;
            var max = 0;

            $("#tbl-case-bill tr").each(function() {
                var self = $(this);

                var item_id = self.find("td:eq(1)").text().trim();

                console.log(item_id);

                if (item_id != 0) {
                    if ($("#bln_modified_" + item_id).val() == "1") {
                        intCountModified += 1;

                        amount = $("#edit_quo_amount_" + item_id).val();

                        bill = {
                            id: item_id,
                            amount: amount
                        };

                        bill_list.push(bill);
                    }
                }
            })

            console.log(bill_list);


            var form_data = new FormData();

            form_data.append("bill_list", JSON.stringify(bill_list));
            form_data.append('_token', '{{ csrf_token() }}');

            $.ajax({
                type: 'POST',
                url: '/update_quotation_bill_by_admin',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        location.reload();
                    } else {
                        Swal.fire(
                            'Notice!',
                            data.message,
                            'warning'
                        )
                    }

                }
            });
        }

        // $(".referral_name").click(function() {
        //       referralMode();
        //   });

        function referralMode(id) {
            $("#dBillList").hide();
            // $("#form_case").hide();
            $("#selected_referral").val(id)
            $("#div-referral").show();
        }

        function CancelReferralMode() {
            $("#dBillList").show();
            // $("#form_case").hide();
            $("#div-referral").hide();
        }

        function createReferralMode() {
            $("#dBillList").hide();
            // $("#form_case").hide();
            $("#div-referral").hide();
            $("#div-referral-create").show();
        }

        function searchReferaral() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_referral");
            filter = input.value.toUpperCase();

            $("#tbl-referral1 tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(1)").text().trim();

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })

            console.log(input);

            // if (filter == "") {
            //     $("#tbl-referral1 tr").each(function() {
            //         var self = $(this);
            //         $(this).hide();
            //     })
            // }
        }

        function selectedReferral(id) {

            var referral = $("#selected_referral").val();
            $("#tbl-referral tr#referral_row_" + id).each(function() {
                var self = $(this);
                var txtName = self.find("td:eq(1)").text().trim();
                var txtEmail = self.find("td:eq(2)").text().trim();
                var txtPhoneNo = self.find("td:eq(3)").text().trim();
                var txtCompany = self.find("td:eq(4)").text().trim();
                var ic_no = self.find("td:eq(5)").text().trim();
                // var bank_id = self.find("td:eq(8)").text().trim();
                var bank_id = self.find("td:eq(6)").text().trim();
                var bank_account = self.find("td:eq(7)").text().trim();
                var txtId = self.find("td:eq(9)").text().trim();

                console.log(txtId);

                if (txtCompany != "" && txtCompany != null) {
                    txtCompany = " (" + txtCompany + ")";
                }

                $("#referral_name_" + referral).val(txtName + txtCompany);
                // $("#referral_email").val(txtEmail);
                // $("#referral_phone_no").val(txtPhoneNo);
                $("#referral_id_" + referral).val(txtId);
                // $("#referral_ic_no").val(ic_no);
                $("#referral_bank_" + referral).val(bank_id);

                $("#referral_account_no_" + referral).val(bank_account);
                // $("#referral_bank_account").val(bank_account);

                CancelReferralMode();

            })
        }


        $('#search_referral').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_referral");
            filter = input.value.toUpperCase();

            console.log(input.value);

            $("#tbl-referral tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(1)").text().trim();

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })

            console.log(input);

            if (filter == "") {
                $("#tbl-referral tr").each(function() {
                    var self = $(this);
                    $(this).hide();
                })
            }
        });

        function SaveSummaryInfo() {
            var formData = new FormData();

            $.ajax({
                type: 'POST',
                url: '/SaveSummaryInfo/' + $("#selected_bill_id").val(),
                data: $('#form_bill_summary').serialize(),
                success: function(data) {

                    console.log(data);
                    if (data.status == 1) {
                        loadCaseBill($("#selected_bill_id").val());
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        // loadCaseBill($("#selected_bill_id").val());
                        // location.reload();
                    }
                }
            });
        }

        function createReferral() {

            $("#btnSubmit").attr("disabled", true);
            $.ajax({
                type: 'POST',
                url: '/create_referral',
                data: $('#form_referral').serialize(),
                success: function(results) {
                    console.log(results);
                    $("#btnSubmit").attr("disabled", false);
                    if (results.status == 1) {

                        var txtCompany = '';

                        if ($("#referral_company_new").val() != "" && $("#referral_company_new").val() !=
                            null) {
                            txtCompany = " (" + $("#referral_company_new").val() + ")";
                        }


                        var referral = $("#selected_referral").val();



                        // $("#referral_name").val($("#referral_name_new").val() + txtCompany);
                        // $("#referral_email").val($("#referral_email_new").val());
                        // $("#referral_phone_no").val($("#referral_phone_no_new").val());
                        // $("#referral_ic_no").val($("#referral_ic_no_new").val());
                        // $("#referral_bank").val($("#referral_bank_new").val());
                        // $("#referral_bank_account").val($("#referral_bank_account_new").val());
                        // $("#referral_id").val(results.message);

                        $("#referral_name_" + referral).val($("#referral_name_new").val() + txtCompany);
                        // $("#referral_email").val(txtEmail);
                        // $("#referral_phone_no").val(txtPhoneNo);
                        // $("#referral_id_" + referral).val(txtId);
                        // $("#referral_ic_no").val(ic_no);
                        // $("#referral_bank").val(bank_id);
                        // $("#referral_bank_account").val(bank_account);

                        CancelReferralMode();

                    } else {

                    }

                },
                error: function(xhr, status, error) {
                    $("#btnSubmit").attr("disabled", false);
                }
            });
        }

        // function editQuotationMode(id, case_id) {
        //     // $(".nav-tabs-custom-ctr").hide();
        //     // $("#case_id").val(case_id);
        //     // $("#selected_id").val(id);
        //     $("#dBillList").hide();
        //     $("#divEditQuotation").show();
        //   }

        function editQuotationMode(case_id) {
            $(".nav-tabs-custom-ctr").hide();
            // $("#selected_id").val(id);
            $("#dBillList").hide();
            $("#dEditQuotation").show();
        }

        function cancelEditQuotationMode(case_id) {
            $(".nav-tabs-custom-ctr").show();
            // $("#selected_id").val(id);
            $("#dEditQuotation").hide();
            $("#dBillList").show();
        }


        function editQuotationModal(amt, id, catId, type, item_name) {
            $("#txtNewAmount").val(amt);
            $("#txtOriginalAmount").val(amt);
            $("#txtID").val(id);
            $("#catID").val(catId);
            $("#typeID").val(type);
            $("#item_name").val(item_name);


            $("#txtMinAmt").val($("#min_" + id).val());
            $("#txtMaxAmt").val($("#max_" + id).val());

            // $("#maxID").val(type);

            if (catId == 1) {
                var newAmount = parseFloat($("#txtNewAmount").val());
                newAmount = newAmount * 1.06

                $("#txtCalculateAmount").val(newAmount.toFixed(2));
            } else {
                $("#txtCalculateAmount").val($("#txtNewAmount").val())
            }
        }

        function editInvoiceModal(amt, id, catId, type, item_name) {
            $("#txtNewAmountInvoice").val(amt);
            $("#txtOriginalAmountInvoice").val(amt);
            $("#txtIDInvoice").val(id);
            $("#catIDInvoice").val(catId);
            $("#typeIDInvoice").val(type);
            $("#item_nameInvoice").val(item_name);

            if (catId == 1) {
                var newAmount = parseFloat($("#txtNewAmountInvoice").val());
                newAmount = newAmount * 1.06

                $("#txtCalculateAmountInvoice").val(newAmount.toFixed(2));
            } else {
                $("#txtCalculateAmountInvoice").val($("#txtNewAmountInvoice").val())
            }
        }

        document.getElementById("ddlAccountItem").onchange = function() {
            $("#txtCalculateAccountAmount").val(0);
            $("#txtAmount").val(0);
        }

        function addAccountItemModal(catId) {
            // $("#txtNewAmount").val(amt);
            // $("#txtOriginalAmount").val(amt);
            // $("#txtID").val(id);
            // $("#catID").val(catId);
            $("#ddlAccountItem").val(0);
            $(".cat_all").hide();
            $(".cat_" + catId).show();
            $("#catID").val(catId);

            $("#txtCalculateAccountAmount").val(0);
            $("#txtAmount").val(0);


            // $("#txtAmount").val();

            // if(catId == 1)
            // {
            //   var newAmount = parseFloat($("#txtNewAmount").val());
            //   // newAmount = newAmount*1.06

            //   $("#txtCalculateAmount").val( newAmount.toFixed(2) );
            // }
            // else
            // {
            //   $("#txtCalculateAmount").val( $("#txtNewAmount").val())
            // }


        }

        function addAccountItemModalInvoice(catId) {
            $("#ddlAccountItemInvoice").val(0);
            $(".cat_all").hide();
            $(".cat_invoice_" + catId).show();
            $("#catIDInvoice").val(catId);

            $("#txtCalculateAccountAmount").val(0);
            $("#txtAmount").val(0);

        }


        function updateQuotationValue() {


            var formData = new FormData();

            formData.append('details_id', $("#txtID").val());
            formData.append('catID', $("#catID").val());
            formData.append('NewAmount', $("#txtNewAmount").val());
            formData.append('typeID', $("#typeID").val());
            formData.append('item_name', $("#item_name").val());

            $maxCap = parseFloat($("#txtMaxAmt").val());
            $minCap = parseFloat($("#txtMinAmt").val());
            $newValue = parseFloat($("#txtNewAmount").val());
            console.log($maxCap);
            console.log($minCap);
            console.log($("#txtNewAmount").val());

            if ($maxCap != 0) {
                if ($newValue > $maxCap) {
                    
                    @if (in_array($current_user->branch_id, [1]))
                        @if (!in_array($current_user->menuroles, ['account', 'admin']) && !in_array($current_user->id, [122,158,165,167,177,176]))
                            Swal.fire('notice!', 'Please make sure all item not higher than max value', 'warning');
                            return;
                        @endif
                    @endif
                    
                    
                }
            }


            if ($minCap != 0) {
                if ($newValue < $minCap) {
                    
                    @if (in_array($current_user->branch_id, [1]))
                      @if (!in_array($current_user->menuroles, ['account', 'admin']) && !in_array($current_user->id, [122,158,165,167,177,176]))
                            Swal.fire('notice!', 'Please make sure all item not lower than min value', 'warning');
                            return;
                        @endif
                    @endif
                    
                  
                }
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/updateQuotationValue',
                // data: $('#form_action').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    $("#div_full_screen_loading").hide();

                    $("#span_update").show();
                    $(".overlay").hide();
                    if (data.status == 1) {
                        // Swal.fire('Success!', 'Quotation updated', 'success');
                        toastController('Quotation updated');

                        loadCaseBill($("#selected_bill_id").val());
                        $('#btnClose').click();
                        $(".modal-backdrop").remove();
                    } else {
                        Swal.fire('Notice!', data.message, 'warning')
                    }

                },
                error: function(xhr, status, error) {
                    $("#div_full_screen_loading").hide();
                }
            });
        }


        function updateInvoiceValue() {
            $("#span_update").hide();
            $(".overlay").show();

            var formData = new FormData();

            formData.append('details_id', $("#txtIDInvoice").val());
            formData.append('catID', $("#catIDInvoice").val());
            formData.append('NewAmount', $("#txtNewAmountInvoice").val());
            formData.append('typeID', $("#typeIDInvoice").val());
            formData.append('item_name', $("#item_nameInvoice").val());

            // formData.append('_token','<?php echo csrf_token(); ?>');

            $(".btn").attr("disabled", true);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/updateInvoiceValue',
                // data: $('#form_action').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);


                    $(".btn").attr("disabled", false);

                    $("#span_update").show();
                    $(".overlay").hide();
                    if (data.status == 1) {
                        // Swal.fire('Success!', 'Quotation updated', 'success');
                        toastController('Quotation updated');

                        loadCaseBill($("#selected_bill_id").val());
                        $('#btnCloseInv').click();
                        $(".modal-backdrop").remove();
                    } else {
                        Swal.fire('Notice!', data.message, 'warning')
                    }

                },
                error: function(xhr, status, error) {
                    $(".btn").attr("disabled", false);
                }
            });
        }

        function addQuotationItem() {
            $("#span_update").hide();
            $(".overlay").show();


            $(".btn").attr("disabled", true);

            var formData = new FormData();


            formData.append('details_id', $("#ddlAccountItem").children(":selected").attr("id"));
            formData.append('catID', $("#catID").val());
            formData.append('NewAmount', $("#txtAmount").val());

            // formData.append('_token','<?php echo csrf_token(); ?>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/addQuotationItem/' + $("#selected_bill_id").val(),
                // data: $('#form_action').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);

                    $(".btn").attr("disabled", false);
                    $("#span_update").show();
                    $(".overlay").hide();
                    if (data.status == 1) {
                        // Swal.fire('Success!', 'Quotation updated', 'success');
                        toastController('Quotation updated');

                        loadCaseBill($("#selected_bill_id").val());
                        $('#btnClose2').click();
                        $(".modal-backdrop").remove();
                    } else {
                        Swal.fire('Notice!', data.message, 'warning')
                    }



                },
                error: function(xhr, status, error) {
                    $(".btn").attr("disabled", false);
                }
            });
        }

        function addInvoiceItem() {
            $("#span_update").hide();
            $(".overlay").show();



            $(".btn").attr("disabled", true);

            var formData = new FormData();


            formData.append('details_id', $("#ddlAccountItemInvoice").children(":selected").attr("id"));
            formData.append('catID', $("#catIDInvoice").val());
            formData.append('NewAmount', $("#txtAmountInvoice").val());

            // formData.append('_token','<?php echo csrf_token(); ?>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/addInvoiceItem/' + $("#selected_bill_id").val(),
                // data: $('#form_action').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);

                    $(".btn").attr("disabled", false);
                    $("#span_update").show();
                    $(".overlay").hide();
                    if (data.status == 1) {
                        // Swal.fire('Success!', 'Quotation updated', 'success');
                        toastController('Quotation updated');

                        loadCaseBill($("#selected_bill_id").val());
                        $('#btnCloseInv2').click();
                        $(".modal-backdrop").remove();
                    } else {
                        Swal.fire('Notice!', data.message, 'warning')
                    }



                },
                error: function(xhr, status, error) {
                    $(".btn").attr("disabled", false);
                }
            });
        }

        function deleteQuotationItem(details_id) {

            Swal.fire({
                title: 'Delete this item?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $("#span_update").hide();
                    $(".overlay").show();


                    $(".btn").attr("disabled", true);

                    var formData = new FormData();


                    formData.append('details_id', details_id);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: '/deleteQuotationItem/' + $("#selected_bill_id").val(),
                        // data: $('#form_action').serialize(),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);

                            $(".btn").attr("disabled", false);
                            $("#span_update").show();
                            $(".overlay").hide();
                            if (data.status == 1) {
                                // Swal.fire('Success!', 'Account item deleted', 'success');
                                toastController('Account item deleted');

                                loadCaseBill($("#selected_bill_id").val());
                                $('#btnClose2').click();
                                $(".modal-backdrop").remove();
                            } else {
                                Swal.fire('Notice!', data.message, 'warning')
                            }

                        },
                        error: function(xhr, status, error) {
                            $(".btn").attr("disabled", false);
                        }
                    });
                }
            })
        }

        function deleteInvoiceItem(details_id) {

            Swal.fire({
                title: 'Delete this item?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $("#span_update").hide();
                    $(".overlay").show();


                    $(".btn").attr("disabled", true);

                    var formData = new FormData();


                    formData.append('details_id', details_id);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: '/deleteInvoiceItem/' + $("#selected_bill_id").val(),
                        // data: $('#form_action').serialize(),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);

                            $(".btn").attr("disabled", false);
                            $("#span_update").show();
                            $(".overlay").hide();
                            if (data.status == 1) {
                                // Swal.fire('Success!', 'Account item deleted', 'success');
                                toastController('Account item deleted');

                                loadCaseBill($("#selected_bill_id").val());
                                $('#btnClose2').click();
                                $(".modal-backdrop").remove();
                            } else {
                                Swal.fire('Notice!', data.message, 'warning')
                            }

                        },
                        error: function(xhr, status, error) {
                            $(".btn").attr("disabled", false);
                        }
                    });
                }
            })
        }

        function quotationCalculationEvent() {
            if ($("#catID").val() == "1") {
                var newAmount = parseFloat($("#txtNewAmount").val());
                // newAmount = newAmount * 1.06;
                newAmount = newAmount * (1 + ($("#ddl_sst_modal").val() * 0.01));


                $("#txtCalculateAmount").val(newAmount.toFixed(2));
            } else {
                $("#txtCalculateAmount").val($("#txtNewAmount").val());
            }

        }

        function quotationCalculationEventInvoice() {
            if ($("#catIDInvoice").val() == "1") {
                var newAmount = parseFloat($("#txtNewAmountInvoice").val());
                // newAmount = newAmount * 1.06;
                newAmount = newAmount * (1 + ($("#ddl_sst_modal").val() * 0.01));


                $("#txtCalculateAmountInvoice").val(newAmount.toFixed(2));
            } else {
                $("#txtCalculateAmountInvoice").val($("#txtNewAmountInvoice").val());
            }

        }

        function quotationCalculationEventAccountItem() {
            if ($("#catID").val() == "1") {
                var newAmount = parseFloat($("#txtAmount").val());
                newAmount = newAmount * 1.06

                $("#txtCalculateAccountAmount").val(newAmount.toFixed(2));
            } else {
                $("#txtCalculateAccountAmount").val($("#txtAmount").val());
            }

        }

        function editActionMode() {
            $(".action-view-group").hide();
            $(".action-edit-group").show();
        }

        function cancelEditActionMode() {
            $(".action-view-group").show();
            $(".action-edit-group").hide();
        }

        function checkListRemarkevent(id) {
            var intChange = 0;
            if ($("#remark_" + id).val() != $("#edited_remarks_" + id).val()) {
                intChange += 1;
                // $('#chklist_' + id).attr('checked', true);
            } else {
                // $('#chklist_' + id).attr('checked', false);
            }



            if ($("#status_" + id).val() != 99) {
                status = 0;
                if ($("#complete_" + id).prop('checked') == true) {
                    status = 1;
                }

                if ($("#na_" + id).prop('checked') == true) {
                    status = 99;
                }

                if ($("#status_" + id).val() != status) {
                    intChange += 1;
                }

            } else {
                if ($("#na_" + id).prop('checked') == false) {
                    intChange += 1;
                }
            }

            if (intChange > 0) {
                $('#chklist_' + id).prop('checked', true);
            } else {
                $('#chklist_' + id).prop('checked', false);
            }

        }

        function saveAllCheckListEvent() {
            var bill_list = [];
            var bill = {};


            $.each($("input[name='checklist_id']:checked"), function() {

                var itemID = $(this).val();
                var status = 0;
                var notApplicable = 0;

                if ($("#complete_" + itemID).prop('checked') == true) {
                    status = 1;
                }

                if ($("#na_" + itemID).prop('checked') == true) {
                    notApplicable = 1;
                }

                remarks = $("#edited_remarks_" + itemID).val();

                bill = {
                    remarks: remarks,
                    itemID: itemID,
                    status: status,
                    notApplicable: notApplicable,
                };

                bill_list.push(bill);


            });
            console.log(bill_list);

            if (bill_list.length <= 0) {
                return;
            }

            $("#div_full_screen_loading").show();

            var form_data = new FormData();

            form_data.append("checklist_id", JSON.stringify(bill_list));
            form_data.append('_token', '{{ csrf_token() }}');

            $.ajax({
                type: 'POST',
                url: '/updateCheckListBulk/{{ $case->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);

                    $("#div_full_screen_loading").hide();
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        // $('#form_action')[0].reset();

                        $.each($("input[name='checklist_id']:checked"), function() {

                            var itemID = $(this).val();
                            var status = 0;
                            var notApplicable = 0;

                            $("#ori_remark_" + itemID).html($("#edited_remarks_" + itemID).val());
                            $("#remark_" + itemID).val($("#edited_remarks_" + itemID).val());

                            $("#checklist_" + itemID).removeClass(
                                "bg-done ion-checkmark ion-person bg-aqua ion-clock bg-overdue ion-clock bg-info"
                            );



                            if ($("#na_" + itemID).prop('checked') == true) {
                                $("#checklist_" + itemID).addClass("ion-clock bg-info");

                                $("#status_span_" + itemID).removeClass('Not Applicable');
                                $("#status_span_" + itemID).html('Not Applicable');

                                $("#status_" + itemID).val(99);
                            } else {
                                if ($("#complete_" + itemID).prop('checked') == true) {
                                    $("#checklist_" + itemID).addClass("ion-checkmark bg-done");
                                    $("#status_" + itemID).val(1);
                                } else {
                                    $("#checklist_" + itemID).addClass("ion-person bg-aqua");
                                    $("#status_" + itemID).val(0);
                                }
                            }

                        });

                        cancelEditActionMode();


                        // $(".overlay").hide();
                        // $('#home-1').html(data.view);
                    } else {
                        Swal.fire(
                            'Notice!',
                            data.message,
                            'warning'
                        )
                    }

                },
                error: function(xhr, status, error) {
                    $("#div_full_screen_loading").hide();
                }
            });

        }

        function checkListRemarkeventV2(id) {
            var intChange = 0;
            // if ($("#remark_" + id).val() != $("#edited_remarks_" + id).val()) {
            //     intChange += 1;
            //     // $('#chklist_' + id).attr('checked', true);
            // } else {
            //     // $('#chklist_' + id).attr('checked', false);
            // }



            if ($("#status_" + id + '_v2').val() != 99) {
                status = 0;
                if ($("#complete_" + id + '_v2').prop('checked') == true) {
                    status = 1;
                }

                if ($("#na_" + id + '_v2').prop('checked') == true) {
                    status = 99;
                }

                if ($("#status_" + id + '_v2').val() != status) {
                    intChange += 1;
                }

            } else {
                if ($("#na_" + id + '_v2').prop('checked') == false) {
                    intChange += 1;
                }
            }

            if (intChange > 0) {
                $('#chklist_' + id + '_v2').prop('checked', true);
            } else {
                $('#chklist_' + id + '_v2').prop('checked', false);
            }

        }

        function saveAllCheckListEventV2() {
            var bill_list = [];
            var bill = {};


            $.each($("input[name='checklist_id_v2']:checked"), function() {

                var itemID = $(this).val();
                var status = 0;
                var notApplicable = 0;
                var checklist_details_id = 0;

                if ($("#complete_" + itemID + "_v2").prop('checked') == true) {
                    status = 1;
                } else {
                    status = 0;
                }

                if ($("#na_" + itemID + "_v2").prop('checked') == true) {
                    notApplicable = 1;
                    status = 99;
                }

                checklist_details_id = $("#checklist_id_" + itemID + "_v2").val();

                bill = {
                    checklist_details_id: checklist_details_id,
                    itemID: itemID,
                    status: status,
                    notApplicable: notApplicable,
                };

                bill_list.push(bill);


            });
            console.log(bill_list);

            if (bill_list.length <= 0) {
                return;
            }

            $("#div_full_screen_loading").show();

            var form_data = new FormData();

            form_data.append("checklist_id", JSON.stringify(bill_list));
            form_data.append('_token', '{{ csrf_token() }}');

            $.ajax({
                type: 'POST',
                url: '/updateCheckListBulkV2/{{ $case->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);

                    $("#div_full_screen_loading").hide();
                    if (data.status == 1) {
                        // Swal.fire(
                        //     'Success!',
                        //     data.message,
                        //     'success'
                        // )

                        toastController('Status updated');
                        $('#checklist').html(data.view);

                        cancelEditActionMode();

                    } else {
                        Swal.fire(
                            'Notice!',
                            data.message,
                            'warning'
                        )
                    }

                },
                error: function(xhr, status, error) {
                    $("#div_full_screen_loading").hide();
                }
            });

        }

        function deleteBill($bill_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            Swal.fire({
                title: 'Delete this bill?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#div_full_screen_loading").show();
                    $.ajax({
                        type: 'POST',
                        url: '/deleteBill/' + $bill_id,
                        data: null,
                        success: function(data) {
                            console.log(data);
                            $("#div_full_screen_loading").hide();
                            if (data.status == 1) {
                                toastController(data.message);
                                loadMainBillTable();
                            } else {
                                toastController(data.message, 'warning');
                            }

                        },
                        error: function(xhr, status, error) {
                            $("#div_full_screen_loading").hide();
                            toastController(data.message, 'warning');
                        }
                    });
                }
            })

        }



        function deleteReceivedBill($voucher_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Delete this bill?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteReceivedBill/' + $voucher_id + '/' + $("#selected_bill_id").val(),
                        data: null,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                // Swal.fire('Success!', data.message, 'success');
                                // location.reload();


                                viewMode();
                                toastController(data.message);
                                loadCaseBill($("#selected_bill_id").val());
                                loadMainBillTable();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        function deleteVoucher($voucher_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                icon: 'warning',
                text: 'Delete this record?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteVoucherRecord/' + $voucher_id,
                        data: null,
                        success: function(data) {
                            if (data.status == 1) {
                                toastController(data.message);

                                if (data.type == 'bill') {
                                    // $('#tbl-bill-receive').html(data.receive);
                                    // $('#tbl-disb-case').html(data.disburse);
                                    // $('#div-bill-summary-details').html(data.billSummary);
                                    
                                    // $('#tbl-case-bill').html(data.view);
                                    loadCaseBill($("#selected_bill_id").val());
                                } else {

                                    $('#tbl-trust-recv').html(data.view);
                                    $('#tbl-trust-disb').html(data.view2);
                                    $('#d-trust-summary').html(data.summary);
                                }
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        function deleteDisburseTrust($voucher_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Delete this trust?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteDisburseTrust/' + $voucher_id,
                        data: null,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        // function deleteVoucher($voucher_id) {
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });

        //     Swal.fire({
        //         title: 'Delete this voucher?',
        //         showCancelButton: true,
        //         confirmButtonText: `Yes`,
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 type: 'POST',
        //                 url: '/deleteVoucher/' + $voucher_id,
        //                 data: null,
        //                 success: function(data) {
        //                     console.log(data);
        //                     if (data.status == 1) {

        //                         Swal.fire('Success!', data.message, 'success');
        //                         location.reload();
        //                     } else {
        //                         Swal.fire('notice!', data.message, 'warning');
        //                     }

        //                 }
        //             });
        //         }
        //     })

        // }

        function updateCaseStatus($case_id, type) {
            $confirmationMSG = '';
            $SuccessMSG = '';
            $url = '/case';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (type == 'CLOSED') {
                $confirmationMSG = 'Close this case?';
                $SuccessMSG = 'Case closed';
            } else if (type == 'ABORTED') {
                $confirmationMSG = 'Abort this case?';
                $SuccessMSG = 'Case aborted';
            } else if (type == 'PENDINGCLOSED') {
                $confirmationMSG = 'Update this case to pending close case?';
                $SuccessMSG = 'Request sent';
                $url = '/reviewing-case';
            } else if (type == 'REOPEN') {
                $confirmationMSG = 'Reopen this case?';
                $SuccessMSG = 'Request sent';
            } else if (type == 'REVIEWING') {
                $confirmationMSG = 'Send this case for review and close?';
                $SuccessMSG = 'Case set to for review';
            }

            var form_data = new FormData();

            form_data.append('type', type);



            Swal.fire({

                icon: 'warning',
                text: $confirmationMSG,
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/updateCaseStatus/' + $case_id,
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', $SuccessMSG, 'success');
                                // window.location.href =  $url;
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        function updateCaseSummary() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/updateCaseSummary/{{ $case->id }}',
                data: $('#formCaseSummary').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        $('#div_case_summary').html(data.view);

                        toastController('Status updated');
                        closeUniversalModal();
                    }

                }

            });


        }

        function InvoiceDateModal() {
            // Load current invoice date into the modal
            var currentInvoiceDate = $(".invoice-date").text();
            
            if (currentInvoiceDate) {
                // Convert from DD-MM-YYYY format to YYYY-MM-DD for date input
                var dateParts = currentInvoiceDate.split('-');
                if (dateParts.length === 3) {
                    var day = dateParts[0];
                    var month = dateParts[1];
                    var year = dateParts[2];
                    var formattedDate = year + '-' + month + '-' + day;
                    $("#input_invoice_date").val(formattedDate);
                }
            } else {
                // If no current date, set today's date
                var today = new Date();
                var formattedToday = today.getFullYear() + '-' + 
                    String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(today.getDate()).padStart(2, '0');
                $("#input_invoice_date").val(formattedToday);
            }
        }

        function saveInvoiceDate() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/saveInvoiceDate/' + $("#selected_bill_id").val(),
                data: $('#formInvoiceDate').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        // $('#div_case_summary').html(data.view);

                        toastController('Invoice date updated');
                        closeUniversalModal();

                        let currentDateInv = new Date(data.LoanCaseBillMain.invoice_date);

                        var dateInv = currentDateInv.getDate();
                        var monthInv = currentDateInv.getMonth();
                        var yearInv = currentDateInv.getFullYear();

                        $(".invoice-date").html(dateInv + "-" + (monthInv + 1) + "-" + yearInv);

                        // alert(yearInv + "-" + (monthInv + 1) + "-" + dateInv);

                        var day = ("0" + dateInv).slice(-2);
                        var month = ("0" + (monthInv + 1)).slice(-2);

                        $("#input_invoice_date").val(yearInv + "-" + month + "-" + day);
                    }

                }

            });


        }

        function saveSSTRate() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();
            form_data.append("sst_rate", $("#ddl_sst_modal").val());

            $.ajax({
                type: 'POST',
                url: '/saveSSTRate/' + $("#selected_bill_id").val(),
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        // $('#div_case_summary').html(data.view);

                        toastController('SST rate updated');
                        closeUniversalModal();
                        loadCaseBill($("#selected_bill_id").val());
                    }

                }

            });


        }

        function submitBonusReview(bonus_type) {

            $msg = 'Submit this case for bonus review?';

            if (bonus_type == 'SMPSIGNED') {
                $msg = 'SMP Sgined and submit for bonus review?';
            }

            var form_data = new FormData();
            form_data.append("bonus_type", bonus_type);

            Swal.fire({
                icon: 'warning',
                text: $msg,
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: '/submitBonusReview/{{ $case->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {
                                toastController(data.message);
                                $("#btnRequestBonus").hide();
                            }

                        }

                    });
                }
            })

        }

        function updateQuotationBillTo() {


            var form_data = new FormData();
            form_data.append("bill_to", $("#ddl_party").val());
            form_data.append("bill_to_type", $("#ddl_party").find(':selected').attr('data-type'));

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/updateQuotationBillTo/' + $("#selected_bill_id").val(),
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        toastController(data.message);
                        // var billto = $("#ddl_party").val();
                        $("#p-quo-client-name").html($("#ddl_party").val());
                        $(".party_info").html(data.view);
                        // loadCaseBill($("#selected_bill_id").val());
                    }

                }

            });

        }

        function updateInvoiceTo() {


            var form_data = new FormData();
            form_data.append("bill_to", $("#ddl_party_inv").val());
            form_data.append("bill_to_type", $("#ddl_party_inv").find(':selected').attr('data-type'));

            if ($("#ddl_party_inv").val() == 0) {
                toastController('Please select party', 'warning');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/updateInvoiceTo/' + $("#selected_bill_id").val(),
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        toastController(data.message);
                        // var billto = $("#ddl_party").val();
                        $("#p-inv-client-name").html($("#ddl_party_inv").val());
                        $(".party_invoice_info").html(data.view);
                    }

                }

            });

        }

        function submitClaimsRequest($percentage, $type) {


            var form_data = new FormData();
            form_data.append("percentage", $percentage);
            form_data.append("type", $type);

            Swal.fire({
                icon: 'warning',
                text: 'Submit this claim?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: '/submitClaimsRequest/{{ $case->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {
                                toastController(data.message);
                                // $("#btnRequestBonus").hide();
                                $("#div_case_claims").html(data.view);
                            }

                        }

                    });
                }
            })

        }

        function quotationItemCheckAllController($flag) {



            if ($flag == 1) {
                document.getElementById("check_all").checked = true;
                document.getElementById("uncheck_all").checked = false;

                $flag = true;
            } else {
                document.getElementById("check_all").checked = false;
                document.getElementById("uncheck_all").checked = true;

                $flag = false;
            }

            $.each($("input[name='quotation']"), function() {
                var itemID = $(this).val();

                var isDisabled = $(this).prop('disabled');

                if (isDisabled == true) {
                    document.getElementById("chk_" + itemID).checked = true;
                } else {
                    document.getElementById("chk_" + itemID).checked = $flag;
                }
                // console.log(isDisabled);

            });

            updateAllQuotationAmount();

        }

        function checkCloseFileBalance(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#input_close_abort").val(type);

            $(".btn-close-abort").hide();

            if (type == 'close') {
                $("#span_close_abort").html('Close File');
                $(".btn-close-file").show();
            } else {
                $("#span_close_abort").html('Abort File');
                $(".btn-abort-file").show();
            }

            $(".div-loading-close-file").show();
            $(".div-result-close-file").hide();

            $.ajax({
                type: 'POST',
                url: '/checkCloseFileBalance/{{ $case->id }}',
                data: null,
                success: function(data) {
                    console.log(data);
                    $("#tblCloseFile").html(data.view);
                    $(".div-loading-close-file").hide();
                    $(".div-result-close-file").show();
                    loadCaseList();
                    loadBillList();
                    // if (data.status == 1) {
                    //     toastController(data.message);
                    //     $("#btnRequestBonus").hide();
                    // }

                    updateCloseFileTotalAmt();

                }

            });
        }

        function closeFile() {
            var bill_list = [];
            var bill = {};

            if ($("#cf_transfer_from").val() == 0 || $("#cf_trx_id").val() == '' || $("#cf_transfer_date").val() == '') {

                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure all mandatory fields fill',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            if ($("#cf_transfer_type").val() == 'oa') {
                if ($("#cf_transfer_to").val() == 0) {

                    Swal.fire({
                        icon: 'warning',
                        text: 'Please make sure all mandatory fields fill',
                        confirmButtonText: `Yes`,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                    return;
                }
            } else if ($("#cf_transfer_type").val() == 'bill') {
                if ($("#cf_transfer_to_ca").val() == 0 || $("#cf_transfer_bill_id").val() == 0) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Please make sure all mandatory fields fill',
                        confirmButtonText: `Yes`,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                    return;
                }
            } else if ($("#cf_transfer_type").val() == 'trust') {
                if ($("#cf_transfer_to_ca").val() == 0 || $("#cf_transfer_case_id").val() == 0) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Please make sure all mandatory fields fill',
                        confirmButtonText: `Yes`,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                    return;
                }
            }


            if ($("#cf_transfer_amount").val() < 0) {

                Swal.fire({
                    icon: 'warning',
                    text: 'The transfer amount is negative value',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            if ($("#cf_transfer_from").val() == $("#cf_transfer_to").val()) {

                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure the transfer from and to not to the same bank',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            // if ($("select#cf_transfer_from").find(':selected').data("account-type") != 'CA' || $("select#cf_transfer_to")
            //     .find(':selected').data("account-type") != 'OA') {
            //     Swal.fire({
            //         icon: 'warning',
            //         text: 'Please make sure the transfer from is CA (Client Account) to OA (Office Account)',
            //         confirmButtonText: `Yes`,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //     });
            //     return;
            // }

            $extra_msg = '';


            if (parseFloat($("#cf_ledger_bal").val()) != 0) {
                $extra_msg = 'and ledger after close file not tally';
            }

            $.each($("input[name='close_file_bill']"), function() {
                itemID = $(this).val();
                console.log($("#bill_no_close_file_" + itemID).val());

                bill = {
                    id: $("#bill_no_close_file_" + itemID).val()
                };

                bill_list.push(bill);
            });

            var form_data = new FormData();
            form_data.append("add_bill", JSON.stringify(bill_list));
            form_data.append("transfer_amount", $("#cf_transfer_amount").val());
            form_data.append("transfer_from", $("#cf_transfer_from").val());
            form_data.append("trx_id", $("#cf_trx_id").val());
            form_data.append("transfer_date", $("#cf_transfer_date").val());
            form_data.append("remark", $("#cf_remark").val());
            form_data.append("close_abort", $("#input_close_abort").val());
            form_data.append("transfer_type", $("#cf_transfer_type").val());
            form_data.append("to_case", $("#cf_transfer_case_id").val());
            form_data.append("to_bill", $("#cf_transfer_bill_id").val());

            if ($("#cf_transfer_type").val() == 'oa') {
                form_data.append("transfer_to", $("#cf_transfer_to").val());
                $extra_msg = $("#cf_transfer_to option:selected").text();
            } else {
                form_data.append("transfer_to", $("#cf_transfer_to_ca").val());
                $extra_msg = $("#cf_transfer_to_ca option:selected").text();;
            }


            Swal.fire({
                icon: 'warning',
                text: 'Close this file and transfer amount (' + $("#cf_transfer_amount").val() +
                    ') to ' + $extra_msg,
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/closeFile/{{ $case->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {
                                Swal.fire('Success!', result.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('notice!', result.message, 'warning');
                            }
                        }
                    });
                }
            })


        }

        function updateCloseFileDate() {

            if ($("#cf_d_transfer_from").val() == $("#cf_d_transfer_to").val()) {

                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure the transfer from and to not to the same bank',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            if ($("select#cf_d_transfer_from").find(':selected').data("account-type") != 'CA' || $(
                    "select#cf_d_transfer_to").find(':selected').data("account-type") != 'OA') {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure the transfer from is CA (Client Account) to OA (Office Account)',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();
            form_data.append("transfer_date", $("#cf_d_transfer_date").val());
            form_data.append("transaction_id", $("#cf_d_trx_id").val());
            form_data.append("transfer_from", $("#cf_d_transfer_from").val());
            form_data.append("transfer_to", $("#cf_d_transfer_to").val());
            form_data.append("remark", $("#cf_d_remark_update").val());

            $.ajax({
                type: 'POST',
                url: '/updateCloseFileDate/{{ $case->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire('Success!', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('notice!', data.message, 'warning');
                    }


                }

            });
        }

        function openFileFromS3(filename) {
            var form_data = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            form_data.append("filename", filename);
            // form_data.append("filename", '9gRrec82ztUG8so4UF2HtkZPb2ZH9Z9f2jD5E9oE.pdf');

            $.ajax({
                type: 'POST',
                url: '/getFileFromS3',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                            .userAgent)) {
                        window.location.href = data;
                    } else {
                        window.open(data, "_blank");
                    }
                }
            });
        }



        var table;
        var table2;

        function iniAttachmentTable() {
            $('#tbl-attachment-yadra1').DataTable({
                pagingType: "full_numbers",
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                autoWidth: false,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                }
            });
        }
    </script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(function() {




            table = $('#tbl-file-yadra').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('caseFile.list', $case->id) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                ]
            });

            iniAttachmentTable();





            // function loadAttachment()
            // {
            //     table2 = $('#tbl-attachment-yadra').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     ajax: "{{ route('caseFile.list', $case->id) }}",
            //     columns: [{
            //             data: 'DT_RowIndex',
            //             name: 'DT_RowIndex'
            //         },
            //         {
            //             data: 'name',
            //             name: 'name'
            //         },
            //         {
            //             data: 'created_at',
            //             name: 'created_at'
            //         },
            //         {
            //             data: 'action',
            //             name: 'action',
            //             orderable: true,
            //             searchable: true
            //         },
            //     ]
            // });
            // }



            // table2 = $('#tbl-ledger-yadra').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     ajax: "{{ route('ledger.list', $case->id) }}",
            //     columns: [{
            //             data: 'DT_RowIndex',
            //             name: 'DT_RowIndex'
            //         },
            //         {
            //             data: 'payment_date',
            //             name: 'payment_date'
            //         },
            //         {
            //             data: 'transaction_id',
            //             name: 'transaction_id'
            //         },
            //         {
            //             data: 'bank_account',
            //             name: 'bank_account'
            //         },
            //         {
            //             data: 'voucher_href',
            //             name: 'voucher_href'
            //         },
            //         {
            //             data: 'payee',
            //             name: 'payee'
            //         },
            //         {
            //             data: 'remark',
            //             name: 'remark'
            //         },
            //         {
            //             data: 'debit',
            //             name: 'debit',
            //             class: 'text-right',
            //             render: $.fn.dataTable.render.number(',', '.', 2)
            //         },
            //         {
            //             data: 'credit',
            //             name: 'credit',
            //             class: 'text-right',
            //             render: $.fn.dataTable.render.number(',', '.', 2)
            //         },
            //         {
            //             data: 'type',
            //             name: 'type'
            //         },
            //         {
            //             data: 'account_approval',
            //             name: 'account_approval'
            //         },
            //     ]
            // });


        });
    </script>
@endsection
