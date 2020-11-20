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
                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\URL::to('/network-firm') }}">Firm</a></li>
                        <li class="breadcrumb-item active">Add Firm</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Firm</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="{{ \Illuminate\Support\Facades\URL::to('/network-firm') }}">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="form-group{{ $errors->has('id') ? ' has-error' : '' }} col-md-3">
                            <label for="id-input">ID</label>
                            <input class="form-control" id="id-input" name="id" type="number" placeholder="Firm ID" value="{{ $maxId + 1 }}" />
                        </div>
                    </div>
                    @if ($errors->has('name'))
                        <span class="help-block">
                             <strong style="color: red">{{ $errors->first('id') }}</strong>
                        </span>
                    @endif
                    <div class="form-row">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-3">
                            <label for="name-input">Name</label>
                            <input class="form-control" id="name-input" name="name" type="text" placeholder="Firm name" required/>
                        </div>
                    </div>
                    @if ($errors->has('name'))
                        <span class="help-block">
                             <strong style="color: red">{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Native</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="native" type="radio" value="1">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="native" type="radio" value="0" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Rewarded Video</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="rewarded_video" type="radio" value="1">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="rewarded_video" type="radio" value="0" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Banner</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="banner" type="radio" value="1">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="banner" type="radio" value="0" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Interstitial</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="interstitial" type="radio" value="1">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="interstitial" type="radio" value="0" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Splash</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="splash" type="radio" value="1">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="splash" type="radio" value="0" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Crawl Day</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="crawl_support" type="radio" value="1">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="crawl_support" type="radio" value="0" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Crawl Hour</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="crawl_support_hour" type="radio" value="2">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="crawl_support_hour" type="radio" value="1" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-3">--}}
{{--                            <label for="api-version-input">Api version</label>--}}
{{--                            <input id="api-version-input" name="api_version" type="number" value="1" class="form-control" required/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="report-currency-input">Report Currency</label>
                            <select class="form-control" id="report-currency-input" name="report_currency">
                                @foreach($currencyList as $currency)
                                    <option value="{{ $currency }}" @if($currency === \App\Models\MySql\NetworkFirm::CURRENCY_USD) selected @endif>{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="rank-input">Rank</label>
                            <input class="form-control" id="rank-input" name="rank" type="number" value="0" required/>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection