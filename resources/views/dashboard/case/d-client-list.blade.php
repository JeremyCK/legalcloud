<div id="div-referral" class="card d_operation" style="display:none">
  <div class="card-header">
    <h4> Referral</h4>
  </div>
  <div class="card-body">

    <div class="form-group row ">
      <div class="col-12">
        <a href="javascript:void(0);" onclick="listMode()" class="btn btn-danger">Cancel</a>
      </div>
    </div>


    <div class="form-group row ">


      <div class="col-6">
        <!-- <label>Referral Name</label> -->
        <input type="text" id="search_referral" name="search_referral" placeholder="Search referral name" class="form-control" />
      </div>
      <div class="col-6">
        <div class="form-group float-right ">
          <a class="btn btn-lg btn-primary" href="javascript:void(0)" onclick="CreateMode()">Create new referral</a>
        </div>
      </div>

    </div>


    <table class="table table-striped table-bordered datatable">
      <thead>
        <tr class="text-center">
          <th>No</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone No</th>
          <th>Company</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="tbl-referral">
        @if(count($referrals))

        @foreach($referrals as $index => $referral)
        <tr id="referral_row_{{ $referral->id }}" style="display:none">
          <td>{{ $index+1 }}</td>
          <td>{{ $referral->name }}</td>
          <td>{{ $referral->email }}</td>
          <td>{{ $referral->phone_no }}</td>
          <td>{{ $referral->company }}</td>
          <td style="display:none">{{ $referral->ic_no }}</td>
          <td style="display:none">{{ $referral->bank_id }}</td>
          <td style="display:none">{{ $referral->bank_account }}</td>
          <td style="display:none">{{ $referral->bank_name }}</td>
          <td class="hide">{{ $referral->id }}</td>
          <td class="text-center">
            <a href="javascript:void(0)" onclick="selectedReferral('{{ $referral->id }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Select</a>

          </td>
        </tr>

        @endforeach
        @else
        <tr>
          <td class="text-center" colspan="5">No data</td>
        </tr>
        @endif

      </tbody>
    </table>

    <!-- <button class="btn btn-primary  float-right" type="button" onclick="">
      <i class="cil-plus"></i> Group voucher
      <div class="overlay" style="display:none">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </button> -->


  </div>
</div>