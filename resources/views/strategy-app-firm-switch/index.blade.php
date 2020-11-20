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
                            <th>Filled Request TK上报</th>
                            <th>Impression TK上报</th>
                            <th>Show TK上报</th>
                            <th>Click TK上报</th>
                            <th>Status</th>
                            <th>Create Time</th>
                            <th>Update Time</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($defaultItem))
                            <tr>
                                <th scope="row">{{ $defaultItem['id'] }}</th>
                                <td>{{ $defaultItem['firm_name'] }}</td>
                                <td>{{ $defaultItem['filled_request_switch'] }}%</td>
                                <td>{{ $defaultItem['impression_switch'] }}%</td>
                                <td>{{ $defaultItem['show_switch'] }}%</td>
                                <td>{{ $defaultItem['click_switch'] }}%</td>
                                <td>{{ $defaultItem['status_name'] }}</td>
                                <td>{{ $defaultItem['create_time'] }}</td>
                                <td>{{ $defaultItem['update_time'] }}</td>
                                <td>
                                    @if ($defaultItem['status'] == $pauseStatus)
                                        <a href="#" onclick="updateStatus({{ $defaultItem['id'] }}, {{ $activeStatus }})" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Running</a>
                                    @elseif($defaultItem['nw_firm_id'] != 0)
                                        <a href="#" onclick="updateStatus({{ $defaultItem['id'] }}, {{ $pauseStatus }})" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Stop</a>
                                    @endif
                                    <a href="{{ URL::to('strategy-app-firm-switch/' . $defaultItem['id'] . '/edit?current_app_id=' . $appId) }}" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a>
                                </td>
                            </tr>
                        @endif
                        @foreach ($data as $val)
                        <tr>
                            <th scope="row">{{ $val['id'] }}</th>
                            <td>{{ $val['firm_name'] }}</td>
                            <td>{{ $val['filled_request_switch'] }}%</td>
                            <td>{{ $val['impression_switch'] }}%</td>
                            <td>{{ $val['show_switch'] }}%</td>
                            <td>{{ $val['click_switch'] }}%</td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>{{ $val['create_time'] }}</td>
                            <td>{{ $val['update_time'] }}</td>
                            <td>
                                @if ($val['status'] == $pauseStatus )
                                <a href="#" onclick="updateStatus({{ $val['id'] }}, {{ $activeStatus }})" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Running</a>
                                @elseif($val['nw_firm_id'] != 0)
                                <a href="#" onclick="updateStatus({{ $val['id'] }}, {{ $pauseStatus }})" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Stop</a>
                                @endif
                                <a href="{{ URL::to('strategy-app-firm-switch/' . $val['id'] . '/edit?current_app_id=' . $appId) }}" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a>
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
                {{ Form::model(null, array('route' => array('strategy-app-firm-switch.store'), 'method' => 'POST')) }}
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
                        <label for="inputCacheTime">Filled Request TK上报PKG概率</label>
                        <div class="input-group">
                            <input type="number" name="filled_request_switch" min="0" max="100" id="filled_request_switch" value="0" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">Impression TK上报PKG概率</label>
                        <div class="input-group">
                            <input type="number" name="impression_switch" min="0" max="100" id="impression_switch" value="0" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">Show TK上报PKG概率</label>
                        <div class="input-group">
                            <input type="number" name="show_switch" min="0" max="100" id="show_switch" value="0" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">Click TK上报PKG概率</label>
                        <div class="input-group">
                            <input type="number" name="click_switch" min="0" max="100" id="click_switch" value="0" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>PKG匹配列表</label>
                        <textarea name="pkg" placeholder="json格式字符串" type="text" class="form-control">{{ $data['package_match_list'] }}</textarea>
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
            url: "{{ URL::to('strategy-app-firm-switch') }}/" + id,
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
