@extends('layouts.channel')

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
                            <a href="{{ URL::to('publisher') }}">Manage Publisher</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Publisher</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Publisher</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('publisher.update', $data['id']), 'method' => 'PUT', 'id' => 'editForm')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher ID</label>
                        <input type="text" class="form-control" readonly="" value="{{ $data['id'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Publisher Name</label>
                        <input type="text" class="form-control" readonly="" value="{{ $data['name'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="text" class="form-control" readonly="" value="{{ $data['email'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Create Time</label>
                        <input type="text" class="form-control" readonly="" value="{{ date('Y-m-d H:i:s', $data['create_time']) }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        @if($data['status'] == 2)
                            <select name="status" class="form-control" readonly>
                                <option value="2"> Pending </option>
                            </select>
                            @else
                        <select name="status" class="form-control">
                            @foreach ($statusMap as $key => $val)
                                @if($key != 2)
                                <option value="{{ $key }}"
                                        @if ($data['status'] == $key) selected="selected" @endif>{{ $val }}</option>
                                    @elseif($data['status'] == 2)
                                    <option value="2" readonly> Pending </option>
                                @endif
                            @endforeach
                        </select>
                            @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>上传第三方数据权限</label>
                        <div class="mt-3">
                            @foreach($reportImportSwitchMap as $status => $statusText)
                                <label class="custom-control custom-radio">
                                    <input name="report_import_switch" type="radio" id="report_import_{{$status}}" value="{{ $status }}" class="custom-control-input"　@if ($data['report_import_switch'] == $status) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $statusText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Channel Note</label>
                        <textarea name="note_channel" type="text" class="form-control">{{ $data['note_channel'] }}</textarea>
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
            var preventDefault = true;
            $("form#editForm").submit(function () {
                if (!preventDefault) {
                    return true;
                }
                var status = $("select[name='status']").val();
                var text = '';
                if (status == 1) {
                    text = 'Are you sure to blocked this publisher account ？';
                } else if (status == 2) {
                    text = 'Are you sure to make this publisher account pending ？';
                }
                if (text) {
                    swal({
                        title: 'Attention',
                        text: text,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#4fa7f3',
                        cancelButtonColor: '#d57171',
                        confirmButtonText: 'Yes'
                    }).then(function () {
                        preventDefault = false;
                        $("form#editForm").submit();
                    });
                    event.preventDefault();
                }
            });
        });
    </script>

@endsection
