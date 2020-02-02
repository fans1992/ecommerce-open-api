<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use GuoJiangClub\Component\User\Models\CustomerFeedback;
use GuoJiangClub\EC\Open\Server\Http\Requests\CustomerServiceRequest;
use iBrand\Sms\Facade as Sms;
use Validator;

class CustomerServiceController extends Controller
{
    public function store(CustomerServiceRequest $customerServiceRequest)
    {
        $input = $customerServiceRequest->only(['message', 'name', 'mobile', 'code']);

        if (!Sms::checkCode($input['mobile'], $input['code'])) {
            return $this->failed('验证码错误');
        }

        CustomerFeedback::query()->create(array_except($input, ['code']));

        return $this->success();
    }


}
