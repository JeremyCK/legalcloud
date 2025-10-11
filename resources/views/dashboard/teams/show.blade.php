@extends('dashboard.base')

@section('content')


<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header"><h4>{{ $templates_main[0]->display_name }}</h4></div>
            <div class="card-body">
                @if(Session::has('message'))
                    <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                @endif
                    <!-- <table class="table table-bordered datatable">
                        <tbody>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <td>
                                    {{ $lang->name }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Short name
                                </th>
                                <td>
                                    {{ $lang->short_name }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Is default
                                </th>
                                <td>
                                    <?php 
                                        if($lang->is_default == true){
                                            echo 'YES';
                                        }else{
                                            echo 'NO';
                                        }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <a class="btn btn-primary" href="{{ route('languages.index') }}">Return</a> -->

                    <table class="table table-striped table-bordered datatable text-center">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Process</th>
                            <th>KPI</th>
                            <th>PIC</th>
                            <th>Duration</th>
                            <th>Checkpoint</th>
                            <!-- <th>Remarks</th> -->
                            <th>System Code</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $index => $template)
                            <tr>
                                <td>{{ $template->process_number }} </td>
                                <td class="text-left">{{ $template->checklist_name }}</td>
                                <td>{{ intval($template->kpi) }} </td>
                                <td>

                                @if($template->role_id == 1)  
                                    System
                                @elseif($template->role_id == 6)  
                                    Sales
                                @elseif($template->role_id == 7)  
                                    Lawyer
                                @elseif($template->role_id == 8)  
                                    Clerk
                                @endif
                                </td>
                                <td>{{ intval($template->duration) }} </td>
                                <td>
                                    @if($template->check_point > 0)
                                    {{ $template->check_point }} 
                                    @else
                                    -
                                    @endif
                                    
                                </td>
                                <!-- <td>{{ $template->remark }} </td> -->
                                <td>{{ $template->system_code }} </td>
                                <td>
                                @if($template->need_attachment == 1)  
                                     <label class="c-switch c-switch-label c-switch-pill c-switch-success">
                                        <input class="c-switch-input" type="checkbox" checked=""><span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                    </label>

                                @else
                                <label class="c-switch c-switch-label c-switch-pill c-switch-success">
                                        <input class="c-switch-input" type="checkbox" unchecked=""><span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                    </label>     
                                @endif
                               </td>  
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')


@endsection