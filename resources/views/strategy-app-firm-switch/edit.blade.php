@extends('layouts.admin')

@section('content')
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
                            <a href="{{ URL::to('strategy-app-firm') }}">App Firm Strategy</a>
                        </li>
                        <li class="breadcrumb-item active">Edit App Firm Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit App Firm Strategy</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-app-firm-switch.update', $data['id']), 'method' => 'PUT')) }}
                <input type="hidden" name="current_app_id" value="{{ $currentAppId }}">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">Filled Request TK上报PKG概率</label>
                        <div class="input-group">
                            <input type="number" name="filled_request_switch" min="0" max="100" id="filled_request_switch" value="{{ $data['filled_request_switch'] }}" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">Impression TK上报PKG概率</label>
                        <div class="input-group">
                            <input type="number" name="impression_switch" min="0" max="100" id="impression_switch" value="{{ $data['impression_switch'] }}" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">Show TK上报PKG概率</label>
                        <div class="input-group">
                            <input type="number" name="show_switch" min="0" max="100" id="show_switch" value="{{ $data['show_switch'] }}" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCacheTime">Click TK上报PKG概率</label>
                        <div class="input-group">
                            <input type="number" name="click_switch" min="0" max="100" id="click_switch" value="{{ $data['click_switch'] }}" class="form-control" placeholder="(0~100%，默认0%，0%=关，100%=开)"/>
                            <div class="input-group-append">
                                <span class="input-group-text small"><small>%</small></span>
                            </div>

                        </div>
                    </div>
                </div>

                @if($data['nw_firm_id'] != 0)
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <div class="mt-3">
                            @foreach ($statusMap as $key => $val)
                                <label class="custom-control custom-radio">
                                    <input name="status" type="radio" value={{ $key }} class="custom-control-input"
                                           @if ($data['status'] == $key) checked="" @endif >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">{{ $val }}</span>
                                </label>
                                @endforeach


                        </div>
                    </div>
                </div>
                @endif

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>PKG匹配列表</label>
                        <textarea name="pkg" placeholder="json格式字符串" type="text" class="form-control">{{ $data['package_match_list'] }}</textarea>
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
    (function($){
        
    });
    </script>

@endsection
