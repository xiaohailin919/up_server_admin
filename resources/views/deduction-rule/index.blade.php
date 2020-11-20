@extends('layouts.admin')

@section('content')
    <style>
        .col-md-4 {
            flex: 0 0 auto;
            max-width: none;
            width: fit-content;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage
                            @if($type == \App\Models\MySql\DeductionRule::TYPE_IMPRESSION)
                                Impression
                            @else
                                Fill Rate
                            @endif
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">Manage
                    @if($type == \App\Models\MySql\DeductionRule::TYPE_IMPRESSION)
                        Impression
                    @else
                        Fill Rate
                    @endif
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
                            <select name="dimension" id="dimension" class="form-control">
                                <option value="all">All Dimension</option>
                                @foreach ($dimensionMap as $key => $val)
                                    <option value="{{ $key }}" @if($dimension == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2" >
                            <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control" placeholder="Publisher ID" />
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="app_id" value="{{ $appId }}" class="form-control" placeholder="App ID"  @if($dimension == \App\Models\MySql\DeductionRule::DIMENSION_PUBLISHER) disabled @endif>
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="placement_id" value="{{ $placementId }}" class="form-control" placeholder="Placement ID" @if($dimension != 'all' && $dimension != \App\Models\MySql\DeductionRule::DIMENSION_PLACEMENT) disabled @endif>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="all" >All Status　　　</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if (is_numeric($status) && $status == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                            @if($type == \App\Models\MySql\DeductionRule::TYPE_IMPRESSION)
                                <a href="{{ URL::to('manage-impression/create') }}" class="btn btn-info">Add</a>
                            @else
                                <a href="{{ URL::to('manage-fill-rate/create') }}" class="btn btn-info">Add</a>
                            @endif
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
                            <th>App</th>
                            <th>Placement</th>
                            <th>Rate</th>
                            <th>Status</th>
                            <th>Manager</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val['id'] }}</td>
                                <td>
                                    {{ $val['publisher_name'] }}<br/>
                                    <small>{{ $val['publisher_id'] }}</small>
                                </td>
                                <td>
                                    @if($val['app_id'] == '')
                                        -
                                    @else
                                        @if($val['app_platform'] == \App\Models\MySql\App::PLATFORM_ANDROID)
                                            <i class="mdi mdi-android" style="color: #a3c83e;"></i>
                                        @elseif($val['app_platform'] == \App\Models\MySql\App::PLATFORM_IOS)
                                            <i class="mdi mdi-apple"></i>
                                        @endif
                                        {{ $val['app_name'] }}
                                        <br/>
                                        {{ $val['app_id'] }}
                                    @endif
                                </td>
                                <td>
                                    @if($val['placement_id'] == '')
                                        -
                                    @else
                                        {{ $val['placement_name'] }}<br/>{{ $val['placement_id'] }}
                                    @endif
                                </td>
                                <td>{{ $val['discount'] / 100 }}</td>
                                <td>{{ $statusMap[$val['status']] }}</td>
                                <td>{{ $val['admin_name'] }}<br/>{{ $val['update_time'] }}</td>
                                @if($type == \App\Models\MySql\DeductionRule::TYPE_IMPRESSION)
                                    <td><a href="{{ URL::to('manage-impression/'.$val['id']) }}" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Edit</a></td>
                                @else
                                    <td><a href="{{ URL::to('manage-fill-rate/'.$val['id']) }}" class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Edit</a></td>
                                @endif
                            </tr>
                        @endforeach
                        @if(count($data) < 10 || $data->total() / 10 == app('request')->input('page'))
                            <tr>
                                <td></td>
                                <td>Default<br/></td>
                                <td><br/></td>
                                <td><br/></td>
                                <td>1</td>
                                <td>Active</td>
                                <td>Jeff<br/>20200201 14:06:38</td>
                                <td><a href="javascript:void(0);" class="btn btn-outline-secondary waves-light waves-effect w-sm btn-sm">Edit</a></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $data->total() + 1 }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $data->appends([
                            'publisherId' => $publisherId,
                            'appId'       => $appId,
                            'placementId' => $placementId,
                            'dimension'   => $dimension,
                            'status'      => $status,
                        ])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection