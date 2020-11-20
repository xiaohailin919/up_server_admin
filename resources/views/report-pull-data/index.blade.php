@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage Report API</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage Report API</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select class="form-control" name="type">
                                <option value="0">全部类型</option>
                                @foreach ($typeMap as $key => $val)
                                    <option value="{{ $key }}" @if ($type == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" type="text" name="publisher_id" value="{{ $publisherId }}" id="inputPublisherId" placeholder="Publisher ID">
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="nw_firm_id">
                                <option value="-1">广告平台</option>
                                @foreach ($nwFirmMap as $key => $val)
                                    <option value="{{ $key }}" @if ($nwFirmId == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="status">
                                <option value="0" >全部状态</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if ($status == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button class="btn btn-primary" type="submit">搜索</button>
                            <a class="btn btn-info" href="{{ \Illuminate\Support\Facades\URL::to('report-unit-log/create') }}">添加</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped" style="text-align: center">
                        <thead>
                        <tr>
                            <th>类型</th>
                            <th>开发者</th>
                            <th>广告平台</th>
                            <th>时间维度</th>
                            <th>拉取日期</th>
                            <th>操作</th>
                            <th>任务开始时间</th>
                            <th>任务结束时间</th>
                            <th>机器数量</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val['type'] }}</td>
                                <td>{{ $val['publisher_name'] }}<br/>{{ $val['publisher_id'] }}</td>
                                <td>{{ $val['nw_firm_name'] }}</td>
                                <td>{{ $val['pull_type'] }}</td>
                                <td>{{ $val['sdate'] }}</td>
                                <td>{{ $val['admin_name'] }}<br/>{{ $val['utime'] }}</td>
                                <td>{{ $val['pull_start_time'] }}</td>
                                <td>{{ $val['pull_end_time'] }}</td>
                                <td>{{ $val['machine_num'] }}</td>
                                <td>{{ $val['status'] }}</td>
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
                        {{ $data->appends(['type' => $type, 'publisherId' => $publisherId, 'status' => $status, 'nwFirmId' => $nwFirmId])->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection