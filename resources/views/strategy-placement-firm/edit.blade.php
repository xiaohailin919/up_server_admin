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
                            <a href="{{ URL::to('strategy-placement-firm') }}">Placement Firm Strategy</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Placement Firm Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Placement Firm Strategy</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-placement-firm.update', $data['id']), 'method' => 'PUT')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwCacheTime">Network缓存时间</label>
                        <div class="input-group">
                            <input type="number" name="nw_cache_time" value="{{ $data['nw_cache_time'] }}"
                                   class="form-control" id="inputNwCacheTime"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwTimeout">Network广告素材超时时间（原Network超时时间）</label>
                        <div class="input-group">
                            <input type="number" name="nw_timeout" value="{{ $data['nw_timeout'] }}"
                                   class="form-control" id="inputNwTimeout"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwTimeout">Network广告数据超时时间</label><label class="version_label">（SDK Version
                            5.1.0及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="ad_data_nw_timeout" value="{{ $data['ad_data_nw_timeout'] }}"
                                   class="form-control" id="inputNwTimeout"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputLoadSuccessUpStatus">Ad Source 维度Up_status有效期</label><label
                                class="version_label">（SDK Version 5.1.0及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="ad_up_status"
                                   value="{{ $data['ad_up_status'] }}" class="form-control"
                                   id="inputLoadSuccessUpStatus"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwOfferRequests">Network下的Offer请求条数</label>
                        <input type="number" name="nw_offer_requests" value="{{ $data['nw_offer_requests'] }}"
                               class="form-control" id="inputNwOfferRequests"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <div>
                            <label class="custom-control custom-radio">
                                <input name="status" type="radio" value="2" class="custom-control-input"
                                       @if ($data['status'] == 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">On</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input name="status" type="radio" value="1" class="custom-control-input"
                                       @if ($data['status'] != 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Off</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputLoadSuccessUpStatus">Header Bidding 超时时间</label>
                        <div class="input-group">
                            <input type="number" step=100 name="header_bidding_timeout"
                                   value="{{ $data['header_bidding_timeout'] }}" class="form-control"
                                   id="inputLoadSuccessUpStatus"/>
                            <div class="input-group-append">
                                <span class="input-group-text">milliseconds</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputLoadSuccessUpStatus">Bid Token缓存有效期</label><label class="version_label">（SDK
                            Version 5.1.1及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="bid_token_cache_time"
                                   value="{{ $data['bid_token_cache_time'] }}" class="form-control"
                                   id="inputLoadSuccessUpStatus"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
{{--                dev20200612 已废弃配置项--}}
{{--                <div class="form-row">--}}
{{--                    <div class="form-group col-md-6">--}}
{{--                        <label>同步展示广告对象给独立插件</label><label class="version_label">（SDK Version 5.4.0及以上支持）</label>--}}
{{--                        <div class="mt-3">--}}
{{--                            @foreach($showSyncSwitchMap as $status => $statusText)--}}
{{--                                <label class="custom-control custom-radio">--}}
{{--                                    <input name="show_sync" type="radio" id="video_clickable_{{$status}}" value="{{ $status }}" class="custom-control-input"　@if ($data['show_sync'] == $status) checked @endif >--}}
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
{{--                                    <input name="click_sync" type="radio" id="video_clickable_{{$status}}" value="{{ $status }}" class="custom-control-input"　@if ($data['click_sync'] == $status) checked @endif >--}}
{{--                                    <span class="custom-control-indicator"></span>--}}
{{--                                    <span class="custom-control-description">{{ $statusText }}</span>--}}
{{--                                </label>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection
