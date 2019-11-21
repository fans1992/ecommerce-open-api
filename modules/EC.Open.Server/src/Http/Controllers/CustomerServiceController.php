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
use Validator;

class CustomerServiceController extends Controller
{
    public function store()
    {
        $input = request()->all();

        $validator = Validator::make($input, [
            'message' => 'required|string',
            'mobile' => 'required|regex:/^1[3456789]\d{9}$/',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors());
        }

        CustomerFeedback::query()->create($input);

        return $this->success();
    }


}
