@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('/report-unit-log') }}">Manage Report API</a></li>
                        <li class="breadcrumb-item active">Manage Report API</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Manage Report API</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="{{ \Illuminate\Support\Facades\URL::to('/report-unit-log') }}" autocomplete="off">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <label class="col-md-1">Publisher Id</label>
                        <div class="form-group col-md-5">
                            <div class="input-group">
                                <input class="form-control{{ $errors->has('publisher_id') ? ' has-error' : '' }}" name="publisher_id" type="text" value="{{ old('publisher_id') }}" placeholder="可选，默认全部开发者"/>
                                <span class="input-group-addon small"><small><i class="dripicons-question" data-toggle="tooltip" title="多个开发者 ID 请按英文逗号隔开"></i></small></span>
                            </div>
                            @if ($errors->has('publisher_id'))
                                <span class="help-block text-danger">
                                <strong>{{ $errors->first('publisher_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">广告平台</label>
                        <div class="form-group col-md-5" >
                            <select class="form-control" name="nw_firm_id">
                                @foreach ($networkFirmNameMap as $key => $val)
                                    <option value="{{$key}}" @if($key === (int)old('nw_firm_id')) selected @endif>{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">时间维度</label>
                        <div class="form-group col-md-5">
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" name="pull_type[]" value="1" type="checkbox" checked>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">天维度</span>
                            </label>
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" name="pull_type[]" value="2" type="checkbox" checked>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">小时维度</span>
                            </label>
                            @if ($errors->has('pull_type'))
                                <span class="help-block text-danger">
                                    <strong>{{ $errors->first('pull_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">开始日期</label>
                        <div class="form-group{{ $errors->has('sdate') ? ' has-error' : '' }} col-md-5">
                            <input class="form-control input-datetimepicker" name="sdate" type="text" value="{{ old('sdate') }}" placeholder="YYYY/MM/DD" required>
                            @if ($errors->has('sdate'))
                                <span class="help-block text-danger">
                                    <strong>{{ $errors->first('sdate') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">结束日期</label>
                        <div class="form-group{{ $errors->has('edate') ? ' has-error' : '' }} col-md-5">
                            <input class="form-control input-datetimepicker" name="edate" type="text" value="{{ old('edate') }}" placeholder="YYYY/MM/DD" required>
                            @if ($errors->has('edate'))
                                <span class="help-block text-danger">
                                    <strong>{{ $errors->first('edate') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">备注</label>
                        <div class="form-group col-md-5">
                            <span>Facebook 的分小时数据只能拉取最近两天，请注意选择日期</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
