@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">API 数据拉取配置</li>
                    </ol>
                </div>
                <h4 class="page-title">API 数据拉取配置</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select class="form-control" name="network_firm_type">
                                @foreach($nwFirmTypeMap as $key => $val)
                                    <option value="{{ $key }}" @if((int)$pageAppends['network_firm_type'] === $key) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="type">
                                <option value="" >时间维度</option>
                                @foreach($typeMap as $key => $val)
                                    <option value="{{ $key }}" @if((int)$pageAppends['type'] === $key) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2" id="monetization-schedule-time" @if((int)$pageAppends['network_firm_type'] === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY) hidden="hidden" @endif>
                            <select class="form-control" name="schedule_time" @if((int)$pageAppends['network_firm_type'] === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY) disabled="disabled" @endif>
                                <option value="">拉取时间</option>
                                @foreach($monetizationScheduleTimeList as $scheduleTime)
                                    <option value="{{ $scheduleTime }}" @if($pageAppends['schedule_time'] === $scheduleTime) selected @endif>{{ $scheduleTime }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2" id="media-buy-schedule-time" @if((int)$pageAppends['network_firm_type'] === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION) hidden="hidden" @endif>
                            <select class="form-control" name="schedule_time" @if((int)$pageAppends['network_firm_type'] === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION) disabled="disabled" @endif>
                                <option value="">拉取时间</option>
                                @foreach($mediaBuyScheduleTimeList as $scheduleTime)
                                    <option value="{{ $scheduleTime }}" @if($pageAppends['schedule_time'] === $scheduleTime) selected @endif>{{ $scheduleTime }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2" id="monetization-nw-firm" @if((int)$pageAppends['network_firm_type'] === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY) hidden="hidden" @endif>
                            <select class="form-control" name="nw_firm_id" @if((int)$pageAppends['network_firm_type'] === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY) disabled="disabled" @endif>
                                <option value="">广告平台</option>
                                @foreach($nwFirmMap as $key => $val)
                                    <option value="{{ $key }}" @if((int)$pageAppends['nw_firm_id'] === $key) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2" id="media-buy-nw-firm" @if((int)$pageAppends['network_firm_type'] === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION) hidden="hidden" @endif>
                            <select class="form-control" name="nw_firm_id" @if((int)$pageAppends['network_firm_type'] === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION) disabled="disabled" @endif>
                                <option value="">广告平台</option>
                                @foreach($mediaBuyNwFirmMap as $key => $val)
                                    <option value="{{ $key }}" @if((int)$pageAppends['nw_firm_id'] === $key) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <button class="btn btn-primary w-sm" type="submit">搜索</button>
                            <a class="btn btn-info w-sm" href="{{ Illuminate\Support\Facades\URL::to('network-crawl/create') }}">添加</a>
                            <a class="btn btn-danger w-sm" onclick="multiDelete()">批量删除</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input name="select_all" type="checkbox"/></th>
                            <th>平台类型</th>
                            <th>时间维度</th>
                            <th>拉取时间</th>
                            <th>拉取范围</th>
                            <th>广告平台</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td><input name="record_selected" value="{{ $val['id'] }}" type="checkbox"></td>
                                <td>{{ $nwFirmTypeMap[$val['network_firm_type']] }}</td>
                                <td>{{ $typeMap[$val['type']] }}</td>
                                <td>{{ $val['schedule_time'] }}</td>
                                <td>{{ $pullTypeMap[$val['pull_type']] }}</td>
                                <td>{{ $allMonetizationMediaBuyNwFirmMap[$val['nw_firm_id']] }}</td>
                                <td>{{ $val['admin_name'] }}<br/>{{ $val['update_time'] }}</td>
                                <td>
                                    <a class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm" href="javascript:" onclick="singleDelete('{{$val['id'] }}', this)">
                                        删除
                                    </a>
                                    <a class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm" href="{{ Illuminate\Support\Facades\URL::to('network-crawl/'. $val['id'] . '/edit' . $uri) }}" >
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
    <script>
        $('select[name="network_firm_type"]').change(function () {
            $('#monetization-nw-firm')      .prop('hidden', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY }}');
            $('#monetization-schedule-time').prop('hidden', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY }}');
            $('#media-buy-nw-firm')         .prop('hidden', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION }}');
            $('#media-buy-schedule-time')   .prop('hidden', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION }}');
            $('#monetization-nw-firm select')      .prop('disabled', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY }}');
            $('#monetization-schedule-time select').prop('disabled', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY }}');
            $('#media-buy-nw-firm select')         .prop('disabled', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION }}');
            $('#media-buy-schedule-time select')   .prop('disabled', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION }}');
        })
        $('[name="select_all"]').click(function () {
            let selects = $('input[name="record_selected"]');
            let selected = $('input[name="record_selected"]:checked');
            if (selected.length === selects.length) {
                Array(selects).forEach(function (node) {
                    $(node).prop('checked', false);
                });
                $(this).prop('checked', false);
            } else {
                Array(selects).forEach(function (node) {
                    $(node).prop('checked', 'checked');
                });
                $(this).prop('checked', 'checked');
            }
        });
        function multiDelete() {
            let checkedBoxes = $('input[name="record_selected"]:checked');
            if (checkedBoxes.length === 0) {
                $.alert({
                    title: 'DELETE CONFIRM!',
                    content: '请选择至少一条记录',
                    type: 'blue',
                    backgroundDismiss: true,
                })
                return false;
            }
            let content = '<table class="table table-striped" style="text-align: center">' +
                '<thead><tr><th>ID</th><th>时间维度</th><th>拉取时间</th><th>拉取范围</th><th>广告平台</th></tr></thead>' +
                '<tbody>';
            let ids = [];
            $(checkedBoxes).each(function (index, node) {
                let id = $(node).val();
                ids.push(id);
                content += '<tr><td>' + id + '</td>';
                let row = $(node).parent().parent()[0];
                $(row).find('td').each(function (index, node) {
                    content += index > 0 && index < 5 ? '<td>' + node.innerText + '</td>' : '';
                });
                content += '</tr>';
            });
            content += '</tbody></table>';
            $.confirm({
                title: 'DELETE CONFIRM!',
                content: '是否确认删除以下记录？<br/>'+ content,
                type: 'red',
                icon: 'glyphicon glyphicon-question-sign',
                columnClass: 'col-md-8 col-md-offset-3',
                buttons: {
                    ok: {
                        text: 'confirm',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '/network-crawl/multi-delete',
                                type: 'POST',
                                data: { ids: ids },
                                success: function() {
                                    location.replace(location.href);
                                }
                            });
                        }
                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-success'
                    }
                }
            });
        }
        function singleDelete(id, event) {
            let row = $(event).parent().parent()[0];
            let content = '<table class="table table-striped" style="text-align: center">' +
                '<thead><tr><th>ID</th><th>时间维度</th><th>拉取时间</th><th>拉取范围</th><th>广告平台</th></tr></thead>' +
                '<tbody><tr><td>' + id + '</td>';
            $(row).find('td').each(function (index, node) {
                content += index > 0 && index < 5 ? '<td>' + node.innerText + '</td>' : '';
            });
            content += '</tr></tbody></table>'
            $.confirm({
                title: 'DELETE CONFIRM!',
                content: '是否确认删除该记录？<br/>'+ content,
                type: 'red',
                icon: 'glyphicon glyphicon-question-sign',
                columnClass: 'col-md-8 col-md-offset-3',
                buttons: {
                    ok: {
                        text: 'confirm',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '/network-crawl/' + id,
                                type: 'DELETE',
                                success: function() {
                                    location.replace(location.href);
                                }
                            });
                        }
                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-success'
                    }
                }
            });
        }
    </script>
@endsection
