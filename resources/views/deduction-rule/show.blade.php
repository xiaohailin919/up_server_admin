@extends('layouts.admin')

@section('content')
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
                    {{ Form::model($data, array('route' => array('manage-impression.update', $data['id']), 'method' => 'PUT')) }}
                @else
                    {{ Form::model($data, array('route' => array('manage-fill-rate.update', $data['id']), 'method' => 'PUT')) }}
                @endif
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="dimension">Dimension</label>
                        <select id="dimension" class="form-control" disabled>
                            @foreach ($dimensionMap as $key => $val)
                                @if ($key == $dimension)
                                    <option  selected="selected">{{ $val }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="publisher_id">Publisher ID</label>
                        <input type="text" value="{{ $data['publisher_id'] }}" class="form-control" disabled/>
                    </div>
                </div>
                <div class="form-row" @if($dimension == \App\Models\MySql\DeductionRule::DIMENSION_PUBLISHER) style="display: none" @endif>
                    <div class="form-group col-md-3">
                        <label for="app_id">App ID</label>
                        <input type="text" value="{{ $data['app_id'] }}" class="form-control" disabled/>
                    </div>
                </div>
                <div class="form-row" @if($dimension != \App\Models\MySql\DeductionRule::DIMENSION_PLACEMENT) style="display: none" @endif>
                    <div class="form-group col-md-3">
                        <label for="placement_id">Placement ID</label>
                        <input type="text" value="{{ $data['placement_id'] }}" class="form-control" disabled/>
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
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script>
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
    </script>
@endsection
