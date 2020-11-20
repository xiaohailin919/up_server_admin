@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage Placement</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage Placement</h4>
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
                            <input type="text" name="placement_uuid" value="{{ $placementUuid }}" class="form-control" id="inputPlacementUuid" placeholder="Placement ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_name" value="{{ $placementName }}" class="form-control" id="inputPlacementName" placeholder="Placement Name">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="format" class="form-control">
                                <option value="all" >All Format</option>
                                @foreach ($formatMap as $key => $val)
                                <option value="{{ $key }}" @if (is_numeric($format) && ($format == $key)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
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
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                            <button type="submit" class="btn btn-success">
                                Export Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Placement ID</th>
                            <th>Placement Name</th>
                            <th>APP ID</th>
                            <th>APP Name</th>
                            <th>Publisher</th>
                            <th>System</th>
                            <th>Format</th>
                            <th>Status</th>
                            <th>Create Time</th>
                            <th>Update Time</th>
                            <th>Private Status</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($placement as $val)
                        <tr>
                            <th scope="row">
                                {{ $val['uuid'] }}<br />
                                <small>{{ $val['id'] }}</small>
                            </th>
                            <td>{{ $val['name'] }}</td>
                            <td>{{ $val['app_uuid'] }}</td>
                            <td>{{ $val['app_name'] }}</td>
                            <td>{{ $val['publisher_name'] }}</td>
                            <td>{{ $val['system_name'] }}</td>
                            <td>{{ $val['format_name'] }}</td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['create_time']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['update_time']) }}</td>
                            <td>{{ $val['private_status_name'] }}</td>
                            <td>
                                <a href="{{ URL::to('placement/' . $val['id'] . '/edit') }}" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $placement->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{
                            $placement->appends(
                            [
                                'publisher_id' => $publisherId,
                                'publisher_name' => $publisherName,
                                'app_id' => $appId,
                                'app_uuid' => $appUuid,
                                'app_name' => $appName,
                                'placement_id' => $placementId,
                                'placement_uuid' => $placementUuid,
                                'placement_name' => $placementName,
                                'status' => $status,
                                'format' => $format,
                                'system' => $system,
                            ])
                            ->links()
                        }}
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <script>
    function updateStatus(id, status){
        console.log(JSON.stringify({id: id, data: {status: status}}));
        $.ajax({
            url: "{{ URL::to('placement/update') }}",
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
    (function($){
        
    });
    </script>

@endsection
