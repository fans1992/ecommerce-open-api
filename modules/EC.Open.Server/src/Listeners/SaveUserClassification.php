<?php

namespace GuoJiangClub\EC\Open\Server\Listeners;

use GuoJiangClub\Component\NiceClassification\NiceClassification;
use GuoJiangClub\EC\Open\Server\Events\UserClassification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

//  implements ShouldQueue 代表此监听器是异步执行的
class SaveUserClassification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    // Laravel 会默认执行监听器的 handle 方法，触发的事件会作为 handle 方法的参数
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserClassification  $event
     * @return void
     */
    public function handle(UserClassification $event)
    {
        // 从事件对象中取出对应的分类
        $submitClassifications = $event->getClassifications();
        NiceClassification::query()->whereIn('id', array_column($submitClassifications, 'id'))->get();
    }
}
