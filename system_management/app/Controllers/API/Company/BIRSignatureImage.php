<?php

namespace App\Controllers\API\Company;

use App\Controllers\BaseController;
use App\Models\API\Company\CompanyModel;
use CodeIgniter\API\ResponseTrait;


class BIRSignatureImage extends BaseController
{
    use ResponseTrait;

    private $companyModel;
    private $imageService;

    public function __construct()
    {
        $this->companyModel = model(CompanyModel::class);
        $this->imageService = service('imageService');
    }

    public function create($id)
    {
        $company = $this->getCompanyOr404($id);

        try {
            $file = $this->request->getFile("image");
            $this->imageService->validateImage($file, 'signature');

            $oldImage = $company->signature_image;
            $newImage = $this->imageService->saveImage($file, 'signature');

            $this->imageService->deleteImage($oldImage, 'signature');

            $company->image = $newImage;
            $result = $this->companyModel->protect(false)->update($id, ["bir_sig_sign" => $company->image]);

            if ($result) {
                $data = $this->companyModel->find($id)->toArray();
                return $this->respond($data,200, 'Image uploaded successfully');
            }

            return $this->fail('Image upload failed');

        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function show($id)
    {
        $company = $this->getCompanyOr404($id);

        if ($company->signature_image) {
            $this->imageService->outputImage($company->signature_image, 'signature');
        }else {
            return $this->failNotFound('Image not found');
        }
    }

    public function delete($id)
    {
        $company = $this->getCompanyOr404($id);

        $this->imageService->deleteImage($company->signature_image, 'signature');

        $company->image = null;
        $this->companyModel->protect(false)->update($id, ["bir_sig_sign" => $company->image]);

        return $this->respondDeleted('Image deleted successfully');
    }
    protected function getCompanyOr404($id)
    {
        $record = $this->companyModel->find($id);
        if ($record === null) {
            return $this->failNotFound('Customer not found');
        } else {
            return $record;
        }
    }

}
