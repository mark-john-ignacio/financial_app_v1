<?php

namespace App\Controllers\NavMenus;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\NavMenus\NavMenuModel;

class NavMenuController extends BaseController
{
    protected $navMenuModel;

    public function __construct()
    {
        $this->navMenuModel = new NavMenuModel();
        $this->view = 'NavMenus/';
    }
    public function index()
    {
        return view($this->view . 'index');
    }

    public function getMenus()
    {
        $menus = $this->navMenuModel->findAll();
        return $this->response->setJSON($menus);
    }

    public function toggleStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $result = $this->navMenuModel->update($id, ['cstatus' => $status]);
        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Status updated successfully']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update status']);
        }
    }
}
