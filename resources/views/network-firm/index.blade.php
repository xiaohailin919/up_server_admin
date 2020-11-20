@extends('layouts.admin')

@section('content')
    <style>
        .mdi-check::before {
            font-weight: bolder;
        }
        .mdi-close::before {
            font-weight: bolder;
        }
        .mdi-check {
            color: #a3c83e;
        }
        .mdi-close {
            color: #d6d6d6;
        }
        .mdi-android {
            font-size: 0.8em;
        }
        .mdi-apple {
            font-size: 0.8em;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage Firm</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage Firm</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input class="form-control" name="id" value="{{ $pageAppends['id'] }}" type="number" placeholder="Network Firm ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" name="firm_name" value="{{ $pageAppends['firm_name'] }}" type="text" placeholder="Firm Name">
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="publisher_id">
                                <option value="">选择开发者</option>
                                @foreach($publisherMap as $id => $name)
                                    <option value="{{ $id }}" @if ($pageAppends['publisher_id'] === (string)$id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="native">
                                <option value="">Native All</option>
                                <option value="0" @if ($pageAppends['native'] === '0') selected @endif>Native No</option>
                                <option value="1" @if ($pageAppends['native'] === '1') selected @endif>Native Support</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="rewarded_video">
                                <option value="">Rewarded Video All</option>
                                <option value="0" @if ($pageAppends['rewarded_video'] === '0') selected @endif>Rewarded Video No</option>
                                <option value="1" @if ($pageAppends['rewarded_video'] === '1') selected @endif>Rewarded Video Support</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="banner">
                                <option value="">Banner All</option>
                                <option value="0" @if ($pageAppends['banner'] === '0') selected @endif>Banner No</option>
                                <option value="1" @if ($pageAppends['banner'] === '1') selected @endif>Banner Support</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="interstitial">
                                <option value="">Interstitial All</option>
                                <option value="0" @if ($pageAppends['interstitial'] === '0') selected @endif>Interstitial No</option>
                                <option value="1" @if ($pageAppends['interstitial'] === '1') selected @endif>Interstitial Support</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="splash">
                                <option value="">Splash All</option>
                                <option value="0" @if ($pageAppends['splash'] === '0') selected @endif>Splash No</option>
                                <option value="1" @if ($pageAppends['splash'] === '1') selected @endif>Splash Support</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="crawl_support">
                                <option value="">Crawl Day All</option>
                                <option value="0" @if ($pageAppends['crawl_support'] === '0') selected @endif>Crawl Day No</option>
                                <option value="1" @if ($pageAppends['crawl_support'] === '1') selected @endif>Crawl Day Support</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="crawl_support_hour">
                                <option value="">Crawl Hour All</option>
                                <option value="1" @if ($pageAppends['crawl_support_hour'] === '1') selected @endif>Crawl Hour No</option>
                                <option value="2" @if ($pageAppends['crawl_support_hour'] === '2') selected @endif>Crawl Hour Support</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="report_currency">
                                <option value="">选择币种</option>
                                @foreach($currencyList as $currency)
                                    <option value={{ $currency }} @if ($pageAppends['report_currency'] === $currency) selected="selected" @endif>{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="order_by">
                                <option value="id" @if($pageAppends['order_by'] !== 'rank') selected @endif>按 ID 排序</option>
                                <option value="rank" @if($pageAppends['order_by'] === 'rank') selected @endif>按 Rank 排序</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ Illuminate\Support\Facades\URL::to('network-firm/create') }}" class="btn btn-info">Add</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Publisher<i class="dripicons-warning" style="color: red;vertical-align: middle" data-toggle="tooltip" title="注意：1. 同时登陆多个账号，后登陆的会挤掉先登录的！2. 任何时候请不要删除或修改任何数据，除非是自己新建的数据！"></i></th>
                            <th style="text-align: center">Native</th>
                            <th style="text-align: center">Rewarded video</th>
                            <th style="text-align: center">Banner</th>
                            <th style="text-align: center">Interstitial</th>
                            <th style="text-align: center">Splash</th>
                            <th style="text-align: center">Crawl Day</th>
                            <th style="text-align: center">Crawl Hour</th>
                            <th>Currency</th>
                            <th>Rank</th>
                            <th>Time(create/update)</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val['id'] }}</td>
                                <td>{{ $val['name'] }}</td>
                                <td>
                                    {{ $val['publisher_name'] }}<br/>
                                    @if ($val['publisher_id'] != 0)
                                        <small>
                                            <a href="{{ \Illuminate\Support\Facades\URL::to('publisher/login?id=' . $val['publisher_id']) }}" target="_blank">
                                                <i class=" mdi mdi-login-variant"></i>Login
                                            </a>
                                        </small>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if ($val['native'] === 1)
                                        <i class="mdi mdi-check"></i><br/>
                                        <i class="mdi mdi-android" @if($val['and_native'] === '0') style="color: #d0021b" title="Android Adapter 未配置" @else style="color: #a3c83e" title="{{ $val['and_native'] }}" @endif></i>
                                        <i class="mdi mdi-apple"   @if($val['ios_native'] === '0') style="color: #d0021b" title="IOS Adapter 未配置" @else title="{{ $val['ios_native'] }}"@endif></i>
                                    @else
                                        <i class="mdi mdi-close"></i><br/>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if ($val['rewarded_video'] === 1)
                                        <i class="mdi mdi-check"></i><br/>
                                        <i class="mdi mdi-android" @if($val['and_rewarded_video'] === '0') style="color: #d0021b" title="Android Adapter 未配置" @else style="color: #a3c83e" title="{{ $val['and_rewarded_video'] }}" @endif></i>
                                        <i class="mdi mdi-apple"   @if($val['ios_rewarded_video'] === '0') style="color: #d0021b" title="IOS Adapter 未配置" @else title="{{ $val['ios_rewarded_video'] }}"@endif></i>
                                    @else
                                        <i class="mdi mdi-close"></i><br/>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if ($val['banner'] === 1)
                                        <i class="mdi mdi-check"></i><br/>
                                        <i class="mdi mdi-android" @if($val['and_banner'] === '0') style="color: #d0021b" title="Android Adapter 未配置" @else style="color: #a3c83e" title="{{ $val['and_banner'] }}" @endif></i>
                                        <i class="mdi mdi-apple"   @if($val['ios_banner'] === '0') style="color: #d0021b" title="IOS Adapter 未配置" @else title="{{ $val['ios_banner'] }}"@endif></i>
                                    @else
                                        <i class="mdi mdi-close"></i><br/>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if ($val['interstitial'] === 1)
                                        <i class="mdi mdi-check"></i><br/>
                                        <i class="mdi mdi-android" @if($val['and_interstitial'] === '0') style="color: #d0021b" title="Android Adapter 未配置" @else style="color: #a3c83e" title="{{ $val['and_interstitial'] }}" @endif></i>
                                        <i class="mdi mdi-apple"   @if($val['ios_interstitial'] === '0') style="color: #d0021b" title="IOS Adapter 未配置" @else title="{{ $val['ios_interstitial'] }}"@endif></i>
                                    @else
                                        <i class="mdi mdi-close"></i><br/>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if ($val['splash'] === 1)
                                        <i class="mdi mdi-check"></i><br/>
                                        <i class="mdi mdi-android" @if($val['and_splash'] === '0') style="color: #d0021b" title="Android Adapter 未配置" @else style="color: #a3c83e" title="{{ $val['and_splash'] }}" @endif></i>
                                        <i class="mdi mdi-apple"   @if($val['ios_splash'] === '0') style="color: #d0021b" title="IOS Adapter 未配置" @else title="{{ $val['ios_splash'] }}"@endif></i>
                                    @else
                                        <i class="mdi mdi-close"></i><br/>
                                    @endif
                                </td>
                                <td style="text-align: center">@if($val['crawl_support'] === 1)  <i class="mdi mdi-check"></i> @else <i class="mdi mdi-close"></i> @endif</td>
                                <td style="text-align: center">@if($val['crawl_support_hour'] === 2)  <i class="mdi mdi-check"></i> @else <i class="mdi mdi-close"></i> @endif</td>
                                <td style="font-family:'Lucida Console', Monaco, monospace;">
                                    @foreach($currencySymbolMap as $currency => $symbol)
                                        @if($val['report_currency'] === $currency) {{ $val['report_currency'] }}<small>  {{ $symbol }}</small> @endif
                                    @endforeach
                                </td>
                                <td>{{ $val['rank'] }}</td>
                                <td>{{ strftime('%Y-%m-%d %H:%M:%S', $val['create_time']) }}<br /><small>{{ strftime('%Y-%m-%d %H:%M:%S', $val['update_time']) }}</small></td>
                                <td>
                                    @if($val['id'] <= \App\Models\MySql\NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)
                                        <a class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm" href="{{ Illuminate\Support\Facades\URL::to('network-firm/'.$val['id'] . '/edit' . $uri) }}">Edit</a>
                                    @else
                                        <a class="btn btn-outline-secondary w-sm btn-sm" disabled="disabled" title="用户自定义厂商不开放编辑">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $data->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $data->appends($pageAppends)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
