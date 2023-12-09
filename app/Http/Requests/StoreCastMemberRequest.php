<?php

namespace App\Http\Requests;

use Core\Domain\Enum\CastMemberType;
use Illuminate\Foundation\Http\FormRequest;

class StoreCastMemberRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $types = implode(",", array_column(CastMemberType::cases(), "value"));
        return [
            'name' => [
                'required',
                'min:3',
                'max:255'
            ],
            'type' => [
                'required',
                'in:' . $types
            ]
        ];
    }
}
