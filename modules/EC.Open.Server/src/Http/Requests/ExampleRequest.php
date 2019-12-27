<?php

namespace GuoJiangClub\EC\Open\Server\Http\Requests;

use App\Http\Requests\FormRequest;

class ExampleRequest extends FormRequest
{
    public function rules()
    {
        switch($this->method())
        {
            // CREATE
            case 'POST':
                {
                    return [
                        // CREATE ROLES
                    ];
                }
            // UPDATE
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        // UPDATE ROLES
                    ];
                }
            case 'GET':
            case 'DELETE':
            default:
                {
                    return [];
                };
        }
    }

    public function messages()
    {
        return [
            // Validation messages
        ];
    }
}