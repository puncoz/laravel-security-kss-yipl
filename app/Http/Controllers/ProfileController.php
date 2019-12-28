<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view("profile.index", compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();

        return view("profile.form", compact('user'));
    }

    public function update(StoreProfileRequest $request)
    {
        $request->persist();

        return redirect()->to(route('profile'));
    }
}
