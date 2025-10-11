
          <div class="row">
            <div class="col-12">
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Activity Log</h3>


                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding" style="width:100%;overflow-x:auto">
                  <table class="table table-striped table-bordered datatable">
                    <tbody>
                      <tr class="text-center">
                        <th>No</th>
                        <th>Name</th>
                        <th>Desc</th>
                        <th>Action</th>
                        <th>Date</th>
                      </tr>
                      @if(count($activityLog))

                      @foreach($activityLog as $index => $activity)
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><a href="/users/{{ $activity->user_id }}">{{ $activity->user_name }}</a></td>
                    <td>{{ $activity->desc }} </td>
                    <td>{{ $activity->action }} </td>
                    <td class="text-center">{{ $activity->created_at }}
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