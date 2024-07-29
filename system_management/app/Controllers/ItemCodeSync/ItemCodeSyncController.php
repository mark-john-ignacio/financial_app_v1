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
    }

    public function mapItemCodes()
    {
        $itemsCopies = $this->itemsCopyModel->findAll();
        $mapping=[];

        //For exact matches
        foreach ($itemsCopies as $oldItem){
            $newItem = $this->itemsModel->where('cpartno', $oldItem->cpartno)->first();
            if($newItem){
                $mapping[] = [
                    'old_code' => $oldItem->cpartno,
                    'item_desc' => $oldItem->citemdesc,
                    'new_code' => $newItem->cpartno,
                    'new_item_desc' => $newItem->citemdesc,
                    'match_type' => 'exact',
                ];
            }
        }

        // Partial matches (e.g., ADOLPHII B to ADOLPHII XL)
        foreach ($itemsCopies as $oldItem){
            if(!isset($mapping[$oldItem->cpartno])){
                $newItem = $this->itemsModel->like('cpartno', $oldItem->cpartno)->first();
                if($newItem){
                    $mapping[] = [
                        'old_code' => $oldItem->cpartno,
                        'item_desc' => $oldItem->citemdesc,
                        'new_code' => $newItem->cpartno,
                        'new_item_desc' => $newItem->citemdesc,
                        'match_type' => 'partial'
                    ];
                }
            }
        }

        return $this->response->setJSON($mapping);

    }
}
