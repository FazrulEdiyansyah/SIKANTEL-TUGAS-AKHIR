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
        $status = $request->query('status', 'all');

        $query = PencairanDana::with(['pengelola'])
            ->selectRaw('batch_id, MAX(judul) as judul, MAX(id) as id, MIN(start_date) as start_date, MAX(end_date) as end_date, SUM(total_penjualan) as total_penjualan, SUM(dana_tenant) as dana_tenant, status, COUNT(*) as tenant_count, MAX(created_at) as created_at')
            ->when($request->filled('search'), function ($q) use ($request) {
                return $q->where(function($query) use ($request) {
                    $searchTerm = strtolower($request->search);
                    $query->whereRaw('LOWER(batch_id) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(judul) LIKE ?', ['%' . $searchTerm . '%']);
                });
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                return $q->whereDate('start_date', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                return $q->whereDate('end_date', '<=', $request->end_date);
            })
            ->when($status !== 'all', function ($q) use ($status) {
                if ($status === 'proposed') {
                    return $q->whereIn('status', ['proposed', 'approved_kaur']);
                } elseif ($status === 'rejected') {
                    return $q->whereIn('status', ['rejected_kaur', 'rejected_kabag']);
                }
                return $q->where('status', $status);
            })
            ->whereNotNull('batch_id')
            
            ->groupBy('batch_id', 'status')
            ->orderBy('created_at', 'desc');

        $pencairan_danas = $query->paginate(10)->withQueryString();

        // Get kantin info for each batch
        $batchIds = $pencairan_danas->pluck('batch_id')->toArray();
        $batchKantins = PencairanDana::whereIn('batch_id', $batchIds)
            ->join('tenants', 'pencairan_danas.tenant_id', '=', 'tenants.id')
            ->join('kantins', 'tenants.kantin_id', '=', 'kantins.id')
            ->select('pencairan_danas.batch_id', 'kantins.nama_kantin')
            ->distinct()
            ->get()
            ->groupBy('batch_id');

        foreach ($pencairan_danas as $pencairan) {
            $kantins = $batchKantins->get($pencairan->batch_id);
            if ($kantins && $kantins->count() == 1) {
                $pencairan->keterangan_kantin = $kantins->first()->nama_kantin;
            } else {
                $pencairan->keterangan_kantin = 'Berbagai Kantin';
            }
        }

        // Count for tabs
        $baseQuery = PencairanDana::whereNotNull('batch_id');
        $statusCounts = [
            'all'      => (clone $baseQuery)->distinct('batch_id')->count('batch_id'),
            'draft'    => (clone $baseQuery)->where('status', 'draft')->distinct('batch_id')->count('batch_id'),
            'proposed' => (clone $baseQuery)->whereIn('status', ['proposed', 'approved_kaur'])->distinct('batch_id')->count('batch_id'),
            'approved' => (clone $baseQuery)->where('status', 'approved')->distinct('batch_id')->count('batch_id'),
            'rejected' => (clone $baseQuery)->whereIn('status', ['rejected_kaur', 'rejected_kabag'])->distinct('batch_id')->count('batch_id'),
        ];

        return view('superadmin.pencairan.index', compact('pencairan_danas', 'status', 'statusCounts'));
    }

    public function create()
    {
        $tenants = Tenant::with('kantin')->where('status', 'aktif')->get();
        $kantins = \App\Models\Kantin::all();
        $approvers = \App\Models\User::whereIn('role', ['kaur', 'kabag'])->get();
        return view('superadmin.pencairan.create', compact('tenants', 'kantins', 'approvers'));
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

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tenant_ids' => 'required|array',
            'tenant_ids.*' => 'exists:tenants,id',
            'approver_1_name' => 'required|string',
            'approver_2_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'keterangan' => 'nullable|string',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
        $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();

        $status = $request->input('action', 'draft') === 'draft' ? 'draft' : 'proposed';
        $batchId = 'REQ-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
        $approverNameCombined = $request->approver_1_name . ' (Kaur) & ' . $request->approver_2_name . ' (Kabag)';
        
        $createdCount = 0;

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $startDate, $endDate, $status, $batchId, $approverNameCombined, &$createdCount) {
                $allDetailsToInsert = [];
                $timestamp = now();

                foreach ($request->tenant_ids as $tenantId) {
                    $orders = Order::where('tenant_id', $tenantId)
                        ->where('payment_status', 'success')
                        ->where('order_status', 'selesai')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->get();

                    if ($orders->isEmpty()) continue; // Jangan buat laporan kalau tidak ada transaksi

                    $totalPenjualan = $orders->sum('total_price');
                    $danaTenant = $totalPenjualan * 0.70;
                    $danaTelu = $totalPenjualan * 0.30;

                    $pencairan = PencairanDana::create([
                        'batch_id' => $batchId,
                        'judul' => $request->judul,
                        'tenant_id' => $tenantId,
                        'pengelola_id' => auth()->id(),
                        'approver_name' => $approverNameCombined,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'total_penjualan' => $totalPenjualan,
                        'dana_tenant' => $danaTenant,
                        'dana_telu' => $danaTelu,
                        'keterangan' => $request->keterangan,
                        'status' => $status
                    ]);

                    foreach ($orders as $order) {
                        $allDetailsToInsert[] = [
                            'pencairan_dana_id' => $pencairan->id,
                            'order_id' => $order->id,
                            'total_price' => $order->total_price,
                            'dana_tenant' => $order->total_price * 0.70,
                            'dana_telu' => $order->total_price * 0.30,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    }
                    
                    $createdCount++;
                }

                // Bulk insert details (chunked per 1000 items to avoid query limits)
                $chunks = array_chunk($allDetailsToInsert, 1000);
                foreach ($chunks as $chunk) {
                    \App\Models\PencairanDanaDetail::insert($chunk);
                }
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }

        if ($createdCount === 0) {
            return back()->with('error', 'Tidak ada data transaksi sukses untuk tenant dan periode yang dipilih.')->withInput();
        }

        $message = $status === 'draft' ? "{$createdCount} Laporan berhasil disimpan sebagai Draft." : "{$createdCount} Laporan berhasil diajukan ke Approver.";
        return redirect()->route('superadmin.pencairan.index', ['status' => $status])->with('success', $message);
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
        
        return view('superadmin.pencairan.show', compact('pencairan_danas', 'batchInfo', 'batch_id'));
    }

    public function propose($batch_id)
    {
        PencairanDana::where('batch_id', $batch_id)
            ->whereIn('status', ['draft', 'rejected_kaur', 'rejected_kabag'])
            ->update(['status' => 'proposed']);
            
        return back()->with('success', 'Seluruh Laporan dalam batch berhasil diajukan.');
    }

    public function approve($batch_id)
    {
        $batch = PencairanDana::where('batch_id', $batch_id)->firstOrFail();
        
        if ($batch->status === 'proposed') {
            PencairanDana::where('batch_id', $batch_id)->update(['status' => 'approved_kaur']);
            return back()->with('success', 'Laporan disetujui (Tahap 1 - Kaur).');
        } elseif ($batch->status === 'approved_kaur') {
            PencairanDana::where('batch_id', $batch_id)->update(['status' => 'completed']);
            return back()->with('success', 'Laporan disetujui sepenuhnya dan selesai.');
        }

        return back()->with('error', 'Status laporan tidak valid untuk disetujui.');
    }

    public function reject(Request $request, $batch_id)
    {
        $batch = PencairanDana::where('batch_id', $batch_id)->firstOrFail();
        $catatan = $request->input('catatan');
        
        if ($batch->status === 'proposed') {
            PencairanDana::where('batch_id', $batch_id)->update(['status' => 'rejected_kaur', 'catatan_kaur' => $catatan]);
            return back()->with('success', 'Laporan ditolak (Tahap 1 - Kaur).');
        } elseif ($batch->status === 'approved_kaur') {
            PencairanDana::where('batch_id', $batch_id)->update(['status' => 'rejected_kabag', 'catatan_kabag' => $catatan]);
            return back()->with('success', 'Laporan ditolak (Tahap 2 - Kabag).');
        }

        return back()->with('error', 'Status laporan tidak valid untuk ditolak.');
    }

    public function destroy($batch_id)
    {
        $pencairans = PencairanDana::where('batch_id', $batch_id)->get();
        
        foreach ($pencairans as $pencairan) {
            $pencairan->details()->delete();
            $pencairan->delete();
        }

        return redirect()->route('superadmin.pencairan.index')->with('success', 'Laporan pencairan berhasil dihapus.');
    }

    public function previewPdf($id)
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

        $data = [
            'pencairan_danas' => $pencairan_danas,
            'batchId' => $batch_id
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengelola.pencairan-dana.pdf_batch', $data);
        return $pdf->download('Laporan-Pencairan-Dana-Batch-'.$batch_id.'.pdf');
    }
}


