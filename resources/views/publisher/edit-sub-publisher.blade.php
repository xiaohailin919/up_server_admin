@extends('layouts.admin')

@section('content')
    <style>
        .btn-sm {
            padding: 4px 8px;
            font-size: 14px;
            line-height: 14px;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">TopOn</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ URL::to('publisher') }}">Manage Publisher</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Publisher</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Publisher</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('publisher.update', $data['id']), 'method' => 'PUT', 'id' => 'editForm')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher ID</label>
                        <input type="text" class="form-control" readonly="" value="{{ $data['id'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher Name</label>
                        <input type="text" class="form-control" readonly="" value="{{ $data['name'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="text" class="form-control" readonly="" value="{{ $data['email'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Create Time</label>
                        <input type="text" class="form-control" readonly="" value="{{ date('Y-m-d H:i:s', $data['create_time']) }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Manager</label>
                        <input type="text" class="form-control" readonly value="{{ date('Y-m-d H:i:s', $data['update_time']) }} &nbsp;&nbsp;{{ $data['admin_name'] }}">
                    </div>
                </div>
                <div id="add-group-pop-up"></div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher Group</label>
                        <a class="btn btn-custom btn-sm" onclick="addGroup()">Add Group</a>
                        <select class="form-control select2 select2-multiple select2-hidden-accessible" name="publisher_group_ids[]" multiple="" data-placeholder="- Publisher Group -">
                            @foreach ($publisherGroupIdNameMap as $publisherGroupId => $publisherGroupName)
                                <option value="{{ $publisherGroupId }}" @if (in_array($publisherGroupId, $data['publisher_group_ids'], false)) selected @endif>{{ $publisherGroupName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher Key</label>
                        <input type="text" class="form-control" @if ($data['publisher_key'] == "") placeholder="API权限打开后自动生成" @endif readonly value="{{ $data['publisher_key'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>API 权限</label>
                        <div class="mt-3">
                            @foreach($apiSwitchMap as $apiStatus => $apiStatusText)
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="api_switch" type="radio" id="api_switch_{{ $apiStatus }}" value="{{ $apiStatus }}" @if ($data['api_switch'] == $apiStatus)  checked @endif @if ($data['mode'] == \App\Models\MySql\Publisher::MODE_BLACK) disabled @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $apiStatusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>开放Device维度的数据</label>
                        <div class="mt-3">
                            @foreach($deviceDataSwitchMap as $deviceDataSwitchStatus => $deviceDataSwitchStatusText)
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="device_data_switch" type="radio" id="device_data_switch_{{$deviceDataSwitchStatus}}" value="{{ $deviceDataSwitchStatus }}" @if ($data['device_data_switch'] == $deviceDataSwitchStatus) checked @endif @if($data['api_switch'] == \App\Models\MySql\Publisher::API_SWITCH_OFF) disabled @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $deviceDataSwitchStatusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>上传第三方数据权限</label>
                        <div class="mt-3">
                            @foreach($reportImportSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="report_import_switch" type="radio" id="report_import_{{$status}}" value="{{ $status }}" class="custom-control-input"@if ($data['report_import_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach ($statusMap as $key => $val)
                                <option value="{{ $key }}"
                                        @if ($data['status'] == $key) selected="selected" @endif>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Note</label>
                        <textarea placeholder="最多支持填写100个字符" type="text" readonly="" class="form-control">{{ $data['note'] }}</textarea>
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
        const token = '{{ csrf_token() }}';

        /**
         * 添加 Publisher Group
         */
        function addGroup() {
            $.confirm({
                title: '添加用户群组',
                content: `<form id="add-publisher-group-form">
                            <div class="form-group">
                                <label for="publisher-group-name-input">群组名</label>
                                <input class="form-control" type="text" id="publisher-group-name-input" maxlength="30" name="name" placeholder="请输入群组名">
                                <input type="hidden" name="_token" value="` + token + `">
                            </div>
                            <i id="priority_error" style="color: red;display:none"></i>
                        </form>
                    `,
                buttons: {
                    confirm: {
                        text: '保存',
                        btnClass: 'btn-blue',
                        action: function() {
                            $.ajax({
                                url: '/publisher-group/',
                                type: 'POST',
                                data: $('#add-publisher-group-form').serializeArray(),
                                success: function () {
                                    location.replace(location.href)
                                },
                                error: function (response) {
                                    let error = '';
                                    if (response.responseJSON.hasOwnProperty('exception')) {
                                        error = response.responseJSON.exception.message;
                                    }
                                    if (response.responseJSON.hasOwnProperty('errors')) {
                                        error = response.responseJSON.errors.name;
                                    }
                                    console.log(response);
                                    console.log(response.responseJSON);
                                    console.log(error);
                                    $.alert({
                                        title: '添加失败',
                                        theme: 'dark',
                                        content: error
                                    })
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        btnClass: 'btn-primary'
                    }
                }
            });
        }

        /**
         * 根据 API 权限设置设备层级报表权限
         */
        function onApiSwitchChange(){
            let apiSwitchVal = $('input[type=radio][name=api_switch]:checked').val();
            let deviceDataSwitchOff = $("#device_data_switch_1");
            let deviceDataSwitchOn = $("#device_data_switch_2");
            switch (apiSwitchVal) {
                case '{{ \App\Models\MySql\PublisherApiInfo::UP_API_PERMISSION_OFF }}':
                    $(deviceDataSwitchOff).prop('checked', true)
                    $(deviceDataSwitchOn).prop('checked', false)
                    $(deviceDataSwitchOff).attr('disabled', true)
                    $(deviceDataSwitchOn).attr('disabled', true)
                    break;
                case '{{ \App\Models\MySql\PublisherApiInfo::UP_API_PERMISSION_ON }}':
                    $(deviceDataSwitchOff).attr('disabled', false)
                    $(deviceDataSwitchOn).attr('disabled', false)
                    break;
            }
        }

        $(function () {
            onApiSwitchChange();
            $('input[name="api_switch"]').change(function () {
                onApiSwitchChange();
            });
        })
    </script>

@endsection
