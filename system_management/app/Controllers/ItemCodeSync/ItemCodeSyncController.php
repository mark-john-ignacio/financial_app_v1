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
        $skuMapping = [
            'SMALL' => 'S',
            'MEDIUM' => 'M',
            'LARGE' => 'L',
            'BIG' => 'XL',
        ];

        foreach ($itemsCopies as $oldItem){
            $exists = false;
            foreach ($mapping as $map){
                if ($map['old_code'] == $oldItem->cpartno){
                    $exists = true;
                    break;
                }
            }
            if (!$exists){
                $suffix = isset($skuMapping[$oldItem->cskucode]) ? $skuMapping[$oldItem->cskucode] : '';
                if ($suffix) {
                    $newItem = $this->itemsModel->where('citemdesc', $oldItem->citemdesc . ' ' . $suffix)->first();
                    if ($newItem) {
                        $this->_addMapping($mapping, $oldItem, $newItem, 'partial using sku code');
                    }
                }
            }
        }

        foreach ($itemsCopies as $oldItem) {
            $exists = false;
            foreach ($mapping as $map) {
                if ($map['old_code'] == $oldItem->cpartno) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $suffix = isset($skuMapping[$oldItem->cskucode]) ? $skuMapping[$oldItem->cskucode] : '';
                if ($suffix) {
                    $cleanedOldItemDesc = $this->removeSuffix($oldItem->citemdesc);
                    //dd($cleanedOldItemDesc);
                    $newItem = $this->itemsModel->where('citemdesc', $cleanedOldItemDesc . ' ' . $suffix)->first();
                    if ($newItem) {
                        $this->_addMapping($mapping, $oldItem, $newItem, 'partial using sku code and remove suffix');
                    }
                }
            }
        }

        // remaining unmatched items
        foreach ($itemsCopies as $oldItem){
            $exists = false;
            foreach ($mapping as $map){
                if ($map['old_code'] == $oldItem->cpartno){
                    $exists = true;
                    break;
                }
            }
            if (!$exists){
                $newItem = $this->itemsModel->first();
                if ($newItem){
                    $this->_addMapping($mapping, $oldItem, $newItem, 'unmatched');
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

    private function removeSuffix($itemDesc)
    {
        return preg_replace('/\s(S|M|L|B)$/', '', $itemDesc);
    }

    //replace item codes on receiving items(receive_t)
    public function replaceItemCodes()
    {
        $mapping = $this->request->getJSON();
        $updated = 0;
        foreach ($mapping as $map){
            $oldItem = $this->itemsCopyModel->where('cpartno', $map->old_code)->first();
            $newItem = $this->itemsModel->where('cpartno', $map->new_code)->first();
            if ($oldItem && $newItem){
                $result = $this->purchaseReceivingItemsModel->where('citemno', $oldItem->cpartno)
                ->set([
                    'citemno' => $newItem->cpartno,
                    'creference' => $oldItem->cpartno . ' -> ' . $newItem->cpartno
                ])
                ->update();
                $affectedRows = $this->purchaseReceivingItemsModel->affectedRows();

                if ($affectedRows > 0) {
                    $updated += $affectedRows;
                }
            }
        }
        return $this->response->setJSON(['updated' => $updated]);
    }
}
