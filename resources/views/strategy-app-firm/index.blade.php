@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">App Firm Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">App Firm Strategy</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Firm Name</th>
                            <th>监控上报</th>
                            <th>仅点击上报</th>
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
                            <td>{{ $val['firm_name'] }}</td>
                            <td>{{ $val['upload_sw'] == 1 ? 'Yes' : 'No' }}</td>
                            <td>{{ $val['click_only'] == 1 ? 'Yes' : 'No' }}</td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['create_time']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['update_time']) }}</td>
                            <td>
                                @if ($val['status'] == 1)
                                <a href="#" onclick="updateStatus({{ $val['id'] }}, 2)" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Running</a>
                                @elseif($val['nw_firm_id'] != 0)
                                <a href="#" onclick="updateStatus({{ $val['id'] }}, 1)" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Stop</a>
                                @endif
                                <a href="{{ URL::to('strategy-app-firm/' . $val['id'] . '/edit') }}" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $data->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                    </div>
                </div>
            </div>
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-app-firm.store'), 'method' => 'POST')) }}
                <input type="hidden" name="app_id" value="{{ $appId }}" />
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputNwCacheTime">厂商</label>
                            <select name="nw_firm_id" class="form-control">
                                @foreach ($firm as $key => $val)
                                <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>监控上报</label>
                            <select name="upload_sw" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>仅点击上报</label>
                            <select name="click_only" class="form-control">
                                <option value="0">No</option>
                                <option value="1" selected>Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script>
    function updateStatus(id, status){
        $.ajax({
            url: "{{ URL::to('strategy-app-firm') }}/" + id,
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
