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

use GuoJiangClub\Component\Address\RepositoryContract as AddressRepository;
use Validator;

class ContactsController extends Controller
{
    protected $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * 订单联系人列表
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $AddressList = $this->addressRepository->getByUser(request()->user()->id);

        return $this->success($AddressList);
    }

    public function store()
    {
        $input = request()->all();

        $validator = Validator::make($input, [
            'accept_name' => 'required|string|max:255',
            'mobile' => 'required|regex:/^1[34578]\d{9}$/',
            'contact_email' => 'required|email',
            'address' => 'string|max:255',
//            'is_default' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors());
        }

        $input['user_id'] = request()->user()->id;

        if (!$address = $this->addressRepository->create($input)) {
            return $this->failed('创建订单联系人失败');
        }

        return $this->success($address);
    }

    public function update($id)
    {
        $update_address = request()->only(['accept_name', 'mobile', 'contact_email', 'address_name', 'address', 'is_default']);

        if (!$address = $this->addressRepository->updateByUser($update_address, $id, request()->user()->id)) {
            return $this->failed('修改订单联系人失败');
        }

        return $this->success($address);
    }

    public function show($id)
    {
        $user_id = request()->user()->id;

        if (!$address = $this->addressRepository->findWhere(['id' => $id, 'user_id' => $user_id])->first()) {
            return $this->failed('获取订单联系人失败');
        }

        return $this->success($address);
    }

    public function default()
    {
        $user_id = request()->user()->id;

        $defaultAddress = $this->addressRepository->getDefaultByUser($user_id);

        return $this->success($defaultAddress);
    }

    public function delete($id)
    {
        $user_id = request()->user()->id;

        if (!$this->addressRepository->deleteWhere(['id' => $id, 'user_id' => $user_id])) {
            return $this->failed('删除订单联系人失败');
        }

        return $this->success();
    }
}
