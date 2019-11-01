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

use Validator;

class CustomerServiceController extends Controller
{
    public function store()
    {
        $input = request()->all();

        $validator = Validator::make($input, [
            'message' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors());
        }

        $input['user_id'] = request()->user()->id;

        if (!$address = $this->addressRepository->create($input)) {
            return $this->failed('创建地址失败');
        }

        return $this->success($address);
    }


}
