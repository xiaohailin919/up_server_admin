@extends('layouts.admin')

@section('content')
        <!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group pull-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">TopOn</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to('unit-change-log') }}">Manage Unit Change Log</a>
                    </li>
                    <li class="breadcrumb-item active">Unit Change Log Detail</li>
                </ol>
            </div>
            <h4 class="page-title">Change Log Detail</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Publisher</label>
                    <input type="text" class="form-control" readonly=""
                           value="{{ $data['publisher_id'] }} | {{$data['publisher_name']}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>App</label>
                    <input type="text" class="form-control" readonly=""
                           value="{{ $data['app_uuid'] }} | {{$data['app_name']}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Placement</label>
                    <input type="text" class="form-control" readonly=""
                           value=" {{$data['placement_uuid']}} | {{ $data['placement_name'] }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card-box">
            now
        </div>
        <div class="card-box">
            <?php
            @print_r("<pre>");
                @print_r($data1);

            ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-box">
            before
        </div>
        <div class="card-box">
            <?php
            @print_r("<pre>");
                @print_r($data2);

            ?>
        </div>
    </div>
</div>

<div class="row">
    <div style="word-break: break-all" class="col-md-6">
        <div class="card-box">
            complete now
        </div>
        <div class="card-box">
            {{$oData1}}
        </div>
    </div>
    <div style="word-break: break-all" class="col-md-6">
        <div class="card-box">
            complete before
        </div>
        <div class="card-box">
            {{$oData2}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <a class="btn btn-default btn-info" href="{{ URL::to('unit-change-log') }}">Commit</a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection