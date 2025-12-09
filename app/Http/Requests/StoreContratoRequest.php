<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Contracts\Validation\Validator;

class StoreContratoRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:contratos,email'], // ÚNICO
            'categoria' => ['required', 'string', 'max:255'],
            'telefone' => [
                'nullable',
                'string',
                'max:16', // Max 16 caracteres para o formato completo
                // Regra para forçar o formato +244 9XX XXX XXX
                'regex:/^\+244\s9[1-9]{2}\s[0-9]{3}\s[0-9]{3}$/'
                // ^\+244 = Começa com +244
                // \s = Um espaço
                // 9[1-9][0-0] = Começa com 9, seguido de 1 a 9, seguido de 0 (Ex: 910, 920, 930...)
                // \s = Espaço
                // [0-9]{3} = Três dígitos
                // $ = Fim da string
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'message' => 'Os dados fornecidos são inválidos.'
        ], 422));
    }

    public function messages(): array
    {
        return [
            // 1. Regras Comuns
            'nome.required' => 'Por favor, preencha o campo Nome.',
            'nome.max' => 'O nome não pode exceder 100 caracteres.',

            'categoria.required' => 'A Categoria é obrigatória.',

            // 2. Regras Específicas para Email (Com required, email e unique)
            'email.required' => 'O campo Email é obrigatório.',
            'email.email' => 'Por favor, insira um endereço de e-mail válido.',
            'email.unique' => 'Este endereço de e-mail já está registrado em outro contrato.',

            'telefone.regex' => 'O campo Telefone deve estar no formato Angolano: +244 9XX XXX XXX.'

        ];
    }
}
