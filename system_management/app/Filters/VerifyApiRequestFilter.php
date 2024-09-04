<?php

namespace App\Filters;
use App\Models\API\Company\CompanyModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\API\ResponseTrait;

class VerifyApiRequestFilter implements FilterInterface
{
    use ResponseTrait;
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$this->isValidApiKey($request)) {
            return service('response')->setStatusCode(403)->setJSON(['error' => 'Invalid API Key']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }

    private function isValidApiKey($request)
    {
        $model = new CompanyModel();
        $apiSecret = $model->find($request->getHeaderLine('COMPANY-ID'))->bir_sig_tin;
        $apiKey = $request->getHeaderLine('API-KEY');

        return $apiKey === $apiSecret;
    }
}