@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage APP</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage APP</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control" id="inputPublisherId" placeholder="Publisher ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_name" value="{{ $publisherName }}" class="form-control" id="inputPublisherName" placeholder="Publisher Name">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $appUuid }}" class="form-control" id="inputAppUuid" placeholder="APP ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_name" value="{{ $appName }}" class="form-control" id="inputAppName" placeholder="APP Name">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="all" >All Status</option>
                                @foreach ($statusMap as $key => $val)
                                <option value="{{ $key }}" @if (is_numeric($status) && ($status == $key)) selected="selected" @endif>{{ $val }}</option>
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
                    </div>
                    <button type="submit" class="btn btn-primary">
                        Search
                    </button>
                    <button type="submit" class="btn btn-success">
                        Export Excel
                    </button>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>APP ID</th>
                            <th>APP Name</th>
                            <th>Publisher <i class="dripicons-warning" style="color: red;vertical-align: middle" data-toggle="tooltip" title="注意：1. 同时登陆多个账号，后登陆的会挤掉先登录的！2. 任何时候请不要删除或修改任何数据，除非是自己新建的数据！"></i></th>
                            <th>System</th>
                            <th>Platform</th>
                            <th>Package Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Create Time</th>
                            <th>Update Time</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($app as $val)
                        <tr>
                            <th scope="row">
                                {{ $val['uuid'] }}<br />
                                <small>{{ $val['id'] }}</small>
                            </th>
                            <td>{{ $val['name'] }}</td>
                            <td>
                                {{ $val['publisher_name'] }}<br />
                                <small>
                                    <a href="{{ URL::to('publisher/login?id=' . $val['publisher_id']) . '&redirect=' . urlencode('/#/app?app_id=' . $val['uuid']) }}"
                                       target="_blank" title="登陆开发者后台并跳转到当前App">
                                        <i class=" mdi mdi-login-variant"></i>
                                        Login
                                    </a>
                                </small>
                            </td>
                            <td>{{ $val['system_name'] }}</td>
                            <td>{{ $val['platform_name'] }}</td>
                            <td>
                                <a href="{{ $val['store_url'] }}" target="_blank">
                                    {{ $val['platform_app_id'] }}
                                </a>
                            </td>
                            <td>
                                {{ $val['category'] }}<br />
                                {{ $val['category_2'] }}
                            </td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['create_time']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['update_time']) }}</td>
                            <td>
                                @if ($val['private_status'] == 1)
                                <a href="#" onclick="confirmUpdatePrivateStatus({{ $val['id'] }}, 3)" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Run</a>
                                @elseif ($val['private_status'] == 3)
                                <a href="#" onclick="confirmUpdatePrivateStatus({{ $val['id'] }}, 1)" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Block</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $app->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{
                            $app->appends(
                            [
                                'publisher_id' => $publisherId,
                                'publisher_name' => $publisherName,
                                'app_id' => $appId,
                                'app_uuid' => $appUuid,
                                'app_name' => $appName,
                                'status' => $status,
                                'system' => $system
                            ])
                            ->links()
                        }}
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <script>
    function updatePrivateStatus(id, privateStatus){
        console.log(JSON.stringify({id: id, data: {private_status: privateStatus}}));
        $.ajax({
            url: "{{ URL::to('app/update') }}", 
            async: true,  
            dataType: 'json',  
            type: 'PUT',  
            data: {id: id, private_status: privateStatus},

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

    function confirmUpdatePrivateStatus(id, privateStatus) {
        //Warning Message
        if(privateStatus == 1){
            swal({
                title: 'Attention',
                text: 'Are you sure you want to block this app?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4fa7f3',
                cancelButtonColor: '#d57171',
                confirmButtonText: 'Yes'
            }).then(function () {
                updatePrivateStatus(id, privateStatus);
            })
        }else if(privateStatus == 3){
            swal({
                title: 'Attention',
                text: 'Are you sure you want to run this app?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4fa7f3',
                cancelButtonColor: '#d57171',
                confirmButtonText: 'Yes'
            }).then(function () {
                updatePrivateStatus(id, privateStatus);
            })
        }
    }
    (function($){
        
    });
    </script>

@endsection
