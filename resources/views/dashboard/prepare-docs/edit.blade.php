@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">

            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                <div class="card ">

                    <div class="card-header">
                        <h4> Edit Return Call </h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        @if(Session::has('error'))
                        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                        @endif
                        <form id="form_adjudication" method="POST" action="{{ route('prepare-docs.update', $main_obj->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="col-12 " style="margin-bottom:10px;">
                                    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModalInvoice" onclick="editInvoiceModal()" class="btn btn-info  sharp " data-toggle="tooltip" data-placement="top" title="View">Search Case Ref No/Client</a>
                                </div>
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Case Ref No</label>
                                            <input class="form-control" value="@if($main_obj->case_id== 0) {{$main_obj->case_ref}} @else {{$main_obj->case_ref_no}} @endif " type="text" name="case_ref_no" required>
                                            <input class="form-control" type="hidden" value="{{$main_obj->case_id}}" name="case_id">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Client</label>
                                            <input class="form-control" type="text" name="client" value="{{$main_obj->client_name}}" required>
                                            <input class="form-control" type="hidden" name="client_id" value="{{$main_obj->client_id}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Signing Date</label>
                                            <input class="form-control" type="text" name="signing_date" value="{{$main_obj->signing_date}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Branch</label>
                                            <select class="form-control" name="branch">
                                                @foreach($branch as $index => $bran)
                                                <option value="{{$bran->id}}" @if($bran->id == $main_obj->branch) selected @endif >{{$bran->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>docs_prepared </label>
                                            <textarea class="form-control" id="docs_prepared" name="docs_prepared" rows="3">{{$main_obj->docs_prepared}}</textarea>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Remark </label>
                                            <textarea class="form-control" id="remark" name="remark" rows="3">{{$main_obj->remark}}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Return Call</label>
                                            <select class="form-control" name="done">
                                                <option value="0" @if($main_obj->done == 1) selected @endif>No</option>
                                                <option value="1" @if($main_obj->done == 1) selected @endif>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-success float-right" type="submit">Update</button>
                            <a href="{{ route('return-call.index') }}" class="btn btn-danger">{{ __('coreuiforms.return') }}</a>
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
                                        <input type="text" id="search_case" name="search_case" class="form-control search_referral" placeholder="Search Case" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="col">
                                        <label>Search Client</label>
                                        <input type="text" id="search_client" name="search_client" class="form-control search_referral" placeholder="Search Client" autocomplete="off" />
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
                                            @if(count($loan_case))
                                            @foreach($loan_case as $index => $case)
                                            <tr id="case_{{ $case->id }}" style="display:none">
                                                <td>{{ $case->case_ref_no }}</td>
                                                <td>{{ $case->name }}</td>
                                                <td style="display:none">{{ $case->id }}</td>
                                                <td style="display:none">{{ $case->customer_id }}</td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0)" onclick="selectedCase('{{ $case->id }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="case">Select</a>
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
                        <button type="button" id="btnClose" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')

<script>
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