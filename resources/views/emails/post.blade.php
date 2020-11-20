<style type="text/css">
    .email {
        font-family: "Microsoft YaHei", sans-serif;
        width: 75%;
        min-width: 400px;
        max-width: 800px;
        margin: 0 auto;
    }

    .title {
        color: #000000;
        text-decoration: none;
    }

    h4 {
        text-align: right;
    }

    .more {
        text-align: right;
    }

    .more a {
        color: #fff;
        text-decoration: none;
        background-color: #5097E9;
    }

    .more:hover {
        text-decoration: #fff;
    }

    .hint {
        text-align: center;
        color: #7f8994;
        font-size: 0.8rem;
        margin: 2px 0;
    }

    .hint a {
        color: #7f8994;
        text-decoration: none;
    }

    .foot {
        margin: 16px 0;
    }

    .foot .hint {
        text-align: left;
    }

</style>
<div class="email">
    <p class="hint"><a href="https://www.toponad.com"><img src="https://www.toponad.com/image/logo-header.png" alt="TopOn logo"></a></p>
    <div class="content">
        <a class="title" href="https://www.toponad.com/posts/{{ $event['id'] }}.html"><h2>{{ $event['title'] }}</h2></a>
        <h4>{{ $event['description'] }}</h4>
        @if($event['thumbnail'] != '')
            <p class="hint"><a class="thumbnail"><img src="http://img.toponad.com/{{ $event['thumbnail'] }}" alt="thumbnail" style="max-width: 75%" width="384px"></a></p>
        @endif
        <div>{!! $event['content'] !!}</div>
        <div class="more"><a href="https://www.toponad.com/posts/{{ $event['id'] }}.html">了解更多</a></div>
    </div>
    <hr/>
    <div class="foot">
        <p class="hint">商务合作：<a href="mailto:business@toponad.com">business@toponad.com</a></p>
        <p class="hint">市场合作：<a href="mailto:leon@toponad.com">leon@toponad.com</a></p>
        <p class="hint">技术支持：<a href="mailto:support@toponad.com">support@toponad.com</a></p>
        <p class="hint">ＱＱ　　：188108875(Harry)</p>
        <p class="hint">微信　　：188108875(Harry)</p>
        <p class="hint"><a href="https://www.toponad.com/unsubscribe?email={{ $email }}&pid={{ $event['id'] }}&signature={{ $signature }}" style="text-decoration: #7f8994">取消订阅</a></p>
    </div>
    <p class="hint"><a><img src="https://www.toponad.com/image/TopOn-foot.png" alt="TopOn foot" style="max-width: 100%"></a></p>
</div>