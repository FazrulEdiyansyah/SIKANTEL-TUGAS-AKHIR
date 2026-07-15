<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Models\PencairanDana;
use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\Order;
use Illuminate\Http\Request;

class ApproverController extends Controller
{
    // ==========================================
    // KAUR WORKFLOW
    // ==========================================
    public function dashboardKaur()
    {
        $pencairans = PencairanDana::with(['pengelola'])
            ->selectRaw('batch_id, MAX(judul) as judul, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, SUM(dana_telu) as dana_telu, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->where('status', 'proposed')
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $this->assignKeteranganKantin($pencairans);
            
        return view('approver.kaur_dashboard', compact('pencairans'));
    }

    public function approveKaur(Request $request, $batch_id)
    {
        $updated = PencairanDana::where('batch_id', $batch_id)
            ->where('status', 'proposed')
            ->update([
                'status' => 'approved_kaur'
            ]);
            
        if (!$updated) {
            return redirect()->route('kaur.dashboard')->with('error', 'Laporan tidak dapat disetujui. Pastikan statusnya masih dalam antrean.');
        }
            
        return redirect()->route('kaur.dashboard')->with('success', 'Laporan (Batch) disetujui oleh Kaur.');
    }

    public function rejectKaur(Request $request, $batch_id)
    {
        $request->validate(['catatan' => 'required|string']);
        $updated = PencairanDana::where('batch_id', $batch_id)
            ->where('status', 'proposed')
            ->update([
                'status' => 'rejected_kaur',
                'catatan_kaur' => $request->catatan
            ]);
            
        if (!$updated) {
            return redirect()->route('kaur.dashboard')->with('error', 'Laporan tidak dapat ditolak. Pastikan statusnya masih dalam antrean.');
        }
        
        return redirect()->route('kaur.dashboard')->with('error', 'Laporan (Batch) ditolak oleh Kaur.');
    }

    public function riwayatKaur()
    {
        $pencairans = PencairanDana::with(['pengelola'])
            ->selectRaw('batch_id, MAX(judul) as judul, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, SUM(dana_telu) as dana_telu, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->whereIn('status', ['approved_kaur', 'approved', 'rejected_kaur', 'rejected_kabag'])
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $this->assignKeteranganKantin($pencairans);
            
        return view('approver.kaur_riwayat', compact('pencairans'));
    }

    // ==========================================
    // KABAG WORKFLOW
    // ==========================================
    public function dashboardKabag()
    {
        $pencairans = PencairanDana::with(['pengelola'])
            ->selectRaw('batch_id, MAX(judul) as judul, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, SUM(dana_telu) as dana_telu, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->where('status', 'approved_kaur')
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $this->assignKeteranganKantin($pencairans);
            
        return view('approver.kabag_dashboard', compact('pencairans'));
    }

    public function approveKabag(Request $request, $batch_id)
    {
        $updated = PencairanDana::where('batch_id', $batch_id)
            ->where('status', 'approved_kaur')
            ->update([
                'status' => 'approved' // final
            ]);
            
        if (!$updated) {
            return redirect()->route('kabag.dashboard')->with('error', 'Laporan tidak dapat disetujui. Pastikan statusnya valid.');
        }
            
        return redirect()->route('kabag.dashboard')->with('success', 'Laporan (Batch) disetujui oleh Kabag (Final).');
    }

    public function rejectKabag(Request $request, $batch_id)
    {
        $request->validate(['catatan' => 'required|string']);
        $updated = PencairanDana::where('batch_id', $batch_id)
            ->where('status', 'approved_kaur')
            ->update([
                'status' => 'rejected_kabag',
                'catatan_kabag' => $request->catatan
            ]);
            
        if (!$updated) {
            return redirect()->route('kabag.dashboard')->with('error', 'Laporan tidak dapat ditolak. Pastikan statusnya valid.');
        }
            
        return redirect()->route('kabag.dashboard')->with('error', 'Laporan (Batch) ditolak oleh Kabag.');
    }

    public function riwayatKabag()
    {
        $pencairans = PencairanDana::with(['pengelola'])
            ->selectRaw('batch_id, MAX(judul) as judul, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, SUM(dana_telu) as dana_telu, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->whereIn('status', ['approved', 'rejected_kabag'])
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $this->assignKeteranganKantin($pencairans);
            
        return view('approver.kabag_riwayat', compact('pencairans'));
    }

    // ==========================================
    // SHARED APPROVER METHODS
    // ==========================================
    private function assignKeteranganKantin($pencairans)
    {
        $batchIds = $pencairans->pluck('batch_id')->toArray();
        if(empty($batchIds)) return;
        $batchKantins = PencairanDana::whereIn('batch_id', $batchIds)
            ->join('tenants', 'pencairan_danas.tenant_id', '=', 'tenants.id')
            ->join('kantins', 'tenants.kantin_id', '=', 'kantins.id')
            ->select('pencairan_danas.batch_id', 'kantins.nama_kantin')
            ->distinct()
            ->get()
            ->groupBy('batch_id');

        foreach ($pencairans as $pencairan) {
            $kantins = $batchKantins->get($pencairan->batch_id);
            if ($kantins && $kantins->count() == 1) {
                $pencairan->keterangan_kantin = $kantins->first()->nama_kantin;
            } else {
                $pencairan->keterangan_kantin = 'Berbagai Kantin';
            }
        }
    }
    public function showPencairan($batch_id)
    {
        $pencairan_danas = PencairanDana::with(['tenant.kantin', 'pengelola'])
            ->where('batch_id', $batch_id)
            ->get();
            
        $batchInfo = $pencairan_danas->first();
        if (!$batchInfo) abort(404);
        
        return view('pengelola.pencairan-dana.show', compact('pencairan_danas', 'batchInfo', 'batch_id'));
    }

    public function generatePdf($id)
    {
        $pencairan = PencairanDana::with(['tenant.kantin', 'pengelola'])->findOrFail($id);
        
        $data = [
            'tenant' => $pencairan->tenant,
            'startDate' => $pencairan->start_date,
            'endDate' => $pencairan->end_date,
            'totalPenjualan' => $pencairan->total_penjualan,
            'danaTenant' => $pencairan->dana_tenant,
            'danaTelu' => $pencairan->dana_telu,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengelola.pencairan-dana.pdf', $data);
        return $pdf->download('Laporan-Pencairan-Dana-'.$pencairan->id.'.pdf');
    }

    // ==========================================
    // READ-ONLY DATA VIEWS
    // ==========================================
    public function kantin(Request $request)
    {
        $query = Kantin::withCount('tenants')->latest();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = strtolower($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->whereRaw('LOWER(nama_kantin) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(lokasi) LIKE ?', ["%{$search}%"]);
            });
        });

        $kantins = $query->paginate(10)->withQueryString();
        return view('approver.kantin', compact('kantins'));
    }

    public function tenant(Request $request)
    {
        $query = Tenant::with('kantin', 'user')->latest();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = strtolower($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->whereRaw('LOWER(nama_tenant) LIKE ?', ["%{$search}%"]);
            });
        });

        $query->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $tenants = $query->paginate(10)->withQueryString();
        return view('approver.tenant', compact('tenants'));
    }

    public function orders(Request $request)
    {
        $query = Order::with('tenant', 'user', 'items')->latest();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = strtolower($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->whereRaw('LOWER(order_id) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        });

        $query->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
            $q->where('order_status', $request->status);
        });

        $orders = $query->paginate(10)->withQueryString();
        return view('approver.orders', compact('orders'));
    }
}
