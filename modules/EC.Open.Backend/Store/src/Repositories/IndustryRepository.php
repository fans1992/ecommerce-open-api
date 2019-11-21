<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;

use GuoJiangClub\EC\Open\Backend\Store\Model\Industry;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CategoryRepositoryEloquent
 * @package namespace App\Repositories;
 */
class IndustryRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Industry::class;
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
    public function getSortIndustry()
    {
        $industry = $this->orderBy('sort', 'asc')->all(['id', 'name', 'parent_id', 'sort', 'level', 'path']);
        return $industry;
    }

    /**
     * 无限极分类
     * @param int $pid
     * @param int $level
     * @return array
     */
    public function getLevelIndustry($pid = 0, $html = ' ', $dep = '')
    {

        $industries = $this->getSortIndustry();

        return $this->buildIndustryTree($industries, $pid, $html, $dep);

    }

    private function buildIndustryTree($industries, $pid = 0, $html = ' ', $dep = '')
    {
        $result = [];
        foreach ($industries as $v) {
            if ($v['level'] > 2) {
                continue;
            }

            if ($v['parent_id'] == $pid) {
                $v['html'] = str_repeat($html, $v['level']);
                $v['dep'] = $v['path'];
                $result[] = $v;
                $result = array_merge($result, self::buildIndustryTree($industries, $v['id'], $html, $v['dep']));
            }
        }

        return $result;
    }

    public function getOneLevelIndustry($pid = 0)
    {
        $industries = $this->getSortIndustry();
        $result = array();
        foreach ($industries as $v) {
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
     * 根据行业分类的父类ID进行数据归类
     * @return array
     */
    public function getIndustryParent()
    {
        $industryData = $this->getSortIndustry();
        $result = array();
        foreach ($industryData as $key => $val) {
            if (isset($result[$val['parent_id']]) && is_array($result[$val['parent_id']])) {
                $result[$val['parent_id']][] = $val;
            } else {
                $result[$val['parent_id']] = array($val);
            }
        }
        return $result;
    }

    public function delIndustry($id)
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
     * @param $industry_id
     * @param $parent_id
     */
    public function setIndustryLevel($industry_id, $parent_id)
    {
        $industry = $this->find($industry_id);
        if ($parent_id) {
            $parentIndustry = $this->find($parent_id);
            $industry->path = $parentIndustry->path . $industry->id . '/';
            $industry->level = $parentIndustry->level + 1;
        } else {
            $industry->path = '/' . $industry->id . '/';
            $industry->level = 1;
        }
        $industry->save();
    }

    /**
     * 设置子分类的path level
     * @param $category_id
     */
    public function setSonIndustryLevel($industry_id)
    {
        $sonIndustry = $this->scopeQuery(function ($query) use ($industry_id) {
            $query->where('path', 'like', '%/' . $industry_id . '/%')
                ->where('id', '<>', $industry_id);
            return $query->orderBy('level', 'asc');
        })->all();

        $industry = $this->find($industry_id);

        if (count($sonIndustry) > 0) {
            $this->setSonIndustryLevelTree($sonIndustry, $industry);
        }
    }

    private function setSonIndustryLevelTree($industries, $parent_industry)
    {
        foreach ($industries as $key => $item) {
            if ($item->parent_id == $parent_industry->id) {
                $item->path = $parent_industry->path . $item->id . '/';
                $item->level = $parent_industry->level + 1;
                $item->save();
                self::setSonIndustryLevelTree($industries, $item);
            }
        }
    }

}
