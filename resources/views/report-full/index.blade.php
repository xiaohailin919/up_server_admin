@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Full Report</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    Full Report
                    <small>（ID及Version支持多个搜索，用英文逗号隔开）</small>
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-12" id="group_by">
                            Group By:
                            <label for="group_date">
                                <input id="group_date" name="groups[]" value="date_time" @if(in_array('date_time', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('date_time', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>Date</span>
                            </label>
                            <label for="group_publisher">
                                <input id="group_publisher" name="groups[]" value="publisher_id" @if(in_array('publisher_id', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('publisher_id', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>Publisher</span>
                            </label>

                            <label for="group_app">
                                <input id="group_app" name="groups[]" value="app_id" @if(in_array('app_id', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('app_id', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>App</span>
                            </label>

                            <label for="group_placement">
                                <input id="group_placement" name="groups[]" value="placement_id" @if(in_array('placement_id', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('placement_id', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>Placement</span>
                            </label>

                            <label for="group_scenario">
                                <input id="group_scenario" name="groups[]" value="scenario" @if(in_array('scenario', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('scenario', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>Scenario</span>
                            </label>

                            <label for="group_unit">
                                <input id="group_unit" name="groups[]" value="unit_id" @if(in_array('unit_id', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('unit_id', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>AD Source</span>
                            </label>

                            <label for="group_area">
                                <input id="group_area" name="groups[]" value="geo_short" @if(in_array('geo_short', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('geo_short', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>Area</span>
                            </label>

                            <label for="group_format">
                                <input id="group_format" name="groups[]" value="format" @if(in_array('format', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('format', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>Format</span>
                            </label>

                            <label for="group_network">
                                <input id="group_network" name="groups[]" value="nw_firm_id" @if(in_array('nw_firm_id', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('nw_firm_id', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>Network</span>
                            </label>

                            <label for="group_group">
                                <input id="group_group" name="groups[]" value="group_id" @if(in_array('group_id', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('group_id', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>Segment</span>
                            </label>

                            <label for="group_sdk_version">
                                <input id="group_sdk_version" name="groups[]" value="sdk_version" @if(in_array('sdk_version', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('sdk_version', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>SDK Version</span>
                            </label>

                            <label for="group_app_version">
                                <input id="group_app_version" name="groups[]" value="app_version" @if(in_array('app_version', $pageAppends['groups'], true)) checked="checked" @endif type="checkbox">
                                <span @if(in_array('app_version', $pageAppends['groups'], true)) style="color:#2980b9;font-weight:bold;" @endif>APP Version</span>
                            </label>
                        </div>
                        <div class="form-group col-md-12" id="group_by_compare" style="display: none;">
                            Group By:
                            <label for="group_date">
                                <select name="group_compare" class="form-control" style="min-width: 126px;">
                                    <option value="date_time">Date</option>
                                    <option value="publisher_id" @if($pageAppends['group_compare'] == 'publisher_id') selected @endif>Publisher</option>
                                    <option value="app_id"       @if($pageAppends['group_compare'] == 'app_id') selected @endif>App</option>
                                    <option value="placement_id" @if($pageAppends['group_compare'] == 'placement_id') selected @endif>Placement</option>
                                    <option value="scenario"     @if($pageAppends['group_compare'] == 'scenario') selected @endif>Scenario</option>
                                    <option value="unit_id"      @if($pageAppends['group_compare'] == 'unit_id') selected @endif>AD Source</option>
                                    <option value="geo_short"    @if($pageAppends['group_compare'] == 'geo_short') selected @endif>Area</option>
                                    <option value="format"       @if($pageAppends['group_compare'] == 'format') selected @endif>Format</option>
                                    <option value="nw_firm_id"   @if($pageAppends['group_compare'] == 'nw_firm_id') selected @endif>Network</option>
                                    <option value="group_id"     @if($pageAppends['group_compare'] == 'group_id') selected @endif>Segment</option>
                                    <option value="sdk_version"  @if($pageAppends['group_compare'] == 'sdk_version') selected @endif>SDK Version
                                    <option value="app_version"  @if($pageAppends['group_compare'] == 'app_version') selected @endif>App Version</option>
                                </select>
                            </label>
                            <label>
                                提示：日期对比功能启用后，仅支持选择单一维度进行对比
                            </label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select name="timezone" class="form-control">
                                @foreach ($timezoneList as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($pageAppends['timezone']) && $key == $pageAppends['timezone']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control daterange-datepicker" type="text" name="daterange"
                                   value="{{ $dateRange['start'] }} - {{ $dateRange['end'] }}">
                        </div>
                        <div class="form-group col-md-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <input type="checkbox" name="compare_switch" value="2" @if($pageAppends['compare_switch'] == 2) checked @endif />
                                    </span>
                                </div>
                                <input class="form-control daterange-datepicker-compare" type="text" name="date_range_compare" disabled="disabled" placeholder="勾选启用日期对比功能" value="">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <div class="input-group">
                                <div class="input-group-prepend" title="反选">
                                    <span class="input-group-text">
                                        <input type="checkbox" name="publisher_exclude" value="2" @if($pageAppends['publisher_exclude'] == 2) checked @endif title="反选"/><span style="color: #8A8F96">&nbsp;&nbsp;反选</span>
                                    </span>
                                </div>
                                <input type="text" name="publisher_id" value="{{ $pageAppends['publisher_id'] }}" class="form-control" id="inputPublisherId" placeholder="Publisher ID">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_name" value="{{ $pageAppends['publisher_name'] }}" class="form-control" id="inputAppName" placeholder="Publisher Name">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $pageAppends['app_uuid'] }}" class="form-control" id="inputAppUuId" placeholder="App ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_name" value="{{ $pageAppends['app_name'] }}" class="form-control" id="inputAppName" placeholder="App Name">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_uuid" value="{{ $pageAppends['placement_uuid'] }}" class="form-control" id="inputPlacementUuid" placeholder="Placement ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="scenario_uuid" value="{{ $pageAppends['scenario_uuid'] }}" class="form-control" id="inputScenarioUuid" placeholder="Scenario ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="unit_id" value="{{ $pageAppends['unit_id'] }}" class="form-control" placeholder="AD Source ID">
                        </div>
                        <div class="form-group col-md-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <input id="geo_exclude" class="input-group-text" type="checkbox" name="geo_exclude" value="2" @if($pageAppends['geo_exclude'] == 2) checked @endif title="Area 反选">
                                    </span>
                                </div>
                                <input class="form-control" type="text" disabled="disabled" placeholder="Area 反选" title="Area 反选">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <select name="geo[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-Area-">
                                @foreach ($geoMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (in_array($key, $pageAppends['geo'], false)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="format" class="form-control">
                                <option value="all">-Format-</option>
                                @foreach ($formatMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($pageAppends['format']) && $key == $pageAppends['format']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="nw_firm_id" class="form-control" id="integrate-firms">
                                <option value="all" @if ($pageAppends['nw_firm_id'] === 'all') selected="selected" @endif>- 聚合广告平台 -</option>
                                @foreach ($nwFirmMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === (int)$pageAppends['nw_firm_id']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="nw_firm_id" class="form-control" id="custom-firms">
                                <option value="all" @if ($pageAppends['nw_firm_id'] === 'all') selected="selected" @endif>- 自定义广告平台 -</option>
                                @foreach ($customNwIdNameWithPublisherMap as $id => $nwFirmPublisher)
                                    <option value="{{ $id }}" @if ($id === (int)$pageAppends['nw_firm_id']) selected="selected" @endif>{{ $nwFirmPublisher['publisher_name'] . '(' . $nwFirmPublisher['publisher_id'] . ') | ' . $nwFirmPublisher['name'] . '(' . $id . ')' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="platform" class="form-control">
                                <option value="all" @if ($pageAppends['platform'] === 'all') selected="selected" @endif>- 系统平台 -</option>
                                @foreach ($platformMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key == $pageAppends['platform']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                            <div class="form-group col-md-2">
                                <input type="text" name="segment_id" value="{{ $pageAppends['segment_id'] }}" class="form-control" placeholder="Segment ID">
                            </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="sdk_version" value="{{ $pageAppends['sdk_version'] }}" class="form-control" placeholder="SDK Version">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_version" value="{{ $pageAppends['app_version'] }}" class="form-control" placeholder="APP Version">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="publisher_type" class="form-control">
                                <option value="all" @if ($pageAppends['publisher_type'] === 'all') selected="selected" @endif>- 开发者类型 -</option>
                                @foreach ($publisherTypeMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if ($key == $pageAppends['publisher_type']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="channel" class="form-control">
                                <option value="all" selected>- 渠道 -</option>
                                @foreach ($channelMap as $key => $val)
                                    <option value="{{ $key }}" @if ($pageAppends['channel'] === $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="is_cn_sdk" class="form-control">
                                @foreach ($sdkTypeMap as $key => $val)
                                    <option value="{{ $key }}" @if ($pageAppends['is_cn_sdk'] == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="app_type_id" class="form-control">
                                <option value="" selected>- 产品类型 -</option>
                                @foreach ($appTypeMap as $key => $val)
                                    <option value="{{ $key }}" @if ($pageAppends['app_type_id'] == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="app_label_id" class="form-control">
                                <option value="" selected>- 标签 -</option>
                                @foreach ($appLabelMap as $parentId => $parentInfo)
                                    <optgroup label="{{ $parentInfo['name'] }}">
                                        @foreach($parentInfo['children'] as $child)
                                            <option value="{{ $child['id'] }}" @if ($pageAppends['app_label_id'] == $child['id']) selected="selected" @endif>{{ $child['name'] }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                            <button type="submit" name="export" value="1" class="btn btn-success">
                                Export
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="row">
                    <div class="col-12 mb-3">
                        <button type="button" class="btn btn-info waves-effect waves-light float-right"
                                data-toggle="modal" data-target=".metrics-modal">Custom Metrics</button>
                    </div>
                </div>
                
                <div class="d-flex">
                    <div class="">
                        <table id="left-table" class="table table-striped" style="width: unset; border-right: 1px solid #e9ecef;">
                        <thead>
                            <tr>
                                @foreach($tableFilter2 as $field => $val)
                                    @if(in_array($field, $sortFields, false))
                                        @if(isset($pageAppends['order_by'][$field]) && $pageAppends['order_by'][$field] === 'desc')
                                            <th class="sort-field" style="height: 113px;">
                                                {!! $val['name'] !!}
                                                <a href="javascript:void(0);" class="sorting" data-field="{{ $field }}" data-sort="asc">
                                                    <i class="sort-field-icon fi-arrow-down"></i>
                                                </a>
                                            </th>
                                        @else
                                            <th class="sort-field" style="height: 113px;">
                                                {!! $val['name'] !!}
                                                <a href="javascript:void(0);" class="sorting" data-field="{{ $field }}" data-sort="desc">
                                                    <i class="sort-field-icon fi-arrow-up"></i>
                                                </a>
                                            </th>
                                        @endif
                                    @else
                                        @if($field === 'sdk_show')
                                            <th style="height: 113px;">
                                                {!! $val['name'] !!}
                                                <i class="dripicons-question" data-toggle="tooltip" title="对应开发者后台的展示数。iOS v410、安卓 v370及以上版本用Show，其余版本用Impression"></i>
                                            </th>
                                        @elseif(in_array('publisher_id', $val['fields'], true))
                                            <th style="height: 113px;">{!! $val['name'] !!} <i class="dripicons-warning" style="color: red;vertical-align: middle" data-toggle="tooltip" title="注意：1. 同时登陆多个账号，后登陆的会挤掉先登录的！2. 任何时候请不要删除或修改任何数据，除非是自己新建的数据！"></i></th>
                                        @else
                                            <th style="height: 113px;">{!! $val['name'] !!}</th>
                                        @endif
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @if($report)
                                @foreach($report as $key => $val)
                                    <tr>
                                        @foreach($tableFilter2 as $field => $val2)
                                            <td>
                                                @foreach($val2['fields'] as $val3)
                                                    <div class="text-truncate">
                                                        @if(isset($val[$val3]))
                                                            @if($val3 === 'app_name')
                                                                @if(isset($val['platform']) && (int)$val['platform'] === 1)
                                                                    <i class="mdi mdi-android" style="color: #a3c83e;"></i>
                                                                @else
                                                                    <i class="mdi mdi-apple"></i>
                                                                @endif
                                                                {{ $val[$val3] }}
                                                            @elseif($val3 === 'date_time' && !in_array('date_time', $pageAppends['groups'], false))
                                                                @if($dateRange['start'] === $dateRange['end'])
                                                                    {{ $dateRange['start'] }}
                                                                @else
                                                                    {{$dateRange['start']}} ~ {{$dateRange['end']}}
                                                                @endif
                                                            @elseif($val3 === 'publisher_id')
                                                                {{ $val[$val3] }}<br /><a href="/publisher/login?id={{ $val[$val3] }}" target="_blank"><small><i class="mdi mdi-login"></i> Login</small></a>
                                                            @elseif ($val3 === 'nw_firm_name')
                                                                @if($val['nw_firm_id'] > \App\Models\MySql\NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)
                                                                    {{ $customNwIdNameWithPublisherMap[$val['nw_firm_id']]['publisher_name'] . '(' . $customNwIdNameWithPublisherMap[$val['nw_firm_id']]['publisher_id'] . ') | ' . $val['nw_firm_name'] . '(' . $val['nw_firm_id'] . ')'  }}
                                                                @else
                                                                    {{ $val['nw_firm_name'] }}
                                                                @endif
                                                            @else
                                                                {{ $val[$val3] }}
                                                            @endif
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>

                                    @if((int)$pageAppends['compare_switch'] === 2)
                                        <tr>
                                            @foreach($tableFilter2 as $field => $val2)
                                                <td>
                                                    @foreach($val2['fields'] as $val3)
                                                        <div class="text-truncate">
                                                            @if(isset($reportCompare[$key][$val3]))
                                                                @if($val3 === 'app_name')
                                                                    @if(isset($reportCompare[$key]['platform']) && (int)$reportCompare[$key]['platform'] === 1)
                                                                        <i class="mdi mdi-android" style="color: #a3c83e;"></i>
                                                                    @else
                                                                        <i class="mdi mdi-apple"></i>
                                                                    @endif
                                                                    {{ $reportCompare[$key][$val3] }}
                                                                @elseif($val3 === 'date_time' && !in_array('date_time', $pageAppends['groups'], false))
                                                                    @if($dateRangeCompare['start'] === $dateRangeCompare['end'])
                                                                        {{ $dateRangeCompare['start'] }}
                                                                    @else
                                                                        {{ $dateRangeCompare['start'] }} ~ {{ $dateRangeCompare['end'] }}
                                                                    @endif
                                                                @else
                                                                    {{ $reportCompare[$key][$val3] }}
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </td>
                                            @endforeach
                                        </tr>

                                        <tr>
                                            @foreach($tableFilter2 as $field => $val2)
                                                <td style="border-bottom: 2px solid #bbb;">-</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    </div>
                    <div class="table-responsive" style="flex-grow: 1;">
                        <table class="table table-striped" id="right-table">
                            <thead>
                            <tr>
                                @foreach ($tableFields as $field => $name)
                                    @php($sdkDescription = '<i class="dripicons-question" data-toggle="tooltip" title="对应开发者后台的展示数。iOS v410、安卓 v370及以上版本用Show，其余版本用Impression"></i>')
                                    @if(!array_key_exists($field, $tableFilter))
                                        @if (in_array($field, $sortFields, false))
                                            @php($sorting = $pageAppends['order_by'][$field] ?? '')
                                            @if ($sorting === 'desc')
                                                <th class="sort-field">
                                                    {!! $name !!}
                                                    @if ($field === 'sdk_show'){!! $sdkDescription !!}@endif
                                                    <a href="javascript:void(0);" class="sorting" data-field="{{ $field }}" data-sort="asc"><i class="sort-field-icon fi-arrow-down"></i></a>
                                                </th>
                                            @else
                                                <th class="sort-field">
                                                    {!! $name !!}
                                                    @if ($field === 'sdk_show'){!! $sdkDescription !!}@endif
                                                    <a href="javascript:void(0);" class="sorting" data-field="{{ $field }}" data-sort="desc"><i class="sort-field-icon fi-arrow-up"></i></a>
                                                </th>
                                            @endif
                                        @else
                                            <th>
                                                {!! $name !!}
                                                @if ($field === 'sdk_show'){!! $sdkDescription !!}@endif
                                            </th>
                                        @endif
                                    @endif
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @if ($report)
                                @foreach($report as $key => $val)
                                    <tr>
                                        @foreach($tableFields as $field => $name)
                                            @if (!array_key_exists($field, $tableFilter))
                                                @if (!isset($val[$field]))
                                                    <td>-</td>
                                                @else
                                                    @if (in_array($field, $tableMoney, false))
                                                        <td>{{ '$' . number_format((float)$val[$field], 2) }}</td>
                                                    @elseif (in_array($field, $tableRate, false))
                                                        <td>{{ round($val[$field], 4) * 100 . '%' }}</td>
                                                    @else
                                                        <td>{{ number_format($val[$field]) }}</td>
                                                    @endif
                                                @endif
                                            @endif
                                        @endforeach
                                    </tr>

                                    {{-- 对比 --}}
                                    @if ((int)$pageAppends['compare_switch'] === 2)
                                        <tr>
                                            @foreach($tableFields as $field => $name)
                                                @if (!array_key_exists($field, $tableFilter))
                                                    @if (!isset($reportCompare[$key][$field]))
                                                        <td>-</td>
                                                    @else
                                                        @if (in_array($field, $tableMoney, false))
                                                            <td>{{ '$' . number_format($reportCompare[$key][$field], 2) }} </td>
                                                        @elseif (in_array($field, $tableRate, false))
                                                            <td>{{ round($reportCompare[$key][$field], 4) * 100 . '%' }} </td>
                                                        @else
                                                            <td>{{ number_format($reportCompare[$key][$field]) }} </td>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach($tableFields as $field => $name)
                                                @if (!array_key_exists($field, $tableFilter))
                                                    @php($v = $reportCompareResult[$key][$field] ?? '-')
                                                    <td style="border-bottom: 2px solid #bbb; color: {{ $v < 0 ? '#37b57c' : '#fe3939' }};">{{ $v }}</td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $report->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $report->appends($pageAppends)->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade metrics-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 class="modal-title" id="myLargeModalLabel" style="display: inline-block; width: 75%">Custom Metrics</h5>
                    <a href="javascript:" id="select-all">全选</a> /
                    <a href="javascript:" id="select-inverse">反选</a> /
                    <a href="javascript:" id="select-none" style="margin-right: 5%">清空</a>
                </div>
                <div class="modal-body">
                    {{ Form::model([], array('url' => "/metrics-setting/full-report", 'method' => 'POST', 'id' => 'editForm')) }}
                    <div class="form-row">
                        @foreach($metricsFields as $metric)
                            @if (preg_match('/--[(a-z)|(_)]*_division/', $metric['field']))
                                <div class="col-12">
                                    <hr/>
                                </div>
                            @else
                                <div class="col-4">
                                    <label class="custom-control custom-checkbox">
                                        <input name="metrics_setting[]" type="checkbox" id="metrics_setting_{{ $metric['id'] }}" value="{{ $metric['id'] }}" class="custom-control-input" @if(empty($metricsSettingIds) || in_array($metric['id'], $metricsSettingIds, false))checked="checked" @endif>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ str_replace('SDK ', '', $metric['name']) }}</span>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="form-row">
                        <div class="col-12 text-right">
                            <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <script>
        var dateStart = moment("{{ $dateRange['start'] }}").format('MM/DD/YYYY'); // moment().subtract(7, 'days').format("MM/DD/YYYY");
        var dateEnd   = moment("{{ $dateRange['end'] }}").format('MM/DD/YYYY'); //

        var dateStartCompare = moment().subtract(14, 'days').format("MM/DD/YYYY");
        var dateEndCompare   = moment().subtract(8, 'days').format("MM/DD/YYYY");

        var dateStertCompareInput = "{{ $dateRangeCompare['start'] }}";
        var dateEndCompareInput   = "{{ $dateRangeCompare['end'] }}";

        if(dateStertCompareInput){
            dateStartCompare = moment(dateStertCompareInput).format('MM/DD/YYYY');
        }
        if(dateEndCompareInput){
            dateEndCompare = moment(dateEndCompareInput).format('MM/DD/YYYY');
        }

        var compareSwitch = {{ $pageAppends['compare_switch'] }};

        function onIntegrateNwFirmIdSelect() {
            console.log('test1')
            if ($('#integrate-firms').val() != 'all') {
                $('#custom-firms').val('all');
                $('#custom-firms').attr('disabled', true);
            } else {
                $('#custom-firms').attr('disabled', false);
            }
        }

        function onCustomNwFirmIdSelect() {
            console.log('test2');
            if ($('#custom-firms').val() != 'all') {
                $('#integrate-firms').val('all');
                $('#integrate-firms').attr('disabled', true);
            } else {
                $('#integrate-firms').attr('disabled', false);
            }
        }

        jQuery(document).ready(function () {
            onIntegrateNwFirmIdSelect();
            onCustomNwFirmIdSelect();
            $('#integrate-firms').change(onIntegrateNwFirmIdSelect);
            $('#custom-firms').change(onCustomNwFirmIdSelect);
            $(".sorting").each(function () {
                $(this).click(function () {
                    var href = window.location.href.replace(/(\?|&)order_by\[(\w+)\]=(\w+)/g, '');
                    var field = $(this).attr('data-field');
                    var sort = $(this).attr('data-sort');
                    window.location.href = href + (href.indexOf('?') <= -1 ? '?' : '&') + 'order_by[' + field + ']=' + sort;
                });
            });

            $('#left-table th').css('height', $('#right-table th').css('height'));

            $('#left-table tr').each(function(index){
                $('#right-table tr').eq(index).height($(this).height());
            });

            setTimeout(function(){
                $('#left-table tr').each(function(index){
                    $('#right-table tr').eq(index).height($(this).height());
                });
            }, 3000);

            $('#right-table tbody tr').mouseenter(function() {
                $('#left-table tbody tr').eq($(this).index()).css('background', '#f5f5f5')
            });
            $('#left-table tbody tr').mouseenter(function() {
                $('#right-table tbody tr').eq($(this).index()).css('background', '#f5f5f5')
            });

            $('#right-table tbody tr').mouseleave(function() {
                $('#left-table tbody tr').eq($(this).index()).css('background', '')
            });
            $('#left-table tbody tr').mouseleave(function() {
                $('#right-table tbody tr').eq($(this).index()).css('background', '')
            });

            // 对比数据
            $("input[name='compare_switch']").click(function(){
                initCompareSwitch();
            });
            initCompareSwitch(true);

            // date range
            $('.daterange-datepicker').daterangepicker({
                format: 'mm/dd/yyyy',
                minDate: "01/01/2018",
                maxDate: moment(),
                ranges: {
                    '@lang('Today')': [moment(), moment()],
                    '@lang('Yesterday')': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '@lang('Last :x Days', ['x' => 7])': [moment().subtract(6, 'days'), moment()],
                    '@lang('Last :x Days', ['x' => 14])': [moment().subtract(13, 'days'), moment()],
                    '@lang('Last :x Days', ['x' => 30])': [moment().subtract(29, 'days'), moment()],
                    '@lang('This Month')': [moment().startOf('month'), moment().endOf('month')],
                    '@lang('Last Month')': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    customRangeLabel: 'Custom'
                },
                showCustomRangeLabel: true,
                showDropdowns: false,
                alwaysShowCalendars: true,
                autoUpdateInput: false,
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-success',
                cancelClass: 'btn-light'
            }, function(start, end, label) {
                dateStart = start.format('MM/DD/YYYY');
                dateEnd   = end.format('MM/DD/YYYY');
                $('.daterange-datepicker').val(dateStart + ' - ' + dateEnd);
                initDateRangeCompare(true);
            });

            $("#select-all").click(function() {
                $("input[name='metrics_setting[]']:checkbox").each(function(){
                    $(this).prop("checked", true);
                });
            });

            $("#select-inverse").click(function() {
                $("input[name='metrics_setting[]']:checkbox").each(function() {
                    if($(this).prop("checked") === false){
                        $(this).prop("checked", true);
                    }else{
                        $(this).prop("checked", false);
                    }

                });
            });

            $("#select-none").click(function() {
                $("input[name='metrics_setting[]']:checkbox").each(function() {
                    $(this).prop("checked", false);
                });
            });
        });

        function initDateRangeCompare(change = false){
            if(compareSwitch != 2){
                return;
            }
            if(change){
                const diff = moment(dateEnd).diff(moment(dateStart), 'day');
                dateStartCompare = moment(dateStart).subtract(diff + 1, 'day').format('MM/DD/YYYY');
                dateEndCompare   = moment(dateEnd).subtract(diff + 1, 'day').format('MM/DD/YYYY');
            }
            $('.daterange-datepicker-compare').daterangepicker({
                format: 'mm/dd/yyyy',
                startDate: dateStartCompare,
                endDate: dateEndCompare,
                minDate: "01/01/2018",
                maxDate: moment(),
                ranges: {
                    '@lang('Today')': [moment(), moment()],
                    '@lang('Yesterday')': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '@lang('Last :x Days', ['x' => 7])': [moment().subtract(6, 'days'), moment()],
                    '@lang('Last :x Days', ['x' => 14])': [moment().subtract(13, 'days'), moment()],
                    '@lang('Last :x Days', ['x' => 30])': [moment().subtract(29, 'days'), moment()],
                    '@lang('This Month')': [moment().startOf('month'), moment().endOf('month')],
                    '@lang('Last Month')': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    customRangeLabel: 'Custom'
                },
                showCustomRangeLabel: true,
                showDropdowns: false,
                alwaysShowCalendars: true,
                autoUpdateInput: false,
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-success',
                cancelClass: 'btn-light',
                callback4SetStartDate: (value) => {
                    const dateStartCompare = moment(value);
                    const diff = moment(dateEnd).diff(moment(dateStart), 'day');
                    if (dateStartCompare.isAfter(moment().subtract(diff, 'day'))) {
                        $('.daterange-datepicker-compare').data('daterangepicker').setStartDate(moment().subtract(diff, 'day'));
                        $('.daterange-datepicker-compare').data('daterangepicker').setEndDate(moment());
                    } else {
                        const dateEndCompare = dateStartCompare.add(diff, 'day');
                        console.log($('.daterange-datepicker-compare'));
                        $('.daterange-datepicker-compare').data('daterangepicker').setEndDate(dateEndCompare);
                    }
                    $('.daterange-datepicker-compare').data('daterangepicker').clickApply();
                }
            }, function(start, end, label) {
                $('.daterange-datepicker-compare').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
            });
            let title = moment(dateStartCompare).format('MM/DD/YYYY') + ' - ' + moment(dateEndCompare).format('MM/DD/YYYY');
            $('.daterange-datepicker-compare').val(title).attr('title', title);
        }

        function initCompareSwitch(init = false){
            if($("input[name='compare_switch']").is(':checked')){
                compareSwitch = 2;
                $("input[name='date_range_compare']").prop('disabled', false);
                $("#group_by_compare").show();
                $("#group_by").hide();
                // date range for compare
                if(!init){
                    initDateRangeCompare(true);
                }else{
                    let title = moment(dateStartCompare).format('MM/DD/YYYY') + ' - ' + moment(dateEndCompare).format('MM/DD/YYYY');
                    $('.daterange-datepicker-compare').val(title).attr('title', title);
                    initDateRangeCompare();
                }
            }else{
                compareSwitch = 1;
                $("input[name='date_range_compare']").prop('disabled', true).val('');
                $("#group_by_compare").hide();
                $("#group_by").show();
            }
        }
    </script>
@endsection
