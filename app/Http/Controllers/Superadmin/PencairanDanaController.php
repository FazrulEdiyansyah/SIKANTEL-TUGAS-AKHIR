<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PencairanDana;
use App\Models\Tenant;
use App\Models\Order;
use Carbon\Carbon;

class PencairanDanaController extends Controller
{
    public function index(Request $request)
    {
        $query = PencairanDana::with(['tenant.kantin', 'pengelola'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function($q) use ($search) {
                $q->where('nama_tenant', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $pencairan_danas = $query->paginate(10)->withQueryString();

        return view('superadmin.pencairan.index', compact('pencairan_danas'));
    }

    public function create()
    {
        $tenants = Tenant::with('kantin')->where('status', 'aktif')->get();
        return view('superadmin.pencairan.create', compact('tenants'));
    }

    public function calculateSales(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tenantId = $request->tenant_id;
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $orders = Order::where('tenant_id', $tenantId)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalPenjualan = $orders->sum('total_price');
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
            'date_range' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        $dates = explode(' - ', $request->date_range);
        if (count($dates) != 2) {
            return back()->withErrors(['date_range' => 'Format tanggal tidak valid'])->withInput();
        }

        $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
        $endDate = \Carbon\Carbon::parse($dates[1])->endOfDay();

        $exists = PencairanDana::where('tenant_id', $request->tenant_id)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->whereNotIn('status', ['rejected_kaur', 'rejected_kabag'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['tenant_id' => 'Periode ini sudah pernah diajukan untuk tenant tersebut dan masih diproses/selesai.'])->withInput();
        }

        $orders = Order::with('items')
            ->where('tenant_id', $request->tenant_id)
            ->where('order_status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalPenjualan = $orders->sum('total_price');

        if ($totalPenjualan <= 0) {
            return back()->withErrors(['date_range' => 'Tidak ada penjualan pada periode ini.'])->withInput();
        }

        $danaTenant = $totalPenjualan * 0.70;
        $danaTelu = $totalPenjualan * 0.30;

        $status = $request->input('action', 'draft') === 'draft' ? 'draft' : 'proposed';

        $pencairan = PencairanDana::create([
            'tenant_id' => $request->tenant_id,
            'pengelola_id' => auth()->id(), // Superadmin as pengelola initiator
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_penjualan' => $totalPenjualan,
            'dana_tenant' => $danaTenant,
            'dana_telu' => $danaTelu,
            'keterangan' => $request->keterangan,
            'status' => $status
        ]);

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                \App\Models\PencairanDanaDetail::create([
                    'pencairan_dana_id' => $pencairan->id,
                    'menu_id' => $item->menu_id,
                    'qty' => $item->qty,
                    'subtotal' => $item->subtotal,
                ]);
            }
        }

        $message = $status === 'draft' ? 'Laporan berhasil disimpan sebagai Draft.' : 'Laporan berhasil diajukan ke Kaur.';
        return redirect()->route('superadmin.pencairan.index', ['status' => $status])->with('success', $message);
    }

    public function show($id)
    {
        $pencairan = PencairanDana::with(['tenant.kantin', 'pengelola', 'details.menu'])->findOrFail($id);
        return view('superadmin.pencairan.show', compact('pencairan'));
    }

    public function propose($id)
    {
        $pencairan = PencairanDana::findOrFail($id);
        if (!in_array($pencairan->status, ['draft', 'rejected_kaur', 'rejected_kabag'])) {
            return back()->with('error', 'Hanya laporan draft atau rejected yang dapat diajukan kembali.');
        }

        $pencairan->update(['status' => 'proposed']);
        return back()->with('success', 'Laporan berhasil diajukan.');
    }

    public function approve($id)
    {
        $pencairan = PencairanDana::findOrFail($id);
        
        if ($pencairan->status === 'proposed') {
            $pencairan->update(['status' => 'approved_kaur']);
            return back()->with('success', 'Laporan disetujui (Tahap 1 - Kaur).');
        } elseif ($pencairan->status === 'approved_kaur') {
            $pencairan->update(['status' => 'completed']);
            return back()->with('success', 'Laporan disetujui sepenuhnya dan selesai.');
        }

        return back()->with('error', 'Status laporan tidak valid untuk disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $pencairan = PencairanDana::findOrFail($id);
        
        if ($pencairan->status === 'proposed') {
            $pencairan->update(['status' => 'rejected_kaur']);
            return back()->with('success', 'Laporan ditolak (Tahap 1 - Kaur).');
        } elseif ($pencairan->status === 'approved_kaur') {
            $pencairan->update(['status' => 'rejected_kabag']);
            return back()->with('success', 'Laporan ditolak (Tahap 2 - Kabag).');
        }

        return back()->with('error', 'Status laporan tidak valid untuk ditolak.');
    }

    public function destroy($id)
    {
        $pencairan = PencairanDana::findOrFail($id);
        
        $pencairan->details()->delete();
        $pencairan->delete();

        return redirect()->route('superadmin.pencairan.index')->with('success', 'Laporan pencairan berhasil dihapus.');
    }

    public function generatePdf(Request $request)
    {
        $tenantId = $request->query('tenant_id');
        $dateRange = $request->query('date_range');

        if (!$tenantId || !$dateRange) {
            return back()->withErrors('Parameter tidak lengkap');
        }

        $dates = explode(' - ', $dateRange);
        $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
        $endDate = \Carbon\Carbon::parse($dates[1])->endOfDay();

        $tenant = Tenant::with('kantin')->findOrFail($tenantId);
        
        $orders = Order::where('tenant_id', $tenantId)
            ->where('order_status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalPenjualan = $orders->sum('total_price');
        $danaTenant = $totalPenjualan * 0.70;
        $danaTelu = $totalPenjualan * 0.30;

        $data = [
            'tenant' => $tenant,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalPenjualan' => $totalPenjualan,
            'danaTenant' => $danaTenant,
            'danaTelu' => $danaTelu,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengelola.pencairan-dana.pdf', $data);
        return $pdf->download('Preview-Laporan-Pencairan-Dana.pdf');
    }
}
