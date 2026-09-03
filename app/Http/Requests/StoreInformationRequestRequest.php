<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInformationRequestRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'applicant_name' => ['required', 'string', 'max:255'],
            'applicant_nik' => ['nullable', 'string', 'max:20'],
            'applicant_occupation' => ['nullable', 'string', 'max:255'],
            'applicant_phone' => ['required', 'string', 'max:20'],
            'applicant_email' => ['required', 'email', 'max:255'],
            'applicant_address' => ['required', 'string', 'max:1000'],
            'purpose' => ['required', 'string', 'max:1000'],
            'information_detail' => ['required', 'string', 'max:5000'],
            'format_requested' => ['required', 'in:digital,cetak'],
            'delivery_method' => ['required', 'in:email,datang_langsung,pos,whatsapp'],
            'response_delivery_method' => ['required', 'in:email,pos,diambil_langsung'],
            'ktp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'power_of_attorney' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'applicant_name' => 'Nama Lengkap',
            'applicant_nik' => 'NIK',
            'applicant_occupation' => 'Pekerjaan',
            'applicant_phone' => 'No. HP/WhatsApp',
            'applicant_email' => 'Email',
            'applicant_address' => 'Alamat',
            'purpose' => 'Tujuan Penggunaan Informasi',
            'information_detail' => 'Informasi yang Dibutuhkan',
            'format_requested' => 'Format yang Diinginkan',
            'delivery_method' => 'Cara Memperoleh Informasi',
            'response_delivery_method' => 'Cara Pengiriman Jawaban',
            'ktp' => 'Dokumen KTP',
            'power_of_attorney' => 'Surat Kuasa',
        ];
    }
}
