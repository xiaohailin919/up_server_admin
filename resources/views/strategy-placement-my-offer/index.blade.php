@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">MyOffer Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">MyOffer Strategy</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_uuid" value="{{ $placementUuid }}" class="form-control" placeholder="Placement ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_name" value="{{ $placementName }}" class="form-control" placeholder="Placement Name">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $appUuid }}" class="form-control" placeholder="APP ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control" placeholder="Publisher ID">
                        </div>

                        <div class="form-group col-md-2">
                            <select name="format" class="form-control">
                                <option value="all" >All Format</option>
                                @foreach ($formatMap as $key => $val)
                                    <option value="{{ $key }}" @if (is_numeric($format) && $format == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                            <a href="{{ URL::to('strategy-placement-my-offer/create') }}" class="btn btn-info">Add</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Placement ID</th>
                            <th>Placement Name</th>
                            <th>App ID</th>
                            <th>App Name</th>
                            <th>Publisher ID</th>
                            <th>Publisher Name</th>
                            <th>AD Type</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val['id'] }}</td>
                                <td>{{ $val['placement_uuid'] }}</td>
                                <td>{{ $val['placement_name'] }}</td>
                                <td>{{ $val['app_uuid'] }}</td>
                                <td>{{ $val['app_name'] }}</td>
                                <td>{{ $val['publisher_id'] }}</td>
                                <td>{{ $val['publisher_name'] }}</td>
                                <td>{{ $val['format_name'] }}</td>
                                <td>
                                    {{ $val['admin_name'] }}<br />
                                    <small>{{ $val['update_time'] }}</small>
                                </td>
                                <td>{{ $val['status_name'] }}</td>
                                <td>
                                    <a href="{{ route("strategy-placement-my-offer.copy", ['src_id' => $val['id']]) }}" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Copy</a>
                                    <a href="{{ URL::to('strategy-placement-my-offer/'.$val['id']) }}" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Edit</a>
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
                            'placement_uuid' => $placementUuid,
                            'placement_name' => $placementUuid,
                            'app_uuid' => $appUuid,
                            'publisher_id' => $publisherId,
                            'format' => $format,
                        ])->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
