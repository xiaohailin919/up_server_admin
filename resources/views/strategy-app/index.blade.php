@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage APP Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage APP Strategy</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $appUuid }}" class="form-control" placeholder="APP ID">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="all" >All Status</option>
                                @foreach ($statusMap as $key => $val)
                                <option value="{{ $key }}" @if ($status == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="platform" class="form-control">
                                <option value="all" >All Platform</option>
                                @foreach ($platformMap as $key => $val)
                                    <option value="{{ $key }}" @if ($platform == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="system" class="form-control">
                                <option value="all" >All System</option>
                                @foreach ($systemMap as $key => $val)
                                    <option value="{{ $key }}" @if ($system == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
{{--                            <button type="submit" class="btn btn-success">--}}
{{--                                Export Excel--}}
{{--                            </button>--}}
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>APP ID</th>
                            <th>APP Name</th>
                            <th>Platform</th>
                            <th>System</th>
                            <th>Status</th>
                            <th>Create Time</th>
                            <th>Update Time</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $val)
                        <tr>
                            <th scope="row">{{ $val['id'] }}</th>
                            <td>
                                {{ $val['app_uuid'] }}<br />
                                <small>{{ $val['app_id'] }}</small>
                            </td>
                            <td>{{ $val['app_name'] }}</td>
                            <td>{{ $val['platform_name'] }}</td>
                            <td>{{ $val['system_name'] }}</td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['create_time']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['update_time']) }}</td>
                            <td>
                                @if ($val['status'] == 1)
                                <a href="#" onclick="confirmUpdateStatus({{ $val['id'] }}, 2)" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Running</a>
                                @else
                                <a href="#" onclick="confirmUpdateStatus({{ $val['id'] }}, 1)" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Block</a>
                                @endif
                                <a href="{{ URL::to('strategy-app/' . $val['id'] . '/edit') }}" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a>
                                <a href="{{ URL::to('strategy-app-firm-switch?app_id=' . $val['app_id']) }}" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Firm Strategy</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $data->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $data->appends([
                            'status' => $status,
                            'system' => $system,
                            'platform' => $platform
                        ])->links() }}
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <script>
    function updateStatus(id, status){
        $.ajax({
            url: "{{ URL::to('strategy-app') }}/" + id,
            async: true,  
            dataType: 'json',  
            type: 'PUT',  
            data: {id: id, status: status},

            success: function(data, status){  
                if(data.status == 1){
                    location.reload();
                }
            },  

            error: function(jqXHR , status , errorThrown){  
                alert("Please try again");
            }
        });  
    }

    function confirmUpdateStatus(id, status) {
        //Warning Message
        if(status == 1){
            swal({
                title: 'Attention',
                text: 'Are you sure you want to block this app?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4fa7f3',
                cancelButtonColor: '#d57171',
                confirmButtonText: 'Yes'
            }).then(function () {
                updateStatus(id, status);
            })
        }else if(status == 2){
            swal({
                title: 'Attention',
                text: 'Are you sure you want to run this app?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4fa7f3',
                cancelButtonColor: '#d57171',
                confirmButtonText: 'Yes'
            }).then(function () {
                updateStatus(id, status);
            })
        }
    }
    (function($){
        
    });
    </script>

@endsection
