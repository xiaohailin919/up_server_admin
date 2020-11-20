{{-- 必须使用 Align 属性，否则无法在 outlook 客户端上居中 --}}
<table style="max-width: 600px;" align="center">
    <tbody>
    <tr>
        <td style="background: #004FDF;height: 58px;padding-left: 32px">
            <a href="https://www.toponad.com" target="_blank">
                <img class="logo" src="https://www.toponad.com/image/index/logo.png?t=201907221546" alt="logo">
            </a>
        </td>
    </tr>
    @if ($posts['type'] == \App\Models\MySql\Posts::TYPE_NEWS || $posts['type'] == \App\Models\MySql\Posts::TYPE_REPORT)
        <tr>
            <td style="padding: 26px 0" align="center">
                <a href="{{ $posts['bp_redirect_url'] }}" target="_blank">
                    <img style="max-width: 600px" src="{{ $posts['thumbnail'] }}" alt="thumbnail">
                </a>
            </td>
        </tr>
    @endif
    <tr>
        <td style="min-width:512px;font-size: 12px;font-weight: 400;line-height: 22px">
            {!! $posts['content'] !!}
        </td>
    </tr>
    <tr>
        <td style="padding: 42px 0" align="center">
            <table>
                <tr>
                    <td style="background:#004FDF;border-radius: 22px;padding: 11px 40px;">
                        <a style="color: #ffffff;text-decoration: none;font-size: 14px;font-weight: bold" href="{{ $posts['bp_redirect_url'] }}">{{ $posts['btn_text'] }}</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="background:#F6F6F6;height: 19px"></td>
    </tr>
    <tr>
        <td>
            <p style="margin: 12px 0;text-align: center;color: #666666;font-weight: 400;font-size: 12px;line-height: 17px">{{ $posts['language'] == \App\Models\MySql\Posts::LANGUAGE_CHINESE ? '关注我们' : 'Follow Us' }}</p>
        </td>
    </tr>
    <tr>
        <td align="center">
            <table>
                <tbody>
                <tr>
                    <td style="padding: 0 6px">
                        <a style="text-decoration: none" href="https://www.toponad.com/image/topon_wechat_qrcode.jpg">
                            <img style="display: inline-block;width: 32px;height: 32px;" src="{{ 'http://' . env('TOPON_HOST') . '/images/wechat.png' }}" alt="wechat">
                        </a>
                    </td>
                    <td style="padding: 0 6px">
                        <a style="text-decoration: none" href="https://www.facebook.com/TopOn-2202518406639775/?ref=bookmarks">
                            <img style="display: inline-block;width: 32px;height: 32px;" src="{{ 'http://' . env('TOPON_HOST') . '/images/facebook.png' }}" alt="facebook">
                        </a>
                    </td>
                    <td style="padding: 0 6px">
                        <a style="text-decoration: none" href="https://twitter.com/TopOn_Global">
                            <img style="display: inline-block;width: 32px;height: 32px;" src="{{ 'http://' . env('TOPON_HOST') . '/images/twitter.png' }}" alt="twitter">
                        </a>
                    </td>
                    <td style="padding: 0 6px">
                        <a style="text-decoration: none" href="https://www.linkedin.com/company/14823889/admin/">
                            <img style="display: inline-block;width: 32px;height: 32px;" src="{{ 'http://' . env('TOPON_HOST') . '/images/linkedin.png' }}" alt="linkedin">
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <p style="text-align: center;margin-top: 32px;font-size: 12px;font-weight: 400;color: #AEAEAE;line-height: 17px">
                {{ $posts['language'] == \App\Models\MySql\Posts::LANGUAGE_CHINESE ? '如果您不想继续收到此类邮件，请点击这里' : "If you don't want to receive this email again, please click here" }}&nbsp;
                <a style="color: #A3D8F9" href="{{ $posts['bp_unsubscribe_url'] }}">{{ $posts['language'] == \App\Models\MySql\Posts::LANGUAGE_CHINESE ? '退订' : 'Unsubscribe' }}</a>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <img style="display: block;width: 1px;height: 1px;" src="{{ $posts['bp_open_url'] }}">
        </td>
    </tr>
    </tbody>
</table>