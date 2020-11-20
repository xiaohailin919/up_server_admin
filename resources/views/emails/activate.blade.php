<div style='font-family: "Microsoft YaHei", sans-serif'>
    @if ($lang === 'zh-cn')
        <p>尊敬的{{ $name }}：</p>
        <p>您好！请登录 TopOn 开发者后台完成最后的注册步骤，登录地址：<a href='https://{{ $domain }}/#/login'>https://{{ $domain }}/#/login</a></p>
        <p>如果您需要任何协助，请通过 <a href="mailto:support@toponad.com">support@toponad.com</a> 与我们联系！</p>
        <br/>
        <p>TopOn 团队</p>
    @else
        {{-- 默认语言为 en，方便以后添加其他语言，如日文等 --}}
        <p>Dear {{ $name }},</p>
        <p>To activate your account，please click the below link to confirm your email.</p>
        <p>url：<a href='https://{{ $domain }}/#/login'>https://{{ $domain }}/#/login</a></p>
        <p>If you need any help, please contact <a href="mailto:support@toponad.com">support@toponad.com</a>.</p>
        <br/>
        <p>Thank you,</p>
        <p>The TopOn Team</p>
    @endif
</div>
