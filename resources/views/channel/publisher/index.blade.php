@extends('layouts.channel')
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

                        <div class="form-group col-md-4">
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
                            <th>Email</th>
                            <th>Status</th>
                            <th>Channel Note</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($publisher as $val)
                        <tr>
                            <th scope="row">{{ $val['id'] }}</th>
                            <td>{{ $val['name'] }}</td>
                            <td> {{ $val['email'] }}</td>
                            <td>{{ $statusMap[$val['status']] }}</td>
                            <td style="white-space:nowrap;" title="{{ $val['note_channel_title'] }}">
                                {{ $val['note_channel'] }}
                            </td>
                            <td>
                                <div class="button-list">
                                    <a href="{{ URL::to("publisher/{$val['id']}/edit") }}" class="btn btn-outline-warning waves-light waves-effect w-sm btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
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
                            'system' => $system,
                            'publisher_type' => $publisherType,
                            'email' => $email,
                        ])->links() }}
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection