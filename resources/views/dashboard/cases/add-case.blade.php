@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">

                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                    <div class="card ">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Add New Case </h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('perfectionCase') }}">
                                        <i class="cil-arrow-left"> </i>Back to list </a>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            @if (Session::has('error'))
                                <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                            @endif
                            <form id="form_case" 
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Ref No </label>
                                                <input class="form-control" type="text" name="ref_no" required>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client Name (Purchaser) </label>
                                                <input class="form-control" type="text" name="client_name_p" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client Name (Vendor) </label>
                                                <input class="form-control" type="text" name="client_name_v" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Case Date</label>
                                                <input class="form-control"  type="date" name="case_date" >
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Completion Date</label>
                                                <input class="form-control"  type="date" name="completion_date" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Sales</label>
                
                                                <select class="form-control" id="sales_id" name="sales_id" >
                                                    <option value="0"> -- Select Sales -- </option>
                                                       
                                                    @if (count($sales))
                                                        @foreach ($sales as $index => $sale)
                                                            <option class="" value="{{ $sale->id }}"> {{ $sale->name }} </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>
                
                                                <select class="form-control" id="branch_id" name="branch_id" >
                                                    <option value="0"> -- Select Sales -- </option>
                                                       
                                                    @if (count($branchs))
                                                        @foreach ($branchs as $index => $branch)
                                                            <option class="" value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Case Type</label>
                                                <select class="form-control" name="case_type">
                                                    <option value="1">Perfection</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>


                                <a href="javascript:void(0);" class="btn btn-success float-right" onclick="CreateCase();" >{{ __('coreuiforms.save') }}</a>
                            </form>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>
@endsection

@section('javascript')
    <script>
        function CreateCase() {
            var formData = new FormData();

            formData.append('pic_id', $("#ddlPIC").val());

            $.ajax({
                type: 'POST',
                url: '/createCaseOther',
                data: $('#form_case').serialize(),
                success: function(data) {
                    if (data.status == 1) {
                        Swal.fire('Success!', 'Case created', 'success');
                        window.location.href = 'case-details/' + data.return_id;
                    } else {
                        Swal.fire('Notice!', data.message, 'warning');
                    }

                }
            });
        }

    </script>
@endsection
