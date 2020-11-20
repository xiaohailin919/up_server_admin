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
                            <a href="{{ URL::to('strategy-placement-my-offer') }}">MyOffer Strategy</a>
                        </li>
                        <li class="breadcrumb-item active">Add MyOffer Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit MyOffer Strategy</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-placement-my-offer.update', $data['id']), 'method' => 'PUT')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Placement ID</label>
                        <input name="placement_id" type="text" value="{{ $data['placement_uuid'] }}" disabled class="form-control"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>myOffer素材加载超时时间</label><label style="color:#ff3111">（SDK Version 5.0.0及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="material_timeout" value="{{ $data['material_timeout'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>秒</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>点击视频可跳转</label><label style="color:#ff3111">（SDK Version 5.0.0及以上支持，仅RV&插屏生效）</label>
                        <div class="mt-3">
                            @foreach($videoClickableSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="video_clickable_switch" type="radio" id="video_clickable_{{$status}}" value="{{ $status }}" class="custom-control-input"　@if ($data['video_clickable'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>视频播放N秒后出现Banner</label><label style="color:#ff3111">（SDK Version 5.0.0及以上支持，仅RV&插屏生效）</label>
                        <div class="input-group">
                            <input type="number" name="show_banner_time" value="{{ $data['show_banner_time'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>秒</small></span>
                            </div>
                        </div>
                        <label style="color:#ff3111;font-weight:bold">注：为-1时，不自动展示，但用户点击时会展示（现有逻辑）。为-2时，一直不展示。为0时，视频播放时立即展示</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>EndCard点击区域控制</label><label style="color:#ff3111">（SDK Version 5.0.0及以上支持，仅RV&插屏生效）</label>
                        <div class="mt-3">
                            @foreach($endCardClickAreaMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="end_card_click_area" type="radio" id="end_card_click_area_{{$status}}" value="{{ $status }}" class="custom-control-input"　@if ($data['endcard_click_area'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>播放视频声音</label><label style="color:#ff3111">（SDK Version 5.0.0及以上支持，仅RV&插屏生效）</label>
                        <div class="mt-3">
                            @foreach($videoMuteSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="video_mute_switch" type="radio" id="video_mute_{{$status}}" value="{{ $status }}" class="custom-control-input"　@if ($data['video_mute'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>视频开始播放N秒后展示关闭按钮</label><label style="color:#ff3111">（SDK Version 5.0.0及以上支持，仅RV&插屏生效）</label>
                        <div class="input-group">
                            <input type="number" name="show_close_time" value="{{ $data['show_close_time'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>秒</small></span>
                            </div>
                        </div>
                        <label style="color:#ff3111;font-weight:bold">注：为0时，开始播放即显示；为-1时，不显示关闭按钮</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>myOffer缓存有效时间</label><label style="color:#ff3111">（SDK Version 5.0.0及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="offer_cache_time" value="{{ $data['offer_cache_time'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>秒</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Android国内版本APK下载二次确认</label>
                        <label style="color:#ff3111">（SDK Version 5.6.6及以上支持）</label>
                        <div class="input-group">
                            @foreach($apkDownloadConfirmMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="apk_download_confirm" type="radio"
                                           id="apk_download_confirm_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"　@if ($data['apk_download_confirm'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>StoreKit加载时机</label>
                        <label style="color:#ff3111">（SDK Version 5.6.6及以上支持）</label>
                        <div class="input-group">
                            @foreach($storeKitTimeMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="storekit_time" type="radio"
                                           id="storekit_time_{{$status}}"
                                           value="{{ $status }}"
                                           class="custom-control-input"　@if ($data['storekit_time'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach ($statusMap as $key => $val)
                                <option value="{{ $key }}" @if ($data['status'] == $key) selected="selected" @endif>{{ $val }}</option>
                            @endforeach
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
@endsection

@section('extra_js')
    @include('layouts.upload_extra_js')
@endsection
