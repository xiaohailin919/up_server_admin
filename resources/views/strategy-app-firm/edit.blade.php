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
                            <a href="{{ URL::to('strategy-app-firm') }}">App Firm Strategy</a>
                        </li>
                        <li class="breadcrumb-item active">Edit App Firm Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit App Firm Strategy</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-app-firm.update', $data['id']), 'method' => 'PUT')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>监控上报</label>
                        <select name="upload_sw" class="form-control">
                            <option value="0" @if ($data['upload_sw'] == 0) selected @endif>No</option>
                            <option value="1" @if ($data['upload_sw'] != 0) selected @endif>Yes</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>仅点击上报</label>
                        <select name="click_only" class="form-control">
                            <option value="0" @if ($data['click_only'] == 0) selected @endif>No</option>
                            <option value="1" @if ($data['click_only'] != 0) selected @endif>Yes</option>
                        </select>
                    </div>
                </div>
                @if($data['nw_firm_id'] != 0)
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <div class="mt-3">
                            <label class="custom-control custom-radio">
                                <input name="status" type="radio" value="2" class="custom-control-input"
                                       @if ($data['status'] == 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">On</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input name="status" type="radio" value="1" class="custom-control-input"
                                       @if ($data['status'] != 2) checked="" @endif >
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Off</span>
                            </label>
                        </div>
                    </div>
                </div>
                @endif
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
    (function($){
        
    });
    </script>

@endsection
