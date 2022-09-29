<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:20',
            'phone' => 'required|string|size:10|unique:users',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Tên không được để trống!',
            'name.string' => 'Tên phải là một chuỗi ký tự!',
            'name.max' => 'Tên không được vượt quá :max kí tự!',
            'phone.required' => 'Số điện thoại không được để trống!',
            'phone.string' => 'Số điện thoại phải là một chuỗi ký tự!',
            'phone.size' => 'Số điện thoại phải có độ dài :size chữ số!',
            'phone.unique' => 'Số điện thoại đã tồn tại!',
        ];
    }
}
