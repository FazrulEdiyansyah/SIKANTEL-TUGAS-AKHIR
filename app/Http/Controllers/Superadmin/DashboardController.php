<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Order;
use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\PencairanDana;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalKantin = Kantin::count();
        $totalTenant = Tenant::count();
        $totalPencairan = PencairanDana::count();
        
        return view('superadmin.dashboard', compact('totalUsers', 'totalOrders', 'totalKantin', 'totalTenant', 'totalPencairan'));
    }
}
