<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Livewire\Component;
use Modules\WooCommerceWebhook\Models\WooCommerceAudit;
use Illuminate\Http\Request;

class AuditList extends Component
{
    public function render()
    {
        return view('woocommercewebhook::livewire.audit-list')
            ->layout('woocommercewebhook::layouts.app', ['title' => 'WooCommerce Audits']);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'status',
            'error_message',
            'request_data',
            'response_data',
            'created_at'
        ];
    
        $length = $request->input('length');
        $start = $request->input('start');
        $column = $request->input('order.0.column');
        $dir = $request->input('order.0.dir') ?: 'desc';
        $searchValue = $request->input('search')['value'] ?? '';
    
        if (!isset($columns[$column])) {
            $column = 0;
        }
    
        $query = WooCommerceAudit::query()
            ->orderBy($columns[$column], $dir);
    
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('status', 'like', '%' . $searchValue . '%')
                    ->orWhere('error_message', 'like', '%' . $searchValue . '%')
                    ->orWhere('request_data', 'like', '%' . $searchValue . '%')
                    ->orWhere('response_data', 'like', '%' . $searchValue . '%');
            });
        }
    
        $total = $query->count();
        $audits = $query->skip($start)->take($length)->get();
    
        return response()->json([
            'data' => $audits->map(function($audit) {
                return [
                    'id' => $audit->id,
                    'status' => $audit->status,
                    'error_message' => $audit->error_message,
                    'request_data' => $audit->request_data,
                    'response_data' => $audit->response_data,
                    'created_at' => $audit->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ]);
    }
}