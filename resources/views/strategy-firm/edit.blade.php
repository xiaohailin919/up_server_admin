@extends('layouts.admin')

@section('content')
    <style>
        h5 {
            font-weight: bold;
        }
        .mt-3 {
            margin: 0!important;
        }
        .help-block strong {
            color: red;
        }
        .select2-container .select2-selection--multiple .select2-selection__choice {
            color: #ffffff;
            text-decoration: none;
            outline: 0;
            background: #428bca;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: #428bca;
        }
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #aaa;
        }
        #delete-request-setting {
            font-size: 1.8em;
            float: right;
            color: #bf7b40
        }
        #delete-request-setting:hover {
            cursor: pointer;
            color: #d0021b;
        }
        .sdk-notice-inline {
            color: red;
        }
        .sdk-notice {
            color: red;
            line-height: 38px;
            text-align: right;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('strategy-firm') }}">广告平台策略设置</a></li>
                        <li class="breadcrumb-item active">修改广告平台策略</li>
                    </ol>
                </div>
                <h4 class="page-title">修改广告平台策略</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{--<form action="{{ \Illuminate\Support\Facades\URL::to('strategy-firm') }}" method="post">--}}
                <form onsubmit="return submitForm()">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <h5 class="form-group col-md-2">规则类型</h5>
                    </div>
                    <div class="form-row">
                        <label class="form-group col-md-1">规则类型</label>
                        <div class="form-group col-md-5">
                            <div class="mt-3">
                                <label class="custom-control custom-radio col-md-3">
                                    <input class="custom-control-input" type="radio" disabled @if($data['placement_id'] === 0) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Platform</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" disabled @if($data['placement_id'] !== 0) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Placement</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- 系统平台 --}}
                    <div class="form-row">
                        <label class="form-group col-md-1">系统平台</label>
                        <div class="form-group col-md-5">
                            <div class="mt-3 row">
                                <label class="custom-control custom-checkbox col-md-3">
                                    <input class="custom-control-input" type="checkbox" disabled @if($data['platform'] === 1) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Android</span>
                                </label>
                                <label class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" disabled @if($data['platform'] === 2) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">IOS</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Placement ID --}}
                    <div class="form-row" @if($data['placement_id'] === 0) hidden @endif>
                        <label class="form-group col-md-1">Placement ID</label>
                        <div class="form-group col-md-5">
                            <input class="form-control" value="{{ $data['placement_id'] }}" type="text" disabled>
                        </div>
                    </div>

                    {{-- 广告类型 --}}
                    <div class="form-row">
                        <label class="form-group col-md-1">广告类型</label>
                        <div class="form-group col-md-5">
                            <input class="form-control" value="{{ $formatMap[$data['format']] }}" type="text" disabled>
                        </div>
                    </div>

                    {{-- 广告平台 --}}
                    <div class="form-row">
                        <label class="form-group col-md-1">广告平台</label>
                        <div class="form-group col-md-5">
                            <input class="form-control" value="{{ $networkFirmMap[$data['nw_firm_id']] }}" type="text" disabled>
                        </div>
                    </div>

                    {{--                    <div class="form-row">--}}
                    {{--                        <h5 class="form-group col-md-2">缓存设置</h5>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="form-row">--}}
                    {{--                        <label class="form-group col-md-1">Network 缓存时间<i class="mdi mdi-help-circle"></i></label>--}}
                    {{--                        <div class="form-group col-md-3">--}}
                    {{--                            <div class="input-group">--}}
                    {{--                                <input class="form-control" type="number" name="">--}}
                    {{--                                <div class="input-group-append">--}}
                    {{--                                    <span class="input-group-text small">秒</span>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="form-row">--}}
                    {{--                        <label class="form-group col-md-1">Network 广告素材超时时间(原 Network 超时时间)</label>--}}
                    {{--                        <div class="form-group col-md-3">--}}
                    {{--                            <div class="input-group">--}}
                    {{--                                <input class="form-control" type="number" name="">--}}
                    {{--                                <div class="input-group-append">--}}
                    {{--                                    <span class="input-group-text small">秒</span>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="form-row">--}}
                    {{--                        <label class="form-group col-md-1">Network 广告数据超时时间</label>--}}
                    {{--                        <div class="form-group col-md-3">--}}
                    {{--                            <div class="input-group">--}}
                    {{--                                <input class="form-control" type="number" name="">--}}
                    {{--                                <div class="input-group-append">--}}
                    {{--                                    <span class="input-group-text small">秒</span>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                        <span class="col-md-2 sdk-notice">(SDK V5.1.0 及以上支持)</span>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="form-row">--}}
                    {{--                        <label class="form-group col-md-1">Ad Source 维度 Up_status 有效期</label>--}}
                    {{--                        <div class="form-group col-md-3">--}}
                    {{--                            <div class="input-group">--}}
                    {{--                                <input class="form-control" type="number" name="">--}}
                    {{--                                <div class="input-group-append">--}}
                    {{--                                    <span class="input-group-text small">秒</span>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                        <span class="col-md-2 sdk-notice">(SDK V5.1.0 及以上支持)</span>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="form-row">--}}
                    {{--                        <label class="form-group col-md-1">Network 下的 Offer 请求条数</label>--}}
                    {{--                        <div class="form-group col-md-3">--}}
                    {{--                            <input class="form-control" type="number" name="">--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                    <br/>--}}
                    {{--                    <div class="form-row">--}}
                    {{--                        <h5 class="form-group col-md-2">Header Biding 设置</h5>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="form-row">--}}
                    {{--                        <label class="form-group col-md-1">Header Bidding 超时时间</label>--}}
                    {{--                        <div class="form-group col-md-3">--}}
                    {{--                            <div class="input-group">--}}
                    {{--                                <input class="form-control" type="number" name="">--}}
                    {{--                                <div class="input-group-append">--}}
                    {{--                                    <span class="input-group-text small">毫秒</span>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="form-row">--}}
                    {{--                        <label class="form-group col-md-1">Bid Token缓存有效期</label>--}}
                    {{--                        <div class="form-group col-md-3">--}}
                    {{--                            <div class="input-group">--}}
                    {{--                                <input class="form-control" type="number" name="">--}}
                    {{--                                <div class="input-group-append">--}}
                    {{--                                    <span class="input-group-text small">秒</span>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                        <span class="col-md-2 sdk-notice">(SDK V5.1.1 及以上支持)</span>--}}
                    {{--                    </div>--}}
                    {{--                    <br/>--}}

                    <div class="form-row">
                        <h5 class="form-group col-md-2">广告源请求设置</h5>
                    </div>
                    <div class="form-row" id="request-setting">
                        <label class="form-group col-md-5" style="line-height: 32px;vertical-align: middle">
                            广告源在上次无填充时，本次指定时间内不发起 Request
                            <span class="sdk-notice-inline">(注：SDK V5.5.5 及以上支持)</span>
                        </label>
                        <label class="form-group col-md-1">
                            <button class="btn btn-sm btn-info" type="button" style="float: right" onclick="addRequestSetting()">添加</button>
                        </label>
                    </div>
                    @foreach($data['unit_fill_rate_range'] as $record)
                        <div class="form-row request-setting-row">
                            <label class="form-group col-md-1"></label>
                            <div class="form-group col-md-9 form-row">
                                <div class="input-group col-md-6">
                                    <input class="form-control" id="fill_rate_min" type="number" step="0.01" name="fill_rate_min[]" min="0" max="100" value="{{ $record[0] }}" required/>
                                    <span class="input-group-addon small"><small>%&emsp;≤ 填充率 ≤</small></span>
                                    <input class="form-control" id="fill_rate_max" type="number" step="0.01" name="fill_rate_max[]" min="0" max="100" value="{{ $record[1] }}" required/>
                                    <div class="input-group-append">
                                        <span class="input-group-text small"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="input-group col-md-5">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text small"><small>间隔</small></span>
                                    </div>
                                    <input class="form-control" type="number" name="request_interval[]" min="0" value="{{ $record[2] }}" required/>
                                    <div class="input-group-append">
                                        <span class="input-group-text small"><small>秒，不发请求</small></span>
                                    </div>
                                </div>
                                <label class="col-md-1" style="margin: 0;">
                                    <i class="mdi mdi-close-circle" id="delete-request-setting" onclick="deleteRequestSetting(this)"></i>
                                </label>
                            </div>
                        </div>
                    @endforeach
                    {{-- 广告源请求设置记录 --}}
                    <br/>

                    <div class="form-row">
                        <label class="form-group col-md-1">状态</label>
                        <div class="form-group col-md-5">
                            <select class="form-control" name="status">
                                <option value="3" @if($data['status'] === 3) selected @endif>启用</option>
                                <option value="1" @if($data['status'] === 1) selected @endif>关闭</option>
                            </select>
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
        /* 增加广告源请求记录 */
        function addRequestSetting() {
            let rows = $('.request-setting-row');
            let row = rows[0].cloneNode(true);
            let minInput = $(row).find('#fill_rate_min')[0];
            let maxInput = $(row).find('#fill_rate_max')[0];
            let rateInput = $(row).find('[name="request_interval[]"]')[0];
            $(minInput).val(0);
            $(maxInput).val(100);
            $(rateInput).val(60);
            $(rows[rows.length - 1]).after(row);
        }

        /* 删除广告源请求记录 */
        function deleteRequestSetting(event) {
            let rows = $('.request-setting-row');
            if (rows.length !== 1) {
                let parent = event.parentNode.parentNode.parentNode;
                $(parent).remove();
            }
        }

        /**
         * 上传表单
         */
        function submitForm() {
            let isCheck = true;
            /* 检测 request setting 区间是否正确设置 */

            /* 检测 request setting 区间是否正确设置 */
            let records = [];
            $('.request-setting-row').each(function (index, node) {
                let thisMinInput = $(node).find('#fill_rate_min')[0];
                let thisMaxInput = $(node).find('#fill_rate_max')[0];
                let thisMin = parseFloat($(thisMinInput).val());
                let thisMax = parseFloat($(thisMaxInput).val());
                records.push([thisMin, thisMax]);
            });
            for (let i = 0; i < records.length; i++) {
                if (records[i][0] > records[i][1]) {
                    isCheck = submitErrorAlert('单条填充率设置，左边值必须小于等于右边值！<br/><span>左：' + records[i][0] + '，右：' + records[i][1] + '</span>');
                    return false;
                }
            }
            if (records.length > 1) {
                for (let i = 0; i < records.length; i++) {
                    for (let j = 0; j < records.length; j++) {
                        if (j === i) {
                            continue;
                        }
                        if (!(records[i][1] < records[j][0] || records[i][0] > records[j][1])) {
                            isCheck = submitErrorAlert('填充率区间重叠！<br/><span>[' + records[i] + "] : [" + records[j] + ']</span>');
                            return false;
                        }
                    }
                }
            }
            if (isCheck) {
                $.ajax({
                    url: '/strategy-firm/{{ $data['id'] }}',
                    type: 'PUT',
                    data: $('form').serializeArray(),
                    success: function (response) {
                        if (response.result === 'error') {
                            createErrorAlert(response.message);
                        } else {
                            location.href = location.href.replace(/\/[0-9]*\/edit/, '');
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
            return false;
        }

        /* 表单提交失败弹窗 */
        function submitErrorAlert(msg) {
            $.alert({
                title: '无法提交表单！',
                content: msg,
                buttons: {
                    ok: {
                        text: '好的',
                        btnClass: 'btn-warning'
                    }
                }
            });
            return false;
        }
        /* 后端处理失败弹窗 */
        function createErrorAlert(error) {
            $.alert({
                title: '无法创建记录！',
                content: error,
                buttons: {
                    ok: {
                        text: '好的',
                        btnClass: 'btn-warning'
                    }
                }
            });
            return false;
        }
    </script>
@endsection