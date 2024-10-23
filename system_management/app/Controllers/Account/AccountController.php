<?php

namespace App\Controllers\Account;


use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\Files\File;
use App\Entities\Account\AccountEntity;
use App\Models\Account\Account;


class AccountController extends BaseController
{

    protected $accountModel;
    protected $view;
    protected $company_code;
    protected $user_id;
    private AccountEntity $accountEntity;

    public function __construct()
    {
        $this->accountModel = new Account();
        $this->view = 'Accounts/'
    }

    public function index()
    {
        //
    }
}
