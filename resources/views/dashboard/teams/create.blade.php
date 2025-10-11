@extends('dashboard.base')

@section('content')


<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">



                <div class="card">
                    <div class="card-header">
                        <h4>Create Team</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('teams.store') }}" >
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">

                                    <h4 style="margin-bottom: 50px;">Team Information</h4>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="name"  type="text" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="hf-password">Description</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
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