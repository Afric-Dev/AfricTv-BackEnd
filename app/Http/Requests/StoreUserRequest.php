<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreUserRequest extends FormRequest
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
                "avatar" => "nullable|image|max:2048",
                "name" => "required",
                "email" => "required|email|unique:users",
                "phone_number" => "required",
                "bio" => "nullable|max:100",
                "link" => "nullable|max:100",
                "password" => "required|confirmed"
        ];
    }
}
