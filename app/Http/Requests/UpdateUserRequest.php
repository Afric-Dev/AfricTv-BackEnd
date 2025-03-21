<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "avatar" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:7048",
            "name" => "required",
            "email" => "required|email",
            "phone_number" => "required",
            "bio" => "nullable|max:100",
            "link" => "nullable|max:100|url",
            "password" => "nullable|confirmed"
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'avatar.image' => 'The avatar must be an image file.',
            'avatar.max' => 'The avatar must not exceed 2048 kilobytes.',
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'phone_number.required' => 'The phone number field is required.',
            'bio.max' => 'The bio must not exceed 100 characters.',
            'link.max' => 'The link must not exceed 100 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
