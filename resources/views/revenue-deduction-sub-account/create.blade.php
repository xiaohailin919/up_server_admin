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
            {{ Form::open(array('url' => array('revenue-deduction-sub-account'), 'method' => 'POST')) }}
            <div class="form-row">
                <div class="form-group col-md-6" >
                    <label>Dimension</label>
                    <select id="dimension" name="dimension" class="form-control">
                        <option value="app" >App</option>
                        <option value="placement" >Placement</option>
                    </select>
                    @if ($errors->has('dimension'))
                        <span class="help-block text-danger">
                            <strong>{{ $errors->first('dimension') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Publisher Id</label>
                    <input name="publisher_id" type="text" class="form-control" value="">
                    @if ($errors->has('publisher_id'))
                        <span class="help-block text-danger">
                            <strong>{{ $errors->first('publisher_id') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>App Id</label>
                    <input name="app_id" type="text" class="form-control" value="">
                    @if ($errors->has('app_id'))
                        <span class="help-block text-danger">
                            <strong>{{ $errors->first('app_id') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
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
    $(document).ready(function () {
        $("#dimension").change(function(){
            var dimension = $(this).val();
            console.log(dimension);
            switch (dimension) {
                case "publisher":
                    $("input[name='publisher_id']").parents(".form-row").show();
                    $("input[name='app_id']").parents(".form-row").hide();
                    $("input[name='placement_id']").parents(".form-row").hide();
                    break;
                case "placement":
                    $("input[name='publisher_id']").parents(".form-row").show();
                    $("input[name='app_id']").parents(".form-row").hide();
                    $("input[name='placement_id']").parents(".form-row").show();
                    break;
                default:
                    $("input[name='publisher_id']").parents(".form-row").show();
                    $("input[name='app_id']").parents(".form-row").show();
                    $("input[name='placement_id']").parents(".form-row").hide();
                    break;
            }
        }).change();
        $('#type').change(function () {
            var type = $("#type").val();
            var text = "";
            switch (type) {
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
    });
</script>

@endsection
