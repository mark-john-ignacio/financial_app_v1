<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Modules\WooCommerceWebhook\Models\WooCommerceProductMapping;
use Modules\WooCommerceWebhook\Services\WooCommerceService;

class AssignProductMapping extends Component
{
    public $myxfin_product_id;
    public $woocommerce_product_id;
    public $editId;
    public $wooProductName = '';
    public $isCheckingProduct = false;
    public $isRefreshingNames = false;
    protected $wooService;

    public function __construct()
    {
        $this->wooService = new WooCommerceService();
    }

    protected $listeners = ['editRow'];

    public function editRow($params)
    {
        $id = is_array($params) ? ($params['id'] ?? null) : $params;

        if (!$id) {
            return;
        }

        $mapping = WooCommerceProductMapping::find($id);

        if (!$mapping) {
            return;
        }

        $this->editId = $id;
        $this->myxfin_product_id = $mapping->myxfin_product_id;
        $this->woocommerce_product_id = $mapping->woocommerce_product_id;
        $this->wooProductName = $this->wooService->getProductName($this->woocommerce_product_id);

        $this->dispatch('showModal');
    }

    public function checkProductName()
    {
        $this->isCheckingProduct = true;
        if(!empty($this->woocommerce_product_id)) {
            $this->wooProductName = $this->wooService->getProductName($this->woocommerce_product_id);
        }
        $this->isCheckingProduct = false;
    }

    public function refreshAllProductNames()
    {
        $this->isRefreshingNames = true;
        $mappings = WooCommerceProductMapping::all();
        foreach($mappings as $mapping) {
            if($mapping->woocommerce_product_id) {
                Cache::forget("woo_product_{$mapping->woocommerce_product_id}");
            }
        }
        $this->isRefreshingNames = false;
        $this->dispatch('refreshTable');
    }

    public function update()
    {
        $this->validate([
            'woocommerce_product_id' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail){
                    $exists = WooCommerceProductMapping::where('woocommerce_product_id', $value)
                        ->where('id','!=', $this->editId)
                        ->exists();

                    if($exists){
                        $fail('This WooCommerce Product ID is already assigned to another item.');
                    }
                }
            ]
        ]);
        $mapping = WooCommerceProductMapping::findOrFail($this->editId);
        $mapping->myxfin_product_id = $this->myxfin_product_id;
        $mapping->woocommerce_product_id = $this->woocommerce_product_id;
        $mapping->save();

        $this->dispatch('hideModal');
        $this->dispatch('refreshTable');

        $this->reset(['myxfin_product_id', 'woocommerce_product_id', 'editId']);
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

        $productIds = $mappings->pluck('woocommerce_product_id')
        ->filter()
        ->unique()
        ->values()
        ->toArray();

        $productNames = !empty($productIds) ?
        $this->wooService->getProductNames($productIds) :
        [];

        return response()->json([
            'data' => $mappings->map(function($mapping) use ($productNames) {
                return [
                    'id' => $mapping->id,
                    'myxfin_product_id' => $mapping->myxfin_product_id,
                    'myxfin_product_code' => $mapping->item->cpartno,
                    'myx_product_name' => $mapping->item->citemdesc,
                    'woocommerce_product_id' => $mapping->woocommerce_product_id,
                    'woo_product_name' => $mapping->woocommerce_product_id ?
                        ($productNames[$mapping->woocommerce_product_id] ?? 'Not Found') :
                        'Not Assigned',
                ];
            }),
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ]);
    }
}
