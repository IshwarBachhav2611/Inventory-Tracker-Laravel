<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Keep the old password if the field was left empty
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect('/profile')->with('success', 'Profile updated.');
    }
}