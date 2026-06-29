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

        $pencairan_danas = PencairanDana::with(['pengelola'])
            ->selectRaw('batch_id, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->when($status == 'proposed', function ($q) {
                return $q->whereIn('status', ['proposed', 'approved_kaur']);
            })
            ->when($status == 'rejected', function ($q) {
                return $q->whereIn('status', ['rejected_kaur', 'rejected_kabag']);
            })
            ->when(!in_array($status, ['proposed', 'rejected']), function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['status' => $status]);

        return view('pengelola.pencairan-dana.index', compact('pencairan_danas', 'status'));
    }

    public function create()
    {
        $tenants = Tenant::with('kantin')->where('status', 'aktif')->get();
        $approvers = \App\Models\User::whereIn('role', ['kaur', 'kabag'])->get();
        return view('pengelola.pencairan-dana.create', compact('tenants', 'approvers'));
    }

    public function calculateSales(Request $request)
    {
        $request->validate([
            'tenant_ids' => 'required|array',
            'tenant_ids.*' => 'exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tenantIds = $request->tenant_ids;
        // Parse dates. Since user might pick a range "d M Y", we parse it carefully if needed.
        // But the JS should send Y-m-d format to this endpoint.
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $results = [];

        foreach ($tenantIds as $tenantId) {
            $orders = Order::where('tenant_id', $tenantId)
                ->where('payment_status', 'success')
                ->where('order_status', 'selesai')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $totalPenjualan = $orders->sum('total_price');
            $danaTenant = $totalPenjualan * 0.70;
            $danaTelu = $totalPenjualan * 0.30;

            $tenant = Tenant::with('kantin')->find($tenantId);

            $results[] = [
                'tenant_id' => $tenantId,
                'total_penjualan' => $totalPenjualan,
                'dana_tenant' => $danaTenant,
                'dana_telu' => $danaTelu,
                'tenant_name' => $tenant->nama_tenant,
                'tenant_foto' => $tenant->foto ? asset('storage/'.$tenant->foto) : null,
                'kantin_name' => $tenant->kantin ? $tenant->kantin->nama_kantin : '-',
                'formatted_penjualan' => 'Rp ' . number_format($totalPenjualan, 0, ',', '.'),
                'formatted_dana_tenant' => 'Rp ' . number_format($danaTenant, 0, ',', '.'),
                'formatted_dana_telu' => 'Rp ' . number_format($danaTelu, 0, ',', '.')
            ];
        }

        return response()->json($results);
    }

    // ==========================================
    // ACTION: STORE PENCAIRAN DANA
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'tenant_ids' => 'required|array',
            'tenant_ids.*' => 'exists:tenants,id',
            'approver_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'keterangan' => 'nullable|string',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
        $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();

        $status = $request->input('action', 'draft') === 'draft' ? 'draft' : 'proposed';

        $hasError = false;
        $errorMessages = [];
        $createdCount = 0;
        $batchId = 'REQ-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

        foreach ($request->tenant_ids as $tenantId) {
            // Kalkulasi
            $orders = Order::with('items')
                ->where('tenant_id', $tenantId)
                ->where('payment_status', 'success')
                ->where('order_status', 'selesai')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $totalPenjualan = $orders->sum('total_price');

            $danaTenant = $totalPenjualan * 0.70;
            $danaTelu = $totalPenjualan * 0.30;

            $pencairan = PencairanDana::create([
                'batch_id' => $batchId,
                'tenant_id' => $tenantId,
                'pengelola_id' => auth()->id(),
                'approver_name' => $request->approver_name,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_penjualan' => $totalPenjualan,
                'dana_tenant' => $danaTenant,
                'dana_telu' => $danaTelu,
                'keterangan' => $request->keterangan,
                'status' => $status
            ]);

            // Simpan detail
            foreach ($orders as $order) {
                \App\Models\PencairanDanaDetail::create([
                    'pencairan_dana_id' => $pencairan->id,
                    'order_id' => $order->id,
                    'total_price' => $order->total_price,
                    'dana_tenant' => $order->total_price * 0.70,
                    'dana_telu' => $order->total_price * 0.30,
                ]);
            }
            $createdCount++;
        }

        if ($hasError && $createdCount === 0) {
            return back()->withErrors(['tenant_ids' => implode(' ', $errorMessages)])->withInput();
        }

        $message = $status === 'draft' ? "{$createdCount} Laporan berhasil disimpan sebagai Draft." : "{$createdCount} Laporan berhasil diajukan ke Approver.";
        if ($hasError) {
            $message .= " Beberapa tenant gagal diproses: " . implode(' ', $errorMessages);
        }

        return redirect()->route('pengelola.pencairan_dana.index', ['status' => $status])->with($hasError ? 'warning' : 'success', $message);
    }

    public function propose($id)
    {
        $pencairan = PencairanDana::findOrFail($id);
        
        // If it belongs to a batch, propose the whole batch
        if ($pencairan->batch_id) {
            PencairanDana::where('batch_id', $pencairan->batch_id)
                ->where('status', 'draft')
                ->update(['status' => 'proposed']);
        } else {
            if ($pencairan->status !== 'draft') {
                return back()->with('error', 'Hanya laporan draft yang dapat diajukan.');
            }
            $pencairan->update(['status' => 'proposed']);
        }

        return back()->with('success', 'Seluruh Laporan dalam batch berhasil diajukan ke Kaur.');
    }

    public function show($batch_id)
    {
        $pencairan_danas = PencairanDana::with(['tenant.kantin', 'pengelola'])
            ->where('batch_id', $batch_id)
            ->get();
            
        if ($pencairan_danas->isEmpty()) {
            abort(404);
        }
        
        $batchInfo = $pencairan_danas->first();
        
        return view('pengelola.pencairan-dana.show', compact('pencairan_danas', 'batchInfo', 'batch_id'));
    }

    public function generatePdf($id)
    {
        $pencairan = PencairanDana::with(['tenant.kantin'])->findOrFail($id);

        $data = [
            'tenant' => $pencairan->tenant,
            'startDate' => $pencairan->start_date,
            'endDate' => $pencairan->end_date,
            'totalPenjualan' => $pencairan->total_penjualan,
            'danaTenant' => $pencairan->dana_tenant,
            'danaTelu' => $pencairan->dana_telu,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengelola.pencairan-dana.pdf', $data);
        return $pdf->download('Laporan-Pencairan-Dana-'.$pencairan->tenant->nama_tenant.'.pdf');
    }

    public function generateBatchPdf($batch_id)
    {
        $pencairan_danas = PencairanDana::with(['tenant.kantin'])
            ->where('batch_id', $batch_id)
            ->get();
            
        if ($pencairan_danas->isEmpty()) {
            abort(404);
        }

        // We can create a unified view for batch PDF or zip them.
        // For simplicity, let's pass all to a single view that iterates them and adds page breaks.
        $data = [
            'pencairan_danas' => $pencairan_danas,
            'batchId' => $batch_id
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengelola.pencairan-dana.pdf_batch', $data);
        return $pdf->download('Laporan-Pencairan-Dana-Batch-'.$batch_id.'.pdf');
    }
}
