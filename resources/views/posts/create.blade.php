@extends('layouts.admin')

@section('content')
    <style>
        .required-field span {
            color: red;
        }
    </style>
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">TopOn</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ \Illuminate\Support\Facades\URL::to('posts') }}">News & Events</a>
                        </li>
                        <li class="breadcrumb-item active">Create News & Events</li>
                    </ol>
                </div>
                <h4 class="page-title">Create News & Events</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form method="post" action="{{ \Illuminate\Support\Facades\URL::to('/posts') }}"
                      enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="required-field">Title <span>*</span></label>
                            <input class="form-control" type="text" name="title" maxlength="200" required/>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="required-field">Type <span>*</span></label>
                            <select class="form-control" name="type" required>
                                @foreach ($typeMap as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="required-field">Language <span>*</span></label>
                            <select class="form-control" name="language" required >
                                @foreach ($languageMap as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row news-extra">
                        <div class="form-group col-md-6">
                            <label class="required-field">Source <span>*</span></label>
                            <input class="form-control" type="text" name="source" maxlength="100"/>
                        </div>
                    </div>
                    <div class="form-row event-extra">
                        <div class="form-group col-md-6">
                            <label class="required-field">Event Time <span>*</span></label>
                            <input class="form-control" type="text" name="event_time" value="{{ date('Y-m-d') }}"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="required-field">Event Location <span>*</span></label>
                            <input class="form-control" type="text" name="event_location" maxlength="200"/>
                        </div>
                    </div>
                    <div class="form-row news-extra report-extra">
                        <div class="form-group col-md-12">
                            <label class="required-field">Content <span>*</span></label>
                            <textarea class="form-control" id="editor" name="content"></textarea>
                        </div>
                    </div>
                    <div class="form-row news-extra report-extra">
                        <div class="form-group col-md-12">
                            <label class="news-extra">Description</label>
                            <label class="report-extra">通知内容</label>
                            <textarea class="form-control" name="description" maxlength="500"></textarea>
                            <small class="report-extra">首页横幅通知的内容，请不超过50字</small>
                        </div>
                    </div>
                    <div class="form-row news-extra">
                        <div class="form-group col-md-12">
                            <label>Keywords</label>
                            <textarea class="form-control" name="keyword" maxlength="500" style="min-height: 50px;"></textarea>
                            <small>关键词个数建议为15个左右，多个关键词使用英文逗号隔开</small>
                        </div>
                    </div>
                    <div class="form-row news-extra">
                        <div class="form-group col-md-6">
                            <label>Categories</label>
                            <select class="form-control select2 select2-multiple select2-hidden-accessible" name="categories[]" multiple="" data-placeholder="-Categories-">
                                @foreach ($categories as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['name'] . ' | ' . $category['slug'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tags</label>
                            <select class="form-control select2 select2-multiple select2-hidden-accessible" name="tags[]" multiple="" data-placeholder="-Tags-">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag['id'] }}">{{ $tag['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Image</label>
                            <input class="form-control" type="file" name="thumbnail"/>
                            <small>News 缩略图（240x136），Events 缩略图（362x162），Report 图片（531x314）</small>
                        </div>
                        <div class="form-group col-md-6 news-extra event-extra">
                            <label class="required-field">Priority <span>*</span></label>
                            <select class="form-control" name="rank">
                                @for ($i = 0; $i <= 9; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group col-md-6 report-extra">
                            <label>Redirect URL</label>
                            <input class="form-control" type="text" name="redirect_url"/>
                            <small>报告点击跳转的下载地址</small>
                        </div>
                    </div>
                    <div class="form-row news-extra event-extra">
                        <div class="form-group col-md-6">
                            <label>Views</label>
                            <input class="form-control" name="views" type="number" value="0" placeholder="views"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Popular</label>
                            <div class="mt-3">
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="popular" type="radio" value="1" checked>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">No</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" name="popular" type="radio" value="2">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Yes</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function () {
            CKEDITOR.replace('editor', { height: 500 });
            $("select[name='type']").change(function(){
                let type = $("select[name='type']").val();
                let news = $(".news-extra");
                let event = $(".event-extra");
                let report = $(".report-extra");
                switch (parseInt(type)) {
                    case {{ \App\Models\MySql\Posts::TYPE_NEWS }}:
                        $(event).hide();
                        $(report).hide();
                        $(news).show();
                        break;
                    case {{ \App\Models\MySql\Posts::TYPE_EVENT }}:
                        $(news).hide();
                        $(report).hide();
                        $(event).show();
                        break;
                    case {{ \App\Models\MySql\Posts::TYPE_REPORT }}:
                        $(news).hide();
                        $(event).hide();
                        $(report).show();
                        break;
                    default:
                        break;
                }
            }).change();
        });
    </script>

@endsection
