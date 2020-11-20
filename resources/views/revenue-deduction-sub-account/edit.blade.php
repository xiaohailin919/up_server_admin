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
                        <li class="breadcrumb-item active">Edit Revenue Deduction (Sub Account)</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Revenue Deduction (Sub Account)</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::open(array('url' => array('revenue-deduction-sub-account', $data['id']), 'method' => 'PUT')) }}
                <input type="hidden" name="dimension" value="{{ $dimension }}" />
                <input type="hidden" name="publisher_id" value="{{ $data['publisher_id'] }}" />
                <input type="hidden" name="app_id" value="{{ $data['app_id'] }}" />
                <input type="hidden" name="placement_id" value="{{ $data['placement_id'] }}" />
                <input type="hidden" name="range" value="{{ $data['random_range']}}">
                <input type="hidden" name="maximum_compensation" value="{{ $data['maximum_compensation']}}">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher ID</label>
                        <input class="form-control" type="text" value="{{ $data['publisher_id'] }}" readonly>
                    </div>
                </div>
                @if (!empty($appUuid))
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>App ID</label>
                        <input class="form-control" type="text" value="{{ $appUuid }}" readonly>
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
                        <label>Type</label>
                        <input class="form-control" type="text" placeholder="Discount" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Expected value <span id="expect_val_unit">@if ($data['assignment_type'] == 2 ) ($) @endif  @if ($data['assignment_type'] == 1 ) (%) @endif  </span></label>
                        <input name="expected_value" type="text" class="form-control"  value="{{ $data['expected_value']}}">
                        @if ($errors->has('expected_value'))
                            <span class="help-block">
                            <strong>{{ $errors->first('expected_value') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option @if ($data['status'] == 3 ) selected @endif  value="3" >Active</option>
                            @if(!empty($appUuid) || !empty($placementUuid))
                                <option @if ($data['status'] == 1 ) selected @endif  value="1" >Paused</option>
                            @endif
                        </select>
                        @if ($errors->has('status'))
                            <span class="help-block">
                            <strong>{{ $errors->first('status') }}</strong>
                        </span>
                        @endif
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
    <script>

    </script>

@endsection
