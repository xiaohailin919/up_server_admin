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
                        <div class="form-group col-md-12">
                            Group By:
                            <label for="group_date">
                                <input id="group_date" name="groups[]" value="date_time"
                                       <?php if (in_array('date_time', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('date_time', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Date <?php if (in_array('date_time', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_publisher">
                                <input id="group_publisher" name="groups[]" value="publisher_id"
                                       <?php if (in_array('publisher_id', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('publisher_id', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Publisher <?php if (in_array('publisher_id', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_app">
                                <input id="group_app" name="groups[]" value="app_id"
                                       <?php if (in_array('app_id', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('app_id', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                App <?php if (in_array('app_id', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_placement">
                                <input id="group_placement" name="groups[]" value="placement_id"
                                       <?php if (in_array('placement_id', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('placement_id', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Placement <?php if (in_array('placement_id', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_unit">
                                <input id="group_unit" name="groups[]" value="unit_id"
                                       <?php if (in_array('unit_id', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('unit_id', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                AD Source<?php if (in_array('unit_id', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_area">
                                <input id="group_area" name="groups[]" value="geo_short"
                                       <?php if (in_array('geo_short', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('geo_short', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Area<?php if (in_array('geo_short', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_format">
                                <input id="group_format" name="groups[]" value="format"
                                       <?php if (in_array('format', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('format', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Format<?php if (in_array('format', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_network">
                                <input id="group_network" name="groups[]" value="nw_firm_id"
                                       <?php if (in_array('nw_firm_id', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('nw_firm_id', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Network<?php if (in_array('nw_firm_id', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_group">
                                <input id="group_group" name="groups[]" value="group_id"
                                       <?php if (in_array('group_id', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('group_id', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Segment<?php if (in_array('group_id', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_sdk_version">
                                <input id="group_sdk_version" name="groups[]" value="sdk_version"
                                       <?php if (in_array('sdk_version', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('sdk_version', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                SDK Version<?php if (in_array('sdk_version', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_app_version">
                                <input id="group_app_version" name="groups[]" value="app_version"
                                       <?php if (in_array('app_version', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('app_version', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                APP Version<?php if (in_array('app_version', $groups)) echo '</span>';?>
                            </label>
                        </div>
                    </div>
                    <div class="form-row">
                        @if($useReportApiV2)
                        <div class="form-group col-md-2">
                            <select name="timezone" class="form-control">
                                @foreach ($timezoneList as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($timezone) && $key == $timezone) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="form-group col-md-2">
                            <input class="form-control input-daterange-datepicker" type="text" name="daterange"
                                   value="{{ $dateRange['start'] }} - {{ $dateRange['end'] }}">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control"
                                   id="inputPublisherId" placeholder="Publisher ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_name" value="{{ $publisherName }}" class="form-control"
                                   id="inputAppName" placeholder="Publisher Name">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $appUuid }}" class="form-control"
                                   id="inputAppUuId" placeholder="App ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_name" value="{{ $appName }}" class="form-control"
                                   id="inputAppName" placeholder="App Name">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_uuid" value="{{ $placementUuid }}" class="form-control"
                                   id="inputPlacementUuid" placeholder="Placement ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="unit_id" value="{{ $unitId }}" class="form-control"
                                   placeholder="AD Source ID">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="geo[]" class="form-control select2 select2-multiple select2-hidden-accessible"
                                    multiple="" data-placeholder="-Area-">
                                @foreach ($geoMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (in_array($key, $geo)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="format" class="form-control">
                                <option value="all">-Format-</option>
                                @foreach ($formatMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($format) && $key == $format) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="nw_firm_id" class="form-control">
                                <option value="all" @if ($nwFirmId === 'all') selected="selected" @endif>-Network-
                                </option>
                                @foreach ($nwFirmMap as $key => $val)
                                    <option value="{{ $val['id'] }}"
                                            @if ($val['id'] == $nwFirmId) selected="selected" @endif>{{ $val['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="platform" class="form-control">
                                <option value="all" @if ($platform === 'all') selected="selected" @endif>-Platform-
                                </option>
                                @foreach ($platformMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if ($key == $platform) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                            <div class="form-group col-md-2">
                                <input type="text" name="segment_id" value="{{ $segmentId }}" class="form-control"
                                       placeholder="Segment ID">
                            </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="sdk_version" value="{{ $sdkVersion }}" class="form-control"
                                   placeholder="SDK Version">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_version" value="{{ $appVersion }}" class="form-control"
                                   placeholder="APP Version">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="publisher_type" class="form-control">
                                <option value="all" @if ($nwFirmId === 'all') selected="selected" @endif>-ALL Publisher
                                    Type-
                                </option>
                                @foreach ($publisherTypeMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if ($key == $publisherType) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                            <div class="form-group col-md-2">
                                <select name="channel" class="form-control">
                                    <option value="all" selected>-All Channel-</option>
                                    @foreach ($channelMap as $key => $val)
                                        <option value="{{ $key }}" @if ($channel === $key) selected="selected" @endif>{{ $val }}</option>
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
                    <table id="left-table" class="table table-striped" style="width: unset; border-right: 1px solid #e9ecef;">
                        <thead>
                            <tr>
                                <?php
                                if ($showFields) {
                                    foreach ($showFields as $field) {
                                        if(!in_array($field, array_keys($tableFilter))){
                                            continue;
                                        }
                                        if (in_array($field, $sortFields)) {
                                            $sorting = isset($orderBy[$field]) ? $orderBy[$field] : '';
                                            if ($sorting == 'desc') {
                                                $sort = '<a href="javascript:void(0);" class="sorting" data-field="' . $field . '" data-sort="asc"><i class="sort-field-icon fi-arrow-down" /></a>';
                                            } else {
                                                $sort = '<a href="javascript:void(0);" class="sorting" data-field="' . $field . '" data-sort="desc"><i class="sort-field-icon fi-arrow-up" /></a>';
                                            }
                                            echo '<th class="sort-field" style="height: 113px;">' . $tableFields[$field] . $sort . '</th>';
                                        } else if(in_array($field, array_keys($tableFields))) {
                                            if($field == "sdk_show"){
                                                echo '<th style="height: 113px;">' . $tableFields[$field] . '<i class="dripicons-question" data-toggle="tooltip" title="对应开发者后台的展示数。iOS v410、安卓 v370及以上版本用Show，其余版本用Impression"></th>';
                                            } else {
                                                echo '<th style="height: 113px;">' . $tableFields[$field] . '</th>';
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($report && $showFields) {
                                foreach ($report as $val) {
                                    echo '<tr>';
                                    foreach ($showFields as $field) {
                                        if(!in_array($field, array_keys($tableFilter))){
                                            continue;
                                        }
                                        if($field == 'app_name'){
                                            if($val['platform'] == 'Android'){
                                                $icon = '<i class="mdi mdi-android" style="color: #a3c83e;"></i>';
                                            }else{
                                                $icon = '<i class="mdi mdi-apple"></i>';
                                            }
                                            echo '<td>' . $icon . ' ' . $val[$field] . '</td>';
                                        }else{
                                            $v = isset($val[$field]) ? $val[$field] : '-';
                                            echo '<td>' . $v . '</td>';
                                        }
                                    }
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="table-responsive" style="flex-grow: 1;">
                        <table class="table table-striped" id="right-table">
                            <thead>
                            <tr>
                                <?php
                                if ($showFields) {
                                    foreach ($showFields as $field) {
                                        if(in_array($field, array_keys($tableFilter))){
                                            continue;
                                        }
                                        if (in_array($field, $sortFields)) {
                                            $sorting = isset($orderBy[$field]) ? $orderBy[$field] : '';
                                            if ($sorting == 'desc') {
                                                $sort = '<a href="javascript:void(0);" class="sorting" data-field="' . $field . '" data-sort="asc"><i class="sort-field-icon fi-arrow-down" /></a>';
                                            } else {
                                                $sort = '<a href="javascript:void(0);" class="sorting" data-field="' . $field . '" data-sort="desc"><i class="sort-field-icon fi-arrow-up" /></a>';
                                            }
                                            echo '<th class="sort-field">' . $tableFields[$field] . $sort . '</th>';
                                        } else if(in_array($field, array_keys($tableFields))) {
                                            if($field == "sdk_show"){
                                                echo '<th>' . $tableFields[$field] . '<i class="dripicons-question" data-toggle="tooltip" title="对应开发者后台的展示数。iOS v410、安卓 v370及以上版本用Show，其余版本用Impression"></th>';
                                            } else {
                                                echo '<th>' . $tableFields[$field] . '</th>';
                                            }

                                        }
                                    }
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($report && $showFields) {
                                foreach ($report as $val) {
                                    echo '<tr>';
                                    foreach ($showFields as $field) {
                                        if(in_array($field, array_keys($tableFilter))){
                                            continue;
                                        }
                                        $v = isset($val[$field]) ? $val[$field] : '-';
                                        echo '<td>' . $v . '</td>';
                                    }
                                    echo '</tr>';
                                }
                            }
                            ?>
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
                    <h5 class="modal-title" id="myLargeModalLabel">Custom Metrics</h5>
                </div>
                <div class="modal-body">
                    {{ Form::model([], array('url' => "/metrics-setting/full-report", 'method' => 'POST', 'id' => 'editForm')) }}
                    <div class="form-row">
                        @foreach($metricsFields as $val)
                            <div class="col-4">
                                <label class="custom-control custom-checkbox">
                                    <input name="metrics_setting[]"
                                           type="checkbox"
                                           id="metrics_setting_{{ $val['id'] }}"
                                           value="{{ $val['id'] }}"
                                           class="custom-control-input"
                                           @if(empty($metricsSettingIds) || in_array($val['id'], $metricsSettingIds))
                                                checked="checked"
                                           @endif
                                    >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $val['name'] }}</span>
                                </label>
                            </div>
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
        jQuery(document).ready(function () {
            $(".sorting").each(function () {
                $(this).click(function () {
                    var href = window.location.href.replace(/(\?|&)order_by\[(\w+)\]=(\w+)/g, '');
                    var field = $(this).attr('data-field');
                    var sort = $(this).attr('data-sort');
                    console.log(href);
                    console.log(field);
                    window.location.href = href + (href.indexOf('?') <= -1 ? '?' : '&') + 'order_by[' + field + ']=' + sort;
                });
            });

            $('#left-table th').css('height', $('#right-table th').css('height'));

            $('#left-table tr').each(function(index){
                $('#right-table tr').eq(index).height($(this).height());
            });

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
        });
    </script>
@endsection
