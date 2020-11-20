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
                        <li class="breadcrumb-item active">Update Manage Revenue Deduction</li>
                    </ol>
                </div>
                <h4 class="page-title">Update Manage Revenue Deduction</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::open(array('url' => array('revenue-deduction', $data['id']), 'method' => 'PUT')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Placement Id</label>
                        <input type="text" class="form-control" readonly value="{{ $onePlacement['uuid']}}">
                        <input name="placement_id" type="text" class="form-control" hidden value="@if($onePlacement['uuid'] != "") {{$onePlacement['uuid']}} @else 0 @endif">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Type</label>
                        <select name="type" class="form-control" id="type">
                            <option @if ($data['assignment_type'] == 1 ) selected @endif  value="1" >Discount</option>
                            <option @if ($data['assignment_type'] == 2 ) selected @endif  value="2" >eCPM</option>
                        </select>
                        @if ($errors->has('type'))
                            <span class="help-block">
                            <strong>{{ $errors->first('type') }}</strong>
                        </span>
                        @endif
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
                        <label>Range (%)</label>
                        <input name="range" type="text" class="form-control"  value="{{ $data['random_range']}}">
                        @if ($errors->has('range'))
                            <span class="help-block">
                            <strong>{{ $errors->first('range') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Maximum Compensation ($)</label>
                        <input name="maximum_compensation" type="text" class="form-control"  value="{{ $data['maximum_compensation']}}">
                        @if ($errors->has('maximum_compensation'))
                            <span class="help-block">
                            <strong>{{ $errors->first('maximum_compensation') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option @if ($data['status'] == 3 ) selected @endif  value="3" >Active</option>
                            <option @if ($data['status'] == 1 ) selected @endif  value="1" >Paused</option>
                        </select>
                        @if ($errors->has('status'))
                            <span class="help-block">
                            <strong>{{ $errors->first('status') }}</strong>
                        </span>
                        @endif
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
    <script>
        $('#type').change(function(){
            console.log("2333");
            var type = $("#type").val();
            var text = "";
            switch(type){
                case "1":
                    text = "(%)";
                    break;
                case "2":
                    text = "($)";
                    break;
            }

            $("#expect_val_unit").text(text);
            $("#expected_value").val("");
        });
    </script>

@endsection
