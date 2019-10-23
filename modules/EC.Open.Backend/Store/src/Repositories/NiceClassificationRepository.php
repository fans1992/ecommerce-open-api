<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;

use GuoJiangClub\EC\Open\Backend\Store\Model\NiceClassification;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CategoryRepositoryEloquent
 * @package namespace App\Repositories;
 */
class NiceClassificationRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NiceClassification::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 获得排序分类
     * @return mixed
     */
    public function getSortNiceClassification()
    {
        $category = $this->orderBy('sort', 'asc')->all(['id', 'classification_name', 'parent_id', 'sort', 'level', 'path']);
        return $category;
    }

    /**
     * 无限极分类
     * @param int $pid
     * @param int $level
     * @return array
     */
    public function getLevelNiceClassification($pid = 0, $html = ' ', $dep = '')
    {

        $niceClassifications = $this->getSortNiceClassification();

        return $this->buildNiceClassificationTree($niceClassifications, $pid, $html, $dep);

    }

    private function buildNiceClassificationTree($niceClassifications, $pid = 0, $html = ' ', $dep = '')
    {
        $result = [];
        foreach ($niceClassifications as $v) {
            if ($v['level'] > 2) {
                continue;
            }

            if ($v['parent_id'] == $pid) {
                $v['html'] = str_repeat($html, $v['level']);
                $v['dep'] = $v['path'];
                $result[] = $v;
                $result = array_merge($result, self::buildNiceClassificationTree($niceClassifications, $v['id'], $html, $v['dep']));
            }
        }

        return $result;
    }

    public function getOneLevelNiceClassification($pid = 0)
    {
        $niceClassifications = $this->getSortNiceClassification();
        $result = array();
        foreach ($niceClassifications as $v) {
            if ($v['level'] > 2) {
                continue;
            }

            if ($v['parent_id'] == $pid) {
                $result[] = $v;
            }
        }

        return $result;
    }

    /**
     * 根据商品分类的父类ID进行数据归类
     * @return array
     */
    public function getNiceClassificationParent()
    {
        $niceClassificationData = $this->getSortNiceClassification();
        $result = array();
        foreach ($niceClassificationData as $key => $val) {
            if (isset($result[$val['parent_id']]) && is_array($result[$val['parent_id']])) {
                $result[$val['parent_id']][] = $val;
            } else {
                $result[$val['parent_id']] = array($val);
            }
        }
        return $result;
    }

    public function delNiceClassification($id)
    {

        if (count($this->findWhere(['parent_id' => $id])) > 0) return false;

        if ($this->delete($id)) return true;

        return false;
        /*if ($this->delete($id))
            if (Category::where('parent_id', $id)->delete())
                return true;
        return false;*/
    }

    /**
     * 设置分类的depth level
     * @param $category_id
     * @param $parent_id
     */
    public function setNiceClassificationLevel($nice_classification_id, $parent_id)
    {
        $niceClassification = $this->find($nice_classification_id);
        if ($parent_id) {
            $parentCategory = $this->find($parent_id);
            $niceClassification->path = $parentCategory->path . $niceClassification->id . '/';
            $niceClassification->level = $parentCategory->level + 1;
        } else {
            $niceClassification->path = '/' . $niceClassification->id . '/';
            $niceClassification->level = 1;
        }
        $niceClassification->save();
    }

    /**
     * 设置子分类的path level
     * @param $nice_classification_id
     */
    public function setSonNiceClassificationLevel($nice_classification_id)
    {
        $sonNiceClassification = $this->scopeQuery(function ($query) use ($nice_classification_id) {
            $query->where('path', 'like', '%/' . $nice_classification_id . '/%')
                ->where('id', '<>', $nice_classification_id);
            return $query->orderBy('level', 'asc');
        })->all();

        $niceClassification = $this->find($nice_classification_id);

        if (count($sonNiceClassification) > 0) {
            $this->setSonCategoryLevelTree($sonNiceClassification, $niceClassification);
        }
    }

    private function setNiceClassificationLevelTree($niceClassification, $parent_nice_classification)
    {
        foreach ($niceClassification as $key => $item) {
            if ($item->parent_id == $parent_nice_classification->id) {
                $item->path = $parent_nice_classification->path . $item->id . '/';
                $item->level = $parent_nice_classification->level + 1;
                $item->save();
                self::setNiceClassificationLevelTree($niceClassification, $item);
            }
        }
    }

}
