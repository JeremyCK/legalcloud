
{{-- <input type="hidden" id="hidden_status" /> --}}
<script>
    function reloadTable() {

        table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "{{ route('case_list.list') }}",
                data: {
                    "status": {{ $status }},
                    "case_type": $("#ddl_portfolio").val(),
                    "lawyer": $("#ddl-lawyer").val(),
                    "clerk": $("#ddl-clerk").val(),
                    "sales": $("#ddl-sales").val(),
                    "chambering": $("#ddl-chamber").val(),
                    "referral": $("#ddl-referral").val(),
                    "parties": $("#parties_name").val(),
                    "branch": $("#branch").val(),
                    "month": $("#ddl_month").val(),
                    "year": $("#ddl_year").val()
                }
            },
            order: [3, 'desc'],
            columns: [{
                    data: 'action',
                    className: "text-center",
                    name: 'action',
                    searchable: false
                },
                {
                    data: 'case_ref_no',
                    name: 'case_ref_no'
                },
                {
                    data: 'client_name',
                    name: 'client_name'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    data: 'spa_date',
                    name: 'spa_date'
                },
                {
                    data: 'completion_date',
                    name: 'completion_date'
                },
                {
                    data: 'branch',
                    name: 'branch'
                },
                {
                    data: 'latest_notes',
                    name: 'latest_notes',
                    searchable: false
                },
            ]
        });
    }

    function searchCase() {
        // $("#form_filter_case").reset();
        document.getElementById("form_filter_case").reset();
        reloadTable();
    }

    function transferModal(id, lawyer_id, clerk_id) {
        $("#txtId").val(id);
        $("#lawyer_id").val(lawyer_id);
        $("#clerk_id").val(clerk_id);
    }

    function TransferCase() {
        var formData = new FormData();

        formData.append('lawyer_id', $("#lawyer_id").val());
        formData.append('clerk_id', $("#clerk_id").val());
        formData.append('skip_lawyer', 1);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: 'transferSystemCase/' + $("#txtId").val(),
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                console.log(data);

                toastController('Case Transferred');
                reloadTable();
                closeUniversalModal();
            }
        });
    }


    function updateCaseStatus($case_id, type) {
        $confirmationMSG = '';
        $SuccessMSG = '';

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
            $confirmationMSG = 'set pending close to this case?';
            $SuccessMSG = 'Case set to pending close';
        } else if (type == 'REVIEWING') {
            $confirmationMSG = 'Send this case for review and close?';
            $SuccessMSG = 'Case set to for review';
        } else if (type == 'REOPEN') {
            $confirmationMSG = 'Reopen this case?';
            $SuccessMSG = 'Case reopen';
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
                            reloadTable();
                            // window.location.href = '/case';
                        } else {
                            Swal.fire('notice!', data.message, 'warning');
                        }

                    }
                });
            }
        })

    }

    function reopenCase($case_id, type) {
        $confirmationMSG = '';
        $SuccessMSG = '';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            text: 'Reopen this case?',
            showCancelButton: true,
            confirmButtonText: `Yes`,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '/reopenCase/' + $case_id,
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                        if (data.status == 1) {

                            Swal.fire('Success!', 'Case reopen', 'success');
                            reloadTable();
                            // window.location.href = '/case';
                        } else {
                            Swal.fire('notice!', data.message, 'warning');
                        }

                    }
                });
            }
        })

    }
</script>