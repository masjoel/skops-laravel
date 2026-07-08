<?php

return [
    'required' => ':attribute wajib diisi.',
    'unique' => ':attribute sudah digunakan.',
    'max' => [
        'string' => ':attribute maksimal :max karakter.',
        'numeric' => ':attribute maksimal :max.',
    ],
    'min' => [
        'string' => ':attribute minimal :min karakter.',
        'numeric' => ':attribute minimal :min.',
    ],
    'numeric' => ':attribute harus berupa angka.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'mimes' => ':attribute harus berupa file dengan tipe: :values.',
    'image' => ':attribute harus berupa gambar.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'nama_jabatan' => 'Nama jabatan',
        'nama' => 'Nama',
        'email' => 'Email',
        'password' => 'Password',
    ],
];
