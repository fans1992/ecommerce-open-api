<?php

namespace App\Console\Commands;

use GuoJiangClub\Component\NiceClassification\NiceClassification;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Log;
use DB;

class Spider extends Command
{
    private $totalPageCount =1;
    private $counter = 1;
    private $concurrency = 1;  // 同时并发抓取

//    private $pids = [1];

    /**
     * 命令行的名称及签名。
     *
     * @var string
     */
    protected $signature = 'command:spider'; //concurrency为并发数  keyWords为查询关键词

    /**
     * 命令行的描述
     *
     * @var string
     */
    protected $description = '尼斯分类爬虫';

    /**
     * 创建新的命令行实例。
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 执行命令行。
     *
     * @return mixed
     */
    public function handle()
    {
//        $this->totalPageCount = count($this->pids);

        $client = new Client([
            'headers' => [
                'User-Agent' => 'Name of your tool/v1.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
            ],
        ]);

        $uri = 'https://www.quandashi.com/brand-order/get-cg-list';


//        $response = $client->request('POST', 'https://www.quandashi.com/brand-order/get-cg-list', [
//            'form_params' => [
//                'pid' => '1',
//            ]
//        ]);
//
//        $res = json_decode($response->getBody()->getContents(),true);
//
//        Log::info($res['msg']);

        $requests = function ($total) use ($client, $uri) {
//            foreach ($this->pids as $key => $pid) {
//
//                yield function () use ($client, $uri, $pid) {
//                    return $client->request('POST', $uri, ['form_params' => ['pid' => $pid]]);
//                };
//            }

            yield function () use ($client, $uri) {
                return $client->request('GET', $uri);
            };

        };

        $pool = new Pool($client, $requests($this->totalPageCount), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $index) use ($client, $uri) {
                //爬取45大类
                $classifications = json_decode($response->getBody()->getContents(), true);
                foreach ($classifications['msg'] as $classification) {
                     DB::table('nice_classification')->insert([
                        'id' => $classification['fcgid'],
                        'classification_name' => $classification['fcgname'],
                        'classification_code' => $classification['fcgnum'],
                        'parent_id' => $classification['fcgparent'],
                    ]);

                    //爬取群组
                    $groupResponse = $client->request('POST', $uri, ['form_params' => ['pid' => $classification['fcgid']]]);
                    $groups = json_decode($groupResponse->getBody()->getContents(), true);

                    foreach ($groups['msg'] as $group) {
                        DB::table('nice_classification')->insert([
                            'id' => $group['fcgid'],
                            'classification_name' => $group['fcgname'],
                            'classification_code' => $group['fcgnum'],
                            'parent_id' => $group['fcgparent'],
                        ]);

                        //爬取商品
                        $productResponse = $client->request('POST', $uri, ['form_params' => ['pid' => $group['fcgid']]]);
                        $products = json_decode($productResponse->getBody()->getContents(), true);
                        foreach ($products['msg'] as $product) {
                            DB::table('nice_classification')->insert([
                                'id' => $product['fcgid'],
                                'classification_name' => $product['fcgname'],
                                'classification_code' => $product['fcgnum'],
                                'parent_id' => $product['fcgparent'],
                            ]);
                        }

                        $this->info("已爬取 " . $group['fcgname'] . "--" . $group['fcgnum'] . " 所有商品");
                    }

                    $this->info("-----------已爬取第". $classification['fcgnum'] . "大类 " . $classification['fcgname'] . " 所有群组-----------------");

                }

                $this->countedAndCheckEnded();
            },
            'rejected' => function ($reason, $index) {
                $this->error("rejected");
                $this->error("rejected reason: " . $reason);
                $this->countedAndCheckEnded();
            },
        ]);

        // 开始发送请求
        $promise = $pool->promise();
        $promise->wait();

    }

    public function countedAndCheckEnded()
    {
        if ($this->counter < $this->totalPageCount) {
            $this->counter++;
            return;
        }
        $this->info("请求结束！");
    }
}
