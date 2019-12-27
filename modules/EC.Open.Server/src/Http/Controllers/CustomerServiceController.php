<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use GuoJiangClub\Component\User\Models\CustomerFeedback;
use GuoJiangClub\EC\Open\Server\Http\Requests\CustomerServiceRequest;
use Validator;

class CustomerServiceController extends Controller
{
    public function store(CustomerServiceRequest $customerServiceRequest)
    {
        $input = $customerServiceRequest->all();
        CustomerFeedback::query()->create($input);

        return $this->success();
    }


}
