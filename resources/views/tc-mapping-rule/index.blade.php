@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">TC Mapping Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">TC Mapping Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input type="text" name="nw_firm_id" value="{{ $nwFirmId }}" class="form-control" placeholder="Network ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="nw_firm_name" value="{{ $nwFirmName }}" class="form-control" placeholder="Network Name">
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
                            <a href="{{ URL::to('tc-mapping-rule/create') }}" class="btn btn-info">Add</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Network ID</th>
                            <th>Network Name</th>
                            <th>Classes</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $val)
                        <tr>
                            <td>{{ $val['nw_firm_id'] }}</td>
                            <td>{{ $val['nw_firm_name'] }}</td>
                            <td>
                                @foreach( $val['class_name'] as $className)
                                    <p>{{ $className }}</p>
                                @endforeach
                            </td>
                            <td>
                                {{ $val['admin_name'] }}<br />
                                <small>{{ $val['update_time'] }}</small>
                            </td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>
                                <a href="{{ URL::to('tc-mapping-rule/'.$val['id']) }}" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Edit</a>
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
