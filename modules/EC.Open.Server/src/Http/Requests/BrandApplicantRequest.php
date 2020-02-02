<?php

namespace GuoJiangClub\EC\Open\Server\Http\Requests;

use App\Http\Requests\FormRequest;

class BrandApplicantRequest extends FormRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
                return [
                    'applicant_subject' => 'required|in:enterprise,individual',
                ];
                break;
                // CREATE
            case 'POST':
                return [
                    'applicant_subject' => 'required|in:enterprise,individual',
                    'applicant_name' => 'required|string',
                    'unified_social_credit_code' => 'required_if:applicant_subject,enterprise',
                    'id_card_no' => 'required_if:applicant_subject,individual',
                    'province' => 'required|string',
                    'city' => 'required|string',
                    'district' => 'required|string',
                    'address' => 'required|string|max:255',
                    'postcode' => 'required|string',
                    'business_license_picture' => 'required|url',
                    'id_card_picture' => 'required_if:applicant_subject,individual|url',
                    'attorney_picture' => 'string|url',
                ];
                break;
            // UPDATE
            case 'PUT':
            case 'PATCH':
                return [
                    // UPDATE ROLES
                ];
                break;
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