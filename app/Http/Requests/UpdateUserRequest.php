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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->route('user')],
            'password' => ['nullable', 'confirmed', 'string', 'min:8'],
            'password_confirmation' => ['nullable', 'string', 'min:8', 'same:password'],
            'number' => ['nullable', 'string', 'max:255', 'unique:users,number,'.$this->route('user')],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'is_active' => ['nullable', 'integer'],
            'designation' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:255'],
            '_method' => ['required', 'string', 'max:255'],
        ];
    }
}
