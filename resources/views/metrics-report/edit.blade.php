@extends('layouts.admin')

@section('content')
    <style>
        .mt-3 {
            margin: 0!important;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('/metrics-report') }}">Report Metrics</a></li>
                        <li class="breadcrumb-item active">Edit Report Metrics</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Report Metrics</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="/metrics-report/{{ $data['id'] }}">

                    <input name="_method" type="hidden" value="PUT">

                    {{ csrf_field() }}

                    <div class="form-row">
                        <div class="form-group col-md-3{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name-input">
                                指标名称
                                <span style="color:#ff3111">（请谨慎修改）</span>
                            </label>
                            <input id="name-input" name="name" type="text" placeholder="Metric Name" value="{{ old('name') ?: $data['name'] }}" class="form-control"/>
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3{{ $errors->has('field') ? ' has-error' : '' }}">
                            <label for="field-input">
                                指标字段
                                <span style="color:#ff3111">（请谨慎修改）</span>
                            </label>
                            <input id="field-input" name="field" type="text" placeholder="Metric Field" value="{{ old('field') ?: $data['field'] }}" class="form-control"/>
                            @if ($errors->has('field'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('field') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="kind-select">Kind</label>
                            <select class="form-control" id="kind-select" disabled>
                                @foreach ($kindMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === $data['kind']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Is Default</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input name="is_default" type="radio" value="0" class="custom-control-input" @if($data['is_default'] === 0) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input name="is_default" type="radio" value="1" class="custom-control-input" @if($data['is_default'] === 1) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">YES</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="group-input">指标分组</label>
                            <input id="group-input" name="group" class="form-control" type="number" step="1" value="{{ $data['group'] }}" required/>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="show-priority-input">
                                弹窗排序
                                <span style="color:#bf8f40">（请在外部修改）</span>
                            </label>
                            <input id="show-priority-input" name="show_priority" class="form-control" type="number" value="{{ $data['show_priority'] }}" disabled/>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="priority-input">
                                报表排序
                                <span style="color:#bf8f40">（请在外部修改）</span>
                            </label>
                            <input id="priority-input" name="priority" class="form-control" type="number" value="{{ $data['priority'] }}" disabled/>
                        </div>
                    </div>

                    <div class="form-row" {{ $errors->has('all') ? '' : 'hidden' }}>
                        <div class="form-group col-md-3 has-error">
                            @if ($errors->has('all'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('all') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection