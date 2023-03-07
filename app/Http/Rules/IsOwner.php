<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Schedule;

class IsOwner implements Rule
{
    public function passes($attribute, $value)
    {
        return auth()->user()->id == Schedule::find($value)->user_id;
    }

    public function message()
    {
        return 'You are forbidden from taking this action!';
    }
}