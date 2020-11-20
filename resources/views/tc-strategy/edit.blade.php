@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('tc-strategy') }}">TC Rate Rules</a></li>
                        <li class="breadcrumb-item active">Edit TC Rate Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit TC Rate Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="/tc-strategy/{{ $data['id'] }}">
                    <input name="_method" type="hidden" value="PUT">
                    {{ csrf_field() }}
                    <input name="index_rule_type"      value="{{ app('request')->input('rule_type') }}"      hidden>
                    <input name="index_app_id"         value="{{ app('request')->input('app_id') }}"         hidden>
                    <input name="index_app_name"       value="{{ app('request')->input('app_name') }}"       hidden>
                    <input name="index_placement_id"   value="{{ app('request')->input('placement_id') }}"   hidden>
                    <input name="index_placement_name" value="{{ app('request')->input('placement_name') }}" hidden>
                    <input name="index_nw_firm_id"     value="{{ app('request')->input('nw_firm_id') }}"     hidden>
                    <input name="index_status"         value="{{ app('request')->input('status') }}"         hidden>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>规则类型</label>
                            <input class="form-control" type="text" value="{{ $ruleTypeMap[$ruleType] }}" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4" id="system-platform-group" @if($ruleType !== 0) hidden @endif>
                            <label>系统平台</label>
                            <input class="form-control" type="text" value="{{ $platformMap[$data['platform_type']] }}" disabled>
                        </div>
                    </div>
                    <div class="form-row" id="app-id-input-group" @if($ruleType !== 1) hidden @endif>
                        <div class="form-group col-md-4">
                            <label>App ID</label>
                            <input class="form-control" type="text" value="{{ $data['app_id'] }}" disabled>
                        </div>
                    </div>
                    <div class="form-row" id="placement-id-input-group" @if($ruleType !== 2) hidden @endif>
                        <div class="form-group col-md-4">
                            <label>Placement ID</label>
                            <input class="form-control" type="text" value="{{ $data['placement_id'] }}" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Network Firm</label>
                            <input class="form-control" type="text" value="{{ $nwFirmMap[$data['nw_firm_id']] }}" disabled>
                        </div>
                    </div>
                    @foreach($typeMap as $key => $val)
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="type-{{ $key }}-input">{{ $val }}</label><br/>
                                <div class="input-group" style="width: 35%;display: inline-flex">
                                    <input id="type-{{ $key }}-input" name="type_{{ $key }}" type="number" value="{{ $types[$key] }}" max="100" min="0" class="form-control" required/>
                                    <div class="input-group-append">
                                        <span class="input-group-text small"><small>%</small></span>
                                    </div>
                                </div>
                                <small>(0 ~ 100%，默认 100%，0% = 关，100% = 开)</small>
                            </div>
                        </div>
                    @endforeach
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="status-select">状态</label>
                            <select id="status-select" name="status" class="form-control">
                                @foreach($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if ($data['status'] === $key) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    <p id="show"></p>
                </form>
            </div>
        </div>
    </div>
@endsection