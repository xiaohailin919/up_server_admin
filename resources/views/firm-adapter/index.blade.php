@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage Firm Adapter</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage Firm Adapter</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input class="form-control" name="firm_id" value="{{ $pageAppends['firm_id'] }}" placeholder="搜索厂商 ID">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="firm_id" class="form-control">
                                <option value="">选择聚合厂商</option>
                                @foreach($firmIdNameMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === (int)$pageAppends['firm_id']) selected @endif>{{ $key }} | {{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="platform" class="form-control">
                                <option value="">选择系统平台</option>
                                @foreach ($platformMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === (int)$pageAppends['platform']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="format" class="form-control">
                                <option value="">选择广告类型</option>
                                @foreach($formatMap as $key => $val)
                                    <option value="{{ $key }}" @if (is_numeric($pageAppends['format']) && $key === (int)$pageAppends['format']) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="publisher_id">
                                <option value="">选择开发者</option>
                                @foreach($publisherMap as $id => $name)
                                    <option value="{{ $id }}" @if (is_numeric($pageAppends['publisher_id']) && $id === (int)$pageAppends['publisher_id']) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <a href="{{ Illuminate\Support\Facades\URL::to('firm-adapter/create') }}" class="btn btn-info">添加</a>
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
                            <th>Firm</th>
                            <th>Publisher<i class="dripicons-warning" style="color: red;vertical-align: middle" data-toggle="tooltip" title="注意：1. 同时登陆多个账号，后登陆的会挤掉先登录的！2. 任何时候请不要删除或修改任何数据，除非是自己新建的数据！"></i></th>
                            <th>Platform</th>
                            <th>Format</th>
                            <th>Adapter</th>
                            <th>Time(create/update)</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val['id'] }}</td>
                                <td>{{ $val['firm_id'] }} | {{ $val['firm_name'] }}</td>
                                <td>
                                    {{ $val['publisher_name'] }}<br/>
                                    @if ($val['publisher_id'] != 0)
                                        <small>
                                            <a href="{{ \Illuminate\Support\Facades\URL::to('publisher/login?id=' . $val['publisher_id']) }}" target="_blank">
                                                <i class=" mdi mdi-login-variant"></i>Login
                                            </a>
                                        </small>
                                    @endif
                                </td>
                                <td>@if($val['platform'] === 1)<i class="mdi mdi-android" style="color: #a3c83e"></i> @else <i class="mdi mdi-apple"></i> @endif</td>
                                <td>{{ $formatMap[$val['format']] }}</td>
                                <td>{{ $val['adapter'] }}</td>
                                <td>{{ strftime('%Y-%m-%d %H:%M:%S', $val['create_time']) }}<br/><small>{{ strftime('%Y-%m-%d %H:%M:%S', $val['update_time']) }}</small></td>
                                <td><a href="{{ Illuminate\Support\Facades\URL::to('firm-adapter/'.$val['id'] . '/edit') }}" class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm">编辑</a></td>
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
        const FIRM_ID_INPUT = $('input[name="firm_id"]');
        function onFirmIdInput() {
            console.log('test')
            $('select[name="firm_id"]').attr('disabled', $(FIRM_ID_INPUT).val() != '');
        }
        $(FIRM_ID_INPUT).change(onFirmIdInput);
        $(function () {
            onFirmIdInput();
        })
    </script>
@endsection
