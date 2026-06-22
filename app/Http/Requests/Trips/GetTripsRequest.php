<?php

namespace App\Http\Requests\Trips;

use App\Enums\TripStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetTripsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer'],
            'status' => ['sometimes', new Enum(TripStatus::class)],
        ];
    }
}
