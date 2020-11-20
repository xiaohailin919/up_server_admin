@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Upload Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">TC Upload Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $appUuid }}" class="form-control" placeholder="APP ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_name" value="{{ $appName }}" class="form-control" id="inputAppName" placeholder="APP Name">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="all" >All Status</option>
                                @foreach ($statusMap as $key => $val)
                                <option value="{{ $key }}" @if ($status === $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                            <a href="{{ URL::to('tc-upload-rule/create') }}" class="btn btn-info">Add</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>App ID</th>
                            <th>App Name</th>
                            <th>类名黑名单</th>
                            <th>URL域名白名单</th>
                            <th>TC总开关</th>
                            <th>收集Webview URL的域名开关</th>
                            <th>收集Storekit的Apple id开关</th>
                            <th>收集外跳openurl开关</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $val)
                        <tr>
                            <td>{{ $val['app_uuid'] }}</td>
                            <td>{{ $val['app_name'] }}</td>
                            <td>
                                @foreach( $val['class_name_black_list'] as $className)
                                    <p>{{ $className }}</p>
                                @endforeach
                            </td>
                            <td>
                                @foreach( $val['domain_name_white_list'] as $domainName)
                                    <p>{{ $domainName }}</p>
                                @endforeach
                            </td>
                            <td>{{ $val['tc_main_switch'] }}</td>
                            <td>{{ $val['collect_webview_url_switch'] }}</td>
                            <td>{{ $val['collect_storekit_apple_id_switch'] }}</td>
                            <td>{{ $val['collect_openurl_switch'] }}</td>
                            <td>
                                {{ $val['admin_name'] }}<br />
                                <small>{{ $val['update_time'] }}</small>
                            </td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>
                                <a href="{{ route("tc-upload-rule.copy", ['src_id' => $val['id']]) }}" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Copy</a>
                                <a href="{{ URL::to('tc-upload-rule/'.$val['id']) }}" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Edit</a>
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
                        {{ $data->appends([
                            'status' => $status,
                        ])->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
