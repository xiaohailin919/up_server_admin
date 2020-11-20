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
                            <a href="{{ URL::to('strategy-app') }}">Manage Placement</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Placement</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Placement</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('placement.update', $data['id']), 'method' => 'PUT')) }}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Placement ID</label>
                            <input type="text" class="form-control" readonly="" value="{{ $data['uuid'] }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Placement Name</label>
                            <input type="text" class="form-control" readonly="" value="{{ $data['name'] }}">
                        </div>
                    </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="placement_status" class="form-control">
                            @foreach ($placementStatusMap as $key => $val)
                                <option value="{{ $key }}" @if ($placementStatus == $key) selected="selected" @endif>{{ $val }}</option>
                            @endforeach
                        </select>
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
        function disableRfSetting()
        {
            $("#inputRfKey").attr("disabled", "disabled");
            $("#inputRfAppId").attr("disabled", "disabled");
            $("#inputRfPower").attr("disabled", "disabled");
            $("#inputRfPower2").attr("disabled", "disabled");
        }

        function enableRfSetting()
        {
            $("#inputRfKey").removeAttr('disabled');
            $("#inputRfAppId").removeAttr('disabled');
            $("#inputRfPower").removeAttr('disabled');
            $("#inputRfPower2").removeAttr('disabled');
        }

        function getRfDownload()
        {
            return $("input[type='radio'][name='rf_download']:checked").val();
        }

        function getRfInstall()
        {
            return $("input[type='radio'][name='rf_install']:checked").val();
        }

        function rfSetting()
        {
            if (getRfDownload() == 0 && getRfInstall() == 0) {
                disableRfSetting();
            } else {
                enableRfSetting();
            }
        }

        $(document).ready(function () {
            rfSetting();

            $("input[type='radio'][name='rf_download']").change(function () {

                rfSetting();
            });

            $("input[type='radio'][name='rf_install']").change(function () {

                rfSetting();
            });
        });
    </script>

@endsection
