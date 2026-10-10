<?php

return [
    'required' => 'Kolom :attribute wajib diisi.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'unique' => ':attribute sudah terdaftar.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'string' => ':attribute harus berupa teks.',
    'boolean' => ':attribute harus bernilai ya atau tidak.',
    'numeric' => ':attribute harus berupa angka.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'in' => ':attribute tidak valid.',
    'max' => [
        'string' => ':attribute maksimal :max karakter.',
        'numeric' => ':attribute maksimal :max.',
        'file' => ':attribute maksimal :max kilobyte.',
    ],
    'min' => [
        'string' => ':attribute minimal :min karakter.',
        'numeric' => ':attribute minimal :min.',
        'file' => ':attribute minimal :min kilobyte.',
    ],
    'attributes' => [
        'name' => 'nama lengkap',
        'email' => 'email',
        'phone' => 'nomor WhatsApp',
        'password' => 'kata sandi',
        'password_confirmation' => 'ulangi kata sandi',
        'current_password' => 'kata sandi saat ini',
        'token' => 'token',
    ],
];
