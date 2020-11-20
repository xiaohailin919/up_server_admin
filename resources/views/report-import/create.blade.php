{{--@extends('layouts.admin')--}}

{{--@section('content')--}}
{{--    <!-- Page-Title -->--}}
{{--    <div class="row">--}}
{{--        <div class="col-sm-12">--}}
{{--            <div class="page-title-box">--}}
{{--                <div class="btn-group pull-right">--}}
{{--                    <ol class="breadcrumb hide-phone p-0 m-0">--}}
{{--                        <li class="breadcrumb-item">--}}
{{--                            <a href="{{ route('home') }}">TopOn</a>--}}
{{--                        </li>--}}
{{--                        <li class="breadcrumb-item">--}}
{{--                            <a href="{{ URL::to('report-import') }}">Upload Network Report</a>--}}
{{--                        </li>--}}
{{--                        <li class="breadcrumb-item active">Upload</li>--}}
{{--                    </ol>--}}
{{--                </div>--}}
{{--                <h4 class="page-title">Upload</h4>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    --}}
{{--    <div class="row">--}}
{{--        <div class="col-md-12">--}}
{{--            <div class="card-box">--}}
{{--                {{ Form::open(array('url' => 'report-import', 'method' => 'POST', 'enctype' => 'multipart/form-data')) }}--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-6">--}}
{{--                            <label>Publisher ID</label>--}}
{{--                            <input type="text" name="publisher_id" class="form-control" value="{{ $publisherId }}" required--}}
{{--                                   data-parsley-trigger="change"--}}
{{--                                   data-parsley-remote="{{ URL::to('publisher/check-exist') }}"--}}
{{--                                   data-parsley-remote-options='{ "type": "GET", "dataType": "json", "data": { "request": "ajax" } }'--}}
{{--                                   data-parsley-error-message="This Publisher is not existed."/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-6">--}}
{{--                            <label>Report Currency</label>--}}
{{--                            <select name="currency" id="currency" class="form-control" required>--}}
{{--                                <option value="CNY">CNY</option>--}}
{{--                                <option value="USD">USD</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-6">--}}
{{--                            <label>Current Exchange Rate</label><label style="color:#ff3111">（上传报表币种是USD的，该汇率值不生效）</label>--}}
{{--                            <div class="input-group">--}}
{{--                                <div class="input-group-prepend">--}}
{{--                                    <span class="input-group-text">--}}
{{--                                        $--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" value="" required min="0" step="0.00000001" data-parsley-errors-container="#exchangeRateErr" />--}}
{{--                            </div>--}}
{{--                            <div id="exchangeRateErr"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-6">--}}
{{--                            <label>Network</label>--}}
{{--                            <select name="network_id" class="form-control" required >--}}
{{--                                @foreach ($firmMap as $key => $val)--}}
{{--                                <option value="{{ $key }}">{{ $val }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-6">--}}
{{--                            <label>Area</label>--}}
{{--                            <select name="geo_short" class="form-control" required >--}}
{{--                                <option value="CN">CN</option>--}}
{{--                                <option value="JP">JP</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-6">--}}
{{--                            <label>Local File</label>--}}
{{--                            <input type="file" name="file" class="form-control" required />--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-6">--}}
{{--                            <button type="submit" class="btn btn-primary" disabled>--}}
{{--                                Loading Exchange Rate ...--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                {{ Form::close() }}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <script>--}}
{{--        $(document).ready(function () {--}}
{{--            $('form').parsley();--}}
{{--            $.ajax({--}}
{{--                url: "{{ URL::to('report-import/exchange-rate') }}",--}}
{{--                async: true,--}}
{{--                dataType: 'json',--}}
{{--                type: 'GET',--}}

{{--                success: function(data, status){--}}
{{--                    if(data.code != 0){--}}
{{--                        alert("Please try again");--}}
{{--                        return;--}}
{{--                    }--}}
{{--                    $("input[name='exchange_rate']").val(data.data.rate);--}}
{{--                    $("button[type='submit']").prop('disabled', false).html('Submit');--}}
{{--                },--}}

{{--                error: function(jqXHR , status , errorThrown){--}}
{{--                    alert("Please try again");--}}
{{--                }--}}
{{--            });--}}

{{--            $("input[name='publisher_id']").blur(function(){--}}
{{--                $.ajax({--}}
{{--                    url: "{{ URL::to('report-import/network-list') }}",--}}
{{--                    async: true,--}}
{{--                    dataType: 'json',--}}
{{--                    type: 'GET',--}}
{{--                    data: {--}}
{{--                        publisher_id: $(this).val()--}}
{{--                    },--}}

{{--                    success: function(data, status){--}}
{{--                        if(data.code != 0){--}}
{{--                            alert("Please try again");--}}
{{--                            return;--}}
{{--                        }--}}
{{--                        var nList = data.data;--}}
{{--                        var option = '';--}}
{{--                        for(i in nList){--}}
{{--                            option += '<option value="' + nList[i].id + '">' + nList[i].nw_firm_name + ' - ' + nList[i].name + '</option>'--}}
{{--                        }--}}
{{--                        $("select[name='network_id']").html(option);--}}
{{--                        $("button[type='submit']").prop('disabled', false).html('Submit');--}}
{{--                    },--}}

{{--                    error: function(jqXHR , status , errorThrown){--}}
{{--                        alert("Please try again");--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}
{{--            // fixRateByCurrency($("#currency").val());--}}
{{--            //--}}
{{--            // $("#currency").change(function () {--}}
{{--            //     let currency = this.value--}}
{{--            //     console.log("currency:" + currency)--}}
{{--            //     fixRateByCurrency(currency)--}}
{{--            // });--}}
{{--        });--}}

{{--        function fixRateByCurrency(currency) {--}}
{{--            switch (currency) {--}}
{{--                case "CNY":--}}
{{--                    $('#exchange_rate').prop('disabled', false);--}}
{{--                    break;--}}
{{--                case "USD":--}}
{{--                    $('#exchange_rate').prop('disabled', 'disabled');--}}
{{--                    $('#exchange_rate').val('1');--}}
{{--                    break;--}}
{{--            }--}}
{{--        }--}}
{{--    </script>--}}

{{--@endsection--}}
