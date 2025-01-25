<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use Modules\WooCommerceWebhook\Models\WooCommerceProductMapping;

class AssignProductMapping extends Component
{



    public function render()
    {
        return view('woocommercewebhook::livewire.assign-product-mapping');
    }

    public function getData(Request $request)
    {
        $columns = [
            'myxfin_product_id',
            'myx_product_name',
            'woocommerce_product_id',
            'woo_product_name',
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
                $query->where('myx_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('myx_product_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('woo_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('woo_product_name', 'like', '%' . $searchValue . '%');
            });
        }

        $total = $query->count();
        $mappings = $query->skip($start)->take($length)->get();

        return response()->json([
            'data' => $mappings->map(function($mapping) {
                return [
                    'myxfin_product_id' => $mapping->myxfin_product_id,
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
