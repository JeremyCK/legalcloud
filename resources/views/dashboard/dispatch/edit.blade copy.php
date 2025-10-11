@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">

            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                <div class="card ">

                    <div class="card-header">
                        <h4> Create new Dispatch </h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        @if(Session::has('error'))
                        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                        @endif
                        <form id="form_adjudication" method="POST" action="{{ route('dispatch.update', $dispatch->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">

                            <div class="col-12 " style="margin-bottom:10px;">
                                <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModalInvoice"  onclick="editInvoiceModal()" class="btn btn-info  sharp " data-toggle="tooltip" data-placement="top" title="View">Search Case Ref No/Client</a>
                                </div>
                              
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Case Ref No </label>
                                            <input class="form-control" type="text"  value="@if($dispatch->case_id== 0) {{$dispatch->case_ref}} @else {{$dispatch->case_ref_no}} @endif " name="case_ref_no"  required>
                                            <input class="form-control" type="hidden" name="case_id" value="{{$dispatch->case_id}}"  value="0">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Contact Person</label>
                                            <input class="form-control" type="text" value="{{$dispatch->contact_name}}"  name="contact_name" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Dispatch Name</label>
                                            <select class="form-control" name="courier_id">
                                                <option value="0">--Assign Dispatch--</option>
                                                @foreach($courier as $index => $cour)
                                                <option value="{{$cour->id}}" @if($cour->id ==$dispatch->courier_id) selected @endif>{{$cour->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Received By</label>
                                            <select class="form-control" name="received_by">
                                                <option value="0">-- Receive By --</option>
                                                @foreach($users as $index => $user)
                                                <option value="{{$user->id}}" @if($user->id ==$dispatch->received_by) selected @endif>{{$user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Branch</label>
                                            <select class="form-control" name="branch">
                                                @foreach($branch as $index => $bran)
                                                <option value="{{$bran->id}}" @if($bran->id ==$dispatch->branch) selected @endif>{{$bran->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    

                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Client</label>
                                            <input class="form-control" type="text" name="client"  value="{{$dispatch->client_name}}" required>
                                            <input class="form-control" type="hidden" name="client_id" value="{{$dispatch->client_id}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Contact No</label>
                                            <input class="form-control" type="text" name="contact_no" value="{{$dispatch->contact_no}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Returned To Office</label>
                                            <input class="form-control" type="datetime-local" name="return_to_office_datetime" value="{{ substr(date('c', strtotime($dispatch->return_to_office_datetime)), 0, 16) }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Status</label>
                                            <select class="form-control" name="status">
                                                <option value="0" @if($dispatch->status == 0) selected @endif>Preparing</option>
                                                <option value="1" @if($dispatch->status == 1) selected @endif>Completed</option>
                                                <option value="2" @if($dispatch->status == 2) selected @endif>Dispatch</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>


                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Job Description</label>
                                            <textarea class="form-control" id="job_desc" name="job_desc" rows="3">{{$dispatch->job_desc}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-success float-right" type="submit">{{ __('coreuiforms.save') }}</button>
                            <a href="{{ route('dispatch.index') }}" class="btn btn-danger">{{ __('coreuiforms.return') }}</a>
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