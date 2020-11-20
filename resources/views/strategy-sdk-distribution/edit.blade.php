@extends('layouts.admin')

@section('content')
    <style>
        .help-block {
            color: red;
        }
        .delete-btn {
            line-height: 38px;
            color: red!important;
        }
        .delete-btn:hover {
            color: #d0021b;
            cursor: pointer;
        }
        .mt-3 {
            margin-top: 0!important;
        }
        .btn-sm {
            padding: 4px 8px;
            font-size: 14px;
            line-height: 14px;
        }
        .form-row {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('strategy-sdk-distribution') }}">SDK 版本分发规则</a></li>
                        <li class="breadcrumb-item active">添加 SDK 版本分发规则</li>
                    </ol>
                </div>
                <h4 class="page-title">添加 SDK 版本分发规则</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="{{ \Illuminate\Support\Facades\URL::to('strategy-sdk-distribution/' . $data['id']) }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PUT">
                    {{-- 规则类型：指定开发者 / 指定开发者群组 --}}
                    <div class="form-row">
                        <label class="form-group col-md-1">规则类型</label>
                        <div class="form-group col-md-5">
                            @if($data['type'] === 1) 开发者 @else 开发者群组 @endif
                        </div>
                    </div>
                    @if ($data['type'] === 1)
                        <div class="form-row">
                            <label class="form-group col-md-1">开发者 ID</label>
                            <div class="form-group col-md-5">
                                <input class="form-control" value="{{ $data['publisher_id'] }}" disabled>
                            </div>
                        </div>
                    @else
                        <div class="form-row">
                            <label class="form-group col-md-1">开发 ID</label>
                            <div class="form-group col-md-5">
                                <input class="form-control" value="{{ $publisherGroupIdNameMap[$data['publisher_group_id']] }}" disabled>
                            </div>
                        </div>
                    @endif

                    <div class="form-row">
                        <label class="form-group col-md-1">安卓原版</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            @foreach($data['android'] as $android)
                                <div class="form-row form-group">
                                    <input class="form-control col-md-10" name="android[]" value="{{ $android }}" placeholder="请填写SDK版本，示例 5.5.7" required>
                                    <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">iOS 原版</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            @foreach($data['ios'] as $ios)
                                <div class="form-row form-group">
                                    <input class="form-control col-md-10" name="ios[]" value="{{ $ios }}" placeholder="请填写SDK版本，示例 5.5.7" required>
                                    <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">Unity 安卓</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            @foreach($data['unity_android'] as $unityAndroid)
                                <div class="form-row form-group">
                                    <input class="form-control col-md-10" name="unity_android[]" value="{{ $unityAndroid }}" placeholder="请填写SDK版本，示例 5.5.7" required>
                                    <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">Unity iOS</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            @foreach($data['unity_ios'] as $unityIOS)
                                <div class="form-row form-group">
                                    <input class="form-control col-md-10" name="unity_ios[]" value="{{ $unityIOS }}" placeholder="请填写SDK版本，示例 5.5.7" required>
                                    <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">Unity 安卓 + iOS</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            @foreach($data['unity_android_ios'] as $unityAndroidIOS)
                                <div class="form-row form-group">
                                    <input class="form-control col-md-10" name="unity_android_ios[]" value="{{ $unityAndroidIOS }}" placeholder="请填写SDK版本，示例 5.5.7" required>
                                    <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1"></label>
                        <div class="form-group col-md-5">
                            <button class="btn btn-primary" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        /* 规则类型切换 */
        function onRuleTypeSelect(node) {
            let val = $(node).val();
            $('#publisher-id-input').attr('hidden', val === '2');
            $('#publisher-group-input').attr('hidden', val === '1');
            $('#publisher-id-input textarea').attr('required', val === '1');
            $('#publisher-group-input select').attr('required', val === '2');
        }

        /* 增加输入行 */
        function addRow(node) {
            let parent = $(node).parent();
            let item = $(node).parent().next();
            let newItem = item[0].cloneNode(true);
            let newInput = $(newItem).find('input')[0];
            $(newInput).val('');
            $(parent).after(newItem);
        }

        /* 删除输入行 */
        function deleteRow(node) {
            if ($(node).parent().parent().find('input').length !== 1) {
                $(node).parent().remove();
            }
        }
    </script>
@endsection