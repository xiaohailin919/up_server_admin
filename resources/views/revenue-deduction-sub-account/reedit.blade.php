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
                            <a href="{{ URL::to('revenue-deduction-sub-account') }}">Revenue Deduction (Sub Account)</a>
                        </li>
                        <li class="breadcrumb-item active">ReRun Revenue Deduction (Sub Account)</li>
                    </ol>
                </div>
                <h4 class="page-title">ReRun Revenue Deduction (Sub Account)</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::open(array('url' => array('revenue-deduction-sub-account/reupdate', $data['id']), 'method' => 'PUT')) }}
                <input type="hidden" name="publisher_id" value="{{ $data['publisher_id'] }}" />
                <input type="hidden" name="app_id" value="{{ $data['app_id'] }}" />
                <input type="hidden" name="placement_id" value="{{ $data['placement_id'] }}" />
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher ID</label>
                        <input readonly type="text" class="form-control"  value="{{ $data['publisher_id']}}">
                    </div>
                </div>
                @if (!empty($appUuid))
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>App ID</label>
                        <input readonly type="text" class="form-control"  value="{{ $appUuid }}">
                    </div>
                </div>
                @endif
                @if (!empty($placementUuid))
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Placement ID</label>
                        <input type="text" class="form-control" readonly value="{{ $placementUuid }}">
                    </div>
                </div>
                @endif
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Date Start</label>
                        <input name="date_start" id="date_start" type="text" class="form-control input-datetimepicker" placeholder="Y/M/D">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Date End</label>
                        <input name="date" id="date" type="text" class="form-control input-datetimepicker" placeholder="Y/M/D">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection