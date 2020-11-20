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
                    <li class="breadcrumb-item active">Edit Manage Revenue Deduction</li>
                </ol>
            </div>
            <h4 class="page-title">Edit Manage Revenue Deduction</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            {{ Form::open(array('url' => array('revenue-deduction'), 'method' => 'POST')) }}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Placement Id</label>
                    <input name="placement_id" type="text" class="form-control" value="">
                    @if ($errors->has('placement_id'))
                        <span class="help-block text-danger">
                            <strong>{{ $errors->first('placement_id') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6" >
                    <label>Type</label>
                    <select id="type" name="type" class="form-control">
                        <option value="1" >Discount</option>
                        <option value="2" >eCPM</option>
                    </select>
                    @if ($errors->has('type'))
                        <span class="help-block text-danger">
                            <strong>{{ $errors->first('type') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Expected value <span id="expect_val_unit"></span></label>
                    <input name="expected_value" id="expected_value" type="text" class="form-control" value="70">
                    @if ($errors->has('expected_value'))
                        <span class="help-block text-danger">
                            <strong>{{ $errors->first('expected_value') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Range % </label>
                    <input name="range" type="text" class="form-control" value="0">
                    @if ($errors->has('range'))
                        <span class="help-block text-danger">
                            <strong>{{ $errors->first('range') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Maximum Compensation $</label>
                    <input name="maximum_compensation" type="text" class="form-control" value="1000">
                    @if ($errors->has('maximum_compensation'))
                        <span class="help-block text-danger">
                            <strong>{{ $errors->first('maximum_compensation') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="3" >Active</option>
                        <option value="1" >Paused</option>
                    </select>
                    @if ($errors->has('status'))
                        <span class="help-block text-danger">
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
    })
</script>

@endsection
