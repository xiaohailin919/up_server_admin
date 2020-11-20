@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Revenue Deduction (Sub Account)</li>
                    </ol>
                </div>
                <h4 class="page-title">Revenue Deduction (Sub Account)</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select name="dimension" class="form-control">
                                <option value="publisher"
                                        @if ($dimension == 'publisher') selected="selected" @endif>Publisher</option>
                                <option value="app-placement"
                                        @if ($dimension == 'app-placement') selected="selected" @endif>App or Placement</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control"
                                   id="inputPublisherId" placeholder="Publisher ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_uuid" value="{{ $appUuid }}" class="form-control"
                                   id="inputAppUuid" placeholder="APP ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_uuid" value="{{ $placementUuid }}" class="form-control"
                                   id="inputPlacementUuid" placeholder="Placement ID">
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
                            <a href="{{ URL::to('revenue-deduction-sub-account/create') }}" class="btn btn-info">Add</a>
                        </div>

                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Publisher</th>
                            <th>APP</th>
                            <th>Placement</th>
                            <th>Expected Value</th>
                            <th>Status</th>
                            <th>Manager</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($blackAssignmentsList as $val)
                            <tr>
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
                                <td>{{ $val['expected_value'] }}</td>
                                <td>{{ $val['status_name'] }}</td>
                                <td>
                                    {{ $val['admin_name'] }}<br/>
                                    <small>{{ $val['utime'] }}</small>
                                </td>
                                <td>
                                    <a href="{{ URL::to('revenue-deduction-sub-account/' . $val['id'] . '/edit?dimension=' . $dimension) }}"
                                       class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a><br/><br/>
                                    <a href="{{ URL::to('revenue-deduction-sub-account/reedit/' . $val['id'] . '?dimension=' . $dimension) }}"
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
