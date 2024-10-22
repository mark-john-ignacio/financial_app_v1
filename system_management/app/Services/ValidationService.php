<?php

namespace App\Services;

use CodeIgniter\Validation\Validation;

class ValidationService
{
    private $validation;

    public function __construct(Validation $validation)
    {
        $this->validation = $validation;
    }

    public function validateUploadedFile(): bool
    {
        $rules = [
            'userfile' => [
                'label' => 'Excel File',
                'rules' => [
                    'uploaded[userfile]',
                    'ext_in[userfile,xlsx,xls]',
                    'mime_in[userfile,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel]',
                    'max_size[userfile,2048]',
                ],
            ],
        ];

        return $this->validation->setRules($rules)->run($_FILES);
    }

    public function getErrors(): array
    {
        return $this->validation->getErrors();
    }
}