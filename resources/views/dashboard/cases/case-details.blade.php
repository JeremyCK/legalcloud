@section('css')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
@endsection

@extends('dashboard.base')
@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">

                                <div class="col-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Perfection Case Details </h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('perfectionCase') }}">
                                        <i class="cil-arrow-left"> </i>Back to list </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-xl-8 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="card-title mb-0 flex-grow-1"> <i class="cil-balance-scale"></i> Case Summary
                                    </h4>
                                </div>
                                <div class="col-6">
                                    @if (in_array($current_user->menuroles, ['admin', 'management']) || in_array($current_user->id, [14]))
                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                            onclick="addNotesMode()" data-toggle="modal" data-target="#modalSummary"
                                            class="btn  btn-primary no-print  float-right"><i class="cil-cog"></i> Update
                                            Case Details</a>
                                    @endif
                                    {{-- <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                        onclick="addNotesMode()" data-toggle="modal" data-target="#modalSummary"
                                        class="btn  btn-primary no-print  float-right"><i class="cil-cog" ></i> Update Case Details</a> --}}
                                </div>
                            </div>

                        </div>
                        <div class="card-body ">
                            <div class="row ">

                                <table class="table mb-0">
                                    <tbody id="div-case-details">
                                        @include('dashboard.cases.div-case-details')
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="card-title mb-0 flex-grow-1"><i class="cil-people"></i> PIC</h4>
                                </div>
                                <div class="col-6">
                                    @if (in_array($current_user->menuroles, ['admin', 'management']) || in_array($current_user->id, [14]))
                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                            onclick="addNotesMode()" data-toggle="modal" data-target="#modalPIC"
                                            class="btn  btn-primary no-print  float-right"><i class="cil-cog"></i> Assign
                                            PIC</a>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="card-body">

                            <table class="table mb-0">
                                <tbody id='pic-list'>
                                    @include('dashboard.cases.pic-list')
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Notes </h4>
                                </div>
                                <div class="col-6">
                                    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                        onclick="addNotesMode()" data-toggle="modal" data-target="#modalNotes"
                                        class="btn  btn-primary no-print  float-right"><i class="cil-plus"></i> Add new
                                        note</a>
                                </div>
                            </div>
                        </div>

                        <div id="div-notes" class="card-body">
                            @include('dashboard.cases.notes-list')
                        </div>
                    </div>
                </div>
            </div>
            <input id="noteMode" value="add" type="hidden" />
            <input id="noteID" value="0" type="hidden" />

            @include('dashboard.cases.modal.modal-notes')
            @include('dashboard.cases.modal.modal-summary')
            @include('dashboard.cases.modal.modal-pic')
        </div>
    </div>
@endsection


@section('javascript')
    <script src="{{ asset('js/jquery.toast.min.js') }}"></script>
    <script>
        $NOTEMODEADD = 'add';
        $NOTEMODEEDIT = 'edit';

        CKEDITOR.replace('summary-ckeditor');
        CKEDITOR.config.height = 300;
        CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
        CKEDITOR.config.removeButtons = 'Image';

        document.getElementById("ddlRole").onchange = function() {
            $(".role_all").hide();
            $("." + $("#ddlRole").val()).show();
        }

        function addNotesMode() {
            CKEDITOR.instances['summary-ckeditor'].setData('');
            $("#noteMode").val($NOTEMODEADD);
            // $("#modalNotes").show();

        }

        function EditNotesMode(id) {
            CKEDITOR.instances['summary-ckeditor'].setData($("#notes_" + id).html());
            $("#noteMode").val($NOTEMODEEDIT);
            $("#noteID").val(id);
        }

        function submitCaseNote() {
            if ($("#noteMode").val() == $NOTEMODEADD) {
                addCaseNotes()
            } else {
                editCaseNotes()
            }

        }

        function updateCaseDetails() {

            var form = $("#formCaseSummary");

            if (form.find('[name=ref_no]').val() == '') {
                Swal.fire('Notice!', 'Please make sure ref no not empty', 'warning');
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/updateCaseDetails/' + '{{ $case->id }}',
                data: $('#formCaseSummary').serialize(),
                success: function(data) {
                    $('#div-case-details').html(data.view);
                    toastController('Details updated');
                    closeUniversalModal();
                }
            });
        }


        function addCaseNotes() {
            var formData = new FormData();
            var desc = CKEDITOR.instances['summary-ckeditor'].getData();

            if ($("#desc").val() == "") {
                return
            }

            formData.append('notes_msg', desc);

            $.ajax({
                type: 'POST',
                url: '/addCaseNotes/' + '{{ $case->id }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#div-notes').html(data.view);
                    toastController('Note submitted');
                    closeUniversalModal();
                }
            });
        }

        function editCaseNotes() {
            var formData = new FormData();
            var desc = CKEDITOR.instances['summary-ckeditor'].getData();

            if ($("#desc").val() == "") {
                return
            }

            formData.append('notes_msg', desc);

            $.ajax({
                type: 'POST',
                url: '/editCaseNotes/' + $("#noteID").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#div-notes').html(data.view);
                    toastController('Note updated');
                    closeUniversalModal();

                }
            });
        }

        function deleteCaseNote($id) {
            var formData = new FormData();

            Swal.fire({
                icon: 'warning',
                text: 'Delete this note?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteCaseNote/' + $id,
                        data: null,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                $('#div-notes').html(data.view);
                                toastController('Note deleted');
                            } else {
                                Swal.fire('Notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function assignPic() {
            var formData = new FormData();

            formData.append('pic_id', $("#ddlPIC").val());
            formData.append('role', $("#ddlRole").val());

            $.ajax({
                type: 'POST',
                url: '/assignPIC/' + '{{ $case->id }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status == 1) {
                        $('#pic-list').html(data.view);
                        toastController('PIC updated');
                        closeUniversalModal();
                    } else {
                        Swal.fire('Notice!', data.message, 'warning');
                    }

                }
            });
        }

        function removePIC(id) {
            var formData = new FormData();

            formData.append('pic_id', $("#ddlPIC").val());

            Swal.fire({
                icon: 'warning',
                text: 'Remove this PIC?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/removePIC/' + id,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                $('#pic-list').html(data.view);
                                toastController('PIC removed');
                                closeUniversalModal();
                            } else {
                                Swal.fire('Notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })


        }
    </script>
@endsection
