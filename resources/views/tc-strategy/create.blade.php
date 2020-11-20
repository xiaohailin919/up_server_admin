@extends('layouts.admin')

@section('content')
    <style>
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
        kbd {
            display: inline-block;
            padding: 3px 5px;
            font-size: 11px;
            line-height: 10px;
            color: #555;
            vertical-align: middle;
            background-color: #fcfcfc;
            border: 1px solid #ccc;
            border-bottom-color: #bbb;
            border-radius: 3px;
            -webkit-box-shadow: inset 0 -1px 0 #bbb;
            box-shadow: inset 0 -1px 0 #bbb;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('tc-strategy') }}">TC Rate Rules</a></li>
                        <li class="breadcrumb-item active">Add TC Rate Rules</li>
                    </ol>
                </div>
                <h4 class="page-title">Add TC Rate Rules</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form id="form" action="{{ \Illuminate\Support\Facades\URL::to('tc-strategy') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>规则类型</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input name="rule_type" type="radio" value="0" class="custom-control-input" @if('0' === old('rule_type') || empty(old('rule_type'))) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Platform</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input name="rule_type" type="radio" value="1" class="custom-control-input" @if('1' === old('rule_type')) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">App</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input name="rule_type" type="radio" value="2" class="custom-control-input" @if('2' === old('rule_type')) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Placement</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4" id="system-platform-group" @if('0' !== old('rule_type') && !empty(old('rule_type'))) hidden @endif>
                            <label>系统平台</label>
                            <div class="mt-3">
                                @php($systemPlatform = old('system_platform'))
                                @foreach($systemPlatformMap as $key => $val)
                                    <label class="custom-control custom-radio">
                                        @if((!empty($systemPlatform) && (int)$systemPlatform === $key) || (empty($systemPlatform) && $key === \App\Models\MySql\TcStrategy::PLATFORM_IOS))
                                            <input name="system_platform" type="radio" value="{{ $key }}" class="custom-control-input" checked>
                                        @else
                                            <input name="system_platform" type="radio" value="{{ $key }}" class="custom-control-input">
                                        @endif
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ $val }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-row" id="app-id-input-group" @if(old('rule_type') !== '1') hidden @endif>
                        <div class="form-group{{ $errors->has('app_id') ? ' has-error' : '' }} col-md-4">
                            <label for="app-id-input">App ID</label>
                            <label style="float: right;color: #9b9b9b" for="app-id-input">请通过换行输入多条数据</label>
{{--                            <input class="form-control" id="app-id-input" name="app_id" type="text" value="{{ old('app_id') }}" placeholder="Please input the App ID" @if(!empty(old('app_id'))) required="required" @endif>--}}
                            <textarea class="form-control" id="app-id-input" name="app_id" type="text" placeholder="Please input the App ID" @if(!empty(old('app_id'))) required="required" @endif>{{ old('app_id') }}</textarea>
                            @if ($errors->has('app_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('app_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row" id="placement-id-input-group" @if(old('rule_type') !== '2') hidden @endif>
                        <div class="form-group{{ $errors->has('placement_id') ? ' has-error' : '' }} col-md-4">
                            <label for="placement-id-input">Placement ID</label>
                            <label style="float: right;color: #9b9b9b" for="placement-id-input">请通过换行输入多条数据</label>
{{--                            <input class="form-control" id="placement-id-input" name="placement_id" type="text" value="{{ old('placement_id') }}" placeholder="Please input the Placement ID" @if(!empty(old('placement_id'))) required="required" @endif>--}}
                            <textarea class="form-control" id="placement-id-input" name="placement_id" type="text" placeholder="Please input the Placement ID" @if(!empty(old('placement_id'))) required="required" @endif>{{ old('placement_id') }}</textarea>
                        @if ($errors->has('placement_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('placement_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>广告平台</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input name="ad_platform" type="radio" value="0" class="custom-control-input" @if(old('ad_platform') === '0' || empty(old('ad_platform'))) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">所有</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input name="ad_platform" type="radio" value="1" class="custom-control-input" @if(old('ad_platform') === '1') checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">指定</span>
                                </label>
                            </div>
                            @if ($errors->has('network_firms'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('network_firms') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row" id="network-firms-input-group" @if(old('ad_platform') === '0' || empty(old('ad_platform'))) hidden @endif>
                        <div class="form-group col-md-4">
                            <label for="network-firms-select">指定广告平台</label>
                            <label style="color:#ff3111">（输入以匹配支持的广告平台，按 <kbd>↑</kbd> / <kbd>↓</kbd> 以选择）</label>
                            <select id="network-firms-select" name="network_firms[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="-Firms-">
                                @foreach ($firmIdNameMap as $key => $val)
                                    @php($networkFirms = old('network_firms', []))
                                    <option value="{{ $key }}" @if (in_array($key, $networkFirms, false)) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @foreach($typeMap as $key => $val)
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="type-{{ $key }}-input">{{ $val }}</label><br/>
                                <div class="input-group" style="width: 35%;display: inline-flex">
                                    <input id="type-{{ $key }}-input" name="type_{{ $key }}" type="number" value="{{ empty(old('type_' . $key)) ? 100 : old('type_' . $key) }}" max="100" min="0" class="form-control" required/>
                                    <div class="input-group-append">
                                        <span class="input-group-text small"><small>%</small></span>
                                    </div>
                                </div>
                                <small>(0 ~ 100%，默认 100%，0% = 关，100% = 开)</small>
                            </div>
                        </div>
                    @endforeach
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="status-select">状态</label>
                            <select id="status-select" name="status" class="form-control">
                                @php($status = old('status'))
                                @foreach($statusMap as $key => $val)
                                    @if((!empty($status) && (int)$status === $key) || (empty($status) && $key === \App\Models\MySql\TcStrategy::STATUS_ACTIVE))
                                        <option value="{{ $key }}" selected>{{ $val }}</option>
                                    @else
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    <p id="show"></p>
                </form>
            </div>
        </div>
    </div>
    <script>
        /* 规则类型切换 */
        $('input:radio[name="rule_type"]').click(function(){
            let val = $('input:radio[name="rule_type"]:checked').val();
            let appInputGroup = $('#app-id-input-group');
            let plmInputGroup = $('#placement-id-input-group');
            let platformGroup = $('#system-platform-group');
            let appInput = $('#app-id-input');
            let plmInput = $('#placement-id-input');
            switch (parseInt(val)) {
                case 0:
                    appInputGroup.attr('hidden', true);
                    plmInputGroup.attr('hidden', true);
                    platformGroup.attr('hidden', false);
                    appInput.attr('required', false);
                    plmInput.attr('required', false);
                    break;
                case 1:
                    appInputGroup.attr('hidden', false);
                    plmInputGroup.attr('hidden', true);
                    platformGroup.attr('hidden', true);
                    appInput.attr('required', true);
                    plmInput.attr('required', false);
                    break;
                case 2:
                    appInputGroup.attr('hidden', true);
                    plmInputGroup.attr('hidden', false);
                    platformGroup.attr('hidden', true);
                    appInput.attr('required', false);
                    plmInput.attr('required', true);
                    break;
            }
        });

        $('input:radio[name="ad_platform"]').click(function(){
            let val = parseInt($('input:radio[name="ad_platform"]:checked').val());
            if (val === 0) {
                $('#network-firms-input-group').attr('hidden', true);
                $('#network-firms-select').attr('required', false);
            } else {
                $('#network-firms-input-group').attr('hidden', false);
                $('#network-firms-select').attr('required', true);
            }
        });
    </script>
@endsection