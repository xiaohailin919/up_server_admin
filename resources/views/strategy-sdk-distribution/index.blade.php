@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">SDK 版本分发规则</li>
                    </ol>
                </div>
                <h4 class="page-title">SDK 版本分发规则</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select class="form-control" name="publisher_group_id">
                                <option value="" >Publisher Group</option>
                                @foreach ($publisherGroupIdNameMap as $publisherGroupId => $publisherGroupName)
                                    <option value="{{ $publisherGroupId }}" @if ($pageAppends['publisher_group_id'] == $publisherGroupId) selected="selected" @endif>{{ $publisherGroupName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" name="publisher_id" type="number" value="{{ $pageAppends['publisher_id'] }}" placeholder="Publisher ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" name="publisher_name" type="text" value="{{ $pageAppends['publisher_name'] }}" placeholder="Publisher Name">
                        </div>
                        <div class="form-group col-md-2">
                            <button class="btn btn-primary" type="submit">搜索</button>
                            <a type="submit" class="btn btn-success" href="{{ \Illuminate\Support\Facades\URL::to('strategy-sdk-distribution/create') }}">添加</a>
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
                            <th>Publisher Group</th>
                            <th>Publisher</th>
                            <th>安卓原版 SDK</th>
                            <th>iOS 原版 SDK</th>
                            <th>Unity 安卓 SDK</th>
                            <th>Unity iOS SDK</th>
                            <th>Unity iOS + 安卓 SDK</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $ruleTypeMap[$val['type']] }}</td>
                                <td>{{ $publisherGroupIdNameMap[$val['publisher_group_id']] ?? '-' }}</td>
                                <td>
                                    @if ($val['publisher_id'] === 0)
                                        @if ($val['publisher_group_id'] === 0)
                                            default
                                        @else
                                            -
                                        @endif
                                    @else
                                        {{ $val['publisher_name'] . '(' . $val['publisher_id'] . ')' }}
                                        <a href="{{ \Illuminate\Support\Facades\URL::to('/publisher/login?id=' . $val['publisher_id']) }}" target="_blank">
                                            <small><i class="mdi mdi-login"></i> Login</small>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ implode(",&ensp;", $val['android']) }}</td>
                                <td>{{ implode(",&ensp;", $val['ios']) }}</td>
                                <td>{{ implode(",&ensp;", $val['unity_android']) }}</td>
                                <td>{{ implode(",&ensp;", $val['unity_ios']) }}</td>
                                <td>{{ implode(",&ensp;", $val['unity_android_ios']) }}</td>
                                <td>{{ $val['admin_name'] }}<br/>{{ $val['update_time'] }}</td>
                                <td>
                                    <a class="btn btn-outline-success waves-light waves-effect w-sm btn-sm" href="{{ \Illuminate\Support\Facades\URL::to('strategy-sdk-distribution/' . $val['id']). '/edit'}}">编辑</a>
                                    @if ($val['publisher_id'] !== 0 || $val['publisher_group_id'] !== 0)
                                        <a class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm" href="javascript:void(0)" onclick="deleteRecord('{{ $val['id'] }}')">删除</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">Total <strong>{{ count($data) }}</strong></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function deleteRecord(id) {
            $.confirm({
                title: '删除确认!',
                content: '是否要删除此记录?<br/>ID = ' + id,
                type: 'red',
                icon: 'glyphicon glyphicon-question-sign',
                buttons: {
                    ok: {
                        text: '确定',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '/strategy-sdk-distribution/' + id,
                                type: 'DELETE',
                                success: function() {
                                    location.replace(location.href);
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        btnClass: 'btn-success'
                    }
                }
            });
        }
    </script>
@endsection
