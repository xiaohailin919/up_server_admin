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
            height: 50px;
        }
        .table-wrapper tbody td {
            max-height: 44px;
        }
        .table-wrapper tbody span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .left-table-wrapper {
            min-width: 168px;
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
            color: #797979;
        }
        .unsorted:hover {
            color: #007bff;
        }
        .select2-container .select2-selection--multiple {
            border: 1px solid #dadada;
        }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number]{
             -moz-appearance:textfield;
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
                        <label class="form-group col-md-1 option-title" style="padding-top: 0.5rem">筛选项</label>
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
                            {{-- 地区多选框 --}}
                            <div class="form-group col-md-2">
                                <select class="form-control select2 select2-multiple select2-hidden-accessible" name="{{ \App\Http\Controllers\ReportTcController::METRICS_GEO_SHORT }}[]" multiple="" data-placeholder="地区">
                                    @foreach ($geoMap as $key => $val)
                                        <option value="{{ $key }}" @if(in_array($key, $geoSelected, false)) selected="selected" @endif>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
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
{{--                                                <a class="unsorted" href="javascript:void(0)" onclick="onlySortByThis('{{ $metric }}', '{{ $orderByMap[$metric] }}')" title="点击标题{{ $orderByMap[$metric] === 'desc' ? '单独按“' . $name . '”进行升序' : '使用默认' }}排序">{{ $name }}</a>--}}
{{--                                                <a href="javascript:void(0);" onclick="onSortClick(this)" data-field="{{ $metric }}" data-direction="{{ $orderByMap[$metric] === 'desc' ? 'asc' : '' }}" title="{{ $orderByMap[$metric] === 'desc' ? '点击进行升序排序' : '点击取消排序' }}">--}}
{{--                                                    <i class="sort-field-icon @if($orderByMap[$metric] === 'desc') fi-arrow-down @else fi-arrow-up @endif"></i>--}}
{{--                                                </a>--}}
                                                <a href="javascript:void(0);" onclick="onlySortByThis('{{ $metric }}', '{{ $orderByMap[$metric] }}')" title="{{ $orderByMap[$metric] === 'desc' ? '点击按' . $name . '进行升序排序' : '点击按默认排序' }}">
                                                    <i class="sort-field-icon @if($orderByMap[$metric] === 'desc') fi-arrow-down @else fi-arrow-up @endif"></i>
                                                </a>
                                            @else
{{--                                                <a class="unsorted" href="javascript:void(0)" onclick="onlySortByThis('{{ $metric }}', '')" title="点击标题单独按“{{ $name }}”进行降序排序">{{ $name }}</a>--}}
{{--                                                <a class="unsorted" href="javascript:void(0);" onclick="onSortClick(this)" data-field="{{ $metric }}" data-direction="desc" title="点击进行降序排序">--}}
{{--                                                    <i class="sort-field-icon fi-arrow-up"></i>--}}
{{--                                                </a>--}}
                                                <a href="javascript:void(0);" onclick="onlySortByThis('{{ $metric }}', '')" title="点击按{{ $name }}进行降序排序">
                                                    <i class="sort-field-icon fi-arrow-up"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if($metric == \App\Http\Controllers\ReportTcController::METRICS_PUBLISH_ID)
                                            <i class="dripicons-warning" style="color: red;vertical-align: middle" data-toggle="tooltip" title="注意：1. 同时登陆多个账号，后登陆的会挤掉先登录的！2. 任何时候请不要删除或修改任何数据，除非是自己新建的数据！"></i>
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
                                                    @if($record[$metric] === 'Android')<i class="mdi mdi-android" style="color: #a3c83e;"></i>@else<i class="mdi mdi-apple"></i>@endif
                                                    &nbsp;{{ $record[$metric] }}
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
{{--                                                <a class="unsorted" href="javascript:void(0)" onclick="onlySortByThis('{{ $metric }}', '{{ $orderByMap[$metric] }}')" title="点击标题{{ $orderByMap[$metric] === 'desc' ? '单独按“' . $name . '”进行升序' : '使用默认' }}排序">{{ $name }}</a>--}}
{{--                                                <a href="javascript:void(0);" onclick="onSortClick(this)" data-field="{{ $metric }}" data-direction="{{ $orderByMap[$metric] === 'desc' ? 'asc' : '' }}" title="{{ $orderByMap[$metric] === 'desc' ? '点击进行升序排序' : '点击取消排序' }}">--}}
{{--                                                    <i class="sort-field-icon @if($orderByMap[$metric] === 'desc') fi-arrow-down @else fi-arrow-up @endif"></i>--}}
{{--                                                </a>--}}
                                                <a href="javascript:void(0);" onclick="onlySortByThis('{{ $metric }}', '{{ $orderByMap[$metric] }}')" title="{{ $orderByMap[$metric] === 'desc' ? '点击按' . $name . '进行升序排序' : '点击按默认排序' }}">
                                                    <i class="sort-field-icon @if($orderByMap[$metric] === 'desc') fi-arrow-down @else fi-arrow-up @endif"></i>
                                                </a>
                                            @else
{{--                                                <a class="unsorted" href="javascript:void(0)" onclick="onlySortByThis('{{ $metric }}', '')" title="点击标题单独按“{{ $name }}”进行降序排序">{{ $name }}</a>--}}
{{--                                                <a class="unsorted" href="javascript:void(0);" onclick="onSortClick(this)" data-field="{{ $metric }}" data-direction="desc" title="点击进行降序排序">--}}
{{--                                                    <i class="sort-field-icon fi-arrow-up"></i>--}}
{{--                                                </a>--}}
                                                <a href="javascript:void(0);" onclick="onlySortByThis('{{ $metric }}', '')" title="点击按{{ $name }}进行降序排序">
                                                    <i class="sort-field-icon fi-arrow-up"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if($metric === 'tc_ctr')
                                            <i class="dripicons-question" style="margin-top: 2px;" data-toggle="tooltip" title="计算公式(TC点击/TC填充*100%"></i>
                                        @elseif($metric === 'tc_request_click_rate')
                                            <i class="dripicons-question" style="margin-top: 2px;" data-toggle="tooltip" title="计算公式(TC请求/点击*100%）"></i>
                                        @elseif($metric === 'tc_cvr')
                                            <i class="dripicons-question" style="margin-top: 2px;" data-toggle="tooltip" title="计算公式(TC安装/TC点击*100%）"></i>
                                        @elseif ($metric === 'tc_filled_request_rate')
                                            <i class="dripicons-question" style="margin-top: 2px;" data-toggle="tooltip" title="计算公式(TC填充/TC请求*100%）"></i>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $record)
                                <tr>
                                    @foreach($fixMetricsMap as $metric => $name)
                                        <td>
                                            <span @if(in_array($metric, $strikingMetrics, true)) style="color: red" @endif>
                                        @if (in_array($metric, $currencyMetrics, true))
                                            {{ '$' . number_format($record[$metric], 2) }}
                                        @elseif(in_array($metric, $percentageMetrics, true))
                                            {{ round($record[$metric], 4) * 100 . '%' }}
                                        @else
                                            {{ number_format($record[$metric]) }}
                                        @endif
                                            </span>
                                        </td>
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
            resizeTable();
        });

        window.onresize = function onresize() {
            resizeTable();
        };

        /**
         * 处理 Order By，将 order by 参数值直接弄成一个 json 字符串
         * 注：暂废弃
         */
        function onSortClick(event) {
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
                jsonStr = '{!! \App\Http\Controllers\ReportTcController::ORDER_BY_PRIMARY !!}';
            }
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
            let href = window.location.href.replace(/([?&])order_by=.*/g, '');  // 将新增 order by 属性加入原数组
            if (orderByMetrics.length === 0) {                                  // order 列表为空，直接取消这个参数
                window.location.href = href;
            } else {                                                            // 删掉原来的，拼上新生成的
                window.location.href = href + (href.indexOf('?') === -1 ? '?' : '&') + 'order_by=' + JSON.stringify(orderByMetrics);
            }
        }

        /**
         * 仅按此维度进行排序
         * @param metric        字段
         * @param orderStatus   当前排序顺序
         */
        function onlySortByThis(metric, orderStatus) {
            console.log(metric + ":" + orderStatus);
            /* 取消链接中原有的排序方式 */
            let href = window.location.href.replace(/([?&])order_by=.*/g, '');
            let order;
            /* 单独添加这一项排序方式 */
            switch (orderStatus) {
                case 'desc':
                    order = JSON.stringify([[metric, 'asc']]);
                    break;
                case 'asc':
                    order = '{!! \App\Http\Controllers\ReportTcController::ORDER_BY_PRIMARY !!}';
                    break;
                default:
                    order = JSON.stringify([[metric, 'desc']]);
                    break;
            }
            window.location.href = href + (href.indexOf('?') === -1 ? '?' : '&') + 'order_by=' + order;
        }

        /**
         * 调整右表格行高与左表格一致
         */
        function resizeTable() {
            let leftTable = $('.left-table')[0];
            let rightTable = $('.right-table')[0];
            let leftRows = $(leftTable).find('tbody tr');
            let rightRows = $(rightTable).find('tbody tr');
            leftRows.each(function (index, row) {
                $(rightRows[index]).outerHeight($(row).outerHeight());
            });
        }
    </script>
@endsection
