@extends('layouts.admin')

@section('content')
    <link href="{{ asset('css/jquery-clockpicker.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery-clockpicker.min.js') }}"></script>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('/network-crawl') }}">API 数据拉取配置</a></li>
                        <li class="breadcrumb-item active">编辑 API 数据拉取配置</li>
                    </ol>
                </div>
                <h4 class="page-title">编辑 API 数据拉取配置</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="{{ \Illuminate\Support\Facades\URL::to('/network-crawl/' . $data['id']) }}">
                    <input name="_method" value="PUT" type="hidden">
                    <input name="full_url" type="hidden" value="{{ request()->fullUrl() }}"/>
                    {{ csrf_field() }}
                    <div class="form-row">
                        <label class="col-md-1">广告平台</label>
                        <div class="form-group">
                            <input class="form-control" value="{{ $nwFirmMap[$data['nw_firm_id']] }}" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">时间维度</label>
                        <div class="form-group">
                            <select class="form-control" name="type" disabled>
                                <option value="1" @if($data['type'] === 1) selected @endif>天维度</option>
                                <option value="2" @if($data['type'] === 2) selected @endif>小时维度</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">拉取时间</label>
                        <div class="form-group">
                            <div class="input-group clockpicker" data-placement="right" data-align="top" data-autoclose="true">
                                <input class="form-control" name="schedule_time" type="text" value="{{ $data['schedule_time'] }}">
                                <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">拉取范围</label>
                        <div class="form-group">
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="pull_type" type="radio" value="1" @if($data['pull_type'] === 1) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">昨天</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="pull_type" type="radio" value="2" @if($data['pull_type'] === 2) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">前天</span>
                                </label>
                            </div>
                        </div>
                    </div>
{{--                    <div class="form-row">--}}
{{--                        <label class="col-md-1">状态</label>--}}
{{--                        <div class="form-group">--}}
{{--                            <select class="form-control" name="status">--}}
{{--                                @foreach($statusMap as $key => $val)--}}
{{--                                    <option value="{{ $key }}" @if($data['status'] === $key) selected @endif>{{ $val  }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="form-row">
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.clockpicker').clockpicker();
        });
    </script>
@endsection
