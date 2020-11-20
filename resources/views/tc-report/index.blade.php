@extends('layouts.admin')

@section('content')
    <style>
        .option-title {
            min-width: fit-content;
            vertical-align: middle;
        }
        .dimension-container {
            padding: 0 6px;
        }
        .active-dimension {
            color: #2980b9;
            font-weight: bold;
        }
        .table-wrapper {
            width: 100%;
            display: flex;
        }
        .table-wrapper th {
            /* 表格头不允许换行 */
            white-space: nowrap;
        }
        .table-wrapper tbody td {
            height: 48px;
            max-height: 48px;
        }
        .table-wrapper tbody span {
            display: block;
            height: 48px;
            max-height: 48px;
            /* 到 128px 后才换行 */
            /*max-width: 128px;*/
            width: max-content;
            word-break: break-all;
        }
        .left-table-wrapper {
            max-width: 65%;
            overflow-x: auto;
            border-right: 2px solid #e9ecef;
        }
        .right-table-wrapper {
            /*min-width: 40%;*/
            flex: 1 1 auto;
            width: unset;
        }
        .unsorted {
            color: #9b9b9b;
        }
        .unsorted:hover {
            color: #007bff;
        }
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
                        <li class="breadcrumb-item active">Tc Report</li>
                    </ol>
                </div>
                <h4 class="page-title">Tc Report</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {{-- 选项 --}}
            <div class="card-box">
                <form method="GET" action="{{ route('report-tc') }}">
                    {{-- 数据维度 --}}
                    <div class="form-row">
                        <label class="form-group col-md-1 option-title">数据维度</label>
                        <div class="dimension-container col-md-11">
                            @foreach($selectableDimensions as $dimensionKey => $dimensionName)
                                <label for="group_{{ $dimensionKey }}" class="form-group">
                                    <input id="group_{{ $dimensionKey }}" name="{{ \App\Http\Controllers\ReportTcController::METRICS_GROUP_BY }}[]" type="checkbox" value="{{ $dimensionKey }}" @if(in_array($dimensionKey, $selectedDimensions, false)) checked="checked" @endif/>
                                    @if (in_array($dimensionKey, $selectedDimensions, false))
                                        <span class="active-dimension">{{ $dimensionName }}</span>
                                    @else
                                        {{ $dimensionName }}
                                    @endif
                                </label>
                                &emsp;
                            @endforeach
                        </div>
                    </div>
                    {{-- END 数据维度 --}}
                    {{-- 筛选项 --}}
                    <div class="form-row">
                        <label class="form-group col-md-1 option-title" style="line-height: 90px">筛选项</label>
                        <div class="col-md-11 form-row">
                            {{-- 输入框 --}}
                            @foreach($inputFieldList as $inputField)
                                <div class="form-group col-md-2 {{ $errors->has($inputField['name']) ? 'has-error' : '' }}" @if($errors->has($inputField['name'])) title="{{ $errors->first($inputField['name']) }}" @endif>
                                    <input class="form-control" type="{{ $inputField['type'] }}" name="{{ $inputField['name'] }}" value="{{ $inputField['value'] }}" placeholder="{{ $inputField['placeholder'] }}">
                                </div>
                            @endforeach
                            {{-- 下拉框 --}}
                            @foreach($selectionList as $selection)
                                <div class="form-group col-md-2">
                                    <select name="{{ $selection['name'] }}" class="form-control">
                                        <option value="">{{ $selection['placeholder'] }}</option>
                                        @foreach ($selection['options'] as $optionValue => $optionName)
                                            <option value="{{ $optionValue }}" @if (is_numeric($selection['value']) && $optionValue === (int)$selection['value']) selected="selected" @endif>{{ $optionName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                            {{-- 按钮 --}}
                            <div class="form-group col-md-2">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <button type="submit" name="{{ \App\Http\Controllers\ReportTcController::METRICS_EXPORT }}" value="1" class="btn btn-success">Export</button>
                            </div>
                        </div>
                    </div>
                    {{-- END 筛选项 --}}
                </form>
            </div>
            {{-- END 选项 --}}
            {{-- 表格 --}}
            <div class="card-box">
                <div class="table-wrapper">
                    {{-- 表格左部分 --}}
                    <div class="left-table-wrapper">
                        <table class="table left-table">
                            <thead>
                            <tr>
                                @foreach($groupByMetricsMap as $metric => $name)
                                    <th>
                                        {{ $name }}
                                        @if(in_array($metric, $sortableMetrics, true))
                                            @if(array_key_exists($metric, $orderByMap))
                                                <a href="javascript:void(0);" onclick="onSortClick(this)" data-field="{{ $metric }}" data-direction="{{ $orderByMap[$metric] === 'desc' ? 'asc' : '' }}" title="{{ $orderByMap[$metric] === 'desc' ? '点击进行升序排序' : '点击取消排序' }}">
                                                    <i class="sort-field-icon @if($orderByMap[$metric] === 'desc') fi-arrow-down @else fi-arrow-up @endif"></i>
                                                </a>
                                            @else
                                                <a href="javascript:void(0);" onclick="onSortClick(this)" data-field="{{ $metric }}" data-direction="desc" title="点击进行降序排序">
                                                    <i class="sort-field-icon fi-arrow-up unsorted"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $record)
                                <tr>
                                    @foreach($groupByMetricsMap as $metric => $name)
                                        @if ($metric === \App\Http\Controllers\ReportTcController::METRICS_PLATFORM)
                                            <td>
                                                <span>
                                                    @if(isset($record[$metric]) && (int)$record[$metric] === 1)
                                                        <i class="mdi mdi-android" style="color: #a3c83e;"></i> Android
                                                    @else
                                                        <i class="mdi mdi-apple"></i> IOS
                                                    @endif
                                                </span>
                                            </td>
                                        @elseif($metric === \App\Http\Controllers\ReportTcController::METRICS_PUBLISH_ID)
                                            <td>
                                                <span>
                                                    {!! $record[$metric] !!}
                                                    <a href="{{ $record[\App\Http\Controllers\ReportTcController::METRICS_PUB_LOGIN_URL] }}" target="_blank">
                                                        <small><i class="mdi mdi-login"></i> Login</small>
                                                    </a>
                                                </span>
                                            </td>
                                        @elseif($metric === \App\Http\Controllers\ReportTcController::METRICS_AD_FORMAT)
                                            <td><span>{{ $adFormatMap[(int)$record[$metric]] }}</span></td>
                                        @elseif($metric === \App\Http\Controllers\ReportTcController::METRICS_NW_FIRM_ID)
                                            <td><span>{{ $nwFirmMap[(int)$record[$metric]] }}</span></td>
                                        @else
                                            <td><span>{!! $record[$metric] !!}</span></td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- END 表格左部分 --}}
                    {{-- 表格右部分：table-responsive 类的 overflow-x 属性支持表格滑动; flex-grow 为 1 使得右侧表格可以在列数不足时撑满剩下宽度 --}}
                    <div class="table-responsive right-table-wrapper">
                        <table class="table right-table">
                            <thead>
                            <tr>
                                @foreach($fixMetricsMap as $metric => $name)
                                    <th>
                                        {{ $name }}
                                        @if(in_array($metric, $sortableMetrics, true))
                                            @if(array_key_exists($metric, $orderByMap))
                                                <a href="javascript:void(0);" onclick="onSortClick(this)" data-field="{{ $metric }}" data-direction="{{ $orderByMap[$metric] === 'desc' ? 'asc' : '' }}" title="{{ $orderByMap[$metric] === 'desc' ? '点击进行升序排序' : '点击取消排序' }}">
                                                    <i class="sort-field-icon @if($orderByMap[$metric] === 'desc') fi-arrow-down @else fi-arrow-up @endif"></i>
                                                </a>
                                            @else
                                                <a href="javascript:void(0);" onclick="onSortClick(this)" data-field="{{ $metric }}" data-direction="desc" title="点击进行降序排序">
                                                    <i class="sort-field-icon fi-arrow-up unsorted"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $record)
                                <tr>
                                    @foreach($fixMetricsMap as $metric => $name)
                                        <td><span>{!! $record[$metric] !!}</span></td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- END 表格右部分 --}}
                </div>
                {{-- 统计与分页 --}}
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $data->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $data->appends($pageAppends)->links() }}
                    </div>
                </div>
                {{-- END 统计与分页 --}}
            </div>
            {{-- END 表格 --}}
        </div>
    </div>
    <script>
        $(function () {
            /**
             * 初始化日期选择器
             */
            $('input[name="{{ \App\Http\Controllers\ReportTcController::METRICS_DATE_TIME }}"]').daterangepicker({
                format: 'mm/dd/yyyy',
                minDate: "01/01/2018",
                maxDate: moment(),
                ranges: {
                    'Today'        : [moment(), moment()],
                    'Yesterday'    : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days'  : [moment().subtract(6, 'days'), moment()],
                    'Last 14 Days' : [moment().subtract(13, 'days'), moment()],
                    'Last 30 Days' : [moment().subtract(29, 'days'), moment()],
                    'This Month'   : [moment().startOf('month'), moment().endOf('month')],
                    'Last Month'   : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
            }, function(start, end) {
                dateStart = start.format('MM/DD/YYYY');
                dateEnd   = end.format('MM/DD/YYYY');
                console.log('Date selected: ' + dateStart + ' ~ ' + dateEnd);
                /* 输入框的 value 并没有显示变化，不必担心 */
                $('input[name="{{ \App\Http\Controllers\ReportTcController::METRICS_DATE_TIME }}"]').val(dateStart + ' - ' + dateEnd);
            });
        });

        /**
         * 处理 Order By，将 order by 参数值直接弄成一个 json 字符串
         */
        function onSortClick(event) {
            console.log('生成 orderBy 参数');
            /* 获取要进行 order by 的属性 */
            let field = $(event).attr('data-field');
            let orderTo = $(event).attr('data-direction');
            /* 获取原 order by 数组 */
            let matches = window.location.href.match((/([?&])order_by=.*/g));
            let jsonStr = '';
            if (matches !== null) {
                jsonStr = matches[0].replace(/%22/g, '"');
                jsonStr = jsonStr.substr(jsonStr.indexOf('order_by=') + 'order_by='.length);
            }
            /* 若采用默认排序, 则赋予默认参数 */
            if (jsonStr === '') {
                jsonStr = '{!! \App\Http\Controllers\ReportTcController::DEFAULT_SORT_PARAM !!}';
            }
            console.log(jsonStr);
            let orderByMetrics = JSON.parse(jsonStr);
            /* 判断是新增、更新、还是撤销 */
            switch (orderTo) {
                case 'desc':
                    orderByMetrics.push([field, orderTo]);
                    break;
                case 'asc':
                    for(let i = 0; i < orderByMetrics.length; i++) {
                        orderByMetrics[i][1] = orderByMetrics[i][0] === field ? orderTo : orderByMetrics[i][1];
                    }
                    break;
                default:
                    for(let i = 0; i < orderByMetrics.length; i++) {
                        if (orderByMetrics[i][0] === field) {
                            orderByMetrics.splice(i, 1);
                        }
                    }
            }
            console.log(orderByMetrics);
            let href = window.location.href.replace(/([?&])order_by=.*/g, '');  // 将新增 order by 属性加入原数组
            if (orderByMetrics.length === 0) {                                  // order 列表为空，直接取消这个参数
                window.location.href = href;
            } else {                                                            // 删掉原来的，拼上新生成的
                window.location.href = href + (href.indexOf('?') === -1 ? '?' : '&') + 'order_by=' + JSON.stringify(orderByMetrics);
            }
        }
    </script>
@endsection
