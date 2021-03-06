<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\GlobalProductIdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private $product_id;
    public function __construct(GlobalProductIdService $product_id)
    {
        $this->product_id = $product_id;
    }

    public function countStatus()
    {
        if ($this->product_id->get()) {
            $leads = Lead::select('status_admin', DB::raw('count(*) as total'))
                 ->groupBy('status_admin')
                 ->where('product_id', $this->product_id->get())
                 ->get();
        } else {
            $leads = Lead::select('status_admin', DB::raw('count(*) as total'))
                 ->groupBy('status_admin')
                 ->get();
        }
        return $leads;
    }

    public function filterSearch(Request $request)
    {
        $leads = Lead::select('status_admin', DB::raw('count(*) as total'))
                 ->groupBy('status_admin');

        $startDate = date('Y-m-d', strtotime($request['startDate']));
        $endDate = date('Y-m-d', strtotime($request['endDate']));

        if (!$request['productFilter'] && $this->product_id->get()) {
            $leads->where('product_id', $this->product_id->get());
        }

        if ($request['startDate'] && $request['endDate']) {
            $leads->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
        }

        if ($request['productFilter']) {
            $leads->where('product_id', $request['productFilter']);
        }

        if ($request['supplierFilter']) {
            $leads->where('supplier_id', $request['supplierFilter']);
        }

        return $leads->get();
    }
}
