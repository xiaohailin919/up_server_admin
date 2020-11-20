@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Upload Network Report</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    Upload Network Report
                    <i class="mdi mdi-information-outline" data-toggle="tooltip"
                       title="Only for Tencent、Baidu and TouTiao" data-trigger="hover" data-placement="right"></i>
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control"
                                   id="inputPublisherId" placeholder="Publisher ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_name" value="{{ $publisherName }}" class="form-control"
                                   id="inputPublisherName" placeholder="Publisher Name">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="nw_firm_id" class="form-control">
                                <option value="">Network</option>
                                @foreach ($nwFirmMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($nwFirmId) && ($nwFirmId == $key)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="">Status</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($status) && ($status == $key)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
{{--                            <a href="{{ URL::to('report-import/create') }}" class="btn btn-success">Upload</a>--}}
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>开发者</th>
                            <th>Network</th>
                            <th>账号</th>
                            <th>数据开始日期</th>
                            <th>数据结束日期</th>
                            <th>导入时间</th>
                            <th>处理完成时间</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val['publisher_name'] }}<br/>{{ $val['publisher_id'] }}</td>
                                <td>{{ $val['firm_name'] }}</td>
                                <td>{{ $val['network_name'] }}<br/>{{ $val['network_id'] }}</td>
                                <td>{{ $val['start_date'] }}</td>
                                <td>{{ $val['end_date'] }}</td>
                                <td>{{ $val['create_time'] }}</td>
                                <td>{{ $val['import_time'] }}</td>
                                <td>{{ $val['status_name'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{$data->total()}}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $data->appends([
                            'publisherId' => $publisherId,
                            'publisherName' => $publisherName,
                            'nwFirmId' => $nwFirmId,
                            'status' => $status
                        ])->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
