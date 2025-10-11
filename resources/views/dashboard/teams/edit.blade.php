@extends('dashboard.base')

@section('content')


<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">



                <div class="card">
                    <div class="card-header">
                        <h4>Edit Team</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('teams.update', $teams->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-12">

                                    <h4 style="margin-bottom: 50px;">Team Information</h4>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="name" value="{{ $teams->name }}" type="text" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="hf-password">Description</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" id="desc" name="desc" rows="2">{{ $teams->desc }}</textarea>
                                        </div>
                                    </div>





                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                                        <div class="col-md-9">
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-danger" href="{{ route('teams.index') }}">Return</a>
                        </form>
                    </div>
                </div>


                <div id="d-listing" class="card">
                    <div class="card-header">
                        <h4>Team member</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" onclick="memberMode()">
                                    <i class="cil-plus"> </i>Edit member
                                </a>
                            </div>

                        </div>
                        <br>
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <!-- <th>Status</th> -->
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($teamMembers))
                                @foreach($teamMembers as $index => $detail)
                                <tr id="tr_member_{{ $detail->id }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $detail->member_name }}</td>
                                    <td>{{ $detail->menuroles }} </td>
                                    <!-- <td class="text-center">
                                        @if($detail->status == 1)
                                        <span class="badge-pill badge-success">Active</span>
                                        @elseif($detail->status == 0)
                                        <span class="badge-pill badge-warning">Inactive</span>
                                        @endif
                                    </td> -->
                                    <!-- <td class="text-center">
                                        <a href="javascript:void(0)" onclick="removeMember('{{$detail->id}}')" class="btn btn-danger shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Remove"><i class="cil-x-circle"></i></a>
                                    </td> -->
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center" colspan="5">No data</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>

                        <!-- <button class="btn btn-primary float-right" type="button">Save member</button> -->
                    </div>
                </div>


                <div id="d-listing-portfolio" class="card">
                    <div class="card-header">
                        <h4>Portfolio</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" onclick="portfolioMode()">
                                    <i class="cil-plus"> </i>Edit Portfolio
                                </a>
                            </div>

                        </div>
                        <br>
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Name</th>
                                    <!-- <th>Role</th> -->
                                    <!-- <th>Status</th> -->
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($teamPortfolio))
                                @foreach($teamPortfolio as $index => $detail)
                                <tr id="tr_member_{{ $detail->id }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $detail->portfolio_name }}</td>
                                    <!-- <td class="text-center">
                                        @if($detail->status == 1)
                                        <span class="badge-pill badge-success">Active</span>
                                        @elseif($detail->status == 0)
                                        <span class="badge-pill badge-warning">Inactive</span>
                                        @endif
                                    </td> -->
                                    <!-- <td class="text-center">
                                        <a href="javascript:void(0)" onclick="removeMember('{{$detail->id}}')" class="btn btn-danger shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Remove"><i class="cil-x-circle"></i></a>
                                    </td> -->
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center" colspan="5">No data</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>

                        <!-- <button class="btn btn-primary float-right" type="button">Save member</button> -->
                    </div>
                </div>



                <div id="dMembers" class="card d_operation" style="display:none">
                    <div class="card-header">
                        <h4>Bill money entry</h4>
                    </div>
                    <div class="card-body">
                        <form id="form_bill" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <!-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 "> -->
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">
                                    <table class="table table-bordered  datatable">
                                        <tbody>
                                            <tr style="background-color: #d8dbe0;">
                                                <td colspan="2">
                                                    Clerk
                                                </td>
                                            </tr>


                                            @foreach($clerks as $index => $clerk)

                                            <tr>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="member" value="{{ $clerk->id }}" id="chk_{{ $clerk->id }}" @if(in_array($clerk->id , $banksUsersRel)) checked @endif>
                                                        <label for="chk_{{ $clerk->id }}">{{$clerk->name }}</label>
                                                    </div>
                                                    <!-- <input class="" name="assignTo[]" type="checkbox" value="{{$clerk->id }}" @if(in_array($clerk->id , $banksUsersRel)) checked @endif> -->
                                                </td>
                                            </tr>


                                            @endforeach

                                            <tr style="background-color: #d8dbe0;">
                                                <td colspan="2">
                                                    Lawyer
                                                </td>
                                            </tr>
                                            @foreach($lawyers as $index => $lawyer)


                                            <tr>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="member" value="{{ $lawyer->id }}" id="chk_{{ $lawyer->id }}" @if(in_array($lawyer->id , $banksUsersRel)) checked @endif>
                                                        <label for="chk_{{ $lawyer->id }}">{{$lawyer->name }}</label>
                                                    </div>
                                                    <!-- <input class="" name="assignTo[]" type="checkbox" value="{{$lawyer->id }}" @if(in_array($lawyer->id , $banksUsersRel)) checked @endif> -->
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr style="background-color: #d8dbe0;">
                                                <td colspan="2">
                                                    Account
                                                </td>
                                            </tr>
                                            @foreach($accounts as $index => $account)


                                            <tr>
                                                <!-- <td>{{$account->name }}</td> -->
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="member" value="{{ $account->id }}" id="chk_{{ $account->id }}"  @if(in_array($account->id , $banksUsersRel)) checked @endif>
                                                        <label for="chk_{{ $account->id }}">{{$account->name }}</label>
                                                    </div>
                                                    <!-- <input class="" name="assignTo[]" type="checkbox" value="{{$account->id }}" @if(in_array($account->id , $banksUsersRel)) checked @endif> -->
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr style="background-color: #d8dbe0;">
                                                <td colspan="2">
                                                    Sales
                                                </td>
                                            </tr>
                                            @foreach($sales as $index => $sale)


                                            <tr>
                                                <!-- <td>{{$sale->name }}</td> -->
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="member" value="{{ $sale->id }}" id="chk_{{ $sale->id }}" @if(in_array($sale->id , $banksUsersRel)) checked @endif>
                                                        <label for="chk_{{ $sale->id }}">{{$sale->name }}</label>
                                                    </div>
                                                    <!-- <input class="" name="assignTo[]" type="checkbox" value="{{$sale->id }}" @if(in_array($sale->id , $banksUsersRel)) checked @endif> -->
                                                </td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>


                                    <button class="btn btn-success float-right" onclick="saveTeamMember('{{ $teams->id }}')" type="button">
                                        <span id="span_update_bill">Save</span>
                                        <div class="overlay" style="display:none">
                                            <i class="fa fa-refresh fa-spin"></i>
                                        </div>
                                    </button>
                                    <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-primary">Cancel</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>


                <div id="dPortfolio" class="card d_operation" style="display:none">
                    <div class="card-header">
                        <h4>Portfolio</h4>
                    </div>
                    <div class="card-body">
                        <form id="form_bill" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <!-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 "> -->
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">
                                    <table class="table table-bordered  datatable">
                                        <tbody>
                                            <tr style="background-color: #d8dbe0;">
                                                <td colspan="2">
                                                    Portfolio
                                                </td>
                                            </tr>


                                            @foreach($portfolio as $index => $port)

                                            <tr>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="portfolio" value="{{ $port->id }}" id="chk_portfolio_{{ $port->id }}" @if(in_array($port->id , $teamPort)) checked @endif>
                                                        <label for="chk_portfolio_{{ $port->id }}">{{$port->name }}</label>
                                                    </div>
                                                    <!-- <input class="" name="assignTo[]" type="checkbox" value="{{$clerk->id }}" @if(in_array($clerk->id , $banksUsersRel)) checked @endif> -->
                                                </td>
                                            </tr>


                                            @endforeach



                                        </tbody>
                                    </table>


                                    <button class="btn btn-success float-right" onclick="savePortfolio('{{ $teams->id }}')" type="button">
                                        <span id="span_update_bill">Save</span>
                                        <div class="overlay" style="display:none">
                                            <i class="fa fa-refresh fa-spin"></i>
                                        </div>
                                    </button>
                                    <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-primary">Cancel</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('javascript')
<script>
    function memberMode() {
        $("#d-listing").hide();
        $("#d-listing-portfolio").hide();
        $("#dMembers").show();
    }

    function portfolioMode() {
        $("#d-listing").hide();
        $("#d-listing-portfolio").hide();
        $("#dPortfolio").show();
    }

    function viewMode() {
        $("#d-listing").show();
        $("#d-listing-portfolio").show();
        $("#dMembers").hide();
        $("#dPortfolio").hide();
    }

    function removeMember($member_id) {
        $('#tr_member_' + $member_id).remove();
    }

    function saveTeamMember($id) {

        var memberList = [];

        var formData = new FormData();

        $.each($("input[name='member']:checked"), function() {
            itemID = $(this).val();
            memberList.push(itemID);
        });

        if (memberList.length > 0) {

            formData.append('memberList', memberList);

            $.ajax({
                type: 'POST',
                url: '/save_team_member/' + $id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);

                    Swal.fire('Success!', 'member Updated', 'success')
                    location.reload();
                }
            });
        }



    }


    function savePortfolio($id) {
        var portfolioList = [];

        var formData = new FormData();

        $.each($("input[name='portfolio']:checked"), function() {
            itemID = $(this).val();
            portfolioList.push(itemID);
        });

        if (portfolioList.length > 0) {

            formData.append('portfolioList', portfolioList);

            $.ajax({
                type: 'POST',
                url: '/save_team_portfolio/' + $id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);

                    Swal.fire('Success!', 'Portfolio Updated', 'success')
                    location.reload();
                }
            });
        }
    }
</script>

@endsection