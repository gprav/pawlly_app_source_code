<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('id');
        $type = session('type');
        switch (strtolower($this->getMethod())) {
            case 'post':
                return [
                    'slug' => 'string',
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('categories', 'name')->where(function ($query) use ($type) {
                            // Add your condition based on the session value
                            return $query->where('type', $type);
                        }),
                    ],
                ];
                break;
            case 'put':
            case 'patch':
                return [
                    'slug' => 'string',
                    'name' => 'required|string',
                ];
                break;
        }

        return [];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => false,
            'message' => $validator->errors()->first(),
            'all_message' => $validator->errors(),
        ];

        if (request()->wantsJson() || request()->is('api/*')) {
            throw new HttpResponseException(response()->json($data, 422));
        }

        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }
}
