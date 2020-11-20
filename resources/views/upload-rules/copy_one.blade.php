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
                        <li class="breadcrumb-item active">Copy Upload Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">Copy Upload Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('upload-rules.store'), 'method' => 'POST')) }}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>App ID</label>
                        <input type="text" class="form-control" name="app_uuid" value="" placeholder="请填写App Uuid" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">Tracking和埋点实时上报规则</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><small>等待上报的条数 >=</small></span>
                            </div>
                            <input type="number" name="tk_max_amount" min="1" id="tk_max_amount" value="{{ $data['tk_max_amount'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>条</small></span>
                            </div>
                        </div>
                        <i>或者</i>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><small>每</small></span>
                            </div>
                            <input type="number" name="tk_interval" id="tk_internal" min="0" value="{{ $data['tk_interval'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text"><small>秒上报一次</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">埋点批量上报规则</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><small>等待上报的条数 >=</small></span>
                            </div>
                            <input type="number" name="da_max_amount" id="da_max_amount" min="1" value="{{ $data['da_max_amount'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>条</small></span>
                            </div>
                        </div>
                        <i>或者</i>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><small>每</small></span>
                            </div>
                            <input type="number" name="da_interval" id="da_internal" min="0" value="{{ $data['da_interval'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text"><small>秒上报一次</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">TC延迟上报规则</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><small>延迟</small></span>
                            </div>
                            <input type="number" name="upload_interval" id="upload_interval" min="0" value="{{ $data['upload_interval'] }}" class="form-control"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>秒上报</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
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
                    <div class="form-group col-md-6" id="da_rt_keys">
                        <label for="network-firms-select">实时上报的埋点Key</label>
                        <button class="btn btn-sm btn-primary" style="float: right" type="button" onclick="onAddClick(this)">添加</button>
                        <div class="form-row title-row" @if(count($data['da_rt_keys_ft']) === 0)hidden @endif>
                            <div class="col-md-3">
                                <label><strong>Key</strong></label>
                            </div>
                            <div class="col-md-9">
                                <label><strong>生效广告类型</strong></label>
                            </div>
                        </div>
                        @if (count($data['da_rt_keys_ft']) === 0)
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
                        @else
                            @php($index = 0)
                            @foreach($data['da_rt_keys_ft'] as $daRtKeys => $selectedFormats)
                                <div class="form-row content-row">
                                    <div class="col-md-3">
                                        <input class="form-control" name="da_rt_keys[]" value="{{ $daRtKeys }}" type="number" placeholder="Key" required>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="da_rt_keys_ft[{{ $index }}][]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-- 广告类型 --" required>
                                            @foreach ($formatMap as $key => $val)
                                                <option value="{{ $key }}" @if(in_array($key, $selectedFormats, false)) selected @endif>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <i class="mdi mdi-close-circle" onclick="onDelClick(this)"></i>
                                    </div>
                                </div>
                                @php($index++)
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6" id="da_not_keys">
                        <label for="network-firms-select">不上报的埋点Key</label>
                        <button class="btn btn-sm btn-primary" style="float: right" type="button" onclick="onAddClick(this)">添加</button>
                        <div class="form-row title-row" @if(count($data['da_not_keys_ft']) === 0)hidden @endif>
                            <div class="col-md-3">
                                <label><strong>Key</strong></label>
                            </div>
                            <div class="col-md-9">
                                <label><strong>生效广告类型</strong></label>
                            </div>
                        </div>
                        @if (count($data['da_not_keys_ft']) === 0)
                            <div class="form-row content-row" hidden>
                                <div class="col-md-3">
                                    <input class="form-control" name="da_not_keys[]" type="number" placeholder="Key">
                                </div>
                                <div class="col-md-8">
                                    <select name="da_not_keys_ft[0][]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-- 广告类型 --">
                                        @foreach ($formatMap as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <i class="mdi mdi-close-circle" onclick="onDelClick(this)"></i>
                                </div>
                            </div>
                        @else
                            @php($index = 0)
                            @foreach($data['da_not_keys_ft'] as $daNotKeys => $selectedFormats)
                                <div class="form-row content-row">
                                    <div class="col-md-3">
                                        <input class="form-control" name="da_not_keys[]" value="{{ $daNotKeys }}" type="number" placeholder="Key" required>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="da_not_keys_ft[{{ $index }}][]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-- 广告类型 --" required>
                                            @foreach ($formatMap as $key => $val)
                                                <option value="{{ $key }}" @if(in_array($key, $selectedFormats, false)) selected @endif>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <i class="mdi mdi-close-circle" onclick="onDelClick(this)"></i>
                                    </div>
                                </div>
                                @php($index++)
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6" id="tk_n_t">
                        <label for="network-firms-select">不上报的 Tracking Type</label>
                        <button class="btn btn-sm btn-primary" style="float: right" type="button" onclick="onAddClick(this)">添加</button>
                        <div class="form-row title-row" @if(count($data['tk_no_t_ft']) === 0)hidden @endif>
                            <div class="col-md-3">
                                <label><strong>Tracking Type</strong></label>
                            </div>
                            <div class="col-md-9">
                                <label><strong>生效广告类型</strong></label>
                            </div>
                        </div>
                        @if (count($data['tk_no_t_ft']) === 0)
                            <div class="form-row content-row" hidden>
                                <div class="col-md-3">
                                    <input class="form-control" name="tk_n_t[]" type="number" placeholder="Key">
                                </div>
                                <div class="col-md-8">
                                    <select name="tk_n_t_ft[0][]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-- 广告类型 --">
                                        @foreach ($formatMap as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <i class="mdi mdi-close-circle" onclick="onDelClick(this)"></i>
                                </div>
                            </div>
                        @else
                            @php($index = 0)
                            @foreach($data['tk_no_t_ft'] as $tkNoType => $selectedFormats)
                                <div class="form-row content-row">
                                    <div class="col-md-3">
                                        <input class="form-control" name="tk_n_t[]" value="{{ $tkNoType }}" type="number" placeholder="Key" required>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="tk_n_t_ft[{{ $index }}][]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-- 广告类型 --" required>
                                            @foreach ($formatMap as $key => $val)
                                                <option value="{{ $key }}" @if(in_array($key, $selectedFormats, false)) selected @endif>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <i class="mdi mdi-close-circle" onclick="onDelClick(this)"></i>
                                    </div>
                                </div>
                                @php($index++)
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Tracking服务器地址</label>
                        <input type="text" placeholder="多个key换行填写" class="form-control" name="tk_address" value="{{ $data['tk_address'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>埋点服务器地址</label>
                        <input type="text" class="form-control" name="da_address" value="{{ $data['da_address'] }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>TC服务器地址</label>
                        <input type="text" class="form-control" name="upload_address" value="{{ $data['upload_address'] }}">
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
    <script>
        function onAddClick(event) {
            let parent = event.parentNode;
            $('#' + parent.id + ' > .title-row').attr('hidden', false);
            let contentRows = $('#' + parent.id + ' > .content-row');
            if ($(contentRows[0]).attr('hidden') !== 'hidden') {
                let newChild = contentRows[0].cloneNode(true);
                /* 将 select 中已生成的元素删除，重新初始化多选 select */
                let spans = $(newChild).find('span');
                $(spans).each(function (index, node) {
                    $(node).remove();
                });
                /* 为 select 重命名 name 属性 */
                let selects = $(newChild).find('select');
                let name = $(selects[0]).attr('name');
                // console.log('name: ' + name + ' ==> ' + name.replace(/\[\d+]/, '[' + index + ']'));
                $(selects[0]).attr('name', name.replace(/\[\d+]/, '[' + contentRows.length + ']'));
                $(selects[0]).val('');
                /* 去掉左边输入框的值 */
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
