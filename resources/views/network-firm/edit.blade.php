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
                        <li class="breadcrumb-item active">Edit Firm</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Firm</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="{{ \Illuminate\Support\Facades\URL::to('/network-firm/' . $data['id']) }}">
                    <input name="_method" type="hidden" value="PUT"/>
                    <input name="full_url" type="hidden" value="{{ request()->fullUrl() }}"/>
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-3">
                            <label for="name-input">Name</label>
                            <input class="form-control" id="name-input" name="name" type="text" value="{{ $data['name'] }}" placeholder="Firm name" required/>
                        </div>
                    </div>
                    @if ($errors->has('name'))
                        <span class="help-block">
                             <strong style="color: red">{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Publisher</label>
                            <input class="form-control" type="text" value="{{ $data['publisher_name'] }} | {{ $data['publisher_id'] }}" disabled/>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Native</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="native" type="radio" value="1" @if ($data['native'] === 1) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="native" type="radio" value="0" @if ($data['native'] === 0) checked @endif >
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
                                    <input class="custom-control-input" name="rewarded_video" type="radio" value="1" @if ($data['rewarded_video'] === 1) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="rewarded_video" type="radio" value="0" @if ($data['rewarded_video'] === 0) checked @endif >
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
                                    <input class="custom-control-input" name="banner" type="radio" value="1" @if ($data['banner'] === 1) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="banner" type="radio" value="0" @if ($data['banner'] === 0) checked @endif >
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
                                    <input class="custom-control-input" name="interstitial" type="radio" value="1" @if ($data['interstitial'] === 1) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="interstitial" type="radio" value="0" @if ($data['interstitial'] === 0) checked @endif >
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
                                    <input class="custom-control-input" name="splash" type="radio" value="1" @if ($data['splash'] === 1) checked @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="splash" type="radio" value="0" @if ($data['splash'] === 0) checked @endif >
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
                                    <input class="custom-control-input" name="crawl_support" type="radio" value="1" @if ($data['crawl_support'] === 1) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="crawl_support" type="radio" value="0" @if ($data['crawl_support'] === 0) checked @endif>
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
                                    <input class="custom-control-input" name="crawl_support_hour" type="radio" value="2" @if ($data['crawl_support_hour'] === 2) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>　
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="crawl_support_hour" type="radio" value="1" @if ($data['crawl_support_hour'] === 1) checked @endif>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
{{--                    <div class="form-row">--}}
{{--                        <div class="form-group col-md-3">--}}
{{--                            <label for="api-version-input">Api version</label>--}}
{{--                            <input id="api-version-input" name="api_version" type="number" value="{{ $data['api_version'] }}" class="form-control" required/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="report-currency-input">Report Currency</label>
                            <select class="form-control" id="report-currency-input" name="report_currency">
                                @foreach($currencyList as $currency)
                                    <option value="{{ $currency }}" @if($data['report_currency'] === $currency) selected @endif>{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="rank-input">Rank</label>
                            <input class="form-control" id="rank-input" name="rank" type="number" value="{{ $data['rank'] }}" required/>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                    @if ($errors->has('submit'))
                        <span class="help-block">
                             <strong style="color: red">{{ $errors->first('submit') }}</strong>
                        </span>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection