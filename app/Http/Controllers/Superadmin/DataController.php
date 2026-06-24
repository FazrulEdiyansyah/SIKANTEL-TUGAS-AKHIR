<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\PencairanDana;

class DataController extends Controller
{
    public function kantin()
    {
        $kantins = Kantin::latest()->paginate(10);
        return view('superadmin.kantin.index', compact('kantins'));
    }

    public function tenant()
    {
        $tenants = Tenant::with('kantin', 'user')->latest()->paginate(10);
        return view('superadmin.tenant.index', compact('tenants'));
    }

    public function orders()
    {
        $orders = Order::with('tenant', 'user', 'items')->latest()->paginate(15);
        return view('superadmin.orders.index', compact('orders'));
    }

    public function pencairan()
    {
        $pencairans = PencairanDana::with('tenant')->latest()->paginate(15);
        return view('superadmin.pencairan.index', compact('pencairans'));
    }
}
