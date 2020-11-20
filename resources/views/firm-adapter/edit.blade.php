@extends('layouts.admin')

@section('content')
    <style>
        .mt-3 {
            margin: 0!important;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="/network-firm">Firm Adapter</a></li>
                        <li class="breadcrumb-item active">Edit Firm Adapter</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Firm Adapter</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('firm-adapter.update', $data['id']), 'method' => 'PUT', 'enctype' => 'multipart/form-data')) }}
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="firm-id-input">Firm ID</label>
                        <input id="firm-id-input" name="firm_id" type="number" placeholder="Firm ID" value="{{ $data['firm_id'] }}" class="form-control" disabled/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Publisher</label>
                        <input class="form-control" name="publisher_id" value="{{ $data['publisher_name'] . ' | ' . $data['publisher_id'] }}" disabled/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Platform</label>
                        <div class="mt-3">
                            <label class="custom-control custom-radio">
                                <input name="platform" type="radio" value="1" class="custom-control-input" @if($data['platform'] == 1) checked @endif disabled>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description"><i class="mdi mdi-android" style="color: #a3c83e"></i> Android</span>
                            </label>　
                            <label class="custom-control custom-radio">
                                <input name="platform" type="radio" value="2" class="custom-control-input" @if($data['platform'] == 2) checked @endif disabled>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description"><i class="mdi mdi-apple"></i> IOS</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="format-selector">Format</label>
                        <select id="format-selector" name="format" class="form-control" disabled>
                            @for ($i = 0; $i < count($formatMap); $i++)
                                <option value="{{ $i }}" @if($data['format'] == $i) selected @endif>{{ $formatMap[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="adapter-input">Adapter</label>
                        <textarea id="adapter-input" name="adapter" placeholder="Please input the adapter name" type="text" class="form-control" required>{{ $data['adapter'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection