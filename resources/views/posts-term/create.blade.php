@extends('layouts.admin')

@section('content')
    <style>
        .mt-3, .my-3 {
             margin-top: 0!important;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('/posts-term') }}">Categories & Tags</a></li>
                        <li class="breadcrumb-item active">Add Categories & Tags</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Categories & Tags</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="{{\Illuminate\Support\Facades\URL::to('/posts-term')}}">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <label class="col-md-1">类型</label>
                        <div class="form-group">
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="type" type="radio" value="1" @if('1' === old('type', '1')) checked @endif onclick="changeDimension()">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">分类</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="type" type="radio" value="2" @if('2' === old('type', '1')) checked @endif onclick="changeDimension()">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">标签</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">名称</label>
                        <div class="form-group col-md-5  {{ $errors->has('name') ? 'has-error' : '' }}">
                            <input class="form-control" name="name" type="text" value="{{ old('name') }}" required/>
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">别名</label>
                        <div class="form-group col-md-5 {{ $errors->has('slug') ? 'has-error' : '' }}">
                            <input class="form-control" name="slug" type="text" value="{{ old('slug') }}"/>
                            @if ($errors->has('slug'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('slug') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">描述</label>
                        <div class="form-group col-md-5  {{ $errors->has('description') ? 'has-error' : '' }}">
                            <textarea class="form-control" name="description">{{ old('description') }}</textarea>
                            @if ($errors->has('description'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row category-hide">
                        <label class="col-md-1">是否热门</label>
                        <div class="form-group">
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="popular" type="radio" value="1" @if('1' === old('popular', '1')) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">否　</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="popular" type="radio" value="2" @if('2' === old('popular', '1')) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">是　</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">排序权重</label>
                        <div class="form-group col-md-5">
                            <input class="form-control" name="rank" type="number" value="{{ old('rank', '0') }}"/>
                        </div>
                    </div>
{{--                    <div class="form-row">--}}
{{--                        <label class="col-md-1">状态</label>--}}
{{--                        <div class="form-group col-md-5">--}}
{{--                            <select class="form-control" name="status">--}}
{{--                                @foreach ($statusMap as $key => $val)--}}
{{--                                    <option value="{{ $key }}" @if ((int)old('status', '3') === $key) selected="selected" @endif>{{ $val }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="form-row">
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function changeDimension() {
            let val = $('input[name="type"]:checked').val();
            $('.category-hide').each(function () {
                $(this).attr('hidden', val === '1' ? 'hidden' : false);
            });
        }
        $(function () {
            changeDimension();
        });

    </script>
@endsection
