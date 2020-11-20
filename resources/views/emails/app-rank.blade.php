{{-- 必须使用 Align 属性，否则无法在 outlook 客户端上居中 --}}
<p style="margin: 10px 0 0 0;padding: 0;color: #172B4D;font-size: 14px;font-weight: 400;letter-spacing: 0">Hi all!</p>
<p style="margin: 10px 0 0 0;padding: 0;color: #172B4D;font-size: 14px;font-weight: 400;letter-spacing: 0">本周({{ date('Y-m-d') }})应用排名如下：</p>
@foreach($appTypeList as $appType)
    <h3 style="font-weight: bold;font-size: 16px;line-height: 1.5;margin: 20px 0 14px 0;letter-spacing: 0;text-transform: none;padding: 0">
        {{ $appType['title'] }}
    </h3>
    <div>
        <table style="background: white;border-collapse: collapse;">
            <thead style="font-size: 14px;font-weight: bold;text-align: left;color:#172B4D;background:#F4F5F7;">
            <tr>
                <td valign="top" style="border:solid #C1C7D0 1.0pt;padding:5.25pt 11.25pt 5.25pt 7.5pt;white-space: nowrap">
                    <div><p align="left"><span>序号</span></p></div>
                </td>
                <td valign="top" style="border:solid #C1C7D0 1.0pt;padding:5.25pt 11.25pt 5.25pt 7.5pt;white-space: nowrap">
                    <div><p align="left"><span>平台</span></p></div>
                </td>
                <td valign="top" style="border:solid #C1C7D0 1.0pt;padding:5.25pt 11.25pt 5.25pt 7.5pt;white-space: nowrap">
                    <div><p align="left"><span>应用名称</span></p></div>
                </td>
                <td valign="top" style="border:solid #C1C7D0 1.0pt;padding:5.25pt 11.25pt 5.25pt 7.5pt;white-space: nowrap">
                    <div><p align="left"><span>公司名称</span></p></div>
                </td>
                <td valign="top" style="border:solid #C1C7D0 1.0pt;padding:5.25pt 11.25pt 5.25pt 7.5pt;white-space: nowrap">
                    <div><p align="left"><span>应用分类</span></p></div>
                </td>
                <td valign="top" style="border:solid #C1C7D0 1.0pt;padding:5.25pt 11.25pt 5.25pt 7.5pt;white-space: nowrap">
                    <div><p align="left"><span>应用标签</span></p></div>
                </td>
                <td valign="top" style="border:solid #C1C7D0 1.0pt;padding:5.25pt 11.25pt 5.25pt 7.5pt;white-space: nowrap">
                    <div><p align="left"><span>创意组数量</span></p></div>
                </td>
                <td valign="top" style="border:solid #C1C7D0 1.0pt;padding:5.25pt 11.25pt 5.25pt 7.5pt;white-space: nowrap">
                    <div><p align="left"><span>下载地址</span></p></div>
                </td>
            </tr>
            </thead>
            <tbody style="font-size: 14px;text-align: left;color:#172B4D;">
            @foreach($appType['list'] as $idx => $item)
                <tr>
                    <td valign="top" style="border:solid #C1C7D0 1.0pt;border-top:none;padding:5.25pt 7.5pt 5.25pt 7.5pt">
                        <p style="margin: 0"><span style="font-family: monospace">{{ $idx + 1 }}</span></p>
                    </td>
                    <td valign="top" style="border:solid #C1C7D0 1.0pt;border-top:none;padding:5.25pt 7.5pt 5.25pt 7.5pt">
                        <p style="margin: 0"><span style="font-family: monospace">{{ $appType['platform'] }}</span></p>
                    </td>
                    <td valign="top" style="border:solid #C1C7D0 1.0pt;border-top:none;padding:5.25pt 7.5pt 5.25pt 7.5pt">
                        <p style="margin: 0"><span>{{ $item['name'] }}</span></p>
                    </td>
                    <td valign="top" style="border:solid #C1C7D0 1.0pt;border-top:none;padding:5.25pt 7.5pt 5.25pt 7.5pt">
                        <p style="margin: 0" style="overflow-wrap: anywhere;"><span>{{ $item['company'] }}</span></p>
                    </td>
                    <td valign="top" style="border:solid #C1C7D0 1.0pt;border-top:none;padding:5.25pt 7.5pt 5.25pt 7.5pt">
                        <p style="margin: 0"><span>{{ $item['category'] }}</span></p>
                    </td>
                    <td valign="top" style="border:solid #C1C7D0 1.0pt;border-top:none;padding:5.25pt 7.5pt 5.25pt 7.5pt">
                        <p style="margin: 0"><span>{{ $item['tag'] }}</span></p>
                    </td>
                    <td valign="top" style="border:solid #C1C7D0 1.0pt;border-top:none;padding:5.25pt 7.5pt 5.25pt 7.5pt">
                        <p style="margin: 0"><span style="font-family: monospace">{{ $item['mate_count'] }}</span></p>
                    </td>
                    <td valign="top" style="border:solid #C1C7D0 1.0pt;border-top:none;padding:5.25pt 7.5pt 5.25pt 7.5pt">
                        <p style="margin: 0;overflow-wrap: anywhere;">
                            <a style="color: #0052CC;font-family: monospace" href="{{ $item['download_url'] }}" target="_blank">
                                {{ $item['download_url'] }}
                            </a>
                        </p>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endforeach