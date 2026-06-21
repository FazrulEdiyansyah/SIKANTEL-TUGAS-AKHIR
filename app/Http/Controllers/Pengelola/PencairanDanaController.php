<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PencairanDana;
use App\Models\Tenant;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PencairanDanaController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'draft'); // Default tab is draft

        $pencairan_danas = PencairanDana::with(['tenant.kantin', 'pengelola'])
            ->where('status', $status)
            ->latest()
            ->paginate(10)
            ->appends(['status' => $status]);

        return view('pengelola.pencairan-dana.index', compact('pencairan_danas', 'status'));
    }

    public function create()
    {
        $tenants = Tenant::with('kantin')->where('status', 'aktif')->get();
        return view('pengelola.pencairan-dana.create', compact('tenants'));
    }

    public function calculateSales(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tenantId = $request->tenant_id;
        // Parse dates. Since user might pick a range "d M Y", we parse it carefully if needed.
        // But the JS should send Y-m-d format to this endpoint.
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $totalPenjualan = Order::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        $danaTenant = $totalPenjualan * 0.70;
        $danaTelu = $totalPenjualan * 0.30;

        $tenant = Tenant::with('kantin')->find($tenantId);

        return response()->json([
            'total_penjualan' => $totalPenjualan,
            'dana_tenant' => $danaTenant,
            'dana_telu' => $danaTelu,
            'tenant_name' => $tenant->nama_tenant,
            'tenant_foto' => $tenant->foto ? asset('storage/'.$tenant->foto) : null,
            'kantin_name' => $tenant->kantin ? $tenant->kantin->nama_kantin : '-',
            'formatted_penjualan' => 'Rp ' . number_format($totalPenjualan, 0, ',', '.'),
            'formatted_dana_tenant' => 'Rp ' . number_format($danaTenant, 0, ',', '.'),
            'formatted_dana_telu' => 'Rp ' . number_format($danaTelu, 0, ',', '.')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'approver_name' => 'required|string',
            'date_range' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // date_range is expected to be "01 Jun 2024 - 07 Jun 2024" or similar
        // Let's parse it safely
        $dates = explode(' - ', $request->date_range);
        if (count($dates) != 2) {
            return back()->withErrors(['date_range' => 'Format tanggal tidak valid.'])->withInput();
        }

        try {
            $startDate = Carbon::createFromFormat('d M Y', trim($dates[0]))->startOfDay();
            $endDate = Carbon::createFromFormat('d M Y', trim($dates[1]))->endOfDay();
        } catch (\Exception $e) {
            // fallback
            try {
                $startDate = Carbon::parse(trim($dates[0]))->startOfDay();
                $endDate = Carbon::parse(trim($dates[1]))->endOfDay();
            } catch (\Exception $e) {
                return back()->withErrors(['date_range' => 'Format tanggal gagal diproses.'])->withInput();
            }
        }

        // Calculate again securely
        $totalPenjualan = Order::where('tenant_id', $request->tenant_id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        $danaTenant = $totalPenjualan * 0.70;
        $danaTelu = $totalPenjualan * 0.30;

        PencairanDana::create([
            'pengelola_id' => Auth::id(),
            'tenant_id' => $request->tenant_id,
            'approver_name' => $request->approver_name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_penjualan' => $totalPenjualan,
            'dana_tenant' => $danaTenant,
            'dana_telu' => $danaTelu,
            'keterangan' => $request->keterangan,
            'status' => 'proposed', // we can set it to proposed immediately when submitted
        ]);

        return redirect()->route('pengelola.pencairan_dana.index', ['status' => 'proposed'])
            ->with('success', 'Laporan pencairan dana berhasil diajukan.');
    }
}
