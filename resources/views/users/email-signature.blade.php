@extends('layouts.admin')
@section('content')
    <!-- Page-Title -->
    <style>
        code {
            padding: 0;
            font-size: 12px;
            color: white;
            background: none;
            border: 0;
            border-radius: 0;
        }
        .card-box {
            min-width: 1280px;
        }
        .email-container {
            border: 1px solid #ccc;
            margin: 2rem auto;
            width: 1200px;
            display: flex;
        }

        .email-left {
            width: 400px;
            border-right: 1px solid #ccc;
            height: 100%;
        }

        .email-left p {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .email-left form div {
            margin: 10px auto;
        }

        .email-container_form label {
            width: 60px;
            margin-left: 20px;
            display: inline-block;
        }

        .email-container_form input {
            border: 1px solid #ccc;
            margin-left: 20px;
            width: 250px;
        }

        .email-container_form .email-left_sumbit {
            margin: 10px auto;
        }

        .email-right {
            width: 800px;
        }

        .email-right_xiaoguo {
            text-align: center;
            margin: 20px auto 20px;
        }

        .email-right_code {
            margin: 1rem 0;
        }

        .email-right_notice {
            margin-top: 3rem;
            margin-bottom: 0;
            text-align: center;
        }

        .email-right_copy {
            margin: 1rem auto;
        }

        #copy-board {
            margin: 0 auto;
            resize: none;
        }



        ._a {
            margin: 0 auto;
            width: 687px;
            height: 248px;
            background: url("https://www.toponad.com/image/index/bc-bg.png") no-repeat center center;
            position: relative;
            background-size: cover;
            box-shadow: 0 1px 3px rgba(26,26,26,.1);
        }
        ._a, ._a code {
            font-family: "PingFangSC-Regular", '微软雅黑', "Helvetica Neue", Arial, 'Raleway', sans-serif!important;
        }
        ._b {
            margin-left: 43px;
            padding-top: 41px;
        }
        ._c {
            font-size: 24px;
            font-weight: 400;
            color: rgba(51, 51, 51, 1);
            margin-right: 12.5px;
        }
        ._d {
            font-size: 21px;
            font-weight: 400;
            color: rgba(51, 51, 51, 1);
        }
        ._e {
            margin: 9px 0 0 43px;
            font-size: 13.5px;
        }
        ._f {
            padding-right: 4px;
            border-right: 0.5px solid #CCC;
        }
        ._g {
            padding-left: 4px;
        }
        ._h, ._i, ._n {
            position: absolute
        }
        ._h {
            top: 14px;
            left: 529px;
            width: 114px;
            height: 114px;
            object-fit: cover;
            background: #d4d4d4;
            border-radius: 50%
        }
        ._i {
            color: #ffffff;
            top: 144px;
            line-height: 16px;
            width: 100%;
            height: 104px;
        }
        ._i code {
            display: inline-block;
        }
        ._j, ._l {
            width: 200px;
            margin-left: 71px;
        }
        ._k, ._m {
            width: 400px;
        }
        ._j, ._k {
            margin-top: 30px;
        }
        ._l, ._m {
            margin-top: 12px;
        }
        ._n {
            width: 127px;
            height: 54px;
            left: 522px;
            top: 167px
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TopOn</a></li>
                        <li class="breadcrumb-item active">Email Signature</li>
                    </ol>
                </div>
                <h4 class="page-title">Email Signature</h4>
            </div>
        </div>
    </div>

    <div class="card-box">
        <div class="email-container">
            <div class="email-left">
                <p>1.填写个人信息</p>
                <form class="email-container_form">
                    <div class="form-group form-row">
                        <label for="c-name" >中文名</label>
                        <input class="form-control" type="text" name="name" maxlength="50" value="{{ $data['name'] }}" required>
                    </div>
                    <div class="form-group form-row">
                        <label for="e-name">英文名</label>
                        <input class="form-control" type="text" name="name_en" maxlength="50" value="{{ $data['name_en'] }}" required>
                    </div>
                    <div class="form-group form-row">
                        <label for="c-office">中文职位</label>
                        <input class="form-control" type="text" name="title" maxlength="50" value="{{ $data['title'] }}" required>
                    </div>
                    <div class="form-group form-row">
                        <label for="e-office">英文职位</label>
                        <input class="form-control" type="text" name="title_en" maxlength="60" value="{{ $data['title_en'] }}" required>
                    </div>
                    <div class="form-group form-row">
                        <label for="tel">手机号码</label>
                        <input class="form-control" type="text" name="phone" value="{{ $data['phone'] }}" required>
                    </div>
                    <div class="form-group form-row">
                        <label for="email">邮箱地址</label>
                        <input class="form-control" type="email" name="email" value="{{ $data['email'] }}" required>
                    </div>
                    <div class="form-group form-row">
                        <label for="weixin">微信号</label>
                        <input class="form-control" type="text" name="wechat" value="{{ $data['wechat'] }}">
                    </div>
                    <div class="form-group form-row">
                        <label for="Skype">Skype</label>
                        <input class="form-control" type="text" name="skype" value="{{ $data['skype'] }}">
                    </div>
                    <div class="form-group form-row">
                        <label for="picture">个人头像</label>
                        <input class="form-control" type="file" name="avatar">
                    </div>
                    <div class="form-row">
                        <button type="submit" class="email-left_sumbit btn btn btn-info">生成签名</button>
                    </div>
                </form>
            </div>

            <div class="email-right">
                <p class="email-right_xiaoguo">2.预览效果</p>
                <div class="email-right_code">
                    <div class="_a">
                        <div class="_b"><span class="_c">{{ $data['name'] ?: '中文名' }}</span><span class="_d">{{ $data['name_en'] ?: '英文名' }}</span></div>
                        <div class="_e"><span class="_f">{{ $data['title'] ?: '中文职位' }}</span><span class="_g">{{ $data['title_en'] ?: '英文职位' }}</span></div>
                        <img class="_h" src="{{ $data['avatar_url'] ?: 'https://www.toponad.com/image/index/logo.png' }}" alt="avatar"/>
                        <div class="_i">
                            <code class="_j">{{ $data['phone'] ?: '手机号码' }}</code><code class="_k">{{ $data['email'] ?: '邮箱地址' }}</code>
                            <code class="_l">{{ $data['wechat'] ?: '微信号' }}</code><code class="_m">{{ $data['skype'] ?: 'Skype' }}</code>
                        </div>
                        <a class="_n" href="https://www.toponad.com" target="_blank"></a>
                    </div>
                </div>
                <p class="email-right_notice">注：确认无误后，请复制HTML代码到企业邮箱配置个人签名</p>
                <div class="form-row">
                    <button class="email-right_copy btn btn-info btn">一键复制</button>
                </div>
                <div class="form-row">
                    <textarea id="copy-board" rows="4" cols="80" hidden readonly></textarea>
                </div>
            </div>
        </div>
    </div>
    <script>
        const copyBoard = document.getElementById('copy-board');

        $('form').submit(function() {
            /* 处理文字预览 */
            $('._c').text($('input[name="name"]'    ).val());
            $('._d').text($('input[name="name_en"]' ).val());
            $('._f').text($('input[name="title"]'   ).val());
            $('._g').text($('input[name="title_en"]').val());
            $('._j').text($('input[name="phone"]'   ).val());
            $('._k').text($('input[name="email"]'   ).val());
            $('._l').text($('input[name="wechat"]'  ).val());
            $('._m').text($('input[name="skype"]'   ).val());

            /* 提交表单获取图片 URL，
               注：使用 ajax 提交带文件数据时，无法使用 serializeArray() 方法，必须使用 FormData 对线才能传递二进制
                  且必须将 async 设置为 false */
            let formData = new FormData(this);
            $.ajax({
                url:  '/email-signature',
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    $('._h').attr('src', response);
                    updateCode();
                },
            })
            return false;
        });

        document.getElementsByClassName('email-right_copy')[0].addEventListener('click', copyCode);

        function copyCode() {
            updateCode();
            $(copyBoard).attr('hidden', false);
            copyBoard.select();
            document.execCommand("Copy");
            $.alert({
                title: '复制成功',
                content: '复制成功，可直接粘贴😎<br/><br/>共' + $(copyBoard).val().length +  '字符<br/><br/>如果粘贴时发现长度变短了😮，应该是超出了编辑框限制的字符长度'
            })
        }

        function updateCode() {
            let style='<style>._a{margin:0 auto 0 0;width:687px;height:248px;background:url("https://www.toponad.com/image/index/bc-bg.png") no-repeat center center;position:relative;background-size:cover;box-shadow:0 1px 3px rgba(26,26,26,.1)}._a,._a code{font-family:"PingFangSC-Regular",\'微软雅黑\',"Helvetica Neue",Arial,\'Raleway\',sans-serif!important}._b{margin-left:43px;padding-top:41px}._c{font-size:24px;font-weight:400;color:rgba(51,51,51,1);margin-right:12.5px}._d{font-size:21px;font-weight:400;color:rgba(51,51,51,1)}._e{margin:9px 0 0 43px;font-size:13.5px}._f{padding-right:4px;border-right:.5px solid #CCC}._g{padding-left:4px}._h,._i,._n{position:absolute}._h{top:14px;left:529px;width:114px;height:114px;object-fit:cover;background:#d4d4d4;border-radius:50%}._i{color:#fff;top:144px;line-height:16px;width:100%;height:104px}._i code{display:inline-block}._j,._l{width:200px;margin-left:71px}._k,._m{width:400px}._j,._k{margin-top:30px}._l,._m{margin-top:12px}._n{width:127px;height:54px;left:522px;top:167px}</style>'
            let html = $('.email-right_code').html();
            html = html.replace(/\r\n/g, "").replace(/\n/g, "").replace(/ {4}/g, "");
            $(copyBoard).val(style + html);
        }
    </script>
@endsection