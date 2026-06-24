<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Order;
use App\Models\Kantin;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalKantin = Kantin::count();
        $totalTenant = Tenant::count();
        $pendingOrders = Order::where('order_status', 'diproses')->count();
        
        return view('superadmin.dashboard', compact('totalUsers', 'totalOrders', 'totalKantin', 'totalTenant', 'pendingOrders'));
    }
}
