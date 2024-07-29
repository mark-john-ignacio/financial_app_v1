<?php

namespace App\Controllers\ItemCodeSync;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Items\ItemsModel;
use App\Models\ItemCodeSync\ItemsCopyModel;
use App\Models\ItemCodeSync\PurchaseReceivingItemsModel;

class ItemCodeSyncController extends BaseController
{
    private ItemsModel $itemsModel;
    private ItemsCopyModel $itemsCopyModel;
    private PurchaseReceivingItemsModel $purchaseReceivingItemsModel;

    public function __construct()
    {
        $this->itemsModel = new ItemsModel();
        $this->itemsCopyModel = new ItemsCopyModel();
        $this->purchaseReceivingItemsModel = new PurchaseReceivingItemsModel();
        $this->view = "ItemCodeSync/";
    }

    public function index(){
        $data = [
            'title' => 'Item Code Sync'
        ];
        return view($this->view . 'index', $data);
    }
    public function mapItemCodes()
    {
        $itemsCopies = $this->itemsCopyModel->findAll();
        $mapping=[];

        //For exact matches
        foreach ($itemsCopies as $oldItem){
            $newItem = $this->itemsModel->where('citemdesc', $oldItem->citemdesc)->first();
            if($newItem){
                $this->_addMapping($mapping, $oldItem, $newItem, 'exact');
            }
        }

        // Partial matches (e.g., ADOLPHII with SKU:SMALL to ADOLPHII S)
        foreach ($itemsCopies as $oldItem){
            if(!isset($mapping[$oldItem->cpartno])){
                $newItem = $this->itemsModel->like('citemdesc', $oldItem->citemdesc)
                ->where('cskucode', 'SMALL')
                ->first();
                if($newItem){
                    $this->_addMapping($mapping, $oldItem, $newItem, 'partial');
                }
            }
        }

        return $this->response->setJSON($mapping);

    }

    private function _addMapping(&$mapping, $oldItem, $newItem, $matchType)
    {
        $mapping[] = [
            'old_code' => $oldItem->cpartno,
            'old_item_desc' => $oldItem->citemdesc,
            'sku_code' => $oldItem->cskucode,
            'new_code' => $newItem->cpartno,
            'new_item_desc' => $newItem->citemdesc,
            'match_type' => $matchType
        ];
    }
}
