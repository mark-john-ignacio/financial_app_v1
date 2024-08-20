<?php

namespace App\Controllers\Testing;

use App\Controllers\BaseController;
use CodeIgniter\Files\File;

class Upload extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        return view('Testing/upload_form', ['errors' => []]);
    }

    public function upload()
    {
        $validationRule = [
            'userfile' => [
                'label' => 'PDF File',
                'rules' => [
                    'uploaded[userfile]',
                    'mime_in[userfile,application/pdf]',
                    'max_size[userfile,2048]', // Example: Increase max size to 2MB (2048KB)
                ],
            ],
        ];
        if (! $this->validateData([], $validationRule)) {
            $data = ['errors' => $this->validator->getErrors()];

            return view('Testing/upload_form', $data);
        }

        $img = $this->request->getFile('userfile');

        if (! $img->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/' . $img->store();

            $data = ['uploaded_fileinfo' => new File($filepath)];

            return view('Testing/upload_success', $data);
        }

        $data = ['errors' => 'The file has already been moved.'];

        return view('Testing/upload_form', $data);
    }
}