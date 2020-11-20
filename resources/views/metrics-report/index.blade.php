@extends('layouts.admin')

@section('content')
    <style>
        .modify-priority {
            color: #bf7b40;
        }
        .modify-priority:hover {
            cursor: pointer;
            color: #86562d;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Report Metrics</li>
                    </ol>
                </div>
                <h4 class="page-title">Report Metrics</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="name-input" hidden></label>
                            <input class="form-control" id="name-input" name="name" type="text" placeholder="指标名称" value="{{ $pageAppends['name'] }}" onblur="onNameFieldInputBlur(this)">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="field-input" hidden></label>
                            <input class="form-control" id="field-input" name="field" type="text" placeholder="指标字段" value="{{ $pageAppends['field'] }}" onblur="onNameFieldInputBlur(this)">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="kind-select" hidden></label>
                            <select name="kind" id="kind-select" class="form-control">
                                @foreach ($kindMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === (int)$pageAppends['kind']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="is-default-select" hidden></label>
                            <select name="is_default" id="is-default-select" class="form-control">
                                <option value="">是否默认指标</option>
                                <option value="0" @if ($pageAppends['is_default'] === '0') selected @endif >非默认指标</option>
                                <option value="1" @if ($pageAppends['is_default'] === '1') selected @endif >是默认指标</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="sort-type-select" hidden></label>
                            <select name="sort_type" id="sort-type-select" class="form-control">
                                <option value="show_priority" @if ($pageAppends['sort_type'] === 'show_priority') selected @endif >按弹窗排序</option>
                                <option value="priority" @if ($pageAppends['sort_type'] === 'priority') selected @endif >按报表排序</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ Illuminate\Support\Facades\URL::to('metrics-report/create') }}" class="btn btn-info">Add</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>指标名称</th>
                            <th>指标字段</th>
                            <th>类型</th>
                            <th style="text-align: center">默认</th>
                            <th style="text-align: center">分组</th>
                            <th>弹窗排序</th>
                            <th>报表排序</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val['id'] }}</td>
                                <td>{{ $val['name'] }}</td>
                                <td>{{ $val['field'] }}</td>
                                <td>
                                    @foreach($kindMap as $key => $value)
                                        @if ($key === (int)$val['kind']) {{ $value }} @endif
                                    @endforeach
                                </td>
                                <td style="text-align: center">
                                    @if($val['is_default'] === 1)  <i class="mdi mdi-check"></i> @else <i class="mdi mdi-close"></i> @endif
                                </td>
                                <td style="text-align: center">{{ $val['group'] }}</td>
                                <td>
                                    {{ $val['show_priority'] }} &emsp;<i class="mdi mdi-border-color modify-priority" onclick="modifyPriority('{{ $val['id'] }}', true)"></i>
                                </td>
                                <td>
                                    {{ $val['priority'] }}      &emsp;<i class="mdi mdi-border-color modify-priority" onclick="modifyPriority('{{ $val['id'] }}', false)"></i>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" onclick="onDeleteClick({{$val['id']}})" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Delete</a>
                                    <a href="{{ Illuminate\Support\Facades\URL::to('metrics-report/'.$val['id'] . '/edit') }}" class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm">Edit</a>
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
                        {{ $data->appends([$pageAppends])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const data = @json($data)['data'];
        function onDeleteClick(id) {
            let record = getRecordById(id);
            $.confirm({
                title: 'DELETE CONFIRM!',
                content: 'Are you sure want to delete this record?<br/>'+ '{<br/>&emsp;&emsp;"id": ' + record['id'] + ',<br/>&emsp;&emsp;"name": ' + record['name'] + ',<br/>&emsp;&emsp;"field": ' + record['field'] + '<br/>}',
                type: 'red',
                icon: 'glyphicon glyphicon-question-sign',
                buttons: {
                    ok: {
                        text: 'confirm',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '/metrics-report/' + id,
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

        /**
         * 更新 show_priority 或者 priority
         *
         * @param id
         * @param isShowPriority 若为 true 则为更新 show_priority
         */
        function modifyPriority(id, isShowPriority) {
            let record = getRecordById(id);
            let title = isShowPriority ? 'Update Show Priority' : 'Update Priority';
            let metrics = isShowPriority ? 'show_priority' : 'priority';
            $.confirm({
                title: title,
                content: `<p>Current <strong>` + record['name'] +`</strong>'s value: ` + record[metrics] + `</p>`
                    + `
                        <form id="priority-form">
                            <div class="form-group">
                                <label for="priority-input">Show Priority</label>
                                <input type="number" id="priority-input" name="` + metrics + `" value="` + record[metrics] + `" placeholder="New value" class="form-control">
                                <input type="hidden" name="_token" value="` + `{{ csrf_token() }}` + `">
                            </div>
                            <i id="priority_error" style="display:none"></i>
                        </form>
                    `,
                buttons: {
                    confirm: {
                        text: 'confirm',
                        btnClass: 'btn-blue',
                        action: function() {
                            let priority = $('#priority-input').val();
                            let priorityError = $('#priority_error');
                            if (priority === '') {
                                priorityError.attr('style', 'color:red;display:inline');
                                priorityError.text('Please input the new value!');
                                return false;
                            }
                            if (priority === record[metrics].toString()) {
                                priorityError.attr('style', 'color:red;display:inline');
                                priorityError.text('Please input a new value!');
                                return false;
                            }
                            $.ajax({
                                url: '/metrics-report/' + id,
                                type: 'PUT',
                                data: $('#priority-form').serializeArray(),
                                success: function () {
                                    location.replace(location.href)
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
        function getRecordById(id) {
            let record;
            for (let i = 0; i < data.length; i++) {
                if (data[i]['id'] === parseInt(id)) {
                    record = data[i];
                }
            }
            return record;
        }
        
        function onNameFieldInputBlur(object) {
            let val = $(object).val();
            $(object).val($.trim(val));
            // 互斥逻辑，有 bug
            // let opponent = $(object).attr('id') === 'name-input' ? $('#field-input') : $('#name-input');
            // opponent.val('');
            // opponent.attr('disabled', val !== '');
        }
    </script>

@endsection
