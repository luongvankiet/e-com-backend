<?php

namespace App\Actions;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function __invoke(UpdateUserRequest|Request $request, User $user): ?User
    {
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');
        $user->address_line_1 = $request->input('address_line_1');
        $user->address_line_2 = $request->input('address_line_2');
        $user->city = $request->input('city');
        $user->state = $request->input('state');
        $user->country = $request->input('country');
        $user->postcode = $request->input('postcode');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return $user;
    }
}
