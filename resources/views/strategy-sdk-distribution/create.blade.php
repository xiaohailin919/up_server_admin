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
                <form id="create-form">
                    {{ csrf_field() }}

                    {{-- 规则类型：指定开发者 / 指定开发者群组 --}}
                    <div class="form-row">
                        <label class="form-group col-md-1">规则类型</label>
                        <div class="form-group col-md-5">
                            <div class="mt-3">
                                <label class="custom-control custom-radio col-md-3">
                                    <input class="custom-control-input" name="type" value="1" type="radio" onchange="onRuleTypeSelect(this)" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">指定开发者</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="type" value="2" type="radio" onchange="onRuleTypeSelect(this)">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">指定开发者群组</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    {{-- Publisher ID, 仅在 rule === 1 时出现，默认 required --}}
                    <div class="form-row" id="publisher-id-input">
                        <label class="form-group col-md-1">开发者 ID</label>
                        <div class="form-group col-md-5">
                            <textarea class="form-control" name="publisher_ids" placeholder="Publisher ID" required="required"></textarea>
                        </div>
                        <span class="col-md-2">每行一个开发者 ID</span>
                    </div>

                    {{-- Publisher Group, 仅在 rule === 2 时出现 --}}
                    <div class="form-row" id="publisher-group-input" hidden>
                        <label class="form-group col-md-1">开发者群组</label>
                        <div class="form-group col-md-6">
                            <select class="form-control select2 select2-multiple select2-hidden-accessible" name="publisher_group_ids[]" multiple="" data-placeholder="- Publisher Group -">
                                @foreach ($publisherGroupIdNameMap as $publisherGroupId => $publisherGroupName)
                                    <option value="{{ $publisherGroupId }}">{{ $publisherGroupName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="form-group col-md-1">安卓原版</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            <div class="form-row form-group">
                                <input class="form-control col-md-10" name="android[]" placeholder="请填写SDK版本，示例 5.5.7" required>
                                <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">iOS 原版</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            <div class="form-row form-group">
                                <input class="form-control col-md-10" name="ios[]" placeholder="请填写SDK版本，示例 5.5.7" required>
                                <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">Unity 安卓</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            <div class="form-row form-group">
                                <input class="form-control col-md-10" name="unity_android[]" placeholder="请填写SDK版本，示例 5.5.7" required>
                                <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">Unity iOS</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            <div class="form-row form-group">
                                <input class="form-control col-md-10" name="unity_ios[]" placeholder="请填写SDK版本，示例 5.5.7" required>
                                <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">Unity 安卓 + iOS</label>
                        <div class="form-group col-md-6">
                            <div class="form-row form-group">
                                <a class="btn btn-custom btn-sm" onclick="addRow(this)">添加</a>
                            </div>
                            <div class="form-row form-group">
                                <input class="form-control col-md-10" name="unity_android_ios[]" placeholder="请填写SDK版本，示例 5.5.7" required>
                                <a class="delete-btn col-md-2" onclick="deleteRow(this)">删除</a>
                            </div>
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
        $(function () {
            $('#create-form').submit(function (event) {
                event.preventDefault();
                $.ajax({
                    type: 'post',
                    url: '/strategy-sdk-distribution',
                    data: $('#create-form').serializeArray(),
                }).done(function(data) {
                    if (data.code === 0) {
                        window.location = '/strategy-sdk-distribution';
                    } else {
                        $.alert({
                            title: '规则创建失败！',
                            content: data.msg,
                            buttons: {
                                ok: {
                                    text: '好的',
                                    btnClass: 'btn-warning'
                                }
                            }
                        });
                    }
                }).fail(function(response) {
                }).always(function() {
                    console.log("Create SDK Distribution rule form submitted");
                });
            });
        });

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