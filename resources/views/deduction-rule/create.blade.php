@extends('layouts.admin')

@section('content')
    <style>
        /* google、safari */
        #publisher_id::-webkit-outer-spin-button,
        #publisher_id::-webkit-inner-spin-button{
            -webkit-appearance: none !important;
            margin: 0;
        }
        /* 火狐 */
        #publisher_id[type="number"]{
            -moz-appearance: textfield;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage
                            @if($type == \App\Models\MySql\DeductionRule::TYPE_IMPRESSION)
                                Impression
                            @else
                                Fill Rate
                            @endif
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">Manage
                    @if($type == \App\Models\MySql\DeductionRule::TYPE_IMPRESSION)
                        Impression
                    @else
                        Fill Rate
                    @endif
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                @if($type == \App\Models\MySql\DeductionRule::TYPE_IMPRESSION)
                    {{ Form::model($data, array('route' => array('manage-impression.store'), 'method' => 'POST')) }}
                @else
                    {{ Form::model($data, array('route' => array('manage-fill-rate.store'), 'method' => 'POST')) }}
                @endif
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="dimension">Dimension</label>
                        <select name="dimension" id="dimension" class="form-control">
                            @foreach ($dimensionMap as $key => $val)
                                <option value="{{ $key }}" @if($key == $dimension)selected="selected"@endif>{{ $val }}</option>
                            @endforeach
                        </select>
                        @if($error != '')
                            <span class="error" style="color:#f6a828">
                                <strong id="submit_error">{{ $error }}</strong><br/>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-row" id="publisher" @if($dimension != \App\Models\MySql\DeductionRule::DIMENSION_PUBLISHER) style="display: none" @else style="display: block" @endif>
                    <div class="form-group col-md-3">
                        <label for="publisher_id">Publisher ID</label>
                        <input name="publisher_id" id="publisher_id" placeholder="Please input the publisher id" type="number" value="{{ $data['publisher_id'] }}" oninput="onPublisherInput()" onblur="publisherCheck()" class="form-control" @if($dimension != \App\Models\MySql\DeductionRule::DIMENSION_PUBLISHER) readonly @endif/>
                        <span id="publisher_error" style="display: none">Publisher id required</span>
                        <span class="error" style="color:#f6a828">
                            <strong id="publisher_not_found" style="display: none">Publisher id not found</strong><br/>
                        </span>
                    </div>
                </div>
                <div class="form-row" id="app" @if($dimension != \App\Models\MySql\DeductionRule::DIMENSION_APP) style="display: none" @else style="display: block" @endif>
                    <div class="form-group col-md-3">
                        <label for="app_id">App ID</label>
                        <input name="app_id" id="app_id" placeholder="Please input the app id" type="text" value="{{ $data['app_id'] }}" oninput="onAppInput()" onblur="appCheck()" class="form-control" @if($dimension == \App\Models\MySql\DeductionRule::DIMENSION_PLACEMENT) readonly @endif/>
                        <span id="app_error" style="display: none">App id required</span>
                        <span class="error" style="color:#f6a828">
                            <strong id="app_not_found" style="display: none">App id not found</strong><br/>
                        </span>
                    </div>
                </div>
                <div class="form-row" id="placement" @if($dimension != \App\Models\MySql\DeductionRule::DIMENSION_PLACEMENT) style="display: none" @else style="display: block" @endif>
                    <div class="form-group col-md-3">
                        <label for="placement_id">Placement ID</label>
                        <input name="placement_id" id="placement_id" placeholder="Please input the placement id" type="text" value="{{ $data['placement_id'] }}" oninput="onPlacementInput()" onblur="placementCheck()" class="form-control"/>
                        <span id="placement_error" style="display: none">Placement id required</span>
                        <span class="error" style="color:#f6a828">
                            <strong id="placement_not_found" style="display: none">Placement id not found</strong><br/>
                        </span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="rate">
                            Rate
                            @if($type == \App\Models\MySql\DeductionRule::TYPE_FILLED_RATE)
                                <small style="color:#ff3111">（实际填充数 = 原始填充数 x 补扣系数）</small>
                            @else
                                <small style="color:#ff3111">（实际展示 = 原始展示 x 补扣系数）</small>
                            @endif
                        </label>
                        <div class="input-group">
                            <input name="rate" id="rate" placeholder="请输入大于零的整数" type="number" min=0 value={{ $data['discount'] }} oninput="rateCheck()" class="form-control"/>
                            <div class="input-group-append">
                                <span id="rate_conversion" class="input-group-text small" style="width: 64px"><small>{{ $data['discount'] / 100 }}</small></span>
                            </div>
                        </div>
                        <span id="rate_error" style="display: none">Please input integer greater than 0</span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach ($statusMap as $key => $val)
                                @if($key != \App\Models\MySql\DeductionRule::STATUS_TO_CHECK)
                                    <option value="{{ $key }}" @if ($key == $data['status']) selected="selected" @endif>{{ $val }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <button id="submit" type="button" class="btn">
                        Submit
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script>
        $('#dimension').change(function() {
            let dimension    = $('#dimension').val();
            let publisherDiv = $('#publisher');
            let appDiv       = $('#app');
            let placementDiv = $('#placement');
            let publisherIpt = $('#publisher_id');
            let appIpt       = $('#app_id');
            let placementIpt = $('#placement_id');
            switch (dimension) {
                case '{{ \App\Models\MySql\DeductionRule::DIMENSION_PUBLISHER }}':
                    publisherDiv.attr('style', 'display:block;');
                    appDiv      .attr('style', 'display:none;');
                    placementDiv.attr('style', 'display:none;');
                    publisherIpt.attr('readonly', false);
                    publisherIpt.attr('placeholder', 'Please input the publisher id');
                    break;
                case '{{ \App\Models\MySql\DeductionRule::DIMENSION_APP }}':
                    publisherDiv.attr('style', 'display:none;');
                    appDiv      .attr('style', 'display:block;');
                    placementDiv.attr('style', 'display:none;');
                    publisherIpt.attr('readonly', true);
                    appIpt      .attr('readonly', false);
                    publisherIpt.attr('placeholder', 'Please input the app id');
                    appIpt      .attr('placeholder', 'Please input the app id');
                    break;
                default:
                    publisherDiv.attr('style', 'display:none;');
                    appDiv      .attr('style', 'display:none;');
                    placementDiv.attr('style', 'display:block;');
                    publisherIpt.attr('readonly', true);
                    appIpt      .attr('readonly', true);
                    publisherIpt.attr('placeholder', 'Please input the placement id');
                    appIpt      .attr('placeholder', 'Please input the placement id');
            }
            publisherIpt.val ('');
            appIpt      .val ('');
            placementIpt.val ('');
            $('#submit_error')       .attr('style', 'display:none;');
            $('#publisher_error')    .attr('style', 'display:none;');
            $('#app_error')          .attr('style', 'display:none;');
            $('#placement_error')    .attr('style', 'display:none;');
            $('#publisher_not_found').attr('style', 'display:none;');
            $('#app_not_found')      .attr('style', 'display:none;');
            $('#placement_not_found').attr('style', 'display:none;');
            submitDisable();
        });

        function onPublisherInput() {
            $('#publisher_not_found').attr('style', 'display:none');
            $('#submit_error')       .attr('style', 'display:none;');
        }

        function onAppInput() {
            $('#app_not_found').attr('style', 'display:none');
            $('#submit_error') .attr('style', 'display:none;');
            $('#publisher_id') .val('');
        }

        function onPlacementInput() {
            $('#placement_not_found').attr('style', 'display:none');
            $('#submit_error')       .attr('style', 'display:none;');
            $('#publisher_id')       .val('');
            $('#app_id')             .val('');
        }

        function publisherCheck() {
            let publisherInput = $('#publisher_id');
            if (publisherInput.val() === '' && !publisherInput.attr('readonly')) {
                $('#publisher_error').attr('style', 'color: #ff3111;display:inline-block;padding: 4px');
                submitDisable();
            } else {
                $('#publisher_error').attr('style', 'display:none');
                queryData();
            }
        }

        function appCheck() {
            let appInput = $('#app_id');
            if (appInput.val() === '' && !appInput.attr('readonly')) {
                $('#app_error').attr('style', 'color: #ff3111;display:inline-block;padding: 4px');
                submitDisable();
            } else {
                $('#app_error').attr('style', 'display:none');
                queryData();
            }
        }

        function placementCheck() {
            if ($('#placement_id').val() === '') {
                $('#placement_error').attr('style', 'color: #ff3111;display:inline-block;padding: 4px');
                submitDisable();
            } else {
                $('#placement_error').attr('style', 'display:none');
                queryData();
            }
        }

        function rateCheck () {
            let rate = $('#rate').val();
            if (rate.length > 5) {
                rate = rate.slice(0,5)
            }
            if (rate < 0) {
                $('#rate_error').attr('style', 'color: #ff3111;display:inline-block;padding: 4px');
            } else {
                $('#rate_error').attr('style', 'display:none');
            }
            $('#rate_conversion').html('<small>' + rate / 100 + '</small>')
        }

        /**
         * 根据 app_id 或 placement_id 请求低一级维度的数据
         */
        function queryData() {
            let publisherInput = $('#publisher_id');
            let appInput       = $('#app_id');
            let placementInput = $('#placement_id');
            let publisherId    = publisherInput.val();
            let appId          = appInput.val();
            let placementId    = placementInput.val();
            if (publisherId === '' && appId === '' && placementId === '') {
                return;
            }
            /* 后台如果检测 publisherId 有数据，则默认验证 publisher 维度 */
            if (placementId !== '' || appId !== '') {
                publisherId = '';
            }
            $.post(
                "/deduction-rule/query-id",
                {
                    'publisher_id' : publisherId,
                    'app_id'       : appId,
                    'placement_id' : placementId,
                },
                function(data){
                    /* 查到了 */
                    console.log(data);
                    if (data.publisher_id) {
                        publisherInput.val(data.publisher_id);
                        appInput.val(data.app_id);
                        publisherInput.attr({"value": data.publisher_id});
                        appInput.attr({"value": data.app_id});
                        submitEnable();
                        return;
                    }
                    /* 查不到，提交按钮封禁 */
                    submitDisable();
                    /* placement id 查不到 */
                    if ($('#placement').css('display') === 'block') {
                        $('#placement_not_found').attr('style', 'display:inline-block');
                        return;
                    }
                    /* app id 查不到 */
                    if ($('#app').css('display') === 'block') {
                        $('#app_not_found').attr('style', 'display:inline-block');
                        return;
                    }
                    /* publisher id 查不到 */
                    console.log('publisher id 查不到');
                    $('#publisher_not_found').attr('style', 'display:inline-block');
                },
                "json"
            );
        }

        function submitDisable() {
            let submit = $('#submit');
            submit.attr('type', 'button');
            submit.removeClass('btn-primary');
        }

        function submitEnable() {
            let submit = $('#submit');
            submit.attr('type', 'submit');
            submit.addClass('btn-primary');
        }
    </script>
@endsection
