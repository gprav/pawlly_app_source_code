<?php

namespace Modules\Service\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ServiceTrainingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->id;
        $rules = [
            'name' => 'required|string|max:255|unique:service_training,name,' . $id,
            'status' => 'boolean',
            // 'durations' => 'required|json',
        ];

        switch (strtolower($this->getMethod())) {
            case 'post':
                $rules['name'] = 'unique:service_training,name,' . $id;
                break;
            case 'put':
            case 'patch':
                $rules['name'] = 'required|string|max:255';
                break;
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $durations = json_decode($this->durations, true);

            if (is_array($durations)) {
                foreach ($durations as $index => $duration) {
                    // if (!isset($duration['hours']) || empty($duration['hours'])) {
                    //     $validator->errors()->add("durations.{$index}.hours", 'Hours is required and must be a non-empty string.');
                    // }
                    // if (!isset($duration['minutes']) || empty($duration['minutes'])) {
                    //     $validator->errors()->add("durations.{$index}.minutes", 'Minutes is required and must be a non-empty string.');
                    // }
                    // if (!isset($duration['amount']) || !is_numeric($duration['amount']) || $duration['amount'] <= 0) {
                    //     $validator->errors()->add("durations.{$index}.amount", 'Price is required and must be a positive number.');
                    // }
                    if (!isset($duration['duration']) || empty($duration['duration'])) {
                        $validator->errors()->add("durations.{$index}.duration", 'Duration is required and must be a non-empty string.');
                    }

                }
            } else {
                $validator->errors()->add('durations', 'Durations must be a valid JSON array.');
            }
        });
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
