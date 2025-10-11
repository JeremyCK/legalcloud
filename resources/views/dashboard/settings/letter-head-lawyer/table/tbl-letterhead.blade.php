<thead>
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>IC</th>
        <th>Action</th>
    </tr>
</thead>
<tbody>
    @foreach ($letterHeadLawyer as $index => $obj)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $obj->parameter_value_1 }}</td>
            <td>{{ $obj->parameter_value_2 }}</td>
            <td>
                <div class="btn-group  normal-edit-mode">
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                        data-toggle="dropdown">
                        <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                        <a class="dropdown-item btn-info" href="javascript:void(0)"
                            onclick="loadEditData({{ $obj->id }},'{{ $obj->parameter_value_1 }}','{{ $obj->parameter_value_2 }}')"
                            data-backdrop="static" data-keyboard="false"
                            data-target="#modalEdit" data-toggle="modal"
                            style="color:white;margin:0"><i style="margin-right: 10px;"
                                class="cil-pencil"></i>Edit</a>
                        <div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)"
                            onclick="deleteVoucher(49861)"
                            style="color:white;margin:0"><i style="margin-right: 10px;"
                                class="cil-x"></i>Delete</a>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>