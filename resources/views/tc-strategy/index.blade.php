@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">TC Rate Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">TC Rate Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select class="form-control" name="rule_type">
                                <option value="ALL" >All Rules</option>
                                @foreach ($ruleTypeMap as $ruleType)
                                    <option value="{{ $ruleType }}" @if ($ruleType === $pageAppends['rule_type'])  selected @endif>{{ $ruleType }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" type="text" name="app_id" placeholder="App ID" value="{{ $pageAppends['app_id'] }}">
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" type="text" name="app_name" placeholder="App Name" value="{{ $pageAppends['app_name'] }}">
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" type="text" name="placement_id" placeholder="Placement ID" value="{{ $pageAppends['placement_id'] }}">
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" type="text" name="placement_name" placeholder="Placement Name" value="{{ $pageAppends['placement_name'] }}">
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="nw_firm_id">
                                <option value="" >All Network</option>
                                @foreach ($nwFirmMap as $key => $val)
                                    <option value="{{ $key }}" @if (is_numeric($pageAppends['nw_firm_id']) && $key === (int)$pageAppends['nw_firm_id']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="status">
                                <option value="{{ \App\Models\MySql\TcStrategy::STATUS_ALL }}" >All Status</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if (is_numeric($pageAppends['status']) && $key === (int)$pageAppends['status']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ Illuminate\Support\Facades\URL::to('tc-strategy/create') }}" class="btn btn-info">Add</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>规则类型</th>
                            <th>系统平台</th>
                            <th>App</th>
                            <th>Placement</th>
                            <th>广告平台</th>
                            @foreach($typeMap as $key => $value)
                                <th style="max-width: 108px;min-width: 93px;text-align: center">{{ $value }}</th>
                            @endforeach
                            <th>Manager</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>
                                    @if ($val['placement_id'] !== \App\Models\MySql\TcStrategy::PLACEMENT_UNSET)
                                        Placement
                                    @elseif ($val['app_id'] !== \App\Models\MySql\TcStrategy::APP_UNSET)
                                        APP
                                    @else
                                        Platform
                                    @endif
                                </td>
                                <td>
                                    @if($val['platform_type'] === \App\Models\MySql\TcStrategy::PLATFORM_IOS)
                                        <i class="mdi mdi-apple"></i>
                                    @else
                                        <i class="mdi mdi-android" style="color: #a3c83e;"></i>
                                    @endif
                                    {{ $allPlatformMap[$val['platform_type']] }}
                                </td>
                                <td>
                                    @if($val['app_id'] === \App\Models\MySql\TcStrategy::APP_UNSET) - @else {{ $val['app_name'] }} @endif
                                    <br/>
                                    @if($val['app_id'] === \App\Models\MySql\TcStrategy::APP_UNSET) - @else {{ $val['app_uuid'] }} @endif
                                </td>
                                <td>
                                    @if($val['placement_id'] === \App\Models\MySql\TcStrategy::PLACEMENT_UNSET) - @else {{ $val['placement_name'] }} @endif
                                    <br/>
                                    @if($val['placement_id'] === \App\Models\MySql\TcStrategy::PLACEMENT_UNSET) - @else {{ $val['placement_uuid'] }} @endif
                                </td>
                                <td>{{ $nwFirmMap[$val['nw_firm_id']] }}</td>
                                <td style="text-align: center">{{ $val['impression_to_plugin'] }}%</td>
                                <td style="text-align: center">{{ $val['click_to_plugin'] }}%</td>
                                <td style="text-align: center">{{ $val['impression_to_qcc'] }}%</td>
                                <td style="text-align: center">{{ $val['click_to_qcc'] }}%</td>
                                <td>{{ $val['admin_name'] }}<br/>{{ $val['update_time'] }}</td>
                                <td>{{ $statusMap[$val['status']] }}</td>
                                <td><a href="{{ Illuminate\Support\Facades\URL::to('tc-strategy/'. $val['id'] . '/edit' . $uri) }}" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Edit</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $data->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $data->appends($pageAppends)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
