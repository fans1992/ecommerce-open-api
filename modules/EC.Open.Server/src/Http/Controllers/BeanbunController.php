<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use Beanbun\Beanbun;
use Log;

class BeanbunController extends Controller
{
    public function test(Beanbun $beanbun)
    {
        $beanbun->name = 'quandashi';
        $beanbun->count = 5;
        $beanbun->interval = 4;
        $beanbun->seed = 'https://tm-buy.aliyun.com/classification/query.json?keyword=45&umToken=&_csrf=6f833063-542a-4764-baea-0e8877317c2b';
//        $beanbun->logFile = storage_path('logs/beanbun.log');

        $beanbun->beforeDownloadPage = function ($beanbun) {
            // 在爬取前设置请求的 headers
            $beanbun->options['headers'] = [
                'authority' => 'tm-buy.aliyun.com',
                'method' => 'GET',
                'path' => '/classification/query.json?keyword=01&umToken=&_csrf=6f833063-542a-4764-baea-0e8877317c2b',
                'scheme' => 'https',
                'accept' => 'application/json',
                'accept-encoding' => 'gzip, deflate, br',
                'accept-language' => 'zh-CN,zh;q=0.9',
                'content-type' => 'application/x-www-form-urlencoded',
                'referer' => 'https://tm-buy.aliyun.com/?spm=5176.13203013.j64pehhhs.18.260d31f9hvttP3&aly_as=LgHijATJ&accounttraceid=eac6ab17f17645dc8cb23623cf247e63drio',
                'sec-fetch-mode' => 'cors',
                'sec-fetch-site' => 'same-origin',
                'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36',
                'cookie' => 'cna=qf6UFL83WxoCAbStbOeeZd4F; _ga=GA1.2.1732552715.1545115476; cnz=58ifFE67D3gCAXTi+nLyom+f; login_aliyunid_token="ACFb6GKM8f+eVjhjg7jsuH+7BRULA/wQlLY1EIspjRY="; aliyun_lang=zh; CLOSE_HELP_GUIDE_V2=true; UM_distinctid=16be787c54d5d8-0855198b5eefc-37647e04-1fa400-16be787c54e76c; cn_1259517254_dplus=%7B%22distinct_id%22%3A%20%2216be787c54d5d8-0855198b5eefc-37647e04-1fa400-16be787c54e76c%22%2C%22%24_sessionid%22%3A%200%2C%22%24_sessionTime%22%3A%201563299001%2C%22initial_view_time%22%3A%20%221563297816%22%2C%22initial_referrer%22%3A%20%22https%3A%2F%2Fwww.baidu.com%2Flink%3Furl%3DFJwfwq9RaJGfwWm5oQA9tttibE2U3nW0VON2RuOit4d9srVXuieF0TiO8U1NpdrB%26wd%3D%26eqid%3Dcf0d99f70001fa47000000035d2e0c9f%22%2C%22initial_referrer_domain%22%3A%20%22www.baidu.com%22%2C%22%24recent_outside_referrer%22%3A%20%22www.baidu.com%22%2C%22%24dp%22%3A%200%2C%22%24_sessionPVTime%22%3A%201563299001%7D; aliyun_choice=CN; activeRegionId=cn-hangzhou; currentRegionId=cn-hangzhou; XSRF-TOKEN=6f833063-542a-4764-baea-0e8877317c2b; ping_test=true; t=1b5d6b16b2667b4d7f76c7de7cef2c85; _tb_token_=7b0365de17e09; cookie2=18f79e23718ea12e94a9bb071c2094c9; channel=dKsTees6Al%2BEiEMRvDS1%2B0koWrkUiyBJM2K8DnApGaU%3D; UC-XSRF-TOKEN=f1c078e5-0768-4cd8-9023-4b15d00872de; console_base_assets_version=2.5.3; _hvn_login=6; login_aliyunid_pk=20924325; FECS-XSRF-TOKEN=769da1c7-d46a-4ccc-95d0-c6f49ec8e4da; FECS-UMID=%7B%22token%22%3A%22Yb084bbccc381fe7ef7cb0147e92d2dd5%22%2C%22timestamp%22%3A%225955084254555C455141617F%22%7D; consoleRecentVisit=dns%2Cecs%2Cdomain%2Cdms%2Crds%2Ccr; _gid=GA1.2.1023997656.1571819541; csg=58fb7484; login_aliyunid="hi2092****@aliyun.com"; login_aliyunid_ticket=K63fQTEM14kzajQdWAnHTnSicc35W_eI2LbTSGKquKukwcU7opRwmInhtQH*mlmsZQ3ByOLYVmqI*1hFEnsSqn7P9bgZlPrvzUObSJ9fdZ$8fGQiWBOjMqWlmyK6LJ6tjCytg3O_wzHP0; login_aliyunid_luid="BG+vXcJ4PbF8c38e27a24eae83c6f76bfc1e4b0a306+gCDWc4cc0lYwhjAa6KdnD3d/m3jVqEjgheXPvu53"; login_aliyunid_csrf=_csrf_tk_1795771828086656; login_aliyunid_suid=6bVpTo6Iywk9IPzRpHkB3DE+0XI9VzJ2B1DX4GI6; login_aliyunid_abi=BG+q9fwS6KEcf2723e2e17f145e2ac58fde3c3f52db+4xoKYF2OMLiVcgi6L7qMfu+qsDb0sDSktDjvSlfD; login_aliyunid_pks=BG+SD6hJuwIQLJE0YZpU9i8X7itdbZjl5rX; hssid=18aooqZwlK9BzP36qqnnUFw1; hsite=6; aliyun_country=CN; aliyun_site=CN; __yunlog_session__=1571828099391; JSESSIONID=35B4B36D52A2A69FC9819517DDE42E36; l=dB_0pf1lvEI9GYpMBOfwquI8arbO1IRbzVVzw4_GVICP_q6pj9zVWZQANFt9CnGV3sdMR3yLGN67BJTLFPUHl7-ila8TuGZh5dTFC; isg=BFJSGJQ8W12No6YFxMMNYDqeox70y1abqXrcoxyrkoX9L_ApBPMED-sZn8u2Y86V',
            ];
        };

        $beanbun->afterDownloadPage = function ($beanbun) {
            Log::info($beanbun);
            // 获取的数据为 json，先解析
            $data = json_decode($beanbun->page, true);
            Log::info($data);

            // 如果没有数据或报错，那可能是被屏蔽了。就把地址才重新加回队列
            if (isset($data['error']) || !isset($data['data'])) {
                $beanbun->queue()->add($beanbun->url);
                $beanbun->error();
            }

            // 如果本次爬取的不是最后一页，就把下一页加入队列
            if ($data['paging']['is_end'] == false) {
                $beanbun->queue()->add($data['paging']['next']);
            }

            $insert = [];
            $date = date('Y-m-d H:i:s');

        };

        $beanbun->start();
    }
}
