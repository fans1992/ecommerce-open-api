<?php

namespace GuoJiangClub\EC\Open\Server\Http\Requests;

use App\Http\Requests\FormRequest;

class OrderAgreementRequest extends FormRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
                return [];
                break;
                // CREATE
            case 'POST':
                return [];
                break;
            // UPDATE
            case 'PUT':
            case 'PATCH':
            return [
                'order_contact.accept_name' => 'required|string',
                'order_contact.mobile' => 'required|regex:/^1[3456789]\d{9}$/',
                'order_contact.email' => 'required|email',
                'order_contact.address' => 'required|string',
                'invoice_title.invoice_type'=>'required|in:special,general',
            ];
            case 'DELETE':
            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            // Validation messages
        ];
    }
}