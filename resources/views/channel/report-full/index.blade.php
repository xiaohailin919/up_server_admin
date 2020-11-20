@extends('layouts.channel')

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
                <h4 class="page-title">Full Report</h4>
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

                            <label for="group_format">
                                <input id="group_format" name="groups[]" value="format"
                                       <?php if (in_array('format', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('format', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Format<?php if (in_array('format', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_area">
                                <input id="group_area" name="groups[]" value="geo_short"
                                       <?php if (in_array('geo_short', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('geo_short', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Area<?php if (in_array('geo_short', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_network">
                                <input id="group_network" name="groups[]" value="nw_firm_id"
                                       <?php if (in_array('nw_firm_id', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('nw_firm_id', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                Network<?php if (in_array('nw_firm_id', $groups)) echo '</span>';?>
                            </label>

                            <label for="group_unit">
                                <input id="group_unit" name="groups[]" value="unit_id"
                                       <?php if (in_array('unit_id', $groups)) echo 'checked="checked"'; ?> type="checkbox">
                                <?php if (in_array('unit_id', $groups)) echo '<span style="color:#2980b9;font-weight:bold;">'; ?>
                                AD Source<?php if (in_array('unit_id', $groups)) echo '</span>';?>
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
                                <input type="text" name="unit_id" value="{{ $unitId }}" class="form-control"
                                       placeholder="AD Source ID">
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
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <?php
                            $sortFields = [
                                'date_time',
                                'sdk_request',
                                'sdk_filled_request',
                                'sdk_impression',
                                'sdk_click',
                                'sdk_rv_start',
                                'sdk_rv_complete',
                                'api_request',
                                'api_filled_request',
                                'api_impression',
                                'api_click',
                                'api_rv_start',
                                'api_rv_complete',
                                'revenue',
                            ];
                            if ($showFields) {
                                foreach ($showFields as $field) {
                                    if (in_array($field, $sortFields)) {
                                        $sorting = isset($orderBy[$field]) ? $orderBy[$field] : '';
                                        if ($sorting == 'desc') {
                                            $sort = '<a href="javascript:void(0);" class="sorting" data-field="' . $field . '" data-sort="asc"><i class="sort-field-icon fi-arrow-down" /></a>';
                                        } else {
                                            $sort = '<a href="javascript:void(0);" class="sorting" data-field="' . $field . '" data-sort="desc"><i class="sort-field-icon fi-arrow-up" /></a>';
                                        }
                                        echo '<th class="sort-field">' . $tableFields[$field] . $sort . '</th>';
                                    } else {
                                        if($field == "sdk_show") {
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
        });
    </script>
@endsection
