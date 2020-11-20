@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Placement Firm Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Placement Firm Strategy</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Firm Name</th>
                        <th>Cache Time</th>
                        <th>
                            Network广告素材<br />
                            超时时间<br />
                            （Network超时时间）
                        </th>
                        <th>
                            Network广告数据<br />
                            超时时间
                        </th>
                        <th>
                            AD Source维度<br />
                            Up_status有效期
                        </th>
                        <th>Offer请求条数</th>
                        <th>HB Timeout</th>
                        <th>
                            Bid Token<br />
                            缓存有效期
                        </th>
                        <th>Status</th>
                        <th>Create Time</th>
                        <th>Update Time</th>
                        <th>Operation</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $val)
                        <tr>
                            <th scope="row">{{ $val['id'] }}</th>
                            <td>{{ $val['firm_name'] }}</td>
                            <td>{{ $val['nw_cache_time'] }}</td>
                            <td>{{ $val['nw_timeout'] }}</td>
                            <td>{{ $val['ad_data_nw_timeout'] }}</td>
                            <td>{{ $val['ad_up_status'] }}</td>
                            <td>{{ $val['nw_offer_requests'] }}</td>
                            <td>{{ $val['header_bidding_timeout'] }}</td>
                            <td>{{ $val['bid_token_cache_time'] }}</td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['create_time']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $val['update_time']) }}</td>
                            <td>
                                @if ($val['status'] == 1)
                                    <a href="#" onclick="updateStatus({{ $val['id'] }}, 2)"
                                       class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Running</a>
                                @else
                                    <a href="#" onclick="updateStatus({{ $val['id'] }}, 1)"
                                       class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Block</a>
                                @endif
                                <a href="{{ URL::to('strategy-placement-firm/' . $val['id'] . '/edit') }}"
                                   class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a>
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
                        {{ $data->appends([
                            'str_pl_id' => $strPlId,
                        ])->links() }}
                    </div>
                </div>
            </div>
            <div class="card-box">
                {{ Form::model($data, array('route' => array('strategy-placement-firm.store'), 'method' => 'POST')) }}
                <input type="hidden" name="str_pl_id" value="{{ $strPlId }}"/>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwCacheTime">厂商</label>
                        <select name="nw_firm_id" class="form-control">
                            @foreach ($firm as $key => $val)
                                <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwCacheTime">Network缓存时间</label>

                        <div class="input-group">
                            <input type="number" name="nw_cache_time" value="1800" required class="form-control"
                                   id="inputNwCacheTime"/>

                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwTimeout">Network广告素材超时时间（原Network超时时间）</label>
                        <div class="input-group">
                            <input type="number" name="nw_timeout" value="{{ $nwTimeout }}"
                                   class="form-control" id="inputNwTimeout"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwTimeout">Network广告数据超时时间</label><label class="version_label">（SDK Version 5.1.0及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="ad_data_nw_timeout" value="-1" class="form-control" id="inputNwTimeout"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputLoadSuccessUpStatus">Ad Source 维度Up_status有效期</label><label
                                class="version_label">（SDK Version 5.1.0及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="ad_up_status"
                                   value="900" class="form-control"
                                   id="inputLoadSuccessUpStatus"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNwOfferRequests">Network下的Offer请求条数</label>
                        <input type="number" name="nw_offer_requests" value="1" required class="form-control"
                               id="inputNwOfferRequests"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="headerBiddingTimeout">Header Bidding 超时时间</label>

                        <div class="input-group">
                            <input type="number" value=3000 step=100 name="header_bidding_timeout"
                                   class="form-control" id="headerBiddingTimeout"/>

                            <div class="input-group-append">
                                <span class="input-group-text">milliseconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputLoadSuccessUpStatus">Bid Token缓存有效期</label><label class="version_label">（SDK Version 5.1.1及以上支持）</label>
                        <div class="input-group">
                            <input type="number" name="bid_token_cache_time"
                                   value="{{ $bidTokenCacheTime }}" class="form-control"
                                   id="inputLoadSuccessUpStatus"/>
                            <div class="input-group-append">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>

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
        function updateStatus(id, status) {
            $.ajax({
                url: "{{ URL::to('strategy-placement-firm') }}/" + id,
                async: true,
                dataType: 'json',
                type: 'PUT',
                data: {id: id, status: status},

                success: function (data, status) {
                    if (data.status == 1) {
                        location.reload();
                    }
                },

                error: function (jqXHR, status, errorThrown) {
                    alert("Please try again");
                }
            });
        }

        (function ($) {

        });
    </script>

@endsection
