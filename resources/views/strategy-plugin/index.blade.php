@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Manage Ads Visibility SDK Strategy</li>
                    </ol>
                </div>
                <h4 class="page-title">Ads Visibility SDK Strategy</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <a href="{{ URL::to('strategy-plugin/create') }}" class="btn btn-info">Add</a>
                            <button type="button" onclick="configureWhiteList()" class="btn btn-info">配置白名单</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Network Firm Id</th>
                        <th>Platform</th>
                        <th>Network Name</th>
                        <th>upload URL</th>
                        <th>PKG延时上报最小值（毫秒）</th>
                        <th>PKG延时上报最大值（毫秒）</th>
                        <th>Operation</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $val)
                        <tr>
                            <th scope="row">{{ $val['id'] }}</th>
                            <td>{{ $val['nw_firm_id'] }}</td>
                            <td>{{ $val['platform'] == 2 ? 'iOS' : 'Android' }}</td>
                            <td>{{ $val['firm_name'] }}</td>
                            <td>{{ $val['package_upload_address_list'] }}</td>
                            <td>{{ $val['pkg_address_timeout_min'] }}</td>
                            <td>{{ $val['pkg_address_timeout_max'] }}</td>
                            <td>{{ $val['status_name'] }}</td>
                            <td>
                                @if ($val['status'] == 2)
                                    <a href="#" onclick="updateStatus({{ $val['id'] }}, 3)"
                                       class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Running</a>
                                @else
                                    <a href="#" onclick="updateStatus({{ $val['id'] }}, 2)"
                                       class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm">Block</a>
                                @endif
                                <a href="{{ URL::to('strategy-plugin/' . $val['id'] . '/edit') }}"
                                   class="btn btn-outline-success waves-light waves-effect w-sm btn-sm">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        Total <strong>{{ $data->total() }}</strong>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        {{ $data->appends([
                            //'str_pl_id' => $strPlId,
                        ])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function updateStatus(id, status) {
            $.ajax({
                url: "{{ URL::to('strategy-plugin') }}/" + id,
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


        var androidIds = JSON.parse('@json($androidIds)');
        var idFas = JSON.parse('@json($idFas)');
        var idFvs = JSON.parse('@json($idFvs)');
        var packages = JSON.parse('@json($packages)');
        var csrfToken = JSON.parse('@json(csrf_token())');
        androidIds = androidIds == null ? '' : androidIds.join('\n');
        idFas      = idFas      == null ? '' : idFas.join('\n');
        idFvs      = idFvs      == null ? '' : idFvs.join('\n');
        packages   = packages   == null ? '' : packages.join('\n');
        /**
         * 配置白名单
         */
        function configureWhiteList() {
            //.join(',\n')
            $.confirm({
                title: '配置独立插件白名单规则',
                columnClass: 'col-md-8',
                content: `
                        <form class="col-md-12" id="priority-form">
                            <input name="_token" type="hidden" value="` + csrfToken + `"/>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label style="width: 100%">Android ID<strong style="color: red;float: right">(所有规则多个内容以 “换行” 分割)</strong></label>
                                    <textarea name="android_ids" type="text" class="form-control">` + androidIds + `</textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Idfa</label>
                                    <textarea name="idfas" type="text" class="form-control">` + idFas + `</textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Idfv</label>
                                    <textarea name="idfvs" type="text" class="form-control">` + idFvs + `</textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>应用包名(Bundle ID)</label>
                                    <textarea name="packages" type="text" class="form-control">` + packages + `</textarea>
                                </div>
                            </div>
                            <span id="test" style="color: red">
                                <strong>注：SDK version 5.5.4 及以上的广告可视化插件支持</strong>
                            </span>
                            <br/>
                        </form>
                    `,
                buttons: {
                    confirm: {
                        text: '保存',
                        btnClass: 'btn-blue',
                        action: function () {
                            let data = $('#priority-form').serializeArray();
                            for (let i = 0; i < data.length; i++) {
                                while (data[i].value.indexOf('\r\n') !== -1) {
                                    data[i].value = (data[i].value).replace('\r\n', '\n');
                                }
                            }
                            $.ajax({
                                url: '/strategy-plugin/white-list',
                                type: 'POST',
                                data: data,
                                success: function (response) {
                                    console.log(response);
                                    if (response.code === '200') {
                                        location.replace(location.href);
                                    } else {
                                        $.alert({
                                            content: response.message === '' ? '更新失败' : response.message
                                        })
                                    }
                                },
                                error: function(response){
                                    if (response.responseJSON.exception.message != null) {
                                        $.alert({
                                            title: 'Error',
                                            theme: 'dark',
                                            content: "暂无权限进行该操作!"
                                        })
                                    }
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        btnClass: 'btn-secondary'
                    }
                }
            });
        }
    </script>

@endsection
