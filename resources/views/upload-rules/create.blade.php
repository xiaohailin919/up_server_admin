@extends('layouts.admin')

@section('content')
    <style>
        .title-row {
            height: fit-content;
            width: 100%;
        }
        .title-row label {
            margin-bottom: 0;
        }
        .content-row {
            padding: 6px 0;
        }
        .content-row .col-md-1 {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .content-row .col-md-1 i {
            font-size: 1.5em;
            color: #f5a623;
        }
        .content-row .col-md-1 i:hover {
            color: #8b572a;
            cursor: pointer;
        }
    </style>
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
                            <a href="{{ \Illuminate\Support\Facades\URL::to('upload-rules') }}">Upload Rules</a>
                        </li>
                        <li class="breadcrumb-item active">Add Upload Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Upload Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form action="{{ \Illuminate\Support\Facades\URL::to('upload-rules') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>Rule Type</label>
                            <div>
                                @foreach($ruleTypeMap as $key => $val)
                                    <label class="custom-control custom-radio">
                                        <input class="custom-control-input" name="rule_type" type="radio" value="{{ $key }}" @if ($data['rule_type'] == $key) checked @endif >
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ $val }}</span>
                                    </label>
                                @endforeach
                                @if ($errors->has('app_uuid'))
                                    <span class="help-block text-danger">
                                    <strong>{{ $errors->first('app_uuid') }}</strong>
                                </span>
                                @endif
                                @if ($errors->has('publisher_group_ids'))
                                    <span class="help-block text-danger">
                                    <strong>{{ $errors->first('publisher_group_ids') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-row" data-rule-type="app">
                        <div class="form-group col-md-8">
                            <label>App ID</label>
                            <input type="text" class="form-control" name="app_uuid" value="" placeholder="请填写App Uuid">
                        </div>
                    </div>
                    <div class="form-row" data-rule-type="publisher_group">
                        <div class="form-group col-md-8">
                            <label>Publisher Group</label>
                            <select class="form-control select2 select2-multiple select2-hidden-accessible" name="publisher_group_ids[]" multiple="" data-placeholder="- Publisher Group -">
                                @foreach ($publisherGroupIdNameMap as $publisherGroupId => $publisherGroupName)
                                    <option value="{{ $publisherGroupId }}">{{ $publisherGroupName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="inputCacheTime">Tracking和埋点实时上报规则</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><small>等待上报的条数 >=</small></span>
                                </div>
                                <input type="number" name="tk_max_amount" min="1" id="tk_max_amount" value="8" class="form-control"/>
                                <div class="input-group-append">
                                    <span class="input-group-text small"><small>条</small></span>
                                </div>
                            </div>
                            <i>或者</i>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><small>每</small></span>
                                </div>
                                <input type="number" name="tk_interval" id="tk_internal" min="0" value="10" class="form-control"/>
                                <div class="input-group-append">
                                    <span class="input-group-text"><small>秒上报一次</small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="inputCacheTime">埋点批量上报规则</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><small>等待上报的条数 >=</small></span>
                                </div>
                                <input type="number" name="da_max_amount" id="da_max_amount" min="1" value="8" class="form-control"/>
                                <div class="input-group-append">
                                    <span class="input-group-text small"><small>条</small></span>
                                </div>
                            </div>
                            <i>或者</i>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><small>每</small></span>
                                </div>
                                <input type="number" name="da_interval" id="da_internal" min="0" value="1800" class="form-control"/>
                                <div class="input-group-append">
                                    <span class="input-group-text"><small>秒上报一次</small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="inputCacheTime">TC延迟上报规则</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><small>延迟</small></span>
                                </div>
                                <input type="number" name="upload_interval" id="upload_interval" min="0" value="{{ $data['upload_interval'] }}"
                                       class="form-control"/>
                                <div class="input-group-append">
                                    <span class="input-group-text small"><small>秒上报</small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>
                                定时器触发上报
                                <small style="color:#ff3111">（iOS SDK >= 5.5.0支持）</small>
                            </label>
                            <div>
                                @foreach($tkTimerSwitchMap as $status => $statusText)
                                    <label class="custom-control custom-radio">
                                        <input name="tk_timer_switch"
                                               type="radio"
                                               id="tk_timer_switch_{{$status}}"
                                               value="{{ $status }}"
                                               class="custom-control-input"
                                               @if ($data['tk_timer_switch'] == $status) checked @endif >
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ $statusText }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8" id="da_rt_keys">
                            <label for="network-firms-select">实时上报的埋点Key</label>
                            <button class="btn btn-sm btn-primary" style="float: right" type="button" onclick="onAddClick(this)">添加</button>
                            <div class="form-row title-row" hidden>
                                <div class="col-md-3">
                                    <label><strong>Key</strong></label>
                                </div>
                                <div class="col-md-9">
                                    <label><strong>生效广告类型</strong></label>
                                </div>
                            </div>
                            <div class="form-row content-row" hidden>
                                <div class="col-md-3">
                                    <input class="form-control" name="da_rt_keys[]" type="number" placeholder="Key">
                                </div>
                                <div class="col-md-8">
                                    <select name="da_rt_keys_ft[0][]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-- 广告类型 --">
                                        @foreach ($formatMap as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <i class="mdi mdi-close-circle" onclick="onDelClick(this)"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8" id="da_not_keys">
                            <label for="network-firms-select">不上报的埋点Key</label>
                            <button class="btn btn-sm btn-primary" style="float: right" type="button" onclick="onAddClick(this)">添加</button>
                            <div class="form-row title-row" hidden>
                                <div class="col-md-3">
                                    <label><strong>Key</strong></label>
                                </div>
                                <div class="col-md-6">
                                    <label><strong>生效广告类型</strong></label>
                                </div>
                                <div class="col-md-3">
                                    <label>
                                        <strong>服务端不下发概率</strong>
                                        <i class="dripicons-question" data-toggle="tooltip" title="分母取值范围是大于等于0的正整数。填写0时是100%下发，填写1时是100%不下发。"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="form-row content-row" hidden>
                                <div class="col-md-3">
                                    <input class="form-control" name="da_not_keys[]" type="number" placeholder="Key">
                                </div>
                                <div class="col-md-6">
                                    <select name="da_not_keys_ft[0][]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-- 广告类型 --">
                                        @foreach ($formatMap as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <small>1 / </small>
                                            </span>
                                        </div>
                                        <input class="form-control" name="da_not_keys_rates[]" type="number" placeholder="Rate">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <i class="mdi mdi-close-circle" onclick="onDelClick(this)"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8" id="tk_n_t">
                            <label for="network-firms-select">不上报的 Tracking Type</label>
                            <button class="btn btn-sm btn-primary" style="float: right" type="button" onclick="onAddClick(this)">添加</button>
                            <div class="form-row title-row" hidden>
                                <div class="col-md-3">
                                    <label><strong>Tracking Type</strong></label>
                                </div>
                                <div class="col-md-6">
                                    <label><strong>生效广告类型</strong></label>
                                </div>
                                <div class="col-md-3">
                                    <label>
                                        <strong>服务端不下发概率</strong>
                                        <i class="dripicons-question" data-toggle="tooltip" title="分母取值范围是大于等于0的正整数。填写0时是100%下发，填写1时是100%不下发。"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="form-row content-row" hidden>
                                <div class="col-md-3">
                                    <input class="form-control" name="tk_n_t[]" type="number" placeholder="Key">
                                </div>
                                <div class="col-md-6">
                                    <select name="tk_n_t_ft[0][]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-- 广告类型 --">
                                        @foreach ($formatMap as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <small>1 / </small>
                                            </span>
                                        </div>
                                        <input class="form-control" name="tk_not_keys_rates[]" type="number" placeholder="Rate">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <i class="mdi mdi-close-circle" onclick="onDelClick(this)"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>Tracking服务器地址</label>
                            <input type="text" class="form-control" name="tk_address" value="{{ $data['tk_address'] }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>埋点服务器地址</label>
                            <input type="text" class="form-control" name="da_address" value="{{ $data['da_address'] }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>TC服务器地址</label>
                            <input type="text" class="form-control" name="upload_address" value="{{ $data['upload_address'] }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>TCP/IP 上报 Tracking 和埋点服务器域名
                                <small style="color:#ff3111">（SDK >= 5.6.3 支持）</small>
                            </label>
                            <input type="text" class="form-control" name="tcp_domain" value="{{ $data['tcp_domain'] }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>TCP/IP 上报 Tracking 和埋点服务器端口
                                <small style="color:#ff3111">（SDK >= 5.6.3 支持）</small>
                            </label>
                            <input type="number" class="form-control" name="tcp_port" min="0" max="65535" value="{{ $data['tcp_port'] }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>Tracking 和埋点上报协议
                                <small style="color:#ff3111">（SDK >= 5.6.3 支持）</small>
                            </label>
                            <div>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="tcp_tk_da_type" type="radio" value="1" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">只上报一种协议</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="tcp_tk_da_type" type="radio" value="2">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">同时上报两种协议</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row" id="tcp-tk-da-type-2-hidden">
                        <div class="form-group col-md-3">
                            <label>TCP/IP 上报 Tracking 和埋点切量比例
                                <small style="color:#ff3111">（SDK >= 5.6.3 支持）</small>
                            </label>
                            <div class="input-group">
                                <input class="form-control" type="number" name="tcp_tk_da_rate" max="100" min="0" value="{{ $data['tcp_tk_da_rate'] }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><small>%</small></span>
                                </div>
                            </div>
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
                        <div class="form-group col-md-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const appRows = $('.form-row[data-rule-type="app"]');
        const publisherGroupRows = $('.form-row[data-rule-type="publisher_group"]');

        function onRuleTypeChange() {
            let val = $('input[name="rule_type"]:checked').val();
            $(appRows).attr('hidden', val == '{{ \App\Models\MySql\StrategyAppLogger::RULE_TYPE_PUBLISHER_GROUP }}');
            $(appRows).find('input').attr('required', val == '{{ \App\Models\MySql\StrategyAppLogger::RULE_TYPE_APP }}');
            {{--$(appRows).find('select').attr('required', val == '{{ \App\Models\MySql\StrategyAppLogger::RULE_TYPE_APP }}');--}}
            $(publisherGroupRows).attr('hidden', val == '{{ \App\Models\MySql\StrategyAppLogger::RULE_TYPE_APP }}');
            {{--$(publisherGroupRows).find('input').attr('required', val == '{{ \App\Models\MySql\StrategyAppLogger::RULE_TYPE_PUBLISHER_GROUP }}');--}}
            $(publisherGroupRows).find('.select2').attr('required', val == '{{ \App\Models\MySql\StrategyAppLogger::RULE_TYPE_PUBLISHER_GROUP }}');
        }

        function onTcpTkDaTypeChange() {
            let val = $('input[name="tcp_tk_da_type"]:checked').val();
            $('#tcp-tk-da-type-2-hidden').attr('hidden', val == '{{\App\Models\MySql\StrategyAppLogger::TCP_TK_DA_TYPE_BOTH}}');
        }
        $(function () {
            /* 页面加载时执行一次 */
            onRuleTypeChange();
            onTcpTkDaTypeChange();
            /* 设置监听器 */
            $('input[name="rule_type"]').change(function () {
                onRuleTypeChange();
            });
            $('input[name="tcp_tk_da_type"]').change(function () {
                onTcpTkDaTypeChange();
            })
        })
        function onAddClick(event) {
            let parent = event.parentNode;
            $('#' + parent.id + ' > .title-row').attr('hidden', false);
            let contentRows = $('#' + parent.id + ' > .content-row');
            if ($(contentRows[0]).attr('hidden') !== 'hidden') {
                let newChild = contentRows[0].cloneNode(true);
                /* 将 select 中已生成的元素删除，重新初始化多选 select */
                let spans = $(newChild).find('span.select2');
                $(spans).each(function (index, node) {
                    $(node).remove();
                });
                /* 为 select 重命名 name 属性 */
                let selects = $(newChild).find('select');
                let name = $(selects[0]).attr('name');
                // console.log('name: ' + name + ' ==> ' + name.replace(/\[\d+]/, '[' + index + ']'));
                $(selects[0]).attr('name', name.replace(/\[\d+]/, '[' + contentRows.length + ']'));
                $(selects[0]).val('');
                /* 去掉左右输入框的值 */
                $(newChild).find('input[type="number"]').val('');
                parent.appendChild(newChild);
                /* 激活 select2 并默认选中全部 */
                let select2 = $(selects[0]).select2();
                select2.val(['0', '1', '2', '3', '4']).trigger("change");
            } else {
                $(contentRows[0]).attr('hidden', false);
                $(contentRows[0]).find('input[type="number"]').attr('required', true);
                $(contentRows[0]).find('select').attr('required', true);
                let select2 = $(contentRows[0]).find('select').select2();
                select2.val(['0', '1', '2', '3', '4']).trigger("change");
            }
        }

        function onDelClick(event) {
            let parent = event.parentNode.parentNode;
            /* 前面是输入栏 或者 后面是输入栏，直接删掉本行，后面所有行必须重置 name */
            if ($(parent).prev().hasClass('content-row') || $(parent).next().length !== 0) {
                /* 获取待删除行的所有兄弟节点 */
                let nextAll = $(parent).siblings('.content-row');
                $(parent).remove();
                /* 遍历所有兄弟节点，为他们重新赋予 name 属性 */
                $(nextAll).each(function (index, node) {
                    if ($(node).hasClass('content-row')) {
                        let formatSelect = $(node).find('select');
                        for(let i = 0; i < formatSelect.length; i++) {
                            let name = $(formatSelect[i]).attr('name');
                            // console.log('name: ' + name + ' ==> ' + name.replace(/\[\d+]/, '[' + index + ']'));
                            $(formatSelect[i]).attr('name', name.replace(/\[\d+]/, '[' + index + ']'));
                        }
                    }
                });
                return;
            }
            /* 隐藏、取消必须、清空内容 */
            $(parent).attr('hidden', true);
            $(parent).find('input[type="number"]').attr('required', false);
            $(parent).find('input[type="number"]').val('');
            $(parent).find('select').attr('required', false);
            $(parent).prev().attr('hidden', true);
        }
    </script>
@endsection

@section('extra_js')
    @include('layouts.upload_extra_js')
@endsection
