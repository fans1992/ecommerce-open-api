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
use Beanbun\Queue\MemoryQueue;
use Log;
use DB;

class BeanbunController extends Controller
{
    public function test(Beanbun $beanbun)
    {
        $beanbun->name = 'quandashi';
        $beanbun->count = 5;
        $beanbun->interval = 4;
        $beanbun->seed = 'https://tm-buy.aliyun.com/classification/query.json?keyword=&umToken=&_csrf=b8d6ca3f-0171-45a4-b3a5-50375e92c247';
//        $beanbun->logFile = storage_path('logs/beanbun.log');

        // 设置队列
        $beanbun->setQueue('redis', [
            'host' => '127.0.0.1',
            'port' => '2207'
        ]);

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
                'cookie' => 'cna=qf6UFL83WxoCAbStbOeeZd4F; CONSOLE_RESOURCEGROUP_ID=; _ga=GA1.2.1732552715.1545115476; cnz=58ifFE67D3gCAXTi+nLyom+f; login_aliyunid_token="ACFb6GKM8f+eVjhjg7jsuH+7BRULA/wQlLY1EIspjRY="; aliyun_lang=zh; CLOSE_HELP_GUIDE_V2=true; UM_distinctid=16be787c54d5d8-0855198b5eefc-37647e04-1fa400-16be787c54e76c; cn_1259517254_dplus=%7B%22distinct_id%22%3A%20%2216be787c54d5d8-0855198b5eefc-37647e04-1fa400-16be787c54e76c%22%2C%22%24_sessionid%22%3A%200%2C%22%24_sessionTime%22%3A%201563299001%2C%22initial_view_time%22%3A%20%221563297816%22%2C%22initial_referrer%22%3A%20%22https%3A%2F%2Fwww.baidu.com%2Flink%3Furl%3DFJwfwq9RaJGfwWm5oQA9tttibE2U3nW0VON2RuOit4d9srVXuieF0TiO8U1NpdrB%26wd%3D%26eqid%3Dcf0d99f70001fa47000000035d2e0c9f%22%2C%22initial_referrer_domain%22%3A%20%22www.baidu.com%22%2C%22%24recent_outside_referrer%22%3A%20%22www.baidu.com%22%2C%22%24dp%22%3A%200%2C%22%24_sessionPVTime%22%3A%201563299001%7D; aliyun_choice=CN; console_base_assets_version=2.4.0; login_aliyunid_pk=20924325; activeRegionId=cn-hangzhou; currentRegionId=cn-hangzhou; consoleRecentVisit=dns%2Cecs%2Ceci%2Cdomain%2Cdms%2Crds; _gid=GA1.2.128642682.1571764102; ping_test=true; t=1b5d6b16b2667b4d7f76c7de7cef2c85; _tb_token_=fee003393e47f; cookie2=11b99e666d55892407f9902f943cff76; _hvn_login=6; aliyunMerakSource=WyJ0cmFkZW1hcmtfcmVnaXN0ZXIuODUxNTE5Mi44MTc3MDU0MjUubm9uLm5vbi5ub24ubm9uIl0=; XSRF-TOKEN=b8d6ca3f-0171-45a4-b3a5-50375e92c247; channel=dKsTees6Al%2BEiEMRvDS1%2B%2BGeb9cY%2BNLvM%2F3Nn%2F0xElI%3D; csg=84fb3f38; login_aliyunid="hi2092****@aliyun.com"; login_aliyunid_ticket=4kzajQdWAnHTnSicc35W_eI2LbTSGKquKukwcU7opRwmInhtQH*mlmsZQ3ByOLYVmqI*1hFEnsSqn7P9bgZlPrvzUObSJ9fdZ$8fGQiWBOjMJ0m4J_ij1l2wRKHgYJ1jI3P7TK61fQME0; login_aliyunid_luid="BG+iXJMy3yCbee1ebbdcb6997de7c3ac36a611360fe+g0LzbY/3OoaUBcElyrRQdo5RL3dg++nzWX9vnaBq"; login_aliyunid_csrf=_csrf_tk_1020271844000970; login_aliyunid_suid="cgS6nvlnvU9BKcpUuM22RKyqW/q22gDnPRQF76s5"; login_aliyunid_abi=BG+xUUCUb1baa8c77bda884a97fa55ed9289b20999a+p64ZPFHylLZfKhXKdOPI9KNyuW138Jt2EzkQzVyH; login_aliyunid_pks=BG+7XEZ+1LjzjxhNYOrye4Bn7itdbZjl5rX; hssid=1BkCcCjAXpcAxkrMhr-EfGA1; hsite=6; aliyun_country=CN; aliyun_site=CN; JSESSIONID=B694442717A126DAB859047CB7E03AFB; l=dB_0pf1lvEI9G9g8BOCN5uI81CbTxIRfguPRwGSHi_5wDs81wOQOkZpnyUJ6cjWcMqYB4JFf5TwTNe3b8PTbah0fzFUVfkqpCef..; isg=BJmZu8wzILiIc_2IoyYmZbVzqIOzjpKXVgcn7rtOXkA-wrhUA3YnqdCExMYR-iUQ',
            ];
        };

        $beanbun->afterDownloadPage = function ($beanbun) {
            Log::info("beanbun worker download {$beanbun->queue['url']} success!");
            // 获取的数据为 json，先解析
            $data = json_decode($beanbun->page, true);
            Log::info($data);

            foreach ($data['data']['data'] as $class) {
                $classes[] = [
                    'id' => $class['id'],
                    'classification_name' => $class['classificationName'],
                    'classification_code' => $class['classificationCode'],
                    'level' => $class['level'],
                    'parent_id' => NULL,

                ];
            }

            Log::info($classes);
//            DB::table('nice_classification')->insert($classes);

            // 如果没有数据或报错，那可能是被屏蔽了。就把地址才重新加回队列
//            if (isset($data['error']) || !isset($data['data'])) {
//                $beanbun->queue()->add($beanbun->url);
//                $beanbun->error();
//            }

            // 如果本次爬取的不是最后一页，就把下一页加入队列
//            if ($data['paging']['is_end'] == false) {
//                $beanbun->queue()->add($data['paging']['next']);
//            }

//            $insert = [];
//            $date = date('Y-m-d H:i:s');

        };

        $beanbun->start();
    }
}
