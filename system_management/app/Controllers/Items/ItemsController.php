<?php

namespace App\Controllers\Items;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\Files\File;
use App\Models\Items\ItemsModel;

class ItemsController extends BaseController
{
    protected $itemsModel;
    protected $view;
    protected $company_code;
    protected $user_id;

    public function __construct()
    {
        $this->itemsModel = new ItemsModel();
        $this->view = 'Items/';
        $this->company_code = session()->get('company_code');
        $this->user_id = session()->get('user_id');

    }
    public function index()
    {
        return view($this->view . 'index');
    }
}
