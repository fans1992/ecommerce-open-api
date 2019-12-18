<?php

namespace GuoJiangClub\EC\Open\Server\Listeners;

use GuoJiangClub\Component\NiceClassification\Models\UserClassification;
use GuoJiangClub\Component\NiceClassification\NiceClassification;
use GuoJiangClub\EC\Open\Server\Events\UserClassificationEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

//  implements ShouldQueue 代表此监听器是异步执行的
class SaveUserClassification implements ShouldQueue
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
     * @param  UserClassificationEvent  $event
     * @return void
     */
    public function handle(UserClassificationEvent $event)
    {
        // 从事件对象中取出对应的分类
        $submitClassifications = $event->getClassifications();

        $classifications = NiceClassification::query()
            ->whereIn('id', array_column($submitClassifications, 'id'))
            ->with(['parent.parent:id,classification_name,classification_code,parent_id,level'])
            ->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);

        foreach ($classifications as $classification) {
            //群组
            if (!$classifications->contains('id', $classification->parent->id)) {
                $classifications->push($classification->parent);
            }

            //分类
            if (!$classifications->contains('id', $classification->parent->parent->id)) {
                $classifications->push($classification->parent->parent);
            }
        }

        $userId = $event->getUserId();

        UserClassification::query()->create([
            'name' => generaterandomstring(),
            'user_id' => $userId,
            'content' => $classifications->toTree(),
        ]);
    }
}
