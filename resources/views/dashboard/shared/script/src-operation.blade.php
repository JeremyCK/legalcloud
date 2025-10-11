<script src="{{ asset('js/dropzone.min.js') }}"></script>
<script id="operation"> 
    $record_id = 0;
    Dropzone.autoDiscover = false;
    var drop = document.getElementById('form_operation')

    @if (isset($operation))
        var myDropzone = new Dropzone(drop, {
            url: "/operationUpload",
            addRemoveLinks: true,
            autoProcessQueue: false,
            maxFilesize: 10, // MB
            maxFiles: 5,
            uploadMultiple: true,
            parallelUploads: 10,
            sending: function(file, xhr, formData) {
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("record_id", $record_id);
            },
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    this.removeFile(file);
                    // showAlert("File Limit exceeded!", "error");
                });
            },
            success: function(file, response) {
                console.log(response);
                $.each(myDropzone.files, function(i, file) {
                    file.status = Dropzone.QUEUED
                });

                if (response.status == 1) {
                    $("#div_full_screen_loading").hide();
                    Swal.fire('Success!', response.message, 'success');
                    window.location.href = "/{{ $operation['url'] }}";
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

        function SaveRecord() {
            $("#div_full_screen_loading").show();
            $.ajax({
                type: 'POST',
                url: '/storeRecords',
                data: $('#form_operation').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {

                        if (myDropzone.files.length > 0) {
                            $record_id = data.record_id;
                            myDropzone.processQueue();
                        } else {
                            $("#div_full_screen_loading").hide();
                            Swal.fire('Success!', data.message, 'success');
                            window.location.href = "/{{ $operation['url'] }}";
                        }

                    } else {
                        Swal.fire('notice!', data.message, 'warning');
                    }

                }
            });
        }

        function UpdateRecord($id) {
            $("#div_full_screen_loading").show();
            $.ajax({
                type: 'POST',
                url: '/updateRecords/' + $id,
                data: $('#form_operation').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {

                        if (myDropzone.files.length > 0) {
                            $record_id = $id;
                            myDropzone.processQueue();
                        } else {
                            $("#div_full_screen_loading").hide();
                            Swal.fire('Success!', data.message, 'success');
                            window.location.href = "/{{ $operation['url'] }}";
                        }

                    } else {
                        Swal.fire('notice!', data.message, 'warning');
                    }

                }
            });
        }
    @endif



    function deleteOperatationAttachment($id, $operationCode)
        {

            var form_data = new FormData();

            form_data.append("operation_code", $("#operation_code").val());

            Swal.fire({
                icon: 'warning',
                text: 'Delete this attachment?',
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
                        url: '/deleteOperationAttachment/' + $id,
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            $("#div_full_screen_loading").hide();
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


    function deleteOperationRecord($id, $operationCode) {
       

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form_data = new FormData();

        form_data.append("operation_code", $operationCode);

        Swal.fire({
            icon: 'warning',
            text: 'Delete this record?',
                showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $("#div_full_screen_loading").show();

                $.ajax({
                    type: 'POST',
                    url: '/deleteOperations/' + $id,
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                        $("#div_full_screen_loading").hide();
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
</script>
