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
                            <a href="{{ URL::to('publisher') }}">Manage Publisher</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Publisher</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Publisher Allow Firms</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('url' => "/publisher/allow-firms/{$data['id']}", 'method' => 'PUT', 'id' => 'editForm')) }}
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
                    <div class="form-group col-md-8">
                        <label class="d-block">
                            广告平台
                            <div class="float-right">
                                <a href="#" id="selectAll">全选</a> /
                                <a href="#" id="selectInverse">反选</a> /
                                <a href="#" id="selectDefault">默认</a>
                            </div>
                        </label>
                        <div class="mt-3 row">
                            @foreach($firms as $key => $val)
                                <div class="col-2">
                                    <label class="custom-control custom-checkbox">
                                        <input name="allow_firms[]"
                                               type="checkbox"
                                               id="firms_{{ $val['id'] }}"
                                               value="{{ $val['id'] }}"
                                               class="custom-control-input"　
                                               @if (in_array($val['id'], $data['allow_firms'])) checked @endif
                                               @if (in_array($val['id'], $defaultAllowFirms)) data-default="1" @endif >
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ $val['name'] }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <span class="help-block">
                            <div><small>1、旧版开发者后台只能使用快手之前的厂商</small></div>
                            <div><small>2、如全不选则表示使用默认厂商</small></div>
                        </span>
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
        $(document).ready(function () {
            $("#selectAll").click(function() {
                $("input[name='allow_firms[]']:checkbox").each(function(){
                    $(this).prop("checked", true);
                });
            });

            $("#selectInverse").click(function() {
                $("input[name='allow_firms[]']:checkbox").each(function() {
                    if($(this).prop("checked") === false){
                        $(this).prop("checked", true);
                    }else{
                        $(this).prop("checked", false);
                    }

                });
            });

            $("#selectDefault").click(function() {
                $("#selectAll").click();
                $("#selectInverse").click();
                $("input[name='allow_firms[]'][data-default='1']").each(function() {
                    $(this).prop("checked", true);
                });
            });
        });
    </script>

@endsection
