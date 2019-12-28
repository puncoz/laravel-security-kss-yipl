<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class StoreProfileRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $currentUserId = auth()->user()->id;

        return [
            'name'  => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$currentUserId}",
        ];
    }

    public function persist()
    {
        $data = [];
        if ( $this->hasFile('display-picture') ) {
            $data['display_picture'] = $this->uploadImage();
        }

        $data['name']  = $this->input('name');
        $data['email'] = $this->input('email');

        auth()->user()->update($data);
    }

    protected function uploadImage(): string
    {
        $uploadedImage = $this->file('display-picture');
        $filename      = uniqid('', false).'.'.$uploadedImage->getClientOriginalExtension();

        Storage::disk('uploads')->putFileAs(
            '/',
            $uploadedImage,
            $filename
        );

        return $filename;
    }
}
