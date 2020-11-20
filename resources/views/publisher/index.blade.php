@extends('layouts.admin')
<style>
    .tooltip-inner {
        max-width: none!important;
    }
</style>
@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage Publisher</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage Publisher</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_id" value="{{ $publisherId }}" class="form-control" id="inputPublisherId" placeholder="Publisher ID">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="publisher_name" value="{{ $publisherName }}" class="form-control" id="inputPublisherName" placeholder="Publisher name">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="email" value="{{ $email }}" class="form-control" id="inputEmail" placeholder="Email">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="status" class="form-control">
                                <option value="all" selected>All Status</option>
                                @foreach ($statusMap as $key => $val)
                                <option value="{{ $key }}" @if ($status === $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="system" class="form-control">
                                <option value="all" >All System</option>
                                @foreach ($systemMap as $key => $val)
                                    <option value="{{ $key }}" @if ($system == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <select name="publisher_type" class="form-control">
                                <option value="all" >All Publisher Type</option>
                                @foreach ($publisherTypeMap as $key => $val)
                                    <option value="{{ $key }}" @if ($publisherType == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <select name="channel" class="form-control">
                                <option value="all" selected>All Channel</option>
                                @foreach ($channelMap as $key => $val)
                                    <option value="{{ $key }}" @if ($channel === $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <input type="text" name="note" value="{{ $note }}" class="form-control" id="note" placeholder="Note">
                        </div>

                        <div class="form-group col-md-2">
                            <select name="search_type" class="form-control">
                                <option value="main">Main Account</option>
                                <option value="sub" @if($searchType == 'sub') selected @endif>Sub Account</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <select class="form-control select2 select2-multiple select2-hidden-accessible" name="publisher_group_ids[]" multiple="" data-placeholder="- Publisher Group -">
                                @foreach ($publisherGroupIdNameMap as $publisherGroupId => $publisherGroupName)
                                    <option value="{{ $publisherGroupId }}" @if (in_array($publisherGroupId, $publisherGroupIds, false)) selected @endif>{{ $publisherGroupName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                            <button type="submit" name="export" value="1" class="btn btn-success">
                                Export Excel
                            </button>
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
                            <th>Publisher Group</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Channel</th>
                            <th>Note</th>
                            <th>Operation <i class="dripicons-warning" style="color: red;vertical-align: middle" data-toggle="tooltip" title="注意：1. 同时登陆多个账号，后登陆的会挤掉先登录的！2. 任何时候请不要删除或修改任何数据，除非是自己新建的数据！3. 预发布环境所有数据来自线上"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($publisher as $val)
                            <tr>
                                <td>{{ $val['id'] }}</td>
                                <td>
                                    @if($val['sub_account_switch'] == 2)
                                        @if(count($val['sub']) > 0)
                                            <a href="javascript:void(0)" class="sub-publisher-btn" data-id="{{ $val['id'] }}" data-show="0">
                                                <i class="mdi mdi-account-multiple"></i>
                                            </a>
                                        @else
                                            <i class="mdi mdi-account-multiple"></i>
                                        @endif
                                    @endif
                                    {{ $val['name'] }}
                                </td>
                                <td>
                                    @foreach($val['publisher_group_ids'] as $publisherGroupId)
                                        {{ $publisherGroupIdNameMap[$publisherGroupId] }}<br/>
                                    @endforeach
                                </td>
                                <td>{{ $val['email'] }}</td>
                                <td>{{ $statusMap[$val['status']] }}</td>
                                <td>{{ $val['channel_name'] }}</td>
                                <td style="white-space:nowrap;" title="{{ $val['note_title'] }}">
                                    {{ $val['note'] }}
                                </td>
                                <td>
                                    <div class="button-list">
                                        @if ($val['status'] == 2 || $val['status'] == 1)
                                            <a href="#" onclick="confirmUpdateStatus({{ $val['id'] }}, 3)" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Running</a>
                                        @endif
                                        <a href="{{ URL::to('publisher/login?id=' . $val['id']) }}" target="_blank" title="24小时内有效，超时请刷新" class="btn btn-outline-info waves-light waves-effect w-sm btn-sm">Login</a>
                                        @if($val['mode'] == 2)
                                            <a href="{{ URL::to('publisher/login?id=' . $val['id'] . '&type=self') }}" target="_blank" title="以开发者视角登录，24小时内有效，超时请刷新" class="btn btn-outline-info waves-light waves-effect w-sm btn-sm">Dev Login</a>
                                        @endif
                                            <a class="btn btn-outline-primary waves-light waves-effect w-sm btn-sm" href="{{ \Illuminate\Support\Facades\URL::to('publisher/login?id=' . $val['id'] . '&env=pre') }}" target="_blank" title="登录到预发布环境">Pre Env</a>
                                        <a href="{{ URL::to("publisher/{$val['id']}/edit") }}" class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm">Edit</a>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($val['sub'] as $v)
                                <tr class="sub-publisher-{{ $val['id'] }}" style="display: none; background-color: #fffbef;">
                                    <td>{{ $v['id'] }}</td>
                                    <td>{{ $v['name'] }}</td>
                                    <td>
                                        @foreach($v['publisher_group_ids'] as $publisherGroupId)
                                            {{ $publisherGroupIdNameMap[$publisherGroupId] }}<br/>
                                        @endforeach
                                    </td>
                                    <td>{{ $v['email'] }}</td>
                                    <td>{{ $statusMap[$v['status']] }}</td>
                                    <td>{{ $v['channel_name'] }}</td>
                                    <td style="white-space:nowrap;" title="{{ $v['note_title'] }}">{{ $v['note'] }}</td>
                                    <td>
                                        <div class="button-list">
                                            @if ($v['status'] == 2 || $v['status'] == 1)
                                                <a href="#" onclick="confirmUpdateStatus({{ $v['id'] }}, 3)" class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Running</a>
                                            @endif
                                            <a class="btn btn-outline-info waves-light waves-effect w-sm btn-sm" href="{{ URL::to('publisher/login?id=' . $v['id']) }}" target="_blank" title="24小时内有效，超时请刷新">Login</a>
                                            @if($v['mode'] == 2)
                                                <a class="btn btn-outline-info waves-light waves-effect w-sm btn-sm" href="{{ URL::to('publisher/login?id=' . $v['id'] . '&type=self') }}" target="_blank" title="以开发者视角登录，24小时内有效，超时请刷新">Dev Login</a>
                                            @endif
                                                <a class="btn btn-outline-primary waves-light waves-effect w-sm btn-sm" href="{{ \Illuminate\Support\Facades\URL::to('publisher/login?id=' . $val['id'] . '&env=pre') }}" target="_blank" title="登录到预发布环境">Pre Env</a>
                                            <a href="{{ \Illuminate\Support\Facades\URL::to("publisher/{$v['id']}/edit") }}" class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $publisher->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $publisher->appends([
                            'publisher_name' => $publisherName,
                            'status' => $status,
                            'channel' => $channel,
                            'system' => $system,
                            'publisher_type' => $publisherType,
                            'email' => $email,
                        ])->links() }}
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <script>
        {{--function updateMode(id, mode){--}}
            {{--$.ajax({--}}
                {{--url: "{{ URL::to('publisher/updateMode') }}",--}}
                {{--async: true,--}}
                {{--dataType: 'json',--}}
                {{--type: 'PUT',--}}
                {{--data: {id: id, mode:mode},--}}

                {{--success: function(data, status){--}}
                    {{--if(data.status == 1){--}}
                       {{--location.reload();--}}
                    {{--}else{--}}
                        {{--alert(data.msg);--}}
                    {{--}--}}
                {{--},--}}

                {{--error: function(jqXHR , status , errorThrown){--}}
                    {{--alert("Please try again");--}}
                {{--}--}}
            {{--});--}}
        {{--}--}}

    function updateStatus(id, status){
        console.log(JSON.stringify({id: id, data: {status: status}}));
        $.ajax({
            url: "{{ URL::to('publisher/activate') }}",
            async: true,  
            dataType: 'json',  
            type: 'POST',
            data: {id: id},

            success: function(data, status){  
                if(data.status == 1){
                    location.reload();
                }
            },  

            error: function(jqXHR , status , errorThrown){  
                alert("Please try again");
            }
        });  
    }
    // function confirmUpdateMode(id, mode){
    //     swal({
    //         title: 'Attention',
    //         text: 'Are you sure you want to switch the mode of this publisher?',
    //         type: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#4fa7f3',
    //         cancelButtonColor: '#d57171',
    //         confirmButtonText: 'Yes'
    //     }).then(function () {
    //         updateMode(id, mode);
    //     })
    // }

    function confirmUpdateStatus(id, status) {
        //Warning Message
        if(status == 1){
            swal({
                title: 'Attention',
                text: 'Are you sure you want to block this publisher account?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4fa7f3',
                cancelButtonColor: '#d57171',
                confirmButtonText: 'Yes'
            }).then(function () {
                updateStatus(id, status);
            })
        }else if(status == 3){
            swal({
                title: 'Attention',
                text: 'Are you sure you want to run this publisher account?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4fa7f3',
                cancelButtonColor: '#d57171',
                confirmButtonText: 'Yes'
            }).then(function () {
                updateStatus(id, status);
            })
        }
    }
    $(document).ready(function(){
        $(".sub-publisher-btn").each(function(){
            $(this).click(function(){
                var id = $(this).attr('data-id');
                if($(this).attr("data-show") == 0){
                    $(this).attr("data-show", 1);
                    $(".sub-publisher-" + id).show();
                }else{
                    $(this).attr("data-show", 0);
                    $(".sub-publisher-" + id).hide();
                }
            });
        });
    });
    </script>
@endsection