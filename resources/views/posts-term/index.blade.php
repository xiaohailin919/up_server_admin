@extends('layouts.admin')
@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Categories & Tags</li>
                    </ol>
                </div>
                <h4 class="page-title">Categories & Tags</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <select class="form-control" name="type">
                                <option value="" >所有类型</option>
                                @foreach ($typeMap as $key => $val)
                                    <option value="{{ $key }}" @if ((int)$pageAppends['type'] === $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" name="name" type="text" value="{{ $pageAppends['name'] }}" placeholder="名称">
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" name="slug" type="text" value="{{ $pageAppends['slug'] }}" placeholder="别名">
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="order_by">
                                <option value="update_time" @if ($pageAppends['order_by'] === 'update_time') selected="selected" @endif>按最新排序</option>
                                <option value="rank"        @if ($pageAppends['order_by'] === 'rank')        selected="selected" @endif>按权重排序</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="popular">
                                <option value="" >是否热门</option>
                                <option value="2" @if ($pageAppends['popular'] === '2') selected="selected" @endif>热门</option>
                                <option value="1" @if ($pageAppends['popular'] === '1') selected="selected" @endif>非热门</option>
                            </select>
                        </div>
{{--                        <div class="form-group col-md-2">--}}
{{--                            <select class="form-control" name="status">--}}
{{--                                <option value="" >所有状态</option>--}}
{{--                                @foreach ($statusMap as $key => $val)--}}
{{--                                    <option value="{{ $key }}" @if ((int)$pageAppends['status'] === $key) selected="selected" @endif>{{ $val }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
                        <div class="form-group col-md-2">
                            <button class="btn btn-primary" type="submit">搜索</button>
                            <a class="btn btn-success" href="{{ \Illuminate\Support\Facades\URL::to('posts-term/create') }}">创建</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>类型</th>
                            <th>名称</th>
                            <th>别名</th>
                            <th>描述</th>
{{--                            <th>父级</th>--}}
                            <th style="text-align: center">文章数</th>
                            <th style="text-align: center">热门</th>
                            <th>权重</th>
{{--                            <th>状态</th>--}}
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $val)
                            <tr>
                                <td>{{ $typeMap[$val['type']] }}</td>
                                <td>{{ $val['name'] }}</td>
                                <td>{{ $val['slug'] }}</td>
                                <td>{{ str_limit($val['description'],  50, '...') }}</td>
{{--                                <td>{{ $val['parent_name'] }}</td>--}}
                                <td style="text-align: center">{{ $val['posts_count'] }}</td>
                                <td style="text-align: center"><i class="mdi @if($val['popular'] === 2) mdi-check @else mdi-close @endif"></i><br/>
                                <td>{{ $val['rank'] }}</td>
{{--                                <td>{{ $statusMap[$val['status']] }}</td>--}}
                                <td>{{ $val['update_time'] }}</td>
                                <td>
                                    <a class="btn btn-outline-danger waves-light waves-effect w-sm btn-sm" href="javascript:void(0)" onclick="deleteRecord('{{ $val['id'] }}')">Delete</a>
                                    <a class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm" href="{{ \Illuminate\Support\Facades\URL::to("posts-term/" . $val['id']. "/edit") }}">Edit</a>
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
    <script>
        const data = @json($data)['data'];
        function deleteRecord(id) {
            let record = getRecordById(id);
            $.confirm({
                title: '确认删除!',
                content: '是否确定要删除这条记录?<br/>'
                    + '{<br/>&emsp;&emsp;"id": ' + record['id']
                    + ',<br/>&emsp;&emsp;"类型": ' + (parseInt(record['type']) === 1 ? "分类" : "标签")
                    + ',<br/>&emsp;&emsp;"名称": ' + record['name']
                    + ',<br/>&emsp;&emsp;"别名": ' + record['slug']
                    + ',<br/>&emsp;&emsp;"文章数": ' + record['posts_count']
                    + '<br/>}',
                type: 'red',
                icon: 'glyphicon glyphicon-question-sign',
                buttons: {
                    ok: {
                        text: '确定',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '/posts-term/' + id,
                                type: 'DELETE',
                                success: function() {
                                    location.replace(location.href);
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        btnClass: 'btn-success'
                    }
                }
            });
        }
        function getRecordById(id) {
            let record;
            for (let i = 0; i < data.length; i++) {
                if (data[i]['id'] === parseInt(id)) {
                    record = data[i];
                }
            }
            return record;
        }
    </script>
@endsection