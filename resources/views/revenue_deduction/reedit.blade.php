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
                            <a href="{{ URL::to('revenue-deduction') }}">Manage Revenue Deduction</a>
                        </li>
                        <li class="breadcrumb-item active">ReRun Manage Revenue Deduction</li>
                    </ol>
                </div>
                <h4 class="page-title">ReRun Manage Revenue Deduction</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::open(array('url' => array('revenue-deduction/reupdate', $data['id']), 'method' => 'PUT')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Placement Id</label>
                        <input type="text" name="placement_id" class="form-control" readonly value="{{ $onePlacement['uuid']}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher Id </label>
                        <input name="publisher_id" readonly type="text" class="form-control"  value="{{ $onePlacement['publisher_id']}}">
                        @if ($errors->has('publisher_id'))
                            <span class="help-block">
                            <strong>{{ $errors->first('publisher_id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input name="date" id="date" type="text" class="form-control" placeholder="Y/M/D">
                    </div>
                </div>

                <div class="form-row">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection