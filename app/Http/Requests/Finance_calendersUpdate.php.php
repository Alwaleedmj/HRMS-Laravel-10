<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Finance_calendersUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "FINANCE_YR"=> "required|unique:finance_calender",
            "FINANCE_YR_DESC"=> "required",
            "start_date"=> "required",
            "end_date"=> "required",
        ];
    }

    public function messages(): array
    {
        return [
            "FINANCE_YR.required"=> "يجب ادخال كود السنة المالة",
            "start_date.required"=> "يجب ادخال بداية السنة المالية",    
            "end_date.required"=> "يجب ادخال نهاية السنة المالية",
        ];
    }
}
