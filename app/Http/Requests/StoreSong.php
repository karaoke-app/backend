<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSong extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['required', 'string', 'max:255'],
            'cues' => ['required', 'array', 'min:2'],
            'cues.*.text' => ['required', 'string', 'max:255'],
            'cues.*.startTime' => ['required', 'numeric'],
            'cues.*.endTime' => ['required', 'numeric'],
            'provider_id' => ['required', 'in:vimeo,youtube'],
            'video_id' => ['required', 'alpha_dash', 'unique:songs,video_id'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
