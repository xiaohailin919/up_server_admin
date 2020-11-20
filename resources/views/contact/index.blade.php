@extends('layouts.admin')

@section('content')
    <style>
        .btn-outline-danger,
        .btn-outline-warning,
        .btn-outline-info,
        .btn-outline-primary {
            margin-top: .5em;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Contact</li>
                    </ol>
                </div>
                <h4 class="page-title">Contact</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select class="form-control" name="admin_id">
                                <option value="">联系人</option>
                                @foreach ($operatorMap as $adminId => $adminName)
                                    <option value="{{ $adminId }}" @if((int)$pageAppends['admin_id'] === $adminId) selected="selected" @endif>{{ str_pad($adminId, 2, ' ', STR_PAD_LEFT) . ' | ' . $adminName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="language">
                                <option value="">语言</option>
                                @foreach ($languageMap as $key => $value)
                                    <option value="{{ $key }}" @if($pageAppends['language'] === $key) selected="selected" @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="status">
                                <option value="">状态</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if ((int)$pageAppends['status'] === $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button class="btn btn-primary" type="submit">Search</button>
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
                            <th>名称</th>
                            <th>邮箱</th>
                            <th>语言</th>
                            <th>电话</th>
{{--                            <th>通讯</th>--}}
                            <th>公司</th>
                            <th>职位</th>
                            <th>留言</th>
                            <th>状态</th>
                            <th>联系人</th>
                            <th>备注</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val['id'] }}</td>
                                <td>{{ $val['name'] }}</td>
                                <td>{{ $val['email'] }}</td>
                                <td>{{ $languageMap[$val['language']]  }}</td>
                                <td>{{ str_pad($val['area_code'], 6, ' ', STR_PAD_LEFT) . ' ' . $val['phone'] }}</td>
{{--                                <td><i class="mdi {{ $imTypeIconMap[$val['im_type']] }}"></i>&nbsp;{{ $val['im_account'] }}</td>--}}
                                <td>{{ $val['company'] }}</td>
                                <td>{{ $val['job_info'] }}</td>
                                <td style="width: 20%">{{ $val['message'] }}</td>
                                <td>{{ $statusMap[$val['status']] }}</td>
                                <td>{{ $val['admin_name'] }}<br/>{{ $val['update_time'] }}</td>
                                <td>{{ $val['remark'] }}</td>
                                <td>
{{--                                    <a class="btn btn-outline-danger w-sm btn-sm" href="javascript:void(0);" onclick="deleteRecord('{{ $val['id'] }}');">删除</a>--}}
                                    @if ($val['status'] === \App\Models\MySql\Contact::STATUS_UNPROCESSED)
                                        <a class="btn btn-outline-warning w-sm btn-sm" href="javascript:void(0);" onclick="setContact('{{ $val['id'] }}', '{{ $val['name'] }}');">已联系</a>
                                    @else
                                        <a class="btn btn-outline-info w-sm btn-sm" href="javascript:void(0);">已联系</a>
                                    @endif
                                    <a class="btn btn-outline-primary w-sm btn-sm" href="javascript:void(0);" onclick="setRemark('{{ $val['id'] }}', '{{ $val['remark'] }}');">备注</a>
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
        function deleteRecord(id) {
            $.confirm({
                title: '确认删除记录',
                content: '是否确认删除此条记录?<br/>ID: ' + id,
                type: 'red',
                icon: 'glyphicon glyphicon-question-sign',
                buttons: {
                    ok: {
                        text: '确认',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url:     '/contact/' + id,
                                type:    'DELETE',
                                success: function () {
                                    location.replace(location.href);
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        btnClass: 'btn-primary'
                    }
                }
            });
        }
        function setContact(id, name) {
            $.confirm({
                title: '确认已联系',
                content: '是否确认已联系此客户?<br/>ID: ' + id + '    姓名：' + name,
                type: 'orange',
                icon: 'glyphicon glyphicon-question-sign',
                buttons: {
                    ok: {
                        text: '确认',
                        btnClass: 'btn-warning',
                        action: function() {
                            $.ajax({
                                url:     '/contact/' + id,
                                type:    'PUT',
                                success: function () {
                                    location.replace(location.href);
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        btnClass: 'btn-primary'
                    }
                }
            });
        }
        function setRemark(id, remark) {
            $.confirm({
                title: '请输入备注',
                content: `
                        <div class="form-group">
                            <textarea class="form-control" name="remark" maxlength="250" placeholder="请输入备注，限250字" required="required">` + remark + `</textarea>
                        </div>
                    `,
                type: 'orange',
                icon: 'glyphicon glyphicon-question-sign',
                buttons: {
                    ok: {
                        text: '确认',
                        btnClass: 'btn-warning',
                        action: function() {
                            $.ajax({
                                url: '/contact/' + id,
                                type: 'PUT',
                                data: {
                                    remark: $('textarea[name="remark"]').val(),
                                },
                                success: function () {
                                    location.replace(location.href);
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        btnClass: 'btn-primary'
                    }
                }
            });
        }
    </script>
@endsection