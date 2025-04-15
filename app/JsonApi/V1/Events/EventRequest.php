<?php

namespace App\JsonApi\V1\Events;

use App\Models\Enums\EventEnum;
use App\Models\Enums\EventStatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class EventRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'event_type' => ['required', new Enum(EventEnum::class)],
            'status' => ['nullable', new Enum(EventStatusEnum::class)],
            'users' => 'nullable|array',
            'users.*.id' => 'nullable|integer|exists:users,id',
        ];
    }
}
