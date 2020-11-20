@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active"> Ads Visibility SDK Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Ads Visibility SDK Strategy</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model(null, array('route' => array('strategy-plugin.store'), 'method' => 'POST')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>消息通知名称列表</label>
                        <textarea name="notice_list" type="text" class="form-control">{{ $noticeList }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputplatform">平台</label>
                        <select name="platform" class="form-control">
                            <option value="1">Android</option>
                            <option value="2">Ios</option>
                        </select>
                    </div>
                </div>
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
                        <label>PKG匹配列表</label><label class="version_label">（SDK Version 5.4.0及以上的广告可视化插件支持）</label>
                        <textarea name="package_match_list" placeholder="json格式字符串" type="text" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>PKG上报服务器地址</label><label class="version_label">（SDK Version 5.4.0及以上的广告可视化插件支持）</label>
                        <textarea name="package_upload_address_list" type="text" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6{{ $errors->has('report_tk_params') ? ' has-error' : '' }}">
                        <label for="r-tk-p-input">上报数据类型</label>
                        <input id="r-tk-p-input" type="text" name="report_tk_params" placeholder='Json 类型字符串，如：["i_nm","i_t"]' class="form-control">
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
                            <input type="number" step="1" name="pkg_address_timeout_min" value="2000"
                                   class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text">～</span>
                            </div>
                            <input type="number" min="-1" step="1" name="pkg_address_timeout_max" value="5000"
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
    <script>
        function updateStatus(id, status) {
            $.ajax({
                url: "{{ URL::to('strategy-plugin') }}/" + id,
                async: true,
                dataType: 'json',
                type: 'PUT',
                data: {id: id, status: status},

                success: function (data, status) {
                    if (data.status == 1) {
                        location.reload();
                    }
                },

                error: function (jqXHR, status, errorThrown) {
                    alert("Please try again");
                }
            });
        }

        (function ($) {

        });
    </script>

@endsection
