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
                            <a href="{{ URL::to('upload-rules') }}">TC Mapping Rules</a>
                        </li>
                        <li class="breadcrumb-item active">Edit TC Mapping Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit TC Mapping Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('tc-mapping-rule.update', $data['id']), 'method' => 'PUT')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Network ID</label>
                        <input type="text" class="form-control" value="{{ $data['nw_firm_id'] }}" name="nw_firm_id" placeholder="请填写Network Id">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Network对应类名</label>
                        <textarea name="class_name" placeholder="每行一个类名" type="text" class="form-control">{{ $data['class_name'] }}</textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach ($statusMap as $key => $val)
                                <option value="{{ $key }}" @if ($data['status'] == $key) selected="selected" @endif>{{ $val }}</option>
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

@endsection

@section('extra_js')
    @include('layouts.upload_extra_js')
@endsection
