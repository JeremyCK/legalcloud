@extends('dashboard.base')

@section('content')


    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">

                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                    <div class="card ">

                        <div class="card-header">
                            <h4> Create new Safe Keeping </h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            @if (Session::has('error'))
                                <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                            @endif
                            <form id="form_adjudication" method="POST" action="{{ route('safe-keeping.store') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                    <div class="col-12 " style="margin-bottom:10px;">
                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                            data-toggle="modal" data-target="#myModalInvoice" onclick="editInvoiceModal()"
                                            class="btn btn-info  sharp " data-toggle="tooltip" data-placement="top"
                                            title="View">Search Case Ref No/Client</a>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Case Ref No </label>
                                                <input class="form-control" type="text" name="case_ref_no" required>
                                                <input class="form-control" type="hidden" name="case_id" value="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client</label>
                                                <input class="form-control" type="text" name="client" required>
                                                <input class="form-control" type="hidden" name="client_id" value="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Document Sent</label>
                                                <input class="form-control" type="text" name="document_sent">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Attention To</label>
                                                <input class="form-control" type="text" name="attention_to">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Received</label>
                                                <select class="form-control" name="received">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>
                                                <select class="form-control" name="branch">
                                                    @foreach ($branch as $index => $bran)
                                                        <option value="{{ $bran->id }}">{{ $bran->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Attachment</label>
                                                <input class="form-control" id="attachment_file" name="attachment_file"
                                                    type="file">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Remark</label>
                                                <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Attachment</label><br />

                                                <label for="file" s  style="width: 100%">
                                                    <div id="dropzone">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <span class="text-center"> Click to upload.<br></span>
                                                            </div>
                                                        </div>
                                                       
                                                        <ul  id="fileList2" class="mailbox-attachments clearfix">
                                                           
                                                        </ul>
                                                    </div>

                                                </label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="file" name="attachment[]"
                                                        multiple onchange="javascript:updateList()">
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file2" multiple onchange="javascript:updateList()">
                                        <label class="custom-file-label" for="file">
                                          <img width="30" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAQlBMVEX///8AAABhYWFlZWWSkpL19fW9vb01NTXf398kJCTw8PBRUVGdnZ1dXV3m5uZ0dHR8fHzExMSMjIzU1NSxsbEhISGIc9b1AAADv0lEQVR4nO2d607jMBhEa1pa6AVaLu//qgixq2+XxmlSe+IZa85vazQjhZMCarJaGWOMMcYYc8WxdQE0m7RpXQHLNqW0bV0CyVP65ql1DRyP6YfH1kVg7P4s3LUugmKd/rJuXQXDJgVdCnWb/qVDoT6l/+lOqI/pN70JdXe1sDOhrq8GdibUzcDAroS6HRzYkVB/a7Q7oV5rtDehXms06EKoQxoNOhDqsEYDeaHmNBqICzWv0UBaqGMaDZSFOqbRQFio4xoNZIV6S6OBqFBvazSQFOoUjQaCQp2m0UBPqNM0GsgJdapGAzGhTtdoICXUORoNhIQ6T6OBjFDnajRQEepcjQYiQp2v0UBCqPdoNBAQ6n0aDeiFeq9GA3Kh3q/RgFuo92s0oBZqiUYDYqGWaTSgFWqpRgNSoZZrNKAUag2NBoxCraHRgFCodTQa0Am1lkYDMqHW02hAJdSaGg2IhFpXowGPUOtqNKARam2NBiRCra/RgEKoCI0GBELFaDRoLlSURoPWQkVpNGgsVJxGg6ZCRWo0aChUrEaDZkJFazRoJFS8RoM2QsVrNGgi1NOCA1M6LT9we3iYzPp5sPXzenrEgeDj2xjDt02S3xyq8DC48KF1rYp4oT5eqI8X6uOF+nihPl6ojxfq44X6eKE+XqiPF+rjhfp4oT5eqI8X6uOF+nihPl6ojxfq44X6eKE+XqiPF+rjhfp4oT5eqI8XLsVhsMehQjJu4bzOuB4sySw9cMksPXDJLD1wySw9cMksPXDJLD1wySw9cMksPXDJLD1wySw9cMksPXDJLD1wySw9cMksPXDJLD1wySw9cMksPXDJLD1wySw9cMksPXDJLD1wySw9cMksPXDJLD1wySw9cMksPXDJLD1wySw9cMksPXDJLD1wybgew8/nq/EcPZaFb6eBx+id3ioksyzE4YUlpznwwpLTHHhhyWkOvLDkNAdeWHKaAy8sOc2BF5ac5sALS05z4IUlpznwwpLTHNRYyP2OhuH3SsxbOOc9G4uTeTdIbuESr6dahtx1d25drBrnzMJj62LVOGYWXloXq8Yls3Dfulg19pmFq8/WzSrxnBu40Kvw8ORftvfSulolXrILM99XVGPsO6HvrctV4X1k4cKvi8Mw/s/zHm4Y2VvFDx+t+xXzMT5wtXpt3bCQ11sD1X066bv1yhMnPjxA90KdcIn+oKqbm5IJ9or3xdON28Qv3tV+Gg+jn2QGedkM/5WHkc/NyIftMfaX45n5L23frM/Hy7zL0xhjjDHGEPIFcc477O4fZUsAAAAASUVORK5CYII=" /> Upload Files</label>
                                    </div>
                                    <ul id="fileList" class="file-list"></ul>


                                </div>


                                <button class="btn btn-success float-right"
                                    type="submit">{{ __('coreuiforms.save') }}</button>
                                <a href="{{ route('adjudication.index') }}"
                                    class="btn btn-danger">{{ __('coreuiforms.return') }}</a>
                            </form>
                        </div>
                    </div>
                </div>


            </div>

            <div id="myModalInvoice" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="form_edit_quotation">
                                <div class="form-group row ">

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="col">
                                            <label>Search Case</label>
                                            <input type="text" id="search_case" name="search_case"
                                                class="form-control search_referral" placeholder="Search Case"
                                                autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="col">
                                            <label>Search Client</label>
                                            <input type="text" id="search_client" name="search_client"
                                                class="form-control search_referral" placeholder="Search Client"
                                                autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-12" style="margin-top:10px">
                                        <table class="table  table-bordered datatable">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Case Ref No</th>
                                                    <th>Client</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl-case">
                                                @if (count($loan_case))
                                                    @foreach ($loan_case as $index => $case)
                                                        <tr id="case_{{ $case->id }}" style="display:none">
                                                            <td>{{ $case->case_ref_no }}</td>
                                                            <td>{{ $case->name }}</td>
                                                            <td style="display:none">{{ $case->id }}</td>
                                                            <td style="display:none">{{ $case->customer_id }}</td>
                                                            <td class="text-center">
                                                                <a href="javascript:void(0)"
                                                                    onclick="selectedCase('{{ $case->id }}');"
                                                                    class="btn btn-primary shadow btn-xs sharp mr-1"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="case">Select</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center" colspan="3">No data</td>
                                                    </tr>
                                                @endif

                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnClose" class="btn btn-default"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script>
        updateList = function() {
            var input = document.getElementById('file2');
            var output = document.getElementById('fileList');
            var children = "";
            for (var i = 0; i < input.files.length; ++i) {
                children += 
                    // '<span class="remove-list" onclick="return this.parentNode.remove()">X</span>' + '</li>'
                    `
                    <li id="file` + i + `">
                        <span  class="mailbox-attachment-icon"><i
                                class="fa fa-file-pdf-o"></i></span>
                        <div class="mailbox-attachment-info">
                            ` + input.files.item(i).name + `
                            <span class="mailbox-attachment-size text-center">
                                <a href="javascript:void(0)" onclick="$('#file` + i + `').remove();">Remove</a>
                            </span>
                        </div>
                    </li>
                    `
            }
            output.innerHTML = children;
        }

        $('#search_case').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_case");
            filter = input.value.toUpperCase();
            $("#search_client").val();

            $("#tbl-case tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(0)").text().trim();

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })

            if (filter == "") {
                $("#tbl-case tr").each(function() {
                    var self = $(this);
                    $(this).hide();
                })
            }
        });

        $('#search_client').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_client");
            filter = input.value.toUpperCase();
            $("#search_case").val();

            $("#tbl-case tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(1)").text().trim();

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })

            if (filter == "") {
                $("#tbl-case tr").each(function() {
                    var self = $(this);
                    $(this).hide();
                })
            }
        });

        function selectedCase(id) {
            $("#tbl-case tr#case_" + id).each(function() {
                var self = $(this);
                var case_ref_no = self.find("td:eq(0)").text().trim();
                var client = self.find("td:eq(1)").text().trim();
                var case_id = self.find("td:eq(2)").text().trim();
                var client_id = self.find("td:eq(3)").text().trim();

                var form = $("#form_adjudication");

                form.find('[name=case_ref_no]').val(case_ref_no);
                form.find('[name=case_id]').val(case_id);
                form.find('[name=client]').val(client);
                form.find('[name=client_id]').val(client_id);

                $('#btnClose').click();
                $(".modal-backdrop").remove();

            })
        }
    </script>
@endsection
