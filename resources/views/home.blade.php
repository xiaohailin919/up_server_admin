@extends('layouts.admin')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="#">TopOn</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Welcome !</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box tilebox-one">
                <i class="fi-box float-right"></i>
                <h6 class="text-muted text-uppercase mb-3">Orders</h6>
                <h4 class="mb-3" data-plugin="counterup">0</h4>
                <span class="badge badge-primary"> +0% </span> <span class="text-muted ml-2 vertical-middle">From previous period</span>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box tilebox-one">
                <i class="fi-layers float-right"></i>
                <h6 class="text-muted text-uppercase mb-3">Revenue</h6>
                <h4 class="mb-3">$<span data-plugin="counterup">0</span></h4>
                <span class="badge badge-primary"> -0% </span> <span class="text-muted ml-2 vertical-middle">From previous period</span>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box tilebox-one">
                <i class="fi-tag float-right"></i>
                <h6 class="text-muted text-uppercase mb-3">Average Price</h6>
                <h4 class="mb-3">$<span data-plugin="counterup">0.00</span></h4>
                <span class="badge badge-primary"> 0% </span> <span class="text-muted ml-2 vertical-middle">From previous period</span>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box tilebox-one">
                <i class="fi-briefcase float-right"></i>
                <h6 class="text-muted text-uppercase mb-3">Product Sold</h6>
                <h4 class="mb-3" data-plugin="counterup">0</h4>
                <span class="badge badge-primary"> +0% </span> <span class="text-muted ml-2 vertical-middle">Last year</span>
            </div>
        </div>
    </div>

@endsection
