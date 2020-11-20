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
                        <label>Company</label>
                        <input type="text" name="company" class="form-control" value="{{ $data['company'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Contact</label>
                        <input type="text" class="form-control" name="contact" value="{{ $data['contact'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Mobile Phone</label>
                        <input type="text" class="form-control" name="phone_number" value="{{ $data['phone_number'] }}">
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
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher Key</label>
                        <input type="text" class="form-control" @if ($data['publisher_key'] == "") placeholder="API权限打开后自动生成" @endif readonly value="{{ $data['publisher_key'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher Type</label>
                        <select name="mode" class="form-control" id="mode">
                            @foreach ($modeMap as $key => $val)
                                <option value="{{ $key }}"
                                        @if ($data['mode'] == $key) selected="selected" @endif>{{ $val }}</option>
                            @endforeach
                        </select>
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
                        <label>Currency</label>
                        <select name="currency" class="form-control" id="currency" @if ($data['currency'] != 'USD') disabled="disabled" @endif>
                            @foreach($currencyMap as $currency)
                                <option value={{ $currency }} @if ($data['currency'] == $currency) selected="selected" @endif>{{ $currency }}</option>
                            @endforeach
                        </select>
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
                        <label>广告平台权限管理</label>
                        <div class="mt-3">
                            已开放：{{ $data['allow_firms_count'] }} / {{ $data['firms_count']  }}
                            <a href="{{ URL::to('publisher/allow-firms/' . $data['id'] . '/edit') }}" class="ml-3">编辑</a>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>API 权限</label>
                        <div class="mt-3">
                            @foreach($apiSwitchMap as $apiStatus => $apiStatusText)
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="api_switch" type="radio" id="api_switch_{{ $apiStatus }}" value="{{ $apiStatus }}" @if ($data['api_switch'] == $apiStatus)  checked @endif>
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
                                    <input class="custom-control-input" name="device_data_switch" type="radio" id="device_data_switch_{{$deviceDataSwitchStatus}}" value="{{ $deviceDataSwitchStatus }}" @if ($data['device_data_switch'] == $deviceDataSwitchStatus)  checked @endif>
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
                        <label>My Offer权限</label>
                        <div class="mt-3">
                            @foreach($myOfferSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="my_offer_switch" type="radio" id="my_offer_{{$status}}" value="{{ $status }}" class="custom-control-input"@if ($data['my_offer_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>不同Placement允许使用同一个广告源</label>
                        <div class="mt-3">
                            @foreach($unitRepeatSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="unit_repeat_switch"
                                           type="radio"
                                           id="unit_repeat_switch_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"
                                           @if ($data['unit_repeat_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>子账号</label>
                        <div class="mt-3">
                            @foreach($subAccountSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="sub_account_switch"
                                           type="radio"
                                           id="sub_account_switch_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"
                                           @if ($data['sub_account_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>子账号支持补扣量</label>
                        <div class="mt-3">
                            @foreach($distributionSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="distribution_switch"
                                           type="radio"
                                           id="distribution_switch_{{ $status }}"
                                           value="{{ $status }}"
                                           class="custom-control-input"
                                           @if ($data['distribution_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>广告平台多账号配置</label>
                        <div class="mt-3">
                            @foreach($networkMultipleSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="network_multiple_switch"
                                           type="radio"
                                           id="network_multiple_switch_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"
                                           @if ($data['network_multiple_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>时区切换</label>
                        <div class="mt-3">
                            @foreach($reportTimezoneSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="report_timezone_switch"
                                           type="radio"
                                           id="report_timezone_switch_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"
                                           @if ($data['report_timezone_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>广告场景</label>
                        <div class="mt-3">
                            @foreach($scenarioSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="scenario_switch"
                                           type="radio"
                                           id="scenario_switch_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"
                                           @if ($data['scenario_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>ADX 权限（打开后，现有Waterfall的ADX广告源状态都置为关闭）</label>
                        <div class="mt-3">
                            @foreach($adxSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="adx_switch"
                                           type="radio"
                                           id="adx_switch_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"
                                           @if ($data['adx_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>新增Waterfall时，ADX广告源的默认状态（包括：新增广告位、新增流量分组等场景）</label>
                        <div class="mt-3">
                            @foreach($adxUnitSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="adx_unit_switch"
                                           type="radio"
                                           id="adx_unit_switch_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"
                                           @if ($data['adx_unit_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>开发者后台版本</label><label style="color:#ff3111">（注意：切换完成后，新版本不能回到老版本）</label>
                        @if($canMigrate)
                            <div class="mt-3">
                                @foreach($migrateStatusMap as $status => $statusText)
                                    <label class="custom-control custom-radio">
                                        <input name="version" type="radio" value="{{ $status }}" class="custom-control-input"@if ($data['migrate_4'] == $status) checked @endif >
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ $statusText }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @else
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input name="version" type="radio" class="custom-control-input" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $migrateStatusMap[$data['migrate_4']] }}</span>
                                </label>
                            </div>
                        @endif

                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Note</label>
                        <textarea name="note" placeholder="最多支持填写100个字符" type="text" class="form-control">{{ $data['note'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Channel Note</label>
                        <textarea readonly type="text" class="form-control">{{ $data['note_channel'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script>
        const token = '{{ csrf_token() }}';
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

        function fixCurrencyByMode(modeVal) {
            switch (modeVal) {
                case "1": // White
                    var dataCurrency = "{{ $data['currency'] }}";
                    if(dataCurrency == 'USD'){
                        $('#currency').prop('disabled', false);
                    }
                    break;
                case "2": // Black
                    $("#currency").val("USD");
                    $('#currency').prop('disabled', 'disabled');
                    break;
            }
        }

        function fixSwitchByMode(switchId, switchVal, modeVal) {
            var offRadioId = switchId + '_' + switchVal[0];
            var onRadioId  = switchId + '_' + switchVal[1];
            var tzRadioId  = switchId + '_' + switchVal[1];

            switch (modeVal) {
                case "1": //White
                    $(offRadioId).attr('disabled', false);
                    $(onRadioId).attr('disabled', false);
                    $(tzRadioId).attr('disabled', false);
                    break;
                case "2": //Black
                    $(offRadioId).prop('checked', true);
                    $(onRadioId).prop('checked', false);

                    $(offRadioId).attr('disabled', true);
                    $(onRadioId).attr('disabled', true);
                    $(tzRadioId).attr('disabled', true);
                    break;
            }
        }

        function fixDeviceSwitchByApiSwitch(apiSwitchVal){
            switch (apiSwitchVal) {
                case "1":
                    $("#device_data_switch_1").prop('checked', true)
                    $("#device_data_switch_2").prop('checked', false)

                    $("#device_data_switch_1").attr('disabled', true)
                    $("#device_data_switch_2").attr('disabled', true)
                    break;
                case "2":
                    $("#device_data_switch_1").attr('disabled', false)
                    $("#device_data_switch_2").attr('disabled', false)
                    break;
            }
        }


        $(document).ready(function () {
            let publisherMode = $("#mode").val()
            fixCurrencyByMode(publisherMode);
            fixSwitchByMode("#report_import", [1, 2], publisherMode);
            fixSwitchByMode("#api_switch", [1, 2], publisherMode);
            fixSwitchByMode("#device_data_switch", [1, 2], publisherMode);
            fixSwitchByMode("#sub_account_switch", [1, 2], publisherMode);
            fixSwitchByMode("#report_timezone_switch", [1, 2], publisherMode);

            let apiSwitchVal = $('input[type=radio][name=api_switch]:checked').val();
            fixDeviceSwitchByApiSwitch(apiSwitchVal)

            var preventDefault = true;
            $("form#editForm").submit(function () {
                if (!preventDefault) {
                    return true;
                }
                var status = $("select[name='status']").val();
                var text = '';
                if (status == 1) {
                    text = 'Are you sure to blocked this publisher account ？';
                } else if (status == 2) {
                    text = 'Are you sure to make this publisher account pending ？';
                }
                if (text) {
                    swal({
                        title: 'Attention',
                        text: text,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#4fa7f3',
                        cancelButtonColor: '#d57171',
                        confirmButtonText: 'Yes'
                    }).then(function () {
                        preventDefault = false;
                        $("form#editForm").submit();
                    });
                    event.preventDefault();
                }
            });

            $("#mode").change(function () {
                let publisherMode = this.value;
                fixCurrencyByMode(publisherMode);
                fixSwitchByMode("#report_import", [1, 2], publisherMode);
                fixSwitchByMode("#api_switch", [1, 2], publisherMode);
                fixSwitchByMode("#device_data_switch", [1, 2], publisherMode);
                fixSwitchByMode("#sub_account_switch", [1, 2], publisherMode);
                fixSwitchByMode("#report_timezone_switch", [1, 2], publisherMode);
            });

            $("input[name='sub_account_switch']").change(function(){
                if($(this).val() == 1){
                    $("input[name='distribution_switch']").prop('disabled', true);
                    $("input[name='distribution_switch'][value='1']").prop('checked', true);
                }else{
                    $("input[name='distribution_switch']").prop('disabled', false);
                }
            });

            $('input[type=radio][name=api_switch]').change(function() {
                let apiSwitchVal = this.value;
                fixDeviceSwitchByApiSwitch(apiSwitchVal);
            });
        });
    </script>

@endsection
