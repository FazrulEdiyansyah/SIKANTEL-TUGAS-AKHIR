<?php

namespace App\Http\Controllers;

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
        $pencairans = PencairanDana::with('tenant')->where('status', 'proposed')->latest()->paginate(15);
        return view('approver.kaur_dashboard', compact('pencairans'));
    }

    public function approveKaur(Request $request, $id)
    {
        $pencairan = PencairanDana::findOrFail($id);
        $pencairan->update([
            'status' => 'approved_kaur'
        ]);
        return redirect()->route('kaur.dashboard')->with('success', 'Pencairan Dana disetujui oleh Kaur.');
    }

    public function rejectKaur(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string']);
        $pencairan = PencairanDana::findOrFail($id);
        $pencairan->update([
            'status' => 'rejected_kaur',
            'catatan_kaur' => $request->catatan
        ]);
        return redirect()->route('kaur.dashboard')->with('error', 'Pencairan Dana ditolak oleh Kaur.');
    }

    // ==========================================
    // KABAG WORKFLOW
    // ==========================================
    public function dashboardKabag()
    {
        $pencairans = PencairanDana::with('tenant')->where('status', 'approved_kaur')->latest()->paginate(15);
        return view('approver.kabag_dashboard', compact('pencairans'));
    }

    public function approveKabag(Request $request, $id)
    {
        $pencairan = PencairanDana::findOrFail($id);
        $pencairan->update([
            'status' => 'approved' // final
        ]);
        return redirect()->route('kabag.dashboard')->with('success', 'Pencairan Dana disetujui oleh Kabag (Final).');
    }

    public function rejectKabag(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string']);
        $pencairan = PencairanDana::findOrFail($id);
        $pencairan->update([
            'status' => 'rejected_kabag',
            'catatan_kabag' => $request->catatan
        ]);
        return redirect()->route('kabag.dashboard')->with('error', 'Pencairan Dana ditolak oleh Kabag.');
    }

    // ==========================================
    // SHARED APPROVER METHODS
    // ==========================================
    public function showPencairan($id)
    {
        $pencairan = PencairanDana::with(['tenant.kantin', 'pengelola', 'details.menu'])->findOrFail($id);
        return view('approver.pencairan.show', compact('pencairan'));
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
