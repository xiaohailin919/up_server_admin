@extends('layouts.admin')

@section('content')
    <style>
        .mt-3 {
            margin: 0!important;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('/firm-adapter') }}">Firm Adapter</a></li>
                        <li class="breadcrumb-item active">Add Firm Adapter</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Firm Adapter</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form id="form" onsubmit="return submitCreation()">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="firm_id_select">Firm</label>
                            <select name="firm_id" id="firm_id_select" class="form-control" onchange="onFirmIdSelectChange()" required>
                                <option>Choose Firm</option>
                            @foreach($firmIdNameMap as $key => $value)
                                    <option value="{{ $key }}">{{ $key }} | {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Platform</label>
                            <div class="mt-3" onclick="$('#create-error').attr('hidden', true);">
                                <label class="custom-control custom-radio">
                                    <input name="platform" type="radio" value="1" class="custom-control-input" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description"><i class="mdi mdi-android" style="color: #a3c83e"></i> Android</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input name="platform" type="radio" value="2" class="custom-control-input">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description"><i class="mdi mdi-apple"></i> IOS</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Publisher</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="publisher" type="radio" value="all" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">全部</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="publisher" type="radio" value="specific">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">指定 Publisher</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row" id="publisher-row" hidden>
                        <div class="form-group col-md-3">
                            <label for="publisher-id-input">Publisher ID</label>
                            <input class="form-control" id="publisher-id-input" name="publisher_id" value="0" placeholder="请输入开发者 ID" type="number" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="format-selector">Format</label>
                            <select id="format-selector" name="format" class="form-control" onchange="$('#create-error').attr('hidden', true);">
                                <option value="-1">Choose format</option>
                                @for ($i = 0,$iMax = count($formatMap); $i < $iMax; $i++)
                                    <option value="{{ $i }}" id="format_{{ $i }}">{{ $formatMap[$i] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="adapter-input">Adapter</label>
                            <textarea id="adapter-input" name="adapter" placeholder="Please input the adapter name" type="text" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <span id="create-error" hidden></span>
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
        const firmIdFormatMap = @json($firmIdFormatMap);
        console.log(firmIdFormatMap);
        function submitCreation() {
            $.ajax({
                url: '/firm-adapter',
                type: 'POST',
                data: $('#form').serializeArray(),
                success: function(response) {
                    if (response[0]) {
                        location.replace('/firm-adapter');
                    } else {
                        let error = $('#create-error');
                        error.text(response[1]);
                        error.attr('style', 'color: #d0021b;');
                        error.attr('hidden', false);
                    }
                }
            });
            return false;
        }

        function onFirmIdSelectChange() {
            let firmId = $('#firm_id_select').val();
            if (firmId === '-1') {
                for (let i = 0; i < 5; i++) {
                    let option = $('#format_' + i);
                    option.attr('disabled', false);
                    option.attr('style', 'color:#4a4a4a');
                    option.attr('title', '');
                }
                return;
            }
            let formatList = firmIdFormatMap[firmId];
            for (let i = 0; i < formatList.length; i++) {
                let option = $('#format_' + i);
                option.attr('disabled', formatList[i] === 0);
                option.attr('style', formatList[i] === 0 ? 'color:#bf404d' : '');
                option.attr('title', formatList[i] === 0 ? 'doesn\'t support' : '');
            }
        }

        function onPublisherTypeChange() {
            console.log('test');
            if ($('input[name="publisher"]:checked').val() == 'all') {
                $('#publisher-row').attr('hidden', 'hidden');
                $('input[name="publisher_id"]').val(0);
            } else {
                $('#publisher-row').attr('hidden', false);
                $('input[name="publisher_id"]').val('');
            }
        }

        $('input[name="publisher"]').change(onPublisherTypeChange);
    </script>
@endsection