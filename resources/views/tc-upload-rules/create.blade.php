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
                            <a href="{{ URL::to('upload-rules') }}">Upload Rules</a>
                        </li>
                        <li class="breadcrumb-item active">Add Upload Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">Add TC Upload Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('tc-upload-rule.store'), 'method' => 'POST')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>App ID</label>
                        <input type="text" class="form-control" name="app_uuid" value="" placeholder="请填写App Uuid" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>类名黑名单</label>
                        <textarea name="cnslst" placeholder="每行一个类名" type="text" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>URL域名白名单</label>
                        <textarea name="wdlst" placeholder="每行一个域名" type="text" class="form-control">{{ $data['wdlst'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">TC总开关概率</label>
                        <div class="input-group">
                            <input type="number" name="tc_switch" min="0" max="100" id="tc_switch" value="0" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">收集Webview URL的域名开关概率</label>
                        <div class="input-group">
                            <input type="number" name="collect_webview_switch" min="0" max="100" id="collect_webview_switch" value="0" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">收集Storekit的Apple id开关概率 </label>
                        <div class="input-group">
                            <input type="number" name="collect_storekit_switch" min="0" max="100" id="collect_storekit_switch" value="0" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">收集外跳openurl开关概率</label>
                        <div class="input-group">
                            <input type="number" name="collect_open_url_switch" min="0" max="100" id="collect_open_url_switch" value="0" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
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
