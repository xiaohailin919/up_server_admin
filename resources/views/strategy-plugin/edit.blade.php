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
                            <a href="{{ URL::to('strategy-app') }}">Manage Ads Visibility SDK Strategy</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Ads Visibility SDK Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Ads Visibility SDK Strategy</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-plugin.update', $data['id']), 'method' => 'PUT')) }}

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>消息通知名称列表</label><label class="version_label">（SDK Version 5.2.1及以上支持）</label>
                        <textarea name="notice_list" type="text" class="form-control">{{ $data['notice_list'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>PKG匹配列表</label><label class="version_label">（SDK Version 5.4.0及以上的广告可视化插件支持）</label>
                        <textarea name="package_match_list" placeholder="json格式字符串" type="text" class="form-control">{{ $data['package_match_list'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>PKG上报服务器地址</label><label class="version_label">（SDK Version 5.4.0及以上的广告可视化插件支持）</label>
                        <textarea name="package_upload_address_list" type="text" class="form-control">{{ $data['package_upload_address_list'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6{{ $errors->has('report_tk_params') ? ' has-error' : '' }}">
                        <label for="r-tk-p-input">上报数据类型</label>
                        <input id="r-tk-p-input" type="text" name="report_tk_params" value="{{ $data['report_tk_params'] }}" placeholder='Json 类型字符串，如：["i_nm","i_t"]' class="form-control">
                        @if ($errors->has('report_tk_params'))
                            <span class="help-block">
                                <strong>{{ $errors->first('report_tk_params') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>
                            PKG延时上报规则
                            <label class="version_label">（SDK Version 5.4.0及以上的广告可视化插件支持）</label>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">延迟</span>
                            </div>
                            <input type="number" step="1" name="pkg_address_timeout_min" value="{{ $data['pkg_address_timeout_min'] }}"
                                   class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text">～</span>
                            </div>
                            <input type="number" step="1" name="pkg_address_timeout_max" value="{{ $data['pkg_address_timeout_max'] }}"
                                   class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text">毫秒，上报</span>
                            </div>
                        </div>
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
