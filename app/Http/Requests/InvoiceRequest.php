<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
            'invoice_item' => ['json', 'string', 'required'],
            'customer_id' => ['numeric', 'required'],
            'date' => ['date', 'required'],
            'due_date' => ['date', 'required'],
            'number' => ['string', Rule::unique('invoices')->ignore($this->id), 'required'],
            'reference' => ['required', 'string'],
            'discount' => ['nullable', 'numeric'],
            'sub_total' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],
            'terms_and_conditions' => ['required', 'string']
        ];
    }
}
