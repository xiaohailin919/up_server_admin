@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Chart Report</li>
                    </ol>
                </div>
                <h4 class="page-title">Chart Report</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET" id="searchForm">
                    <div class="form-row">
                        <span class="custom-control-description"> &nbsp; &nbsp;Type: &nbsp;&nbsp;&nbsp;</span>
                        <label class="custom-control custom-radio">
                            <input name="report_type" type="radio" value="1" class="custom-control-input"
                                   @if($reportType == 1) checked @endif>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Trend&nbsp;<i class="dripicons-question" data-toggle="tooltip" title="对比一段连续时间内的数据指标变化"></i></span>

                        </label>
                        <label class="custom-control custom-radio">
                            <input name="report_type" type="radio" value="2" class="custom-control-input"
                                   @if($reportType == 2) checked @endif>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Comparision&nbsp;<i class="dripicons-question" data-toggle="tooltip" title="对比同一时间点，不同日期的数据指标变化"></i></span>
                        </label>
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
                        <div class="form-group col-md-2" id="daterange_container">
                            <input class="form-control input-daterange-datepicker" type="text" name="daterange"
                                   id="daterange"
                                   value="{{ $dateRange['start'] }} - {{ $dateRange['end'] }}">
                        </div>
                        <div class="form-group col-md-2" id="date_container" style="display: none">
                            <input class="form-control" type="text" name="date_multi" id="date_multi">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control"
                                   id="inputPublisherId" placeholder="Publisher ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $appUuid }}" class="form-control"
                                   id="inputAppUuId" placeholder="App ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_uuid" value="{{ $placementUuid }}" class="form-control"
                                   id="inputPlacementUuid" placeholder="Placement ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="scenario_uuid" value="{{ $scenarioUuid }}" class="form-control"
                                   id="inputScenarioUuid" placeholder="Scenario ID">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="format" class="form-control">
                                <option value="all">-- AD Format --</option>
                                @foreach ($formatMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($format) && $key == $format) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="geo[]" class="form-control select2 select2-multiple select2-hidden-accessible"
                                    multiple="" data-placeholder="Area">
                                @foreach ($geoMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (in_array($key, $geo)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                            <div class="form-group col-md-2">
                                <select name="nw_firm_id" class="form-control" id="integrate-firms">
                                    <option value="all" @if ($nwFirmId === 'all') selected="selected" @endif>- 聚合广告平台 -</option>
                                    @foreach ($nwFirmMap as $key => $val)
                                        <option value="{{ $key }}" @if ($key === (int)$nwFirmId) selected="selected" @endif>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <select name="nw_firm_id" class="form-control" id="custom-firms">
                                    <option value="all" @if ($nwFirmId === 'all') selected="selected" @endif>- 自定义广告平台 -</option>
                                    @foreach ($customNwIdNameWithPublisherMap as $id => $nwFirmPublisher)
                                        <option value="{{ $id }}" @if ($id === (int)$nwFirmId) selected="selected" @endif>{{ $nwFirmPublisher['publisher_name'] . '(' . $nwFirmPublisher['publisher_id'] . ') | ' . $nwFirmPublisher['name'] . '(' . $id . ')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="unit_id" value="{{ $unitId }}" class="form-control"
                                   placeholder="AD Source ID">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="platform" class="form-control">
                                <option value="all" @if ($platform === 'all') selected="selected" @endif>-- Platform --
                                </option>
                                @foreach ($platformMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if ($key == $platform) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="group_id" value="{{ $groupId }}" class="form-control"
                                   id="inputSegmentId" placeholder="Segment ID">
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
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            {{--height:380vh;--}}
            <div class="card-box" id="report_box" style="display: none;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <div class="float-left">
                        <a href="#" data-toggle="modal" data-target="#CustomMetricDlg"> Custom Metrics</a>
                    </div>
                </div>
                @if($reportType == 1)
                    <canvas id="canvas" height="100"></canvas>
                @else
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist"></div>
                        <div class="tab-content" id="nav-tabContent"></div>
                @endif
            </div>

            <div class="card-box">
                <div class="modal fade" id="CustomMetricDlg" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-lg modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Custom Metrics</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="d-block">
                                            &nbsp;&nbsp;
                                            <div class="float-right">
                                                <a href="#" id="selectAll">全选</a> /
                                                <a href="#" id="selectInverse">反选</a> /
                                                <a href="#" id="selectDefault">默认</a>
                                            </div>
                                        </label>
                                        <div class="row">
                                            @foreach($metricsCfg as $metricName => $val)
                                                <div class="col-4">
                                                    <label class="custom-control custom-checkbox">
                                                        <input name="metric[]"
                                                               type="checkbox"
                                                               id="metric_{{ $metricName }}"
                                                               value="{{ $metricName }}"
                                                               class="custom-control-input"
                                                               @if ($val['default']) data-default="1" @endif >
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-description">{{ $val['text'] }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="saveToLocal()">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="float-right">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="#" style="display: inline-block; width: 100px;" onclick="hideReport()">
                            <span class="pointer">
                                <i class="mdi mdi-chart-bar"></i> <span id="hide_chart"></span>
                            </span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="#" style="display: inline-block; width: 130px;" onclick="requestToExport()">
                                    <input id="export_btn"  hidden value="0"/>
                            <span class="pointer">
                                <i class="mdi mdi-download"></i> Export to Excel
                            </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Hour</th>
                            <th>App Strategy Request</th>
                            <th>Placement Strategy Request</th>
                            <th>Load</th>
                            <th>Load FilledRate</th>
                            <th>Request</th>
                            <th>Request FilledRate</th>
                            <th>Show&nbsp;<i class="dripicons-question" data-toggle="tooltip" title="对应开发者后台的展示数。iOS v410、安卓 v370及以上版本用Show，其余版本用Impression"></i></th>
                            <th>Impression</th>
                            <th>Click</th>
                            <th>CTR</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($excelReportData as $index => $reportData)
                            <tr>
                                <td>{{ $reportData['date_time'] }}</td>
                                <td>{{ $reportData['hour'] }}</td>
                                <td class="number_format">{{ $reportData['app_strategy_request'] }}</td>
                                <td class="number_format">{{ $reportData['placement_strategy_request'] }}</td>
                                <td class="number_format">{{ $reportData['loads'] }}</td>
                                <td>{{ $reportData['loads_filled_rate_val'] }}</td>
                                <td class="number_format">{{ $reportData['request'] }}</td>
                                <td>{{ $reportData['request_filled_rate_val'] }}</td>
                                <td class="number_format">{{ $reportData['show'] }}</td>
                                <td class="number_format">{{ $reportData['impression'] }}</td>
                                <td class="number_format">{{ $reportData['click'] }}</td>
                                <td>{{ $reportData['ctr_val'] }}</td>
                            </tr>
                        @endforeach
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
    <style>
        .form-group .ng-button {
            display: none;
        }

        .custom_ng_button {
            border-radius: 0px;
            border-left-width: 0px;
            cursor: pointer;
            display: inline-block;
            padding: 5px;
            position: relative;
            text-align: center;
            background: #eeeeee;
            color: #000000;
            font-weight: 300;
            text-decoration: none;
            border-top: solid #acacac 1px;
            border-right: solid #acacac 1px;
            border-bottom: solid #acacac 1px;
            transition: background-color 0.1s, border-color 0.1s;
            white-space: nowrap;
            -webkit-appearance: none;
            vertical-align: middle;
            margin-right: 5px;
        }
    </style>
    <script src="{{ asset('plugins/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('plugins/chart.js/utils.js') }}"></script>
    <script src="{{ asset('js/ng_all.js') }}"></script>
    <script src="{{ asset('js/ng_ui.js') }}"></script>
    <script src="{{ asset('js/ng_ui.js') }}"></script>
    <script src="{{ asset('js/components/calendar.js') }}"></script>
    <script>
        var dateTimeRangeList;

        function formatCash(str) {
            return str.split('').reverse().reduce((prev, next, index) => {
                return ((index % 3) ? next : (next + ',')) + prev
            })
        }

        function switchDateComponent(reportType) {
            switch (reportType) {
                case "1":
                    $("#daterange_container").show();
                    $("#date_container").hide();
                    break;
                case "2":
                    $("#daterange_container").hide();
                    $("#date_container").show();
                    break;
                default:
                    break;
            }
        }

        function saveToLocal() {
            // localStorage
            if (!window.localStorage) {
                return false;
            }

            localStorage.clear();// reset
            console.log("after clear localstorage:", localStorage)
            // var metricList = Array();
            $('input[name="metric[]"]:checked').each(function(){
                // metricList.push($(this).val());
                localStorage.setItem($(this).val(), "1")
            });

            let reportType = "{{ $reportType }}";
            renderChartReportByType(reportType);
            return false
        }

        function getCustomMetrics() {
            var metricKeyList = Array();
            for(var i=0; i < window.metricCfg.length; i++){
                // console.log("window.metricCfg[i]:", window.metricCfg[i])
                var metric = window.metricCfg[i];
                if(localStorage.hasOwnProperty(metric)){
                    metricKeyList.push(metric);
                }
            }
            /*for(var i=0; i < localStorage.length; i++){
                if(localStorage.key(i) != "hide_report") {
                    metricKeyList.push(localStorage.key(i));
                }
            }*/

            if(metricKeyList.length == 0){
                $('input[name="metric[]"]').each(function(index, ele){
                    if($(ele).attr("data-default") == 1){
                        metricKeyList.push($(ele).val());
                        localStorage.setItem($(ele).val(), "1") // update the localStorage which will be used at next time( when invoking this function)
                    }
                });
            }
            return metricKeyList;
        }

        function renderTrendReport(reportData) {
            if(reportData.length == 0){
                return false;
            }

            let config = {
                type: 'line',
                data: {
                    labels: reportData.date_time_hour,
                    datasets: [],
                },
                options: {
                    responsive: true,
                    // maintainAspectRatio:false,
                    title: {
                        display: false,
                        text: ''
                    },
                    tooltips: {
                        mode: 'label',
                        intersect: false,
                        position: 'nearest'
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    scales: {
                        tickets: false,
                        xAxes: [{
                            gridLines: {
                                display: false,//竖线
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Date'
                            }
                        }],
                        // yAxes: Array(),
                        yAxes: [{
                            ticks: {
                                display: true
                            },
                            position: 'left',
                            scaleShowLabels: {display: false, drawBorder: false,},
                            gridLines: {
                                display: true,
                                drawBorder: false,
                            },
                        },/*{
                            ticks: {
                                display: false
                            },
                            position: 'right',
                            scaleShowLabels: {display: false, drawBorder: false,},
                            gridLines: {
                                display: false,
                                drawBorder: false,
                            },
                        }*/]
                    }
                }
            };

            var metricKeyList = getCustomMetrics();
            console.log("metricKeyList:", metricKeyList)

            var showTicketYAxes = false
            if(metricKeyList.length <= 2){
                showTicketYAxes = true
                config.options.scales.yAxes = Array();
            }

           var postionMap = {
                "0": "left",
                "1": "right"
            }
            metricKeyList.forEach(function (key, index) {
                console.log("key:", key)
                if (key != "date_time_hour") {
                    var colorNames = Object.keys(window.chartColors);
                    var colorName = colorNames[config.data.datasets.length % colorNames.length];
                    var newColor = window.chartColors[colorName];

                    var dataSetCfg = {
                        label: window.metricKeyCfg[key],
                        backgroundColor: newColor,
                        borderColor: newColor,
                        data: reportData[key],
                        fill: false,
                        // yAxisID: "yAxes_id_" + key
                    }
                    if(showTicketYAxes){
                        dataSetCfg["yAxisID"] = "yAxes_id_" + key
                    }
                    config.data.datasets.push(dataSetCfg);

                    if(showTicketYAxes){
                        config.options.scales.yAxes.push({
                            ticks: {
                                display: showTicketYAxes
                            },
                            position: postionMap[index],
                            scaleShowLabels: {display: false, drawBorder: false,},
                            id: "yAxes_id_" + key,
                            gridLines: {
                                display: true,
                                drawBorder: false,
                            },
                        })
                    }
                }
            });

            if(window.trendChartReport){
                window.trendChartReport.destroy()
            }
            let ctx = document.getElementById('canvas').getContext('2d');
            window.trendChartReport = new Chart(ctx, config);
            window.trendChartReport.update();
        }

        function renderTabComparisionReport(reportData, forceReload) {
            if(reportData.length == 0){
                return false;
            }

            console.log("renderTabComparisionReport:", reportData)
            var $tabs = $('#nav-tab').children('a');
            $tabs.each(function () {
                var tabName = $(this).attr("id")
                // console.log("tabName:", tabName)
                // console.log("hour_data:", reportData.hour_data)
                // console.log("tab:", reportData.hour_data[tabName])
                renderComparisionReport(reportData.hour_data[tabName], tabName, forceReload);
            });

        }

        function renderComparisionReport(metricsReportData, tabName, forceReload) {
            console.log("metricsReportData:", metricsReportData)
            var config = {
                type: 'line',
                data: {
                    labels: [
                        '0点', '1点', '2点', '3点', '4点', '5点', '6点',
                        '7点', '8点', '9点', '10点', '11点', '12点',
                        '13点', '14点', '15点', '16点', '17点', '18点',
                        '19点', '20点', '21点', '22点', '23点',
                    ],
                    datasets: [],
                },
                options: {
                    responsive: true,
                    // maintainAspectRatio:false,
                        title: {
                         display: false,
                         text: ''
                     },
                    tooltips: {
                        mode: 'label',
                        intersect: false,
                        position: 'nearest'
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true,
                        axis: "y"
                    },
                    scales: {
                        tickets: false,
                        xAxes: [{
                            gridLines: {
                                display: false,//竖线
                            },
                        }],
                        /*xAxes: [{
                                                display: true,
                                                scaleLabel: {
                                                    display: true,
                                                    labelString: 'Month'
                                                }
                                            }],*/
                        /*yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: f,
                                labelString: 'Value'
                            }
                        }]*/
                        yAxes: [{
                            ticks: {
                                display: true
                            },
                            scaleShowLabels: {display: true, drawBorder: false,},
                            gridLines: {
                                display: true,
                                drawBorder: false,
                            },
                        }]
                    }
                }
            };

            //for request metric
            // Object.keys(reportData.hour_data.request).forEach(function (key) {
            Object.keys(metricsReportData).forEach(function (key) {
                var colorNames = Object.keys(window.chartColors);
                var colorName = colorNames[config.data.datasets.length % colorNames.length];
                var newColor = window.chartColors[colorName];

                config.data.datasets.push({
                    label: key,
                    backgroundColor: newColor,
                    borderColor: newColor,
                    // data: reportData.hour_data.request[key],
                    data: metricsReportData[key],
                    fill: false
                });
            });

            let ctx = document.getElementById(tabName + '_canvas').getContext('2d');
            window.comparisionChartReport = new Chart(ctx, config);
        }

        window.onload = function () {
            if(!isHideReport()){
                $("#report_box").show();
                $("#hide_chart").text("Hide Chart")
            } else {
                $("#hide_chart").text("Show Chart")
            }

            let reportType = "{{ $reportType }}";
            if(reportType == 2){
                var calendarDateTimeList = @json($calendarDateTimeList);

            }

            if (reportType == 1 && @json($trendReportData).length <= 0)
            {
                console.log("trendReportData empty")
                return
            }

            if (reportType == 2 && @json($comparisionReportData).length <= 0)
            {
                console.log("comparisionReportData empty")
                return
            }

            console.log("reportData:", @json($trendReportData))
            renderChartReportByType(reportType);
        };

        function renderChartReportByType(reportType) {
            if(isHideReport()){
                return false;
            }

            switch (reportType) {
                case "1":
                    // renderTabHeader(reportType);
                    renderTrendReport(@json($trendReportData))
                    break
                case "2":
                    renderTabHeader();
                    renderTabComparisionReport(@json($comparisionReportData));
                    break
                default:
                    break
            }
        }

        function formatDate(dateString) {
            // console.log("start:" + dateString) // "Thu Oct 10 2019";
            let month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            let week = ['Sun ', 'Mon ', 'Tue ', 'Wed ', 'Thu ', 'Fri ', 'Sat '];

            let monthVal = 0;
            for (let i = 0; i < month.length; i++) {
                if (dateString.replace(month[i], i + 1) != dateString) {
                    dateString = dateString.replace(month[i], i + 1)
                    monthVal = i + 1;
                    break
                }
            }

            for (let i = 0; i < week.length; i++) {
                if (dateString.replace(week[i], "") != dateString) {
                    dateString = dateString.replace(week[i], "")
                    break
                }
            }

            let dateSplitResult = dateString.split(" ")
            // console.log("dateString:" + dateString)

            dateSplitResult[0] = (Array(2).join("0") + dateSplitResult[0]).slice(-2)
            return dateSplitResult[2] + "/" + dateSplitResult[0] + "/" + dateSplitResult[1];
        }

        function updateDateTimeRangeList(calendarObj){
            if (Object.prototype.toString.call(calendarObj.get_selected_date()) == '[object Array]') {
                let dateList = calendarObj.get_selected_date()
                let dateLength = dateList.length
                let dateArray = Array()
                for (let i = 0; i < dateLength; i++) {
                    let dateVal = formatDate(dateList[i].toDateString());
                    dateArray.push(dateVal)
                }
                dateTimeRangeList = dateArray
            } else {
                let dateObj = calendarObj.get_selected_date();
                if(dateObj == null){
                    dateTimeRangeList = null;
                } else {
                    let dateArray = Array()
                    dateArray.push(formatDate(dateObj.toDateString()))
                    dateTimeRangeList = dateArray
                }
            }

            if(dateTimeRangeList && dateTimeRangeList.length > 1){
                var showText = "已选择" + dateTimeRangeList.length + "天"
                $("#date_container > span.ng-input-button-container > input.ng_cal_input_field").val(showText);
            }
        }

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
            $(".number_format").each(function(){
                $(this).text(formatCash($(this).text()))
            });

            $("#searchForm").submit(function (eventObj) {
                if ($('input[type=radio][name=report_type]:checked').val() == 2) {
                    if(dateTimeRangeList != null){
                        console.log("calendar_date_time:" + JSON.stringify(dateTimeRangeList))
                        $("<input />").attr("type", "hidden")
                            .attr("name", "calendar_date_time")
                            .attr("value", JSON.stringify(dateTimeRangeList))
                            .appendTo("#searchForm");
                    }
                }

                $("<input />").attr("type", "hidden")
                    .attr("name", "export")
                    .attr("value", $("#export_btn").val())
                    .appendTo("#searchForm");

                return true;
            });

            // dateTimeRangeList global
            ng.ready(function () {
                window.calendar = new ng.Calendar({
                    date_format: "Y/m/d",
                    input: 'date_multi',
                    end_date: 'year + 10',
                    display_date: new Date(),
                    start_date: new Date().setDate(new Date().getDate() - 40),
                    num_months: 2,
                    num_col: 2,
                    multi_selection: true,
                    // calendar_img: "",
                    max_selection: 5,
                    events: {
                        onSelect: function () {
                            updateDateTimeRangeList(this);
                        },
                        onUnSelect: function() {
                            updateDateTimeRangeList(this);
                        },
                        onClear: function(){
                            // window.calendar.select_date(new Date())
                            updateDateTimeRangeList(this);
                        },
                        onClose: function(){
                            updateDateTimeRangeList(this);
                            if(window.dateTimeRangeList == null){
                                window.calendar.select_date(new Date())
                            }
                        }
                    }
                });


                var inputNode = document.createElement("input");
                inputNode.id = "confirm_btn"
                inputNode.type = "button";
                inputNode.value = "Confirm"
                inputNode.className = "custom_ng_button"

                var liNode = document.createElement("li");
                $(liNode).append(inputNode)
                $("td.ng_cal_bottom_bar > ul.ng-buttons-horizontal").append(liNode)

                $(inputNode).click(function(){
                    window.calendar.close();
                })

                var dateList = @json($calendarDateTimeList);
                console.log("dateList:", dateList)
                dateList.forEach(function(ele){
                    console.log("ele:", typeof ele)
                    window.calendar.select_date(new Date(ele));
                })

            });

            $('input[type=radio][name=report_type]').change(function () {
                let reportType = this.value
                console.log("reportType:", reportType)
                switchDateComponent(reportType)
            });

            switchDateComponent("{{ $reportType }}");

            $("#selectAll").click(function () {
                console.log("selectAllClick")
                $("input[name='metric[]']:checkbox").each(function () {
                    $(this).prop("checked", true);
                });
            });

            $("#selectInverse").click(function () {
                console.log("InverseClick")
                $("input[name='metric[]']:checkbox").each(function () {
                    if ($(this).prop("checked") === false) {
                        $(this).prop("checked", true);
                    } else {
                        $(this).prop("checked", false);
                    }

                });
            });

            $("#selectDefault").click(function () {
                $("#selectAll").click();
                $("#selectInverse").click();
                $("input[name='metric[]'][data-default='1']").each(function () {
                    $(this).prop("checked", true);
                });
            });
        });

        function isHideReport(){
            if (!window.localStorage) {
                return false;
            }

            return localStorage.getItem("hide_report") == "true";
        }

        function hideReport(){
            if (!window.localStorage) {
                return false;
            }
            if(isHideReport()){
                localStorage.setItem("hide_report", "false")
                console.log("will show")
                $("#report_box").show();
                $("#hide_chart").text("Hide Chart")
                renderChartReportByType("{{ $reportType }}");
            } else {
                localStorage.setItem("hide_report", "true")
                $("#hide_chart").text("Show Chart")
                $("#report_box").hide();
            }
        }

        function requestToExport() {
            $("#export_btn").val(1);
            $("#searchForm").submit();
            $("#export_btn").val(0);// reset the value
        }

        $('#CustomMetricDlg').on('show.bs.modal', function () {
            var metricList = getCustomMetrics();
            for(var i=0; i < metricList.length; i++) {
                var metricName = metricList[i];
                $("#metric_" + metricName).prop("checked", true);
            }
        })

        function renderTabHeader(){
            $("#nav-tab").empty();
            $("#nav-tabContent").empty();

            var metricList = getCustomMetrics();
            for(var i=0; i < metricList.length; i++){
                var metricName = metricList[i];
                var aNode = document.createElement("a");
                if(i == 0) {
                    aNode.className = "nav-item nav-link active";
                } else {
                    aNode.className = "nav-item nav-link";
                }
                aNode.id = metricName;
                $(aNode).attr("data-toggle", "tab");
                $(aNode).attr("href", "#nav-" + metricName);
                $(aNode).attr("role", "tab");
                $(aNode).attr("aria-controls", "#nav-" + metricName);
                $(aNode).attr("aria-selected", "true");
                aNode.innerText = window.metricKeyCfg[metricName];
                $("#nav-tab").append(aNode);


                var divNode = document.createElement("div");
                if(i == 0) {
                    divNode.className = "tab-pane fade show active";
                } else {
                    divNode.className = "tab-pane fade show";
                }
                divNode.id = "nav-" + metricName;
                divNode.role = "tabpanel";
                $(divNode).attr("aria-labelledby", "#nav-" + metricName + "-tab");

                var canvasContainerNode = document.createElement("div");
                canvasContainerNode.className = "card-box";

                var canvasNode = document.createElement("canvas");
                canvasNode.id = metricName + "_canvas";
                canvasNode.height = "100";
                $(canvasContainerNode).append(canvasNode);
                $(divNode).append(canvasContainerNode);

                $("#nav-tabContent").append(divNode)
            }
        }
    </script>
@endsection
