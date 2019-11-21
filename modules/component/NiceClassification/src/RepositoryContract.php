<?php

/*
 * This file is part of ibrand/category.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\NiceClassification;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface RepositoryContract.
 */
interface RepositoryContract extends RepositoryInterface
{
    /**
     * get category ids by some id.
     * @param $categoryId
     * @param bool $excludeSelf
     * @return mixed
     */

    public function getSubIdsById($niceClassificationId, $excludeSelf = false);

    /**
     * get all categories.
     * @param int $depth
     * @return mixed
     */
    public function getNiceClassifications($depth = 0);

    /**
     * @param $catKeyword
     * @param int $depth
     * @return mixed
     */
    public function getSubNiceClassificationsByNameOrId($catKeyword, $depth = 0);

}
