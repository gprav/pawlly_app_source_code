<?php

namespace Modules\Service\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ServiceDurationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $type = session('type');

        $serviceDurationId = $this->route('service_duration');

        return [

            'price' => 'required|numeric|min:0',
            'duration' => [
                'required',
                'string',
                // Ensure unique duration for the given type in the service_duration table
                Rule::unique('service_duration', 'duration')
                    ->where('type', $type)
                    ->ignore($serviceDurationId),
            ],
        ];
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

    protected function prepareForValidation()
    {
        $this->merge([
            'duration' => sprintf('%02d:%02d', $this->hours, $this->minutes),
        ]);
    }
}
