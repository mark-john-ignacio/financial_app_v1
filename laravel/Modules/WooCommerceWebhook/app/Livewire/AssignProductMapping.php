<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use Modules\WooCommerceWebhook\Models\WooCommerceProductMapping;

class AssignProductMapping extends Component
{

    public $myxfin_product_id;
    public $woocommerce_product_id;
    public $editId;
    
    // Add these methods
    public function edit($id)
    {
        $mapping = WooCommerceProductMapping::findOrFail($id);
        $this->editId = $id;
        $this->myxfin_product_id = $mapping->myxfin_product_id;
        $this->woocommerce_product_id = $mapping->woocommerce_product_id;
    }
    
    public function update()
    {
        $mapping = WooCommerceProductMapping::findOrFail($this->editId);
        $mapping->myxfin_product_id = $this->myxfin_product_id;
        $mapping->woocommerce_product_id = $this->woocommerce_product_id;
        $mapping->save();
    
        $this->dispatch('refreshTable');
        $this->dispatch('closeModal');
    }


    public function render()
    {
        return view('woocommercewebhook::livewire.assign-product-mapping')
            ->layout('woocommercewebhook::layouts.app', ['title' => 'Assign Product Mapping']);
    }

    public function getData(Request $request)
    {
        $columns = [
            'myxfin_product_id',
            'woocommerce_product_id',
        ];

        $length = $request->input('length');
        $start = $request->input('start');
        $column = $request->input('order.0.column');
        $dir = $request->input('order.0.dir') ?: 'desc';
        $searchValue = $request->input('search')['value'] ?? '';

        if (!isset($columns[$column])) {
            $column = 0;
        }

        $query = WooCommerceProductMapping::with('item')
            ->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('myxfin_product_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('woocommerce_product_id', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('item', function($query) use ($searchValue) {
                        $query->where('citemdesc', 'like', '%' . $searchValue . '%')
                            ->orWhere('cpartno', 'like', '%' . $searchValue . '%');
                    });
            });
        }

        $total = $query->count();
        $mappings = $query->skip($start)->take($length)->get();

        return response()->json([
            'data' => $mappings->map(function($mapping) {
                return [
                    'id' => $mapping->id,
                    'myxfin_product_id' => $mapping->myxfin_product_id,
                    'myxfin_product_code' => $mapping->item->cpartno,
                    'myx_product_name' => $mapping->item->citemdesc,
                    'woocommerce_product_id' => $mapping->woocommerce_product_id,
                    'woo_product_name' => $mapping->woocommerce_product_id ? 'Product Name' : 'Not Assigned',
                ];
            }),
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ]);
    }
}
