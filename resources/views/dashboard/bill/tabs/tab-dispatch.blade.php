
          <div class="row">
            <div class="col-12">
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Dispatch</h3>

                  <div class="box-tools">
                    <button class="btn btn-primary" type="button" onclick="dispatchMode('0', '{{ $cases->id }}');">
                      <i class="cil-plus"></i> Create new dispatch
                    </button>
                  </div>

                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding" style="width:100%;overflow-x:auto">
                  <table class="table table-striped table-bordered datatable">
                    <tbody>
                      <tr class="text-center">
                        <th>Package</th>
                        <th>Courier</th>
                        <th>Departure</th>
                        <th>Destination</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                      @if(count($loan_dispatch))

                      @foreach($loan_dispatch as $index => $dispatch)
                      <tr>
                        <td class="text-center">{{ $dispatch->package_name }}</td>
                        <td class="text-center">{{ $dispatch->courier_name }}</td>
                        <td>
                          <i class="fa fa-clock-o"></i><span id="depart_time_{{ $dispatch->id }}"> {{ $dispatch->departure_time }}</span> <br />
                          <i class="fa fa-map-marker"></i><span id="depart_address_{{ $dispatch->id }}"> {{ $dispatch->departure_address }}</span>
                        </td>
                        <td>
                          <i class="fa fa-clock-o"></i><span id="depart_time_{{ $dispatch->id }}"> {{ $dispatch->delivered_time }}</span> <br />
                          <i class="fa fa-map-marker"></i><span id="depart_address_{{ $dispatch->id }}"> {{ $dispatch->destination_address }}</span>
                        </td>
                        <td class="text-center">
                          @if($dispatch->status == 1)
                          <span class="label label-success">Delivered</span>
                          @elseif($dispatch->status == 0)
                          <span class="label label-warning">Preparing</span>
                          @elseif($dispatch->status == 2)
                          <span class="label label-info">Sending</span>
                          @endif
                        </td>
                        <td class="text-center">
                          <a href="javascript:void(0)" onclick="dispatchMode('{{ $dispatch->id }}', '{{ $case->id }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Edit</a>
                          <!-- <a href="javascript:void(0)" onclick="voucherMode('{{ $dispatch->id }}', '{{ $case->id }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Time</a> -->

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
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
            </div>
          </div>