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
                <h4 class="page-title">Upload Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select name="publisher_group_id" class="form-control">
                                <option value="" >Publisher Group</option>
                                @foreach ($publisherGroupIdNameMap as $id => $name)
                                    <option value="{{ $id }}" @if ($pageAppends['publisher_group_id'] == $id) selected="selected" @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $pageAppends['app_uuid'] }}" class="form-control" placeholder="APP ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_name" value="{{ $pageAppends['app_name'] }}" class="form-control" id="inputAppName" placeholder="APP Name">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="" >All Status</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if ($pageAppends['status'] == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <button class="btn btn-primary" type="submit">Search</button>
                            <a class="btn btn-info" href="{{ \Illuminate\Support\Facades\URL::to('upload-rules/create') }}">Add</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Rule Type</th>
                            <th>Publisher Group</th>
                            <th>App ID</th>
                            <th>App Name</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $val)
                        <tr>
                            <th scope="row">{{ $val['id'] }}</th>
                            <td>{{ $val['rule_type'] }}</td>
                            <td>{{ $val['publisher_group_name'] }}</td>
                            <td>{{ $val['app_uuid'] }}</td>
                            <td>{{ $val['app_name'] }}</td>
                            <td>
                                {{ $val['admin_name'] }}<br />
                                <small>{{ $val['update_time'] }}</small>
                            </td>
                            <td>{{ $val['status'] }}</td>
                            <td>
                                <a class="btn btn-outline-success waves-light waves-effect w-sm btn-sm" href="{{ route("upload-rules.copy", ['src_id' => $val['id']]) }}">Copy</a>
                                <a class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm" href="{{ \Illuminate\Support\Facades\URL::to('upload-rules/'.$val['id']) }}">Edit</a>
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

@endsection
