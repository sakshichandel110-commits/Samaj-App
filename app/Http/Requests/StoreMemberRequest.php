<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable',
            'mobile_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'marital_status' => 'nullable|string|max:50',

            'surname' => 'nullable|string|max:255',
            'gotra' => 'nullable|string|max:255',
            'native_place' => 'nullable|string|max:255',

            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:20',

            'document_type' => 'required_with:document_image|in:Aadhaar,PAN,Passport,Driving License',
            'document_image' => 'required_with:document_type|file|image|max:5120',

            'family_members' => 'nullable|array',
            'family_members.*.name' => 'required_with:family_members|string|max:255',
            'family_members.*.relation' => 'required_with:family_members|string|max:100',
            'family_members.*.mobile_number' => 'nullable|string|max:50',
            'family_members.*.designation' => 'nullable|string|max:255',
            'family_members.*.education' => 'nullable|string|max:255',
        ];
    }
}
