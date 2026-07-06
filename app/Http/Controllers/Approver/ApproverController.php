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
            ->selectRaw('batch_id, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, SUM(dana_telu) as dana_telu, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->where('status', 'proposed')
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
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
            ->selectRaw('batch_id, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, SUM(dana_telu) as dana_telu, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->whereIn('status', ['approved_kaur', 'approved', 'rejected_kaur', 'rejected_kabag'])
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('approver.kaur_riwayat', compact('pencairans'));
    }

    // ==========================================
    // KABAG WORKFLOW
    // ==========================================
    public function dashboardKabag()
    {
        $pencairans = PencairanDana::with(['pengelola'])
            ->selectRaw('batch_id, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, SUM(dana_telu) as dana_telu, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->where('status', 'approved_kaur')
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
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
            ->selectRaw('batch_id, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, SUM(dana_telu) as dana_telu, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->whereIn('status', ['approved', 'rejected_kabag'])
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('approver.kabag_riwayat', compact('pencairans'));
    }

    // ==========================================
    // SHARED APPROVER METHODS
    // ==========================================
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
    public function kantin()
    {
        $kantins = Kantin::latest()->paginate(10);
        return view('approver.kantin', compact('kantins'));
    }

    public function tenant()
    {
        $tenants = Tenant::with('kantin', 'user')->latest()->paginate(10);
        return view('approver.tenant', compact('tenants'));
    }

    public function orders()
    {
        $orders = Order::with('tenant', 'user', 'items')->latest()->paginate(15);
        return view('approver.orders', compact('orders'));
    }
}
