@extends('layouts.admin')
@section('content')
    <style>
        .email-input-error {
            color: red;
            font-size: small;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Subscribers</li>
                    </ol>
                </div>
                <h4 class="page-title">Subscribers</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Search-Bar -->
            <div class="card-box">
                <form method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input id="email-input" type="text" name="email" value="{{ $email }}" class="form-control" placeholder="Email" onblur="onEmailEntered()">
                            <span class="email-input-error" hidden>Please input a correct email addr</span>
                        </div>
                        <div class="form-group col-md-2">
                            <select id="status-input" name="status" class="form-control" @if($email) disabled @endif>
                                <option value="" >Both</option>
                                @foreach ($statusMap as $key => $val)
                                    <option value="{{ $key }}" @if ($status == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select id="type-input" name="type" class="form-control" @if($email) disabled @endif>
                                <option value="" >All Type</option>
                                @foreach ($typeMap as $key => $val)
                                    <option value="{{ $key }}" @if ($type == $key) selected="selected" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button id="submit-btn" type="submit" class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Table -->
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Subscribe</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $val)
                            <tr>
                                <td>{{ $val['id'] }}</td>
                                <td>{{ $val['email'] }}</td>
                                <td>@if($val['user_name']) {{ $val['user_name'] }} [Publisher] @elseif($val['admin_name']) {{ $val['admin_name']}} [Admin] @else - @endif</td>
                                <td>{{ $statusMap[$val['unsubscribe']] }}</td>
                                <td>{{ $typeMap[$val['subscribe_type']] }}</td>
                                <td>{{ $val['create_time'] }}</td>
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
                            'email'  => $email,
                            'status' => $status,
                            'type'   => $type,
                        ])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const emailRegExp = /^[\w.-]+@([1-9]|[a-z]|[A-Z])+(\.[A-Za-z]{2,4}){1,2}$/;
        function onEmailEntered() {
            let email    = $('#email-input').val();
            let submit   = $('#submit-btn');
            let emailErr = $('.email-input-error');
            if (email !== '' && !emailRegExp.test(email)) {
                emailErr.attr('hidden', false);
                submit.attr('disabled', true);
                return;
            } else if (email === '') {
                $('#status-input').attr('disabled', false);
                $('#type-input').attr('disabled', false);
            } else {
                $('#status-input').attr('disabled', true);
                $('#type-input').attr('disabled', true);
            }
            emailErr.attr('hidden', true);
            submit.attr('disabled', false);
        }
    </script>
@endsection