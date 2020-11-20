@extends('layouts.admin')

@section('content')
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
                            <a href="{{ URL::to('strategy-app') }}">Manage APP Strategy</a>
                        </li>
                        <li class="breadcrumb-item active">Edit APP Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit APP Strategy</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-app.update', $data['id']), 'method' => 'PUT')) }}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>APP ID</label>
                            <input type="text" class="form-control" readonly="" value="{{ $data['app_id'] }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputCacheTime">Cache time</label>
                            <?php
                            if(count($errors->all()) !== 0) {
                                $cacheTime = old('cache_time');
                            } else {
                                $cacheTime = $data['cache_time'];
                            }
                            ?>
                            <div class="input-group">
                                <input type="number" name="cache_time" value="{{ $cacheTime }}" class="form-control" id="inputCacheTime" />
                                <div class="input-group-append">
                                    <span class="input-group-text">seconds</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputNewPsidTime">(冷启动)生成新 ps_id 时间间隔</label>
                            <div class="input-group">
                                <input type="number" name="new_psid_time" value="{{ $data['new_psid_time'] }}" class="form-control" id="inputNewPsidTime" />
                                <div class="input-group-append">
                                    <span class="input-group-text">seconds</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputPsidHotLeave">(热启动)重新生成新ps_id-离开应用的时间</label>
                        <label class="version_label">（SDK Version 5.5.4及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="psid_hot_leave" value="{{ $data['psid_hot_leave'] }}" class="form-control" id="inputPsidHotLeave" />
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>离开应用后是否采用倒计时</label>
                        <label class="version_label">（SDK Version 5.5.4及以上支持）</label>
                        <?php
                        if(count($errors->all()) !== 0) {
                            $leaveAppSwitch = old('leave_app_switch');
                        } else {
                            $leaveAppSwitch = $data['leave_app_switch'];
                        }
                        ?>
                        <div>
                            <label class="custom-control custom-radio">
                                <input name="leave_app_switch" type="radio" value="2" class="custom-control-input"
                                       @if ($leaveAppSwitch != 1) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Yes</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input name="leave_app_switch" type="radio" value="1" class="custom-control-input"
                                       @if ($leaveAppSwitch == 1) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">No</span>
                            </label>
                        </div>
                    </div>
                </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputNewPsidTime">提前初始化Network SDK</label>
                            <label class="version_label">（SDK Version 5.4.1及以上支持）</label>
                            <div class="mt-3 row">
                                @foreach($networkFirmList as $id => $name)
                                    <div class="col-2">
                                        <label class="custom-control custom-checkbox">
                                            <input name="network_pre_init[]"
                                                   type="checkbox"
                                                   id="network_pre_init_{{ $id }}"
                                                   value="{{ $id }}"
                                                   class="custom-control-input"　
                                                   @if (in_array($id, array_keys((array)$data['network_pre_init_list']))) checked @endif >
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ $name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>MyOffer素材缓存区大小</label>
                            <div class="input-group">
                                <input type="number" name="cache_areasize" value="{{ $data['cache_areasize'] }}" class="form-control" />
                                <div class="input-group-append">
                                    <span class="input-group-text">MB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputPlTimeout">Placement 超时时间</label>
                            <div class="input-group">
                                <input type="number" name="placement_timeout" value="{{ old('placement_timeout', $data['placement_timeout']) }}" class="form-control" id="inputPlTimeout" />
                                <div class="input-group-append">
                                    <span class="input-group-text">seconds</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>GDPR 服务端上报规则</label>
                            <?php
                            if(count($errors->all()) !== 0) {
                                $gdprConsentSet = old('gdpr_consent_set');
                            } else {
                                $gdprConsentSet = $data['gdpr_consent_set'];
                            }
                            ?>
                            <div>
                                <label class="custom-control custom-radio">
                                    <input name="gdpr_consent_set" type="radio" value="0" class="custom-control-input"
                                           @if ($gdprConsentSet === 0) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Personalized</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input name="gdpr_consent_set" type="radio" value="1" class="custom-control-input"
                                           @if ($gdprConsentSet === 1) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Nonpersonalized</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>GDPR 只读服务端配置</label>
                            <?php
                            if(count($errors->all()) !== 0) {
                                $gdprServerOnly = old('gdpr_server_only');
                            } else {
                                $gdprServerOnly = $data['gdpr_server_only'];
                            }
                            ?>
                            <div>
                                <label class="custom-control custom-radio">
                                    <input name="gdpr_server_only" type="radio" value="1" class="custom-control-input"
                                           @if ($gdprServerOnly == 1) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input name="gdpr_server_only" type="radio" value="0" class="custom-control-input"
                                           @if ($gdprServerOnly != 1) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                            <span class="help-block" style="color:#ff3111">
                                <small>当“GDPR只读服务端配置”=Yes时</small><br />
                                <small>1、在欧盟地区，当“GDPR服务端上报规则”设置值进行下发</small><br />
                                <small>2、在非欧盟地区，服务端下发的当“GDPR服务端上报规则”=Personalized</small>
                            </span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 {{ $errors->has('gdpr_notify_url') ? ' has-error' : '' }}">
                            <label for="inputGdprNotifyUrl">GDPR 提醒界面URL</label>
                            <?php
                            if(count($errors->all()) !== 0) {
                                $gdprNotifyUrl = old('gdpr_notify_url');
                            } else {
                                $gdprNotifyUrl = $data['gdpr_notify_url'];
                            }
                            ?>
                            <input name="gdpr_notify_url" value="{{ $gdprNotifyUrl }}" class="form-control" id="inputGdprNotifyUrl" />
                            @if ($errors->has('gdpr_notify_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('gdpr_notify_url') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>在EU地区用户未设置GDPR时，是否采用Newtork SDK默认设置</label>
                            <?php
                            if(count($errors->all()) !== 0) {
                                $networkGdprSwitch = old('network_gdpr_switch');
                            } else {
                                $networkGdprSwitch = $data['network_gdpr_switch'];
                            }
                            ?>
                            <div>
                                <label class="custom-control custom-radio">
                                    <input name="network_gdpr_switch" type="radio" value="2" class="custom-control-input"
                                           @if ($networkGdprSwitch == 2) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input name="network_gdpr_switch" type="radio" value="1" class="custom-control-input"
                                           @if ($networkGdprSwitch != 2) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>消息通知名称列表</label><label class="version_label">（SDK Version 5.4.0及以上支持）</label>
                            <textarea name="notice_list" placeholder="json格式字符串" type="text" class="form-control">{{ $data['notice_list'] }}</textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>设备数据上报控制</label>
                            <div>
                                <label class="custom-control custom-checkbox">
                                    <input name="data_level[m]" type="checkbox" value="1" class="custom-control-input"
                                           @if ($data['data_level']['m'] == 1) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Mac</span>
                                </label>
                                <label class="custom-control custom-checkbox">
                                    <input name="data_level[i]" type="checkbox" value="1" class="custom-control-input"
                                           @if ($data['data_level']['i'] == 1) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">IMEI</span>
                                </label>
                                <label class="custom-control custom-checkbox">
                                    <input name="data_level[a]" type="checkbox" value="1" class="custom-control-input"
                                           @if ($data['data_level']['a'] == 1) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Android ID</span>
                                </label>
                            </div>
                            <span class="help-block" style="color:#ff3111">
                                <small>注：MAC/IMEI仅在国内Android版本上报</small>
                            </span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>收集 Crash 日志开关</label>
                            <span class="help-block" style="color:#ff3111">（Android SDK Version 5.6.2 及以上支持）</span>
                            <div>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="crash_sw" type="radio"
                                           value="{{ \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF }}"
                                           @if ($data['crash_sw'] == \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF) checked="checked" @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">关</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="crash_sw" type="radio"
                                           value="{{ \App\Models\MySql\StrategyApp::CRASH_SWITCH_ON }}"
                                           @if ($data['crash_sw'] == \App\Models\MySql\StrategyApp::CRASH_SWITCH_ON) checked="checked" @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">开</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="crash-type-row" class="form-row" @if($data['crash_sw'] == \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF) hidden @endif>
                        <div class="form-group col-md-6">
                            <label>收集指定包名的 Crash 日志</label>
                            <span class="help-block" style="color:#ff3111">（Android SDK Version 5.6.2 及以上支持）</span>
                            <div>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="crash_type" type="radio" value="all" @if($data['crash_sw'] == \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF || $data['crash_list'] === '') checked @endif>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">全部</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="crash_type" type="radio" value="specific" @if($data['crash_sw'] == \App\Models\MySql\StrategyApp::CRASH_SWITCH_ON && $data['crash_list'] !== '') checked @endif>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">指定包名</span>
                            </label>
                            </div>
                        </div>
                    </div>
                    <div id="crash-list-row" class="form-row" @if($data['crash_sw'] == \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF || $data['crash_list'] === '') hidden @endif>
                        <div class="form-group col-md-6">
                            <label>Crash 收集包名匹配列表</label>
                            <span class="help-block" style="color:#ff3111">（Android SDK Version 5.6.2 及以上支持）</span>
                            <textarea class="form-control" name="crash_list" required  @if($data['crash_sw'] == \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF || $data['crash_list'] === '') disabled @endif>{{ $data['crash_list'] }}</textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Create time</label>
                            <input type="text" class="form-control" readonly="" value="{{ date('Y-m-d H:i:s', $data['create_time']) }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Update time</label>
                            <input type="text" class="form-control" readonly="" value="{{ date('Y-m-d H:i:s', $data['update_time']) }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Status</label>
                            <?php
                            if(count($errors->all()) !== 0) {
                                $status = old('status');
                            } else {
                                $status = $data['status'];
                            }
                            ?>
                            <div>
                                <label class="custom-control custom-radio">
                                    <input name="status" type="radio" value="2" class="custom-control-input"
                                           @if ($status == 2) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">On</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input name="status" type="radio" value="1" class="custom-control-input"
                                           @if ($status != 2) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Off</span>
                                </label>
                            </div>
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
        $(document).ready(function () {
            /* 收集 Crash 日志开关 */
            $('input[type="radio"][name="crash_sw"]').change(function () {
                let crashTypeAll = $('input[name="crash_type"][value="all"]');
                let crashTypeSpecific = $('input[name="crash_type"][value="specific"]');
                let crashList = $('textarea[name="crash_list"]');
                let crashListRow = $('#crash-list-row');
                if ($(this).val() == '{{ \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF }}') {
                    crashListRow.prop('hidden', 'hidden');
                } else {
                    if ($(crashList).val() !== '') {
                        crashTypeAll.prop('checked', false);
                        crashTypeSpecific.prop('checked', true);
                        crashListRow.prop('hidden', false);
                        crashList.prop('disabled', false);
                    }
                }
                $('#crash-type-row').prop('hidden', $(this).val() == '{{ \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF }}');
                $(crashList).prop('required', $(this).val() != '{{ \App\Models\MySql\StrategyApp::CRASH_SWITCH_OFF }}')
            });

            /* 收集指定包名的 Crash 日志：全部 / 指定 */
            $('input[type="radio"][name="crash_type"]').change(function () {
                $('#crash-list-row').prop('hidden', $(this).val() == 'all');
                $('textarea[name="crash_list"]').prop('disabled', $(this).val() == 'all');
            });
        });
    </script>

@endsection
