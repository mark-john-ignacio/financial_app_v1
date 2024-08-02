<?php

namespace App\Controllers\BIRForms;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BIRForms\BIRFormModel;
use App\Models\BIRForms\BIRFormImageModel;
use App\Entities\BIRForms\BIRFormImageEntity;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class BIRFormImage extends BaseController
{
    private BIRFormImageModel $birFormImageModel;
    private BIRFormModel $birFormModel;

    public function __construct()
    {
        $this->model = new BIRFormImageModel();
        $this->birFormModel = new BIRFormModel();
        $this->view = 'BIRForms/Form/Image/';
    }
    public function index()
    {
        //
    }

    public function new($form_id)
    {
        $form_image = $this->getItemOr404($form_id);
        $form = $this->birFormModel->find($form_id);
        $data = [
            'form_image' => $form_image,
            'form' => $form,
            "title" => "Edit Form Image"
        ];
        return view($this->view.'new', $data);
    }

    public function create($form_id)
    {
        $form = $this->birFormModel->find($form_id);
        if ($form === null){
            throw new PageNotFoundException("Form with $form_id id not found");
        }

        $file = $this->request->getFile("form_image");
        if (! $file->isValid()){

            $error_code = $file->getError();
            if ($error_code === UPLOAD_ERR_NO_FILE){
                return redirect()->back()
                                ->with("errors", ["No file selected"]);

            }

            throw new RuntimeException($file->getErrorString(). " " . $error_code);
        }

        if ($file->getSizeByUnit("mb") > 2 ){
            return redirect()->back()
                ->with("errors", "File too large. Max size is 2MB");
        }
        if (! in_array($file->getMimeType(), ["image/jpeg", "image/png"])){
            return redirect()->back()
                ->with("errors", "Invalid file type. Only JPEG and PNG allowed");
        }

        $path = $file->store("bir_form_images");
        $path = WRITEPATH . "uploads/" . $path;

    }

    private function getItemOr404($id): BIRFormImageEntity
    {
        $item = $this->model->where('form_id', $id)->first();
        if ($item === null){
            throw new PageNotFoundException("Form Image with $id id not found");
        }

        return $item;
    }
}
