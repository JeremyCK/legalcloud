@section('css')
    {{-- <style>
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
</style> --}}
@endsection

@extends('dashboard.base')
@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">

                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                    <div class="card ">

                        <div class="card-header">
                            <h4> Create new Case <i class="fa fa-plus-circle"></i></h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            @if (Session::has('error'))
                                <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                            @endif
                            <form method="POST" id="form_case">
                                @csrf
                                <div class="row">

                                    <div class="col-12 ">
                                        <h4 style="margin-bottom: 20px;"><i class="fa fa-users"></i> Team</h4>
                                        <input class="form-control" id="clerk_id" type="hidden" name="clerk_id">
                                        <input class="form-control" id="lawyer_id" type="hidden" name="lawyer_id">
                                        <input class="form-control" id="hidden_remark" type="hidden" name="hidden_remark">
                                        <input class="form-control" id="client_id" type="hidden" name="client_id">
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>

                                                <select id="branch" class="form-control" name="branch" required>
                                                    @foreach ($Branchs as $index => $branch)
                                                        <option data-auto="{{$branch->case_automation }}" @if($branch->id == $current_user->branch_id) selected @endif value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    @if($is_manual == 1)

                                    <div id="div_lawyer" class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Lawyer</label>

                                                <select id="lawyer" class="form-control" name="lawyer">
                                                    <option value="0">-- Auto assign --</option>
                                                    @foreach ($lawyers as $index => $lawyer)
                                                        @php
                                                            $branch_list = '';
                                                            $handle_branch =  $lawyer->handle_branch;
                                                            $handle_branch = explode(',', $handle_branch);

                                                            foreach($handle_branch as $branch)
                                                            {
                                                                $branch_list .= ' branch_'.$branch;
                                                            }
                                                            
                                                        @endphp
                                                        <option chamber="1"  class="team_all branch_{{ $lawyer->branch_id }}  {{$branch_list}}"
                                                            value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="div_clerk" class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Clerk</label>

                                                <select id="clerk" class="form-control" name="clerk">
                                                    <option value="0">-- Auto assign --</option>
                                                    @foreach ($clerks as $index => $clerk)
                                                        @php
                                                        $branch_list = '';
                                                        $handle_branch =  $lawyer->handle_branch;
                                                        $handle_branch = explode(',', $handle_branch);

                                                        foreach($handle_branch as $branch)
                                                        {
                                                            $branch_list .= ' branch_'.$branch;
                                                        }
                                                        
                                                    @endphp
                                                        <option
                                                            class="team_all team_clerk_all branch_{{ $clerk->branch_id }}  {{$branch_list}}"
                                                            value="{{ $clerk->id }}">{{ $clerk->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    @endif
                                </div>

                                <hr />
                                <div class="row">
                                    <div class="col-12 ">
                                        <h4 style="margin-bottom: 20px;"><i class="fa fa-user"></i> Client Information</h4>
                                    </div>
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label><span class="text-danger">* </span>Customer Type</label>
                                                <select id="customer_type" class="form-control" name="customer_type"
                                                    required>
                                                    <option value="1">Personal</option>
                                                    <option value="2">Company</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row dPersonal">
                                            <div class="col">
                                                <label><span class="text-danger">* </span>IC</label>
                                                <input class="form-control" id="client_ic" name="client_ic" type="text"
                                                    required />
                                            </div>
                                        </div>

                                        <div class="form-group row dCompany" style="display:none">
                                            <div class="col">
                                                <label><span class="text-danger">* </span>Company Reg No</label>
                                                <input class="form-control" id="company_reg_no" type="text"
                                                    name="company_reg_no">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Tel No</label>
                                                <input class="form-control" type="text" name="client_phone_no">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Race</label>
                                                <select class="form-control" id="race" name="race">
                                                    <option value="0">-- Please select race -- </option>
                                                    <option value="1">Chinese</option>
                                                    <option value="2">Malay</option>
                                                    <option value="3">Indian</option>
                                                    <option value="4">Others</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div id="div-other-race" class="form-group row hide">
                                            <div class="col">
                                                <label>Others(Race)</label>
                                                <input class="form-control" type="text" name="client_race_others"
                                                    id="client_race_others" value="">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label><span class="text-danger">* </span>Name</label>
                                                <input class="form-control" name="client_name" type="text" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Email</label>
                                                <input class="form-control" type="text" name="client_email">
                                            </div>
                                        </div>

                                        <div class="form-group row hide">
                                            <div class="col">
                                                <label>First House</label>
                                                <select class="form-control" id="first_house" name="first_house">
                                                    <option value="0">No </option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Address</label>
                                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <hr />

                                <div class="row" style="margin-top:40px;">
                                    <div class="col-12 ">
                                        <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral
                                            Information</h4>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <input class="form-control" type="hidden" name="referral_id" id="referral_id">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Referral Name</label>
                                                <!-- <input class="form-control" name="referral_name" type="text" /> -->

                                                <div class="input-group">
                                                    <input class="form-control" id="referral_name" type="text"
                                                        name="referral_name" disabled>
                                                    <div class="input-group-append"><span class="input-group-text"> 
                                                        <a class="" href="javascript:void(0)" data-backdrop="static"
                                                        data-keyboard="false" style="margin:0" data-toggle="modal"
                                                        onclick="loadReferralList()" data-target="#modalReferral"
                                                        class="btn btn-xs btn-primary"><i style="margin-right: 10px;"
                                                            class="fa fa-refresh"></i>Select</a>    
                                                    </span></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Referral email</label>
                                                <input class="form-control" type="text" name="referral_email"
                                                    id="referral_email" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Bank</label>
                                                <input class="form-control" name="referral_bank" id="referral_bank"
                                                    type="text" disabled />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Referral phone no</label>
                                                <input class="form-control" name="referral_phone_no"
                                                    id="referral_phone_no" type="text" disabled />
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>IC No</label>
                                                <input class="form-control" name="referral_ic_no" id="referral_ic_no"
                                                    type="text" disabled />
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Bank Account</label>
                                                <input class="form-control" name="referral_bank_account"
                                                    id="referral_bank_account" type="text" disabled />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <div class="row" style="margin-top:40px;">
                                    <div class="col-12 ">
                                        <h4 style="margin-bottom: 20px;"><i class="fa fa-building"></i> Property
                                            Information</h4>
                                    </div>
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Case Type</label>

                                                <select id="bank" class="form-control" name="bank" required>
                                                    @foreach ($portfolios as $index => $portfolio)
                                                        <option value="{{ $portfolio->id }}">{{ $portfolio->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <div class="col">
                                                <label><span class="text-danger">* </span> Targeted Collection
                                                    Amount</label>
                                                <input type="number" value="0" id="targeted_collect_amount"
                                                    name="targeted_collect_amount" class="form-control" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Property address</label>
                                                <textarea class="form-control" id="property_address" name="property_address" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <!-- <div class="form-group row">
                                            <div class="col">
                                                <label>Remarks</label>
                                                <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
                                            </div>
                                        </div> -->
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Purchase Price</label>
                                                <input type="number" value="0" id="purchase_price"
                                                    name="purchase_price" class="form-control" />
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Loan Sum</label>
                                                <input type="number" value="0" id="loan_sum" name="loan_sum"
                                                    class="form-control" />
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <div class="col">
                                                <label><span class="text-danger">* </span> Agreed fee</label>
                                                <input type="number" value="0" id="agreed_fee" name="agreed_fee"
                                                    class="form-control" />
                                            </div>
                                        </div>




                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Remarks</label>
                                                <!-- <textarea class="form-control" id="desc" name="desc" rows="3"></textarea> -->
                                                <textarea class="form-control" id="desc" name="desc"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <button id="btnSubmit" class="btn btn-success float-right" type="button" onclick="createCase()">Create case</button> -->
                                <button id="btnSubmit" class="btn btn-success float-right" type="button"
                                    onclick="clientProfileCheck()">Create case</button>
                                <button id="btnClientProfileModal" class="btn btn-success float-right" type="button"
                                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                    data-target="#modalClientProfile" style="display:none">Create case</button>
                                <a href="{{ route('case.index') }}"
                                    class="btn btn-danger">{{ __('coreuiforms.return') }}</a>
                            </form>


                            @include('dashboard.case.d-referral-list')
                            @include('dashboard.case.d-referral-create')
                            @include('dashboard.case.d-client-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modalClientProfile" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="form_add">
                        <input type="hidden" value="0" id="txtId" name="txtId" />

                        <div class="form-group row ">
                            <div class="col">
                                <h4>Client profile detected</h4>
                                <label>System detected client profile and cases exist, select same team to handle this
                                    case?</label>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr class="text-center">
                                    <th>Ref No</th>
                                    <th>Lawyer</th>
                                    <th>Clerk</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbl-client-case">
                            </tbody>
                        </table>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success float-right"
                        onclick="createCaseWithSameTeam(0,0,0)">Create with new team
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                </div>
            </div>

        </div>
    </div>

    
    @include('dashboard.case.modal.modal-referral', ['newcase'=>1])
    @include('dashboard.case.modal.modal-create-new-referral')
    {{-- <div id="div_full_screen_loading" class="loading" style="display:none;">
    <div class='uil-ring-css' style='transform:scale(0.79);'>
        <div></div>
    </div>
</div> --}}
@endsection

@section('javascript')
{{-- <script src="//cdn.ckeditor.com/4.23.0-lts/standard/ckeditor.js"></script> --}}

<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
{{-- <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script> --}}
    <script>
        document.getElementById("race").onchange = function() {
            if ($("#race").val() == "4") {
                $("#div-other-race").show();
            } else {
                $("#div-other-race").hide();
            }
        }

        document.getElementById("branch").onchange = function() {
            $(".team_all").hide();
            $(".branch_" + $("#branch").val()).show();

            caseAutomationController();

            $("#lawyer").val(0);
            $("#clerk").val(0);
        }

        function caseAutomationController()
        {
            if($('#branch').find(':selected').attr('data-auto') ==1)
            {
                $("#div_lawyer").hide();
                $("#div_clerk").hide();
            }
            else if($('#branch').find(':selected').attr('data-auto') == 2)
            {
                @if($current_user->manual_create_case == 1)
                    $("#div_lawyer").show();
                    $("#div_clerk").show();
                @else
                    $("#div_lawyer").hide();
                    $("#div_clerk").hide();
                @endif

            }
            else{
                $("#div_lawyer").show();
                $("#div_clerk").show();
            }
        }

        document.getElementById("lawyer").onchange = function() {
            // $(".team_clerk_all ").hide();
            // $(".team_clerk_" + $(this).children(":selected").attr("teams")).show();
            // $("#clerk").val(0);
        }

        $(".team_all ").hide();

        @if($current_user->branch_id == 3)
        $(".branch_" + 3).show();
        @else
        $(".branch_" + {{$current_user->branch_id}}).show();
        @endif

        caseAutomationController();
        
        CKEDITOR.replace('desc');
        CKEDITOR.config.height = 300;
        CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
        CKEDITOR.config.removeButtons = 'Image';

        $('#search_referral').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_referral");
            filter = input.value.toUpperCase();



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

        // $("#referral_name").click(function() {
        //     referralMode();
        // });

        function referralMode() {
            $(".d_operation").hide();
            $("#form_case").hide();
            $("#div-referral").show();
        }

        function clientMode() {
            $(".d_operation").hide();
            $("#form_case").hide();
            $("#div-referral").show();
        }


        function CreateMode() {
            $(".d_operation").hide();
            $("#div-referral-create").show();
        }

        function listMode() {
            $(".d_operation").hide();
            $("#form_case").show();
            $("#div-referral").hide();
        }

        document.getElementById("customer_type").onchange = function() {

            $('#client_ic').attr('required', false);
            $('#company_reg_no').attr('required', false);

            if ($("#customer_type").val() == "1") {
                $(".dPersonal").show();
                $(".dCompany").hide();

                $('#client_ic').attr('required', true);
            } else {
                $(".dPersonal").hide();
                $(".dCompany").show();

                $('#company_reg_no').attr('required', true);
            }
        }

        function selectedReferral(id) {
            $("#tbl-referral tr#referral_row_" + id).each(function() {
                var self = $(this);
                var txtName = self.find("td:eq(1)").text().trim();
                var txtEmail = self.find("td:eq(2)").text().trim();
                var txtPhoneNo = self.find("td:eq(3)").text().trim();
                var txtCompany = self.find("td:eq(4)").text().trim();
                var ic_no = self.find("td:eq(5)").text().trim();
                var bank_id = self.find("td:eq(8)").text().trim();
                var bank_account = self.find("td:eq(7)").text().trim();
                var txtId = self.find("td:eq(9)").text().trim();

                if (txtCompany != "" && txtCompany != null) {
                    txtCompany = " (" + txtCompany + ")";
                }

                $("#referral_name").val(txtName + txtCompany);
                $("#referral_email").val(txtEmail);
                $("#referral_phone_no").val(txtPhoneNo);
                $("#referral_id").val(txtId);
                $("#referral_ic_no").val(ic_no);
                $("#referral_bank").val(bank_id);
                $("#referral_bank_account").val(bank_account);

                listMode();

            })
        }

        $(document).on('click', '.editData', function () {

        var self = $(this).closest('tr');;
        var txtId = self.find("td:eq(1)").text().trim();
        var txtName = self.find("td:eq(2)").text().trim();
        var txtEmail = self.find("td:eq(5)").text().trim();
        var txtPhoneNo = self.find("td:eq(6)").text().trim();
        var txtCompany = self.find("td:eq(3)").text().trim();
        var ic_no = self.find("td:eq(4)").text().trim();
        // var bank_id = self.find("td:eq(8)").text().trim();
        // var bank_account = self.find("td:eq(7)").text().trim();
        // var txtId = self.find("td:eq(9)").text().trim();
        
        
        if (txtCompany != "" && txtCompany != null) {
            txtCompany = " (" + txtCompany + ")";
        }

        $("#referral_name").val(txtName + txtCompany);
        $("#referral_email").val(txtEmail);
        $("#referral_phone_no").val(txtPhoneNo);
        $("#referral_id").val(txtId);
        $("#referral_ic_no").val(ic_no);
        // $("#referral_bank").val(bank_id);
        // $("#referral_bank_account").val(bank_account);

        closeUniversalModal();

        });

        // $(document).on('click', '.editData', function () {

            
            

        //     var val = $(this).closest('tr').find('td:eq(1)').text(); 
        //     console.log(val);

        //     var self = $(this);
            
        //     // var txtName = self.find("td:eq(1)").text().trim();
        //     console.log(self);
        // });

        function clientProfileCheck() {

            $blnProcess = 0;

            $('form#form_case').find('input').each(function() {
                if (!$(this).prop('required')) {} else {
                    if ($(this).val() == "") {
                        console.log($(this).attr('name'));
                        $blnProcess += 1;
                    }
                }
            });

            // if ($("#lawyer").children(":selected").attr("chamber") == 0)
            // {
            //     if ($("#clerk").val() == 0)
            //     {
            //         Swal.fire('Warning!', 'Please select the clerk for this team', 'error');
            //         return;
            //     }

            // }

            if ($blnProcess > 0) {
                Swal.fire('Warning!', 'Please make sure all mandatory field provided', 'error');
                return;
            }

            var data = $('#form_case').serialize();

            $("#hidden_remark").val(CKEDITOR.instances['desc'].getData());

            $("#div_full_screen_loading").show();
            $("#btnSubmit").attr("disabled", true);
            $.ajax({
                type: 'POST',
                url: '/clientProfileCheck',
                data: $('#form_case').serialize(),
                success: function(results) {
                    console.log(results);
                    $("#btnSubmit").attr("disabled", false);
                    $("#div_full_screen_loading").hide();

                    if (results.status == 2) {
                        $("#btnClientProfileModal").click();
                        $('#tbl-client-case').html(results.data.view);
                    } else if (results.status == 1) {
                        Swal.fire('Success!', results.message, 'success');
                        window.location.href = '{{ route('cases.list', 'active') }}';
                    } else {
                        Swal.fire('Warning!', results.message, 'error');
                    }

                },
                error: function(xhr, status, error) {
                    $("#btnSubmit").attr("disabled", false);
                    $("#div_full_screen_loading").hide();
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

                        $("#referral_name").val($("#referral_name_new").val() + txtCompany);
                        $("#referral_email").val($("#referral_email_new").val());
                        $("#referral_phone_no").val($("#referral_phone_no_new").val());
                        $("#referral_ic_no").val($("#referral_ic_no_new").val());
                        $("#referral_bank").val($("#referral_bank_new").val());
                        $("#referral_bank_account").val($("#referral_bank_account_new").val());
                        $("#referral_id").val(results.message);

                        closeUniversalModal();
                        // listMode();
                    } else {

                    }

                },
                error: function(xhr, status, error) {
                    $("#btnSubmit").attr("disabled", false);
                }
            });
        }

        function createCase() {

            // if ($("#customer_type").val() == "1") {

            //     if ($("#client_ic").val() == "") {
            //         Swal.fire('Notice!', 'Please make sure all required field are fill', 'warning');
            //         return;
            //     }
            // } else {
            //     if ($("#company_reg_no").val() == "") {
            //         Swal.fire('Notice!', 'Please make sure all required field are fill', 'warning');
            //         return;
            //     }
            // }

            // if ($("#race").val() == "4") {
            //     if ($("#client_race_others").val() == "") {
            //         Swal.fire('Notice!', 'Please define the race if other is selected', 'warning');
            //         return;
            //     }
            // }


            $("#btnSubmit").attr("disabled", true);
            $.ajax({
                type: 'POST',
                url: '/create_case',
                data: $('#form_case').serialize(),
                success: function(results) {
                    console.log(results);
                    $("#btnSubmit").attr("disabled", false);
                    if (results.status == 1) {
                        Swal.fire(
                            'Success!',
                            results.data,
                            'success'
                        )

                        // window.location.href = '/case';
                        window.location.href = '{{ route('cases.list', 'active') }}';
                    } else {
                        Swal.fire(
                            'Warning!',
                            results.message,
                            'warning'
                        )
                    }

                },
                error: function(xhr, status, error) {
                    $("#btnSubmit").attr("disabled", false);
                }
            });

            return;



            if ($("#form_case")[0].checkValidity() == true) {

                $("#btnSubmit").attr("disabled", true);
                $.ajax({
                    type: 'POST',
                    url: '/create_case',
                    data: $('#form_case').serialize(),
                    success: function(results) {
                        console.log(results);
                        $("#btnSubmit").attr("disabled", false);
                        if (results.status == 1) {
                            Swal.fire(
                                'Success!',
                                results.data,
                                'success'
                            )

                            // window.location.href = '/case';
                            window.location.href = '{{ route('cases.list', 'active') }}';
                        } else {
                            Swal.fire(
                                'Warning!',
                                results.message,
                                'warning'
                            )
                        }

                    },
                    error: function(xhr, status, error) {
                        $("#btnSubmit").attr("disabled", false);
                    }
                });
            } else {
                Swal.fire('Notice!', 'Please make sure all required field are fill', 'warning');
            }

        }

        function createCaseWithSameTeam($clerk_id, $lawyer_id, $client_id) {


            $("#btnSubmit").attr("disabled", true);

            $("#clerk_id").val($clerk_id);
            $("#lawyer_id").val($lawyer_id);
            $("#client_id").val($client_id);
            $.ajax({
                type: 'POST',
                url: '/createCaseWithSameTeam',
                data: $('#form_case').serialize(),
                success: function(results) {
                    console.log(results);
                    $("#btnSubmit").attr("disabled", false);

                    if (results.status == 2) {
                        $("#btnClientProfileModal").click();
                        $('#tbl-client-case').html(results.data.view);
                    } else if (results.status == 1) {
                        Swal.fire('Success!', results.message, 'success');
                        // window.location.href = '/case';
                        window.location.href = '{{ route('cases.list', 'active') }}';
                    } else {
                        Swal.fire('Warning!', results.message, 'warning');
                    }


                },
                error: function(xhr, status, error) {
                    $("#btnSubmit").attr("disabled", false);
                }
            });

            return;



            if ($("#form_case")[0].checkValidity() == true) {

                $("#btnSubmit").attr("disabled", true);
                $.ajax({
                    type: 'POST',
                    url: '/create_case',
                    data: $('#form_case').serialize(),
                    success: function(results) {
                        console.log(results);
                        $("#btnSubmit").attr("disabled", false);
                        if (results.status == 1) {
                            Swal.fire(
                                'Success!',
                                results.data,
                                'success'
                            )

                            // window.location.href = '/case';
                            window.location.href = '{{ route('cases.list', 'active') }}';
                        } else {
                            Swal.fire(
                                'Warning!',
                                results.message,
                                'error'
                            )
                        }

                    },
                    error: function(xhr, status, error) {
                        $("#btnSubmit").attr("disabled", false);
                    }
                });
            } else {
                Swal.fire('Notice!', 'Please make sure all required field are fill', 'warning');
            }

        }
        
    </script>
@endsection
