<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2018/11/27
 * Time: 18:04
 */

namespace App\Services;

use App\Models\MySql\App as AppMyModel;
use App\Models\MySql\MGroupRelationship as MGroupRelationshipMyModel;
use App\Models\MySql\Placement as PlacementMyModel;
use App\Models\MySql\Posts;
use App\Models\MySql\Publisher as PublisherMyModel;
use App\Models\MySql\Subscriber;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Mail
{
    const TIMEOUT = 180;

    /**
     * 根据其他参数和指定的 UUID 生成签名参数
     * rule: sha1(uuid + time + recipients + copy_recipients + subject)
     *
     * @param array $data
     * @param string $uuid
     * @return string
     */
    private static function generateSignature(array $data, string $uuid) : string
    {
        $tmpStr = $uuid . $data['time'];
        foreach ($data['recipients'] as $k => $v) {
            $tmpStr .= $v;
        }
        $tmpStr .= $data['subject'];
        return sha1($tmpStr);
    }

    /**
     * 向 publisher 发送激活通知邮件
     *
     * @param $publisher mixed 新用户（数组）
     */
    public static function toPublisherEmail($publisher)
    {
        $client = new Client(['headers' => ['Content-Type' => 'application/json']]);

        if ($publisher['system'] !== 1) {
            Log::error('system param is error');
            return;
        }

        /* 邮件参数 */
        $data = [];
        $data['time']         = time();
        $data['sender']       = env('MAIL_SERVICE_UUID1');
        $data['recipients'][] = $publisher['email'];
        $data['subject']      = $publisher['language'] === 'zh-cn' ? '欢迎使用TopOn广告聚合平台！' : 'Welcome to TopOn!';

        $domain = (new PublisherMyModel())->getChannelDomain($publisher['channel_id']);
        $view   = view('emails.activate')
            ->with(['lang' => $publisher['language'], 'name' => $publisher['name'], 'domain' => $domain]);

        $data['content'] = response($view)->getContent();
        $data['sign']    = self::generateSignature($data, env('MAIL_SERVICE_UUID1'));

        /* 发送邮件 */
        $response = $client->post(
            env('MAIL_SERVICE_URL'),
            [
                'body'    => json_encode($data),
                'timeout' => self::TIMEOUT,
            ]
        );

        /* 结果处理 */
        $res = json_decode($response->getBody()->getContents(), true);
        Log::info('Send activation notification email: TO: ' . $data['recipients'][0] . ', RESULT: [code:' . $res['code'] . ',msg:' . $res['msg']. ']');
        if (isset($res['code']) && $res['code'] === 0) {
            PublisherMyModel::query()->where('id', '=', $publisher['id'])->update(['check_mail_status' => 1]);
        }
    }

    /**
     * 检测新注册的用户，并发送注册提示邮件给运营
     */
    public static function toRemindRegEmail()
    {
        $publisherMyModel = new PublisherMyModel();

        $start = 0;
        $length = 1000;
        $where = ['mail_status' => 0];
        $count = $publisherMyModel->getCount($where);

        while ($start < $count) {
            $publisherList = $publisherMyModel->getBatch([], $where, $start, $length);
            foreach ($publisherList as $k => $v) {
                // 子账号不发邮件
                if($v['sub_account_parent'] > 0){
                    continue;
                }

                $res = self::toRemind($v);
                if (isset($res['code']) && $res['code'] == 0) {
                    $query = $publisherMyModel->queryBuilder();
                    $query->where('id', '=', $v['id'])->update(['mail_status' => 1]);
                } else {
                    Log::error('send service mail error');
                }
            }
            $start = $start + $length;
        }
    }

    /**
     * 发送注册提示邮件给运营
     *
     * @param $onePublisher mixed 新用户（数组）
     * @return mixed|void
     */
    private static function toRemind($onePublisher)
    {
        $client = new Client(
            ['headers' => ['Content-Type' => 'application/json']]
        );

        $publisherMyModel = new PublisherMyModel();

        $data = [];
        $data['time'] = time();
        if ($onePublisher['system'] === 1) {
            $data['content'] = 'Hi all ,<br/><br/>' .
                $onePublisher['name'] . ' ( ' . $onePublisher['email'] . ' ) has already registered with TopOn. Please activate the account as soon as possible. <br />' .
                '<strong>Details</strong><br />' .
                'Channel: ' . (new PublisherMyModel())->getChannelName($onePublisher['channel_id']) . '<br />' .
                'Publisher ID: ' . $onePublisher['id'] . '<br />' .
                'Publisher Name: ' . $onePublisher['name'] . '<br />' .
                'Email: ' . $onePublisher['email'] . '<br />' .
                'Create Time: ' . date('Y-m-d H:i:s', $onePublisher['create_time']) . '<br />' .
                'Company: ' . $onePublisher['company'] . '<br />' .
                'Contact: ' . $onePublisher['contact'] . '<br />' .
                'Mobile Phone: ' . $onePublisher['phone_number'] . '<br />' .
                'WeChat: ' . (empty($onePublisher['wechat']) ? '-' : $onePublisher['wechat']) . '<br />' .
                'QQ: ' . (empty($onePublisher['qq']) ? '-' : $onePublisher['qq']) . '<br />' .
                'Skype: ' . (empty($onePublisher['skype']) ? '-' : $onePublisher['skype']) . '<br />' .
                'Message: ' . (empty($onePublisher['message']) ? '-' : $onePublisher['message']) . '<br />' .
                'Learn About TopOn From: ' . ($onePublisher['source'] === 1 ? $onePublisher['source_other'] : $publisherMyModel->getSourceName($onePublisher['source'])) . '<br />' .
                '<br/><br/><br/><br/>';
        } else if ($onePublisher['system'] === 2) {
            $data['content'] = 'Hi all ,<br/><br/> '
                . $onePublisher['name'] . ' ( ' . $onePublisher['email']
                . ' ) has already registered with AutoMediation. Please activate the account as soon as possible .';
        } else {
            Log::error('system param is error');
            return;
        }
        $data['sender'] = env('MAIL_SERVICE_UUID1');
        $data['recipients'][] = env('MAIL_SUPPORT');
        $data['subject'] = 'New developer registration !';
        $tmpStr = env('MAIL_SERVICE_UUID1') . $data['time'];
        foreach ($data['recipients'] as $k => $v) {
            $tmpStr .= $v;
        }
        $tmpStr .= $data['subject'];
        $data['sign'] = sha1($tmpStr);
        $response = $client->post(
            env('MAIL_SERVICE_URL'),
            [
                'body' => json_encode($data),
                'timeout' => self::TIMEOUT,
            ]
        );
        $content = $response->getBody()->getContents();
        Log::info($content);
        return json_decode($content, true);
    }

    public static function toRemindInfo()
    {
        $publisherMyModel = new PublisherMyModel();
        $placementMyModel = new PlacementMyModel();
        $mGroupRelationshipMyModel = new MGroupRelationshipMyModel();

        $start = 0;
        $length = 1000;
        $where = ['status' => 3];
        $count = $publisherMyModel->getCount($where);
        $subject = "【聚合平台】非活跃开发者监控-" . date("Y-m-d") . ";";
        $content = "<style type=\"text/css\">
    .tg {
        border-collapse: collapse;
        border-spacing: 0;
        border-color: #aabcfe;
        min-width: 650px;
    }

    .tg td {
        font-family: Arial, sans-serif;
        font-size: 14px;
        padding: 10px 5px;
        border-style: solid;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: #aabcfe;
        color: #669;
        background-color: #e8edff;
    }

    .tg th {
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 10px 5px;
        border-style: solid;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: #aabcfe;
        color: #039;
        background-color: #b9c9fe;
    }

    .tg .tg-baqh {
        text-align: center;
        vertical-align: middle;
        font-size: medium;
    }

    .tg .tg-mb3i {
        background-color: #FFFFFF;
        text-align: center;
        vertical-align: middle;
        max-width: 400px;
        word-break: break-all;
    }

    .tg .tg-lqy6 {
        text-align: right;
        vertical-align: middle
    }

    .tg .tg-6k2t {
        background-color: #FFFFFF;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    .tg .tg-yw4l {
        vertical-align: middle;
        text-align: center;
        max-width: 400px;
        word-break: break-all;
    }

    .red {
        color: red !important;
    }

    .blue {
        color: blue !important;
    }

    .yellow {
        color: yellow !important;
    }

    .green {
        color: green !important;
    }

    .orange {
        color: orange !important;
    }
</style>";
        $content .= "<table class=\"tg\" style=\"width: 100%;\">";
        $content .= "<tr><th class='tg-6k2t'>Type</th><th class='tg-6k2t'>Publisher Id</th><th class='tg-6k2t'>Publisher Name</th><th class='tg-6k2t'>Placement Id</th><th class='tg-6k2t'>Placement Name</th><th class='tg-6k2t'>Format</th></tr>";
        $isSendMailFlag = false;
        while ($start < $count) {
            $publisherList = $publisherMyModel->getBatch([], $where, $start, $length, ['id' => 'desc']);
            foreach ($publisherList as $k => $v) {
                $tmpWhere = ['publisher_id' => $v['id']];
                $placementCount = $placementMyModel->getCount($tmpWhere);
                if (time() >= ($v['create_time'] + 7 * 24 * 3600) && time() < ($v['create_time'] + 30 * 24 * 3600) && $placementCount <= 0) {
                    $content .= "<tr>";
                    $content .= "<td>7天未建Placement</td>";
                    $content .= "<td>" . $v['id'] . "</td>";
                    $content .= "<td>" . $v['name'] . "</td>";
                    $content .= "<td>-</td>";
                    $content .= "<td>-</td>";
                    $content .= "<td>-</td>";
                    $content .= "</tr>";
                    $isSendMailFlag = true;
                }
                if ($placementCount > 0) {
                    $placementList = $placementMyModel->get([], $tmpWhere);
                    foreach ($placementList as $kk => $vv) {
                        $mGroupCount = $mGroupRelationshipMyModel->getCount(['placement_id' => $vv['id']]);
                        if (time() >= ($vv['create_time'] + 7 * 24 * 3600) && time() < ($vv['create_time'] + 30 * 24 * 3600) && $mGroupCount <= 0) {
                            $content .= "<tr>";
                            $content .= "<td>7天未建MGroup</td>";
                            $content .= "<td>" . $v['id'] . "</td>";
                            $content .= "<td>" . $v['name'] . "</td>";
                            $content .= "<td>" . $vv['uuid'] . "</td>";
                            $content .= "<td>" . $vv['name'] . "</td>";
                            $content .= "<td>" . $placementMyModel->getFormatName($vv['format']) . "</td>";
                            $content .= "</tr>";
                            $isSendMailFlag = true;
                        }
                    }
                }
            }
            $content .= "</table>";
            $start = $start + $length;
        }

        if ($isSendMailFlag) {
            $client = new Client(
                ['headers' => ['Content-Type' => 'application/json']]
            );
            $data = [];
            $data['time'] = time();
            $data['recipients'][] = 'support@uparpu.com';
            $data['subject'] = $subject;
            $data['content'] = $content;
            $tmpStr = env('MAIL_SERVICE_UUID1') . $data['time'];
            foreach ($data['recipients'] as $k => $v) {
                $tmpStr .= $v;
            }
            $tmpStr .= $data['subject'];
            $data['sign'] = sha1($tmpStr);
            $response = $client->post(
                env("MAIL_SERVICE_URL"),
                [
                    'body' => json_encode($data),
                    'timeout' => self::TIMEOUT,
                ]
            );
            //此处应该增加log
            $content = $response->getBody()->getContents();
            $res = json_decode($content, true);
            if (!isset($res['code']) || $res['code'] != 0) {
                Log::error("service code is error");
            }
        }
    }

    public static function remindPendingPlacement()
    {
        $publisherModel = new PublisherMyModel();
        $placementModel = new PlacementMyModel();
        $publisherWhere = ['mode' => 2];
        $appModel = new AppMyModel();

        $publisherBlackCount = $publisherModel->getCount($publisherWhere);
        $start = 0;
        $length = 1000;
        $isSendMailFlag = false;

        $subject = "【聚合平台】开发者新建Placement监控-" . date("Y-m-d") . " " . date("H") . "点";
        $content = "<style type=\"text/css\">
    .tg {
        border-collapse: collapse;
        border-spacing: 0;
        border-color: #aabcfe;
        min-width: 650px;
    }

    .tg td {
        font-family: Arial, sans-serif;
        font-size: 14px;
        padding: 10px 5px;
        border-style: solid;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: #aabcfe;
        color: #669;
        background-color: #e8edff;
    }

    .tg th {
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 10px 5px;
        border-style: solid;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: #aabcfe;
        color: #039;
        background-color: #b9c9fe;
    }

    .tg .tg-baqh {
        text-align: center;
        vertical-align: middle;
        font-size: medium;
    }

    .tg .tg-mb3i {
        background-color: #FFFFFF;
        text-align: center;
        vertical-align: middle;
        max-width: 400px;
        word-break: break-all;
    }

    .tg .tg-lqy6 {
        text-align: right;
        vertical-align: middle
    }

    .tg .tg-6k2t {
        background-color: #FFFFFF;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    .tg .tg-yw4l {
        vertical-align: middle;
        text-align: center;
        max-width: 400px;
        word-break: break-all;
    }

    .red {
        color: red !important;
    }

    .blue {
        color: blue !important;
    }

    .yellow {
        color: yellow !important;
    }

    .green {
        color: green !important;
    }

    .orange {
        color: orange !important;
    }
</style>";
        $content .= "<table class=\"tg\" style=\"width: 100%;\">";
        $content .= "<tr><th class='tg-6k2t'>Publisher Id</th><th class='tg-6k2t'>Publisher Name</th><th class='tg-6k2t'>App Id</th><th class='tg-6k2t'>App Name</th><th class='tg-6k2t'>Platform</th><th class='tg-6k2t'>Placement Id</th><th class='tg-6k2t'>Placement Name</th><th class='tg-6k2t'>AD Format</th><th class='tg-6k2t'>Created Time</th></tr>";


        while ($start < $publisherBlackCount) {
            $publisherList = $publisherModel->getBatch([], $publisherWhere, $start, $length);
            $start = $start + $length;

            foreach ($publisherList as $k => $v) {
                $publisherId = $v['id'];
                $placementWhere = ['publisher_id' => $publisherId, 'status' => 2];
                $placementList = $placementModel->get([], $placementWhere);
                foreach ($placementList as $kk => $vv) {
                    $oneApp = $appModel->getOne([], ['id' => $vv['app_id']]);
                    if (empty($oneApp)) {
                        continue;
                    }
                    $content .= "<tr>";
                    $content .= "<td>" . $publisherId . "</td>";
                    $content .= "<td>" . $v['name'] . "</td>";
                    $content .= "<td>" . $oneApp['uuid'] . "</td>";
                    $content .= "<td>" . $oneApp['name'] . "</td>";
                    $content .= "<td>" . $appModel->getPlatformName($oneApp['platform']) . "</td>";
                    $content .= "<td>" . $vv['uuid'] . "</td>";
                    $content .= "<td>" . $vv['name'] . "</td>";
                    $content .= "<td>" . $placementModel->getFormatName($vv['format']) . "</td>";
                    $content .= "<td>" . date('Y-m-d H:i:s', $vv['create_time']) . "</td>";
                    $content .= "</tr>";
                    $isSendMailFlag = true;
                }
            }
        }
        $content .= "</table>";

        if ($isSendMailFlag) {
            $client = new Client(
                ['headers' => ['Content-Type' => 'application/json']]
            );
            $data = [];
            $data['time'] = time();
            $data['recipients'][] = 'jeff@salmonads.com';
            $data['recipients'][] = 'zengzhihai@salmonads.com';
            $data['subject'] = $subject;
            $data['content'] = $content;
            $tmpStr = env('MAIL_SERVICE_UUID0') . $data['time'];
            foreach ($data['recipients'] as $k => $v) {
                $tmpStr .= $v;
            }
            $tmpStr .= $data['subject'];
            $data['sign'] = sha1($tmpStr);
            $response = $client->post(
                env("MAIL_SERVICE_URL"),
                [
                    'body' => json_encode($data),
                    'timeout' => self::TIMEOUT,
                ]
            );
            //此处应该增加log
            $content = $response->getBody()->getContents();
            $res = json_decode($content, true);
            if (!isset($res['code']) || $res['code'] != 0) {
                Log::error("service code is error");
            }
        }

    }

    /**
     * 向单个邮箱发送订阅邮件
     *
     * @param $email string 单个邮箱地址
     * @param $post Posts 单条文章记录
     * @return bool 若成功发送，则返回 true
     */
    public static function toPublishPost($email, $post): bool
    {
        $client = new Client(['headers' => ['Content-Type' => 'application/json']]);
        $data   = [];
        $data['time'] = time();
        $data['sender'] = env('MAIL_SERVICE_UUID1');
        $data['recipients'][] = $email;
        $data['subject'] = $post['title'];
        $signature = md5(Subscriber::UNSUBSCRIBE_TOKEN . '@@@' . $email . '$$$' . $post['id']);
        $view = view('emails.post')->with(['event' => $post, 'email' => $email, 'signature' => $signature]);
        $data['content'] = response($view)->getContent();
        $data['sign'] = self::generateSignature($data, env('MAIL_SERVICE_UUID1'));
        $response = $client->post(
            env('MAIL_SERVICE_URL'),
            [
                'body' => json_encode($data),
                'timeout' => self::TIMEOUT,
            ]
        );
        $res = json_decode($response->getBody()->getContents(), true);
        Log::info('Send post email: TO: ' . $data['recipients'][0] . ', RESULT: [code:' . $res['code'] . ',msg:' . $res['msg']. ']');
        return isset($res['code']) && $res['code'] === 0;
    }
}