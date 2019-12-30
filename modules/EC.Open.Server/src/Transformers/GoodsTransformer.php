<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

use GuoJiangClub\Component\Order\Repositories\CommentRepository;

class GoodsTransformer extends BaseTransformer
{
    protected $type;

    public function __construct($type = 'detail')
    {
        $this->type = $type;
    }

    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = [
        'products', 'photos', 'oneComment', 'questions'
    ];

    public static $excludeable = [
        'content',
    ];

    public function transformData($model)
    {
        $tags = explode(',', $model->tags);

        $model->tags = '' == $tags[0] ? [] : $tags;

        if ('list' == $this->type) {
            return array_except($model->toArray(), self::$excludeable);
        }

        return $model->toArray();
    }

    public function includePhotos($model)
    {
        $photos = $model->photos()->orderBy('is_default', 'desc')->orderBy('sort', 'desc')->get();

        return $this->collection($photos, new GoodsPhotoTransformer(), '');
    }

    public function includeProducts($model)
    {
        $products = $model->products->filter(function ($item) {
            return $item->store_nums > 0;
        });

        return $this->collection($products, new ProductTransformer(), '');
    }

    public function includeOneComment($model)
    {
        $commentRepository = app(CommentRepository::class);

        $comments = $commentRepository->getRecommendByItem($model->id)->take(1);

        return $this->collection($comments, new CommentTransformer(), '');
    }

    public function includeQuestions($model)
    {
        $questions = $model->questions()->orderByDesc('sort')->get();
        return $this->collection($questions, new GoodsQustionTransformer(), '');
    }
}

class GoodsPhotoTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return $model->toArray();
    }
}

class GoodsQustionTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return $model->toArray();
    }
}

class ProductTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return $model->toArray();
    }
}
