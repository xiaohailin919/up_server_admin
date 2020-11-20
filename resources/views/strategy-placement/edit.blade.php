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
                            <a href="{{ \Illuminate\Support\Facades\URL::to('strategy-placement') }}">Manage Placement Strategy</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Placement Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Placement Strategy</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-placement.update', $data['id']), 'method' => 'PUT')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Placement ID</label>
                        <input class="form-control" type="text" readonly="" value="{{ $data['placement_id'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Cache Time</label><label style="color:#ff3111">（SDK Version
                            4.3.0及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="cache_time" value="{{ $data['strategy_cache_time'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>等待策略更新超时时间</label><label style="color:#ff3111">（SDK Version 4.3.0及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="up_timeout" min="-1" value="{{ $data['strategy_cache_timeout'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div class="form-row">--}}
                {{--<div class="form-group col-md-6">--}}
                {{--<label>广告位展示逻辑</label>--}}
                {{--<div class="mt-3">--}}
                {{--<label class="custom-control custom-radio">--}}
                {{--<input name="show_type" type="radio" value="0" class="custom-control-input" --}}
                {{--@if ($data['show_type'] == 0) checked="" @endif >--}}
                {{--<span class="custom-control-indicator"></span>--}}
                {{--<span class="custom-control-description">按优先级</span>--}}
                {{--</label>--}}
                {{--<label class="custom-control custom-radio">--}}
                {{--<input name="show_type" type="radio" value="1" class="custom-control-input"--}}
                {{--@if ($data['show_type'] == 1) checked="" @endif >--}}
                {{--<span class="custom-control-indicator"></span>--}}
                {{--<span class="custom-control-description">轮播</span>--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                @if($format !=1 && $format != 2 && $format != 3 && $format != 4)
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Wi-Fi下视频自动播放</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="wifi_autoplay" type="radio" value="1" @if ($data['wifi_autoplay'] == 1) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">是</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="wifi_autoplay" type="radio" value="0" @if ($data['wifi_autoplay'] == 0) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">否</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Network并发请求数</label>
                        <input class="form-control" type="number" name="nw_requests" value="{{ $data['nw_requests'] }}"/>
                        <span class="help-block">
                            <small>AD Request Number</small>
                        </span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Network缓存时间</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="nw_cache_time" value="{{ $data['nw_cache_time'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Network广告素材超时时间（原Network超时时间）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="nw_timeout" value="{{ $data['nw_timeout'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Network广告数据超时时间</label><label class="version_label">（SDK Version 5.1.0及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="ad_data_nw_timeout" value="{{ $data['ad_data_nw_timeout'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($format != 1 && $format != 2 && $format != 3 && $format != 4)
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Network下的Offer请求条数</label>
                            <input class="form-control" type="number" name="nw_offer_requests" value="{{ $data['nw_offer_requests'] }}"/>
                        </div>
                    </div>
                    {{--<div class="form-row">--}}
                    {{--<div class="form-group col-md-6">--}}
                    {{--<label>该广告位是否执行刷新逻辑</label>--}}
                    {{--<div class="mt-3">--}}
                    {{--<label class="custom-control custom-radio">--}}
                    {{--<input name="refresh" type="radio" value="0" class="custom-control-input" --}}
                    {{--@if ($data['refresh'] == 0) checked="" @endif >--}}
                    {{--<span class="custom-control-indicator"></span>--}}
                    {{--<span class="custom-control-description">关闭</span>--}}
                    {{--</label>--}}
                    {{--<label class="custom-control custom-radio">--}}
                    {{--<input name="refresh" type="radio" value="1" class="custom-control-input"--}}
                    {{--@if ($data['refresh'] == 1) checked="" @endif >--}}
                    {{--<span class="custom-control-indicator"></span>--}}
                    {{--<span class="custom-control-description">开启</span>--}}
                    {{--</label>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                @endif
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Frequency Caps</label>
                        <div class="mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <input type="checkbox" disabled @if($data['cap_hour'] !== '') checked @endif />
                                    </span>
                                </div>
                                <input class="form-control" type="number" readonly value="{{  $data['cap_hour'] }}"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">hourly impressions per user</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <input type="checkbox" disabled @if($data['cap_day'] !== '') checked @endif />
                                    </span>
                                </div>
                                <input class="form-control" type="number" readonly value="{{ $data['cap_day'] }}"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">daily impressions per user</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Pacing</label>
                        <div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" disabled @if ($data['pacing'] !== '') checked="checked" @endif />
                                        </span>
                                    <span class="input-group-text">show a maximum of 1 AD / </span>
                                </div>
                                <input class="form-control" type="number" readonly value="{{ $data['pacing'] }}"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">minutes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($format != 1 && $format != 3 && $format != 4)
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Auto Refresh</label>
                            <div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input name="auto_refresh" value="1" type="checkbox" {{ $data['auto_refresh'] ? 'checked' : '' }} />
                                        </span>
                                        <span class="input-group-text">refresh time</span>
                                    </div>
                                    <input class="form-control" type="number" name="auto_refresh_time" value="{{ $data['auto_refresh_time'] }}" min="0"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">seconds</span>
                                    </div>
                                </div>
                            </div>
                            <span class="help-block">
                                <small>We will automatically refresh the placement at the configured refresh time. It is recommended that the refresh time should not be too short. Refreshing too frequently will affect the fill rate of the placement.</small>
                            </span>
                        </div>
                    </div>
                @endif
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Create time</label>
                        <input class="form-control" type="text" readonly="" value="{{ date('Y-m-d H:i:s', $data['create_time']) }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Update time</label>
                        <input class="form-control" type="text" readonly="" value="{{ date('Y-m-d H:i:s', $data['update_time']) }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>AD Delivery</label>
                        <input class="form-control" type="text" readonly="" @if ($data['delivery'] == 1) value="On" @else value="Off" @endif />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <div class="mt-3">
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="status" type="radio" value="2" @if ($data['status'] == 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">On</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="status" type="radio" value="1" @if ($data['status'] != 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Off</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>长超时时间</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="sdk_timeout" value="{{ $data['sdk_timeout'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                        <span class="help-block">
                            <small>SDK长时间超时时间</small>
                        </span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Placement 维度Up_status有效期</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="load_success_up_status" value="{{ $data['load_success_up_status'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Ad Source 维度Up_status有效期</label>
                        <label class="version_label">（SDK Version 5.1.0及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="ad_up_status" value="{{ $data['ad_up_status'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Load 失败后重试最小等待时间</label>
                        <label class="version_label">（SDK Version 5.6.2 及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="load_fail_wtime" min="0" value="{{ $data['load_fail_wtime'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">秒</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>广告位 Load Cap 设置</label>
                        <label class="version_label">（SDK Version 5.6.2 及以上支持）</label>
                        <input class="form-control" type="number" name="load_cap" min="-1" value="{{ $data['load_cap'] }}"/>
                        <span class="help-block">
                            注：-1 和 0 均为不限制广告位 Load Cap，默认为 -1
                        </span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>广告位 Load Cap 的计时周期</label>
                        <label class="version_label">（SDK Version 5.6.2 及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="load_cap_time" min="0" value="{{ $data['load_cap_time'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">秒</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>有效缓存数量设置</label>
                        <label class="version_label">（SDK Version 5.6.2 及以上支持）</label>
                        <input class="form-control" type="number" name="cached_offers_num" min="0" value="{{ $data['cached_offers_num'] }}"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Bid Token缓存有效期</label><label class="version_label">（SDK Version 5.1.1及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="bid_token_cache_time" value="{{ $data['bid_token_cache_time'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Header Bidding 超时时间</label>
                        <div class="input-group">
                            <input class="form-control" type="number" step=100 name="header_bidding_timeout" value="{{ $data['header_bidding_timeout'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">milliseconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>WaterFall_HB 最大超时时间</label><label class="version_label">（SDK Version 5.5.7及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" step=1 name="hb_start_time" value="{{ $data['hb_start_time'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">milliseconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Bid 询价最大等待时间</label><label class="version_label">（SDK Version 5.5.7及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" step=1 name="hb_bid_timeout" value="{{ $data['hb_bid_timeout'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">milliseconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Bid 失败后下次 Bid 时间间隔</label><label class="version_label">（SDK Version 5.6.1及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" step=1 name="bid_fail_interval" value="{{ $data['bid_fail_interval'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>是否自动Request</label>
                        <div class="mt-3">
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="request_auto" type="radio" value="1" @if ($data['request_auto'] == 1) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">开启</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="request_auto" type="radio" value="0" @if ($data['request_auto'] == 0) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">关闭</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Native Template</label>
                        <div class="mt-3">
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="extra_template" type="radio" value="0" @if ($data['extra_template'] == 0) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Native Original</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="extra_template" type="radio" value="1" @if ($data['extra_template'] == 1) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Native Banner</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="extra_template" type="radio" value="2" @if ($data['extra_template'] == 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Native Splash</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Native Splash Template extra Parameter</label>
                        <textarea class="form-control" name="extra_parameter" type="text"
                            >@if ( $data['extra_parameter'] != null || $data['extra_parameter'] != ''){{$data['extra_parameter']}}@else{"pucs":1,"apdt":2,"aprn":6,"puas":1,"cdt":5,"ski_swt":1,"aut_swt":1}@endif</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>myOffer请求条数</label><label class="version_label">（SDK Version 5.0.0及以上支持）</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="my_offer_num" value="{{ $data['myoffer_num'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">条</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>是否使用兜底MyOffer</label><label class="version_label">（SDK Version 5.0.0及以上支持）</label>
                        <div class="mt-3">
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="use_my_offer_no_filled" type="radio" value="2" @if ($data['use_myoffer_nofilled'] == 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Yes</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="use_my_offer_no_filled" type="radio" value="1" @if ($data['use_myoffer_nofilled'] == 1) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">No</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="use_my_offer_no_filled" type="radio" value="3" @if ($data['use_myoffer_nofilled'] == 3) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Yes</span>
                                <label class="version_label">(isReady 返回 False，但可以 show。V5.5.4 及以上支持）</label>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>是否预加载MyOffer</label><label class="version_label">（SDK Version 5.0.0及以上支持）</label>
                        <div class="mt-3">
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="preload_my_offer" type="radio" value="2" @if ($data['preload_myoffer'] == 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Yes</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" name="preload_my_offer" type="radio" value="1" @if ($data['preload_myoffer'] != 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">No</span>
                            </label>
                        </div>
                    </div>
                </div>
{{--                <div class="form-row">--}}
{{--                    <div class="form-group col-md-6">--}}
{{--                        <label>同步展示广告对象给独立插件</label><label class="version_label">（SDK Version 5.4.0及以上支持）</label>--}}
{{--                        <div class="mt-3">--}}
{{--                            @foreach($showSyncSwitchMap as $status => $statusText)--}}
{{--                                <label class="custom-control custom-radio">--}}
{{--                                    <input class="custom-control-input" name="show_sync" type="radio" value="{{ $status }}" @if ($data['show_sync'] == $status) checked @endif>--}}
{{--                                    <span class="custom-control-indicator"></span>--}}
{{--                                    <span class="custom-control-description">{{ $statusText }}</span>--}}
{{--                                </label>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="form-row">--}}
{{--                    <div class="form-group col-md-6">--}}
{{--                        <label>同步点击广告对象给独立插件</label><label class="version_label">（SDK Version 5.4.0及以上支持）</label>--}}
{{--                        <div class="mt-3">--}}
{{--                            @foreach($clickSyncSwitchMap as $status => $statusText)--}}
{{--                                <label class="custom-control custom-radio">--}}
{{--                                    <input class="custom-control-input" name="click_sync" type="radio" value="{{ $status }}" @if ($data['click_sync'] == $status) checked @endif>--}}
{{--                                    <span class="custom-control-indicator"></span>--}}
{{--                                    <span class="custom-control-description">{{ $statusText }}</span>--}}
{{--                                </label>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>
                            点击Tracking服务器地址
                            <span style="color:#ff3111">（SDK Version 4.6.0及以上支持）</span>
                        </label>
                        <textarea class="form-control" name="click_address" type="text">{{ $data['click_address'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>
                            开源版点击Tracking服务器地址
                            <span style="color:#ff3111">（SDK Version 5.5.0及以上支持）</span>
                        </label>
                        <textarea class="form-control" name="opensource_click_address" type="text">{{ $data['opensource_click_address'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>
                            点击Tracking延时上报规则
                            <span style="color:#ff3111">（SDK Version 4.6.0及以上支持）</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">延迟</span>
                            </div>
                            <input class="form-control" type="number" min="-1" step="1" name="click_address_timeout_min" value="{{ $data['click_address_timeout_min'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">～</span>
                            </div>
                            <input class="form-control" type="number" min="-1" step="1" name="click_address_timeout_max" value="{{ $data['click_address_timeout_max'] }}"/>
                            <div class="input-group-append">
                                <span class="input-group-text">毫秒，上报</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>
                            广告展示收益同步给三方监测平台
                            <span style="color:#ff3111">（SDK Version 5.6.8及以上支持）</span>
                        </label>
                        <div class="input-group">
                            <textarea class="form-control"
                                      name="sync_ilrd_2_mmp"
                                      type="text">{{ $data['sync_ilrd_2_mmp'] }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-6">
                        <button class="btn btn-primary" type="submit">
                            Submit
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            $("input[name='click_address_timeout_min']").blur(function () {
                $("input[name='click_address_timeout_max']").prop('min', $(this).val());
            });
            $("input[name='click_address_timeout_max']").blur(function () {
                $("input[name='click_address_timeout_min']").prop('max', $(this).val());
            });
        });
    </script>

@endsection
