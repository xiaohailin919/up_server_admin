@extends('layouts.admin')

@section('content')
    <link href="{{ asset('css/jquery-clockpicker.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery-clockpicker.min.js') }}"></script>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('/network-crawl') }}">API 数据拉取配置</a></li>
                        <li class="breadcrumb-item active">添加 API 数据拉取配置</li>
                    </ol>
                </div>
                <h4 class="page-title">添加 API 数据拉取配置</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form id="form" onsubmit="return submitForm()">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <label class="col-md-1">平台类型</label>
                        <div class="form-group">
                            <select class="form-control" name="network_firm_type">
                                @foreach($nwFirmTypeMap as $key => $val)
                                    <option value="{{ $key }}" @if($key === \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MONETIZATION) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">时间维度</label>
                        <div class="form-group">
                            <select class="form-control" name="type">
                                <option value="1">天维度</option>
                                <option value="2">小时维度</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">拉取时间</label>
                        <div class="form-group">
                            <div class="input-group clockpicker" data-placement="right" data-align="top" data-autoclose="true">
                                <input class="form-control" name="schedule_time" type="text" value="00:00">
                                <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">拉取范围</label>
                        <div class="form-group">
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="pull_type" type="radio" value="1" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">昨天</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="pull_type" type="radio" value="2">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">前天</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="col-md-1">广告平台</label>
                        <div class="form-group col-md-5">
                            <small>
                                <a id="select-all" href="javascript:">全选</a> / <a id="select-inverse" href="javascript:">反选</a><br/>
                            </small>
                            <div class="mt-3">
                                <div class="multi-checkbox day-nw-firm">
                                    @foreach($dayNwFirmMap as $key => $val)
                                        <label class="custom-control custom-checkbox col-md-3">
                                            <input class="custom-control-input" name="nw_firm_id[]" value="{{ $key }}" type="checkbox">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ $val }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="multi-checkbox hour-nw-firm" hidden="hidden">
                                    @foreach($hourNwFirmMap as $key => $val)
                                        <label class="custom-control custom-checkbox col-md-3">
                                            <input class="custom-control-input" name="nw_firm_id[]" value="{{ $key }}" type="checkbox">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ $val }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="multi-checkbox day-media-buy-nw-firm" hidden="hidden">
                                    @foreach($dayMediaBuyNwFirmMap as $key => $val)
                                        <label class="custom-control custom-checkbox col-md-3">
                                            <input class="custom-control-input" name="nw_firm_id[]" value="{{ $key }}" type="checkbox">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ $val }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="multi-checkbox hour-media-buy-nw-firm" hidden="hidden">
                                    @foreach($hourMediaBuyNwFirmMap as $key => $val)
                                        <label class="custom-control custom-checkbox col-md-3">
                                            <input class="custom-control-input" name="nw_firm_id[]" value="{{ $key }}" type="checkbox">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ $val }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
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
        $(function () {
            /* 切换平台类型 */
            $('select[name="network_firm_type"]').change(function () {
                $('.multi-checkbox input:checkbox').each(function () {
                    $(this).prop('checked', false);
                });
                let networkFirmType = $(this).val();
                let type = $('select[name="type"]').val();
                if (networkFirmType == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY }}') {
                    $('.hour-nw-firm').prop('hidden', 'hidden');
                    $('.day-nw-firm').prop('hidden', 'hidden');
                    $('.hour-media-buy-nw-firm').prop('hidden', type == '{{ \App\Models\MySql\NetworkCrawl::TYPE_DAY }}');
                    $('.day-media-buy-nw-firm').prop('hidden', type == '{{ \App\Models\MySql\NetworkCrawl::TYPE_HOUR }}');
                } else {
                    $('.day-media-buy-nw-firm').prop('hidden', 'hidden');
                    $('.hour-media-buy-nw-firm').prop('hidden', 'hidden');
                    $('.hour-nw-firm').prop('hidden', type == '{{ \App\Models\MySql\NetworkCrawl::TYPE_DAY }}');
                    $('.day-nw-firm').prop('hidden', type == '{{ \App\Models\MySql\NetworkCrawl::TYPE_HOUR }}');
                }
            });
            /* 切换时间维度 */
            $('select[name="type"]').change(function () {
                $('.multi-checkbox input:checkbox').each(function () {
                    $(this).prop('checked', false);
                });
                let networkFirmType = $('select[name="network_firm_type"]').val();
                if (networkFirmType == '{{ \App\Models\MySql\NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY }}') {
                    $('.hour-nw-firm').prop('hidden', 'hidden');
                    $('.day-nw-firm').prop('hidden', 'hidden');
                    $('.hour-media-buy-nw-firm').prop('hidden', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::TYPE_DAY }}');
                    $('.day-media-buy-nw-firm').prop('hidden', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::TYPE_HOUR }}');
                } else {
                    $('.hour-media-buy-nw-firm').prop('hidden', 'hidden');
                    $('.day-media-buy-nw-firm').prop('hidden', 'hidden');
                    $('.hour-nw-firm').prop('hidden', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::TYPE_DAY }}');
                    $('.day-nw-firm').prop('hidden', $(this).val() == '{{ \App\Models\MySql\NetworkCrawl::TYPE_HOUR }}');
                }
            })
            /* 多选 */
            $('#select-all').click(function() {
                $('.multi-checkbox input:checkbox').each(function () {
                    $(this).prop('checked', 'checked');
                });
                $('.multi-checkbox[hidden="hidden"] input:checkbox').each(function () {
                    $(this).prop('checked', false);
                });
            });
            /* 反选 */
            $('#select-inverse').click(function() {
                $('.multi-checkbox input:checkbox').each(function () {
                    let checked = $(this).prop('checked');
                    $(this).prop('checked', !checked);
                });
                $('.multi-checkbox[hidden="hidden"] input:checkbox').each(function () {
                    $(this).prop('checked', false);
                });
            });
            $('.clockpicker').clockpicker();
        });
        function submitForm() {
            let platformChecked = $('input[name="nw_firm_id[]"]:checked');
            if (platformChecked.length === 0) {
                $.alert({
                    title: '无法提交表单！',
                    content: '请选择至少一个广告平台！',
                    buttons: {
                        ok: {
                            text: '好的',
                            btnClass: 'btn-warning'
                        }
                    }
                });
                return false;
            }
            $.ajax({
                url: '/network-crawl',
                type: 'post',
                data: $('form').serializeArray(),
                success: function () {
                    location.href = '/network-crawl';
                },
                error: function (response) {
                    $.alert({content: response});
                }
            });
            return false;
        }
    </script>
@endsection
