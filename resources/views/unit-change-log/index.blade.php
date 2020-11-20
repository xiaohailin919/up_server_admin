@extends('layouts.admin')

@section('content')
        <!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group pull-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                    <li class="breadcrumb-item active">Manage Change Log</li>
                </ol>
            </div>
            <h4 class="page-title">Manage Change Log</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <form method="GET">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <input class="form-control input-daterange-datepicker" type="text" name="daterange"
                               value="{{ $dateRange['start'] }} - {{ $dateRange['end'] }}">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control"
                               id="inputPublisherId" placeholder="Publisher ID">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" name="placement_uuid" value="{{ $placementUuid }}" class="form-control"
                               id="inputPlacementUuid" placeholder="Placement ID">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" name="traffic_group_id" value="{{ $trafficGroupId }}" class="form-control"
                               id="traffic_group_id" placeholder="Traffic Group ID">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <input type="text" name="segment_id" value="{{ $segmentId }}" class="form-control"
                               id="segment_id" placeholder="Segment ID">
                    </div>
                    <div class="form-group col-md-4">
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>
                        {{--<button type="submit" name="export" value="1" class="btn btn-success">--}}
                        {{--Export Excel--}}
                        {{--</button>--}}
                    </div>
                </div>
            </form>
        </div>
        <div class="card-box">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Publisher</th>
                        <th>APP</th>
                        <th>Placement</th>
                        <th>Email</th>
                        <th>IP</th>
                        <th>Operation</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($list as $val)
                        <tr>
                            <th scope="row">
                                {{ $val['create_time'] }}
                            </th>
                            <td>
                                {{ $val['publisher_name'] }} </br>
                                {{ $val['publisher_id'] }}
                            </td>
                            <td>
                                {{ $val['app_name'] }} </br>
                                {{ $val['app_uuid'] }}
                            </td>
                            <td>
                                {{ $val['placement_name'] }} </br>
                                {{ $val['placement_uuid'] }}
                            </td>
                            <td>{{ $val['email'] }}</td>
                            <td>{{ $val['ip'] }}</td>
                            <td>
                                <a href="{{ URL::to('unit-change-log/' . $val['id'] . '/edit') }}"
                                   class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    Total <strong>{{ $list->total() }}</strong>
                </div>
                <div class="col-sm-12 col-md-7">
                    {{
                        $list->appends(
                        [
                            'daterange'        => $daterange,
                            'publisher_id'     => $publisherId,
                            'placement_uuid'   => $placementUuid,
                            'traffic_group_id' => $trafficGroupId,
                            'segment_id'       => $segmentId,
                        ])
                        ->links()
                    }}
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function () {
        $(".sorting").each(function () {
            $(this).click(function () {
                var href = window.location.href.replace(/(\?|&)order_by\[(\w+)\]=(\w+)/g, '');
                var field = $(this).attr('data-field');
                var sort = $(this).attr('data-sort');
                console.log(href);
                console.log(field);
                window.location.href = href + (href.indexOf('?') <= -1 ? '?' : '&') + 'order_by[' + field + ']=' + sort;
            });
        });
    });
</script>

@endsection
