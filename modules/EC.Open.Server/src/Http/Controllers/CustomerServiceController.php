<?php

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
