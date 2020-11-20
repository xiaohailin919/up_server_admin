@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Revenue Deduction (Main Account)</li>
                    </ol>
                </div>
                <h4 class="page-title">Revenue Deduction (Main Account)</h4>
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
                            <input type="text" name="app_uuid" value="{{ $appUuid }}" class="form-control"
                                   id="inputAppUuid" placeholder="APP ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_name" value="{{ $appName }}" class="form-control"
                                   id="inputAppName" placeholder="APP Name">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_uuid" value="{{ $placementUuid }}" class="form-control"
                                   id="inputPlacementUuid" placeholder="Placement ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_name" value="{{ $placementName }}" class="form-control"
                                   id="inputPlacementName" placeholder="Placement Name">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="type" class="form-control">
                                <option value="all">Type</option>
                                @foreach ($typeMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($type) && ($type == $key)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="all">All Status</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (is_numeric($status) && ($status == $key)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                            <a href="{{ URL::to('revenue-deduction/add') }}" class="btn btn-info">Add</a>
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
                            <th>Publisher</th>
                            <th>APP</th>
                            <th>Placement</th>
                            <th>Type</th>
                            <th>Expected Value</th>
                            <th>Range</th>
                            <th>Actual Value</th>
                            <th>Status</th>
                            <th>Manager</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($blackAssignmentsList as $val)
                            <tr>
                                <th scope="row">
                                    {{ $val['id'] }}
                                </th>
                                <td>
                                    {{ $val['publisher_name'] }}<br/>
                                    <small>{{ $val['publisher_id'] }}</small>
                                </td>
                                <td>
                                    {{ $val['app_name'] }}<br/>
                                    <small>{{ $val['app_uuid'] }}</small>
                                </td>
                                <td>
                                    {{ $val['placement_name'] }}<br/>
                                    <small>{{ $val['placement_uuid'] }}</small>
                                </td>
                                <td>{{ $val['type_name'] }}</td>
                                <td>{{ $val['expected_value'] }}</td>
                                <td>{{ $val['random_range'] }}</td>
                                <td>{{ $val['actual_value'] }}</td>
                                <td>{{ $val['status_name'] }}</td>
                                <td>
                                    {{ $val['admin_name'] }}<br/>
                                    <small>{{ $val['utime'] }}</small>
                                </td>
                                <td>
                                    <a href="{{ URL::to('revenue-deduction/' . $val['id'] . '/edit') }}"
                                       class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a><br/><br/>
                                    <a href="{{ URL::to('revenue-deduction/reedit/'.$val['id']) }}"
                                       class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Rerun</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{$page->getCount()}}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {!!$page->show()!!}
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection
