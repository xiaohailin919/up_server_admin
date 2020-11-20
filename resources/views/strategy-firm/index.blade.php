@extends('layouts.admin')

@section('content')
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">广告平台策略设置</li>
                    </ol>
                </div>
                <h4 class="page-title">广告平台策略设置</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <div class="input-group">
                                <input class="form-control" name="placement_id" type="text" placeholder="Placement ID" value="{{ $placementId }}">
                                <span class="input-group-addon small"><i class="dripicons-question" data-toggle="tooltip" title="输入 0 以搜索 Platform 类型数据"></i></span>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <input name="app_id" type="text" placeholder="App ID" value="{{ $appId }}" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <input name="publisher_id" type="number" placeholder="Publisher ID" value="{{ $publisherId }}" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="format" class="form-control">
                                <option value="{{ \App\Models\MySql\StrategyFirm::ALL_FORMAT - 1 }}" >- 广告类型 -</option>
                                <option value="{{ \App\Models\MySql\StrategyFirm::ALL_FORMAT }}" @if (isset($pageAppends['format']) && \App\Models\MySql\StrategyFirm::ALL_FORMAT === (int)$pageAppends['format']) selected @endif>全部</option>
                                @foreach ($formatMap as $key => $val)
                                    <option value="{{ $key }}" @if (isset($pageAppends['format']) && $key === (int)$pageAppends['format']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="platform" class="form-control">
                                <option value="{{ \App\Models\MySql\StrategyFirm::ALL_PLATFORM }}" >- 系统平台 -</option>
                                @foreach ($platformMap as $key => $val)
                                    <option value="{{ $key }}" @if (isset($pageAppends['platform']) && $key === (int)$pageAppends['platform']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="nw_firm" class="form-control">
                                <option value="{{ \App\Models\MySql\StrategyFirm::ALL_NW_FIRM - 1 }}" >- 广告平台 -</option>
                                <option value="{{ \App\Models\MySql\StrategyFirm::ALL_NW_FIRM }}" @if (isset($pageAppends['nw_firm']) && \App\Models\MySql\StrategyFirm::ALL_NW_FIRM === (int)$pageAppends['nw_firm']) selected @endif>全部</option>
                                @foreach ($nwFirmMap as $key => $val)
                                    <option value="{{ $key }}" @if (isset($pageAppends['nw_firm']) && $key === (int)$pageAppends['nw_firm']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="{{ \App\Models\MySql\StrategyFirm::ALL_STATUS }}" >- 状态 -</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if (isset($pageAppends['status']) && $key === (int)$pageAppends['status']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <a href="{{ Illuminate\Support\Facades\URL::to('strategy-firm/create') }}" class="btn btn-info">添加</a>
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
                                <th>广告位</th>
                                <th>应用</th>
                                <th>开发者</th>
                                <th>系统平台</th>
                                <th>广告类型</th>
                                <th>广告平台</th>
                                <th>状态</th>
                                <th>管理员</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $val)
                                <tr>
                                    <td>
                                        @if ($val['placement_id'] !== \App\Models\MySql\StrategyFirm::ALL_PLACEMENT)
                                            Placement
                                        @else
                                            Platform
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($val['placement_uuid']) && $val['placement_uuid'] !== '')
                                            {{ $val['placement_name'] }}<br/>{{ $val['placement_uuid'] }}
                                        @else
                                            -<br/>-
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($val['app_uuid']) && $val['app_uuid'] !== '')
                                            {{ $val['app_name'] }}<br/>{{ $val['app_uuid'] }}
                                        @else
                                            -<br/>-
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($val['publisher_id']) && $val['publisher_id'] !== '')
                                            {{ $val['publisher_name'] }}<br/>ID: {{ $val['publisher_id'] }}
                                        @else
                                            -<br/>-
                                        @endif
                                    </td>
                                    <td>
                                        @if ($val['platform'] === \App\Models\MySql\StrategyFirm::PLATFORM_IOS)
                                            <i class="mdi mdi-apple"></i> IOS
                                        @else
                                            <i class="mdi mdi-android" style="color: #a3c83e;"></i> Android
                                        @endif
                                    </td>
                                    <td>
                                        {{ (int)$val['format'] === \App\Models\MySql\StrategyFirm::ALL_FORMAT ? '全部' : $formatMap[$val['format']] }}
                                    </td>
                                    <td>
                                        {{ (int)$val['nw_firm_id'] === \App\Models\MySql\StrategyFirm::ALL_NW_FIRM ? '全部' : $nwFirmMap[$val['nw_firm_id']] }}
                                    </td>
                                    <td>
                                        {{ $statusMap[$val['status']] }}
                                    </td>
                                    <td>
                                        {{ isset($val['admin_name']) && $val['admin_name'] !== '' ? $val['admin_name'] : '-' }}
                                        <br/>
                                        {{ $val['time'] }}
                                    </td>
                                    <td>
                                        <a href="{{ Illuminate\Support\Facades\URL::to('strategy-firm/copy/' . $val['id']) }}" class="btn btn-outline-info waves-light waves-effect w-sm btn-sm">
                                            复制
                                        </a>
                                        <a href="{{ Illuminate\Support\Facades\URL::to('strategy-firm/'. $val['id'] . '/edit' . $uri) }}" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">
                                            编辑
                                        </a>
                                    </td>
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
