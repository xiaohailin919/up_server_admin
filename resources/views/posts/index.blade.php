@extends('layouts.admin')
@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">News & Events</li>
                    </ol>
                </div>
                <h4 class="page-title">News & Events</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input class="form-control" name="post_time" type="date" value="{{ $pageAppends['post_time'] }}" placeholder="Date">
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" name="title" type="text" value="{{ $pageAppends['title'] }}" placeholder="Title">
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="type">
                                <option value="">All Type</option>
                                @foreach ($typeMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === (int)$pageAppends['type']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="language">
                                <option value="">All Language</option>
                                @foreach ($languageMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === $pageAppends['language']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="popular">
                                <option value="">All Popular</option>
                                <option value="2" @if('2' === $pageAppends['popular']) selected="selected" @endif>Popular</option>
                                <option value="1" @if('1' === $pageAppends['popular']) selected="selected" @endif>Popular Not</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="status">
                                <option value="">All Status</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === (int)$pageAppends['status']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select class="form-control" name="email_status">
                                <option value="">All Mail Status</option>
                                @foreach ($emailStatusMap as $key => $val)
                                    <option value="{{ $key }}" @if ($key === (int)$pageAppends['email_status']) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button class="btn btn-primary" type="submit">Search</button>
                            <a class="btn btn-success" href="{{ \Illuminate\Support\Facades\URL::to('posts/create') }}">New</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Priority</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Language</th>
                            <th>Title</th>
                            <th style="text-align: center">Popular</th>
                            <th>Mail</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $val)
                            <tr>
                                <td>{{ $val['rank'] }}</td>
                                <td>
                                    {{ ($val['status'] == 2) ? '-' : $val['date'] }}
                                </td>
                                <td>{{ $val['type_name'] }}</td>
                                <td>{{ $val['language_name'] }}</td>
                                <td>
                                    <span title="{{ $val['title'] }}">{{ str_limit($val['title'],  50, '...') }}</span>
                                </td>
                                <td style="text-align: center"><i class="mdi @if($val['type'] === 3) mdi-minus @elseif($val['popular' === 2]) mdi-check @else mdi-close @endif"></i><br/>
                                <td>{{ $val['send_email_success'] }}/{{ $val['send_email_subscriber'] }}</td>
                                <td>
                                    {{ $val['admin_name'] }}<br />
                                    {{ $val['update_time'] }}
                                </td>
                                <td>{{ $val['status_name'] }}</td>
                                <td>
                                    @if ($val['type'] === 3)
                                        <button class="btn btn-outline-secondary waves-light waves-effect w-sm btn-sm" disabled>Send Email</button>
                                    @else
                                        @if($val['send_email'] == 1)
                                            <button class="btn btn-outline-success waves-light waves-effect w-sm btn-sm" href="javascript:void(0);" onclick="publicEmail({{ $val['id'] }});">Send Email</button>
                                        @elseif($val['send_email_success'] > 0)
                                            <button class="btn btn-outline-secondary w-sm btn-sm" disabled>Email Sent</button>
                                        @else
                                            <button class="btn btn-outline-secondary w-sm btn-sm" disabled>Sending</button>
                                        @endif
                                    @endif
                                    <a class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm" href="{{ \Illuminate\Support\Facades\URL::to("posts/" . $val['id'] . "/edit") }}">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-5">Total <strong>{{ $data->total() }}</strong></div>
                    <div class="col-sm-12 col-md-7">{{ $data->appends($pageAppends)->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function publicEmail(postId) {
            $.confirm({
                title: 'Public Email Confirm',
                content: 'Are you sure want to mass out this post?',
                type: 'green',
                icon: 'glyphicon glyphicon-question-sign',
                buttons: {
                    ok: {
                        text: 'confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.get("/post/send-email", { post_id: postId});
                            location.replace(location.href);
                        }
                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-primary'
                    }
                }
            });
        }
    </script>
@endsection