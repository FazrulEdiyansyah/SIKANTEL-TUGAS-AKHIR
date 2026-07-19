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
            ->where('pengelola_id', auth()->id())
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
        $baseQuery = PencairanDana::whereNotNull('batch_id')->where('pengelola_id', auth()->id());
        $statusCounts = [
            'all'      => (clone $baseQuery)->distinct('batch_id')->count('batch_id'),
            'draft'    => (clone $baseQuery)->where('status', 'draft')->distinct('batch_id')->count('batch_id'),
            'proposed' => (clone $baseQuery)->whereIn('status', ['proposed', 'approved_kaur'])->distinct('batch_id')->count('batch_id'),
            'approved' => (clone $baseQuery)->where('status', 'approved')->distinct('batch_id')->count('batch_id'),
            'rejected' => (clone $baseQuery)->whereIn('status', ['rejected_kaur', 'rejected_kabag'])->distinct('batch_id')->count('batch_id'),
        ];

        return view('pengelola.pencairan-dana.index', compact('pencairan_danas', 'status', 'statusCounts'));
    }

    public function create(Request $request)
    {
        $tenants = Tenant::with('kantin')->where('status', 'aktif')->get();
        $kantins = \App\Models\Kantin::all();
        $approvers = \App\Models\User::whereIn('role', ['kaur', 'kabag'])->get();
        
        $duplicateData = null;
        if ($request->has('duplicate_from')) {
            $oldRecords = PencairanDana::where('batch_id', $request->duplicate_from)->get();
            if ($oldRecords->isNotEmpty()) {
                $first = $oldRecords->first();
                $approversArray = explode(' & ', $first->approver_name);
                $app1 = $approversArray[0] ?? '';
                $app2 = $approversArray[1] ?? '';
                
                // Remove "(Kaur)" and "(Kabag)" if present
                $app1 = trim(str_replace('(Kaur)', '', $app1));
                $app2 = trim(str_replace('(Kabag)', '', $app2));

                $duplicateData = [
                    'judul' => $first->judul,
                    'start_date' => $first->start_date ? $first->start_date->format('Y-m-d') : date('Y-m-01'),
                    'end_date' => $first->end_date ? $first->end_date->format('Y-m-d') : date('Y-m-t'),
                    'keterangan' => $first->keterangan,
                    'tenant_ids' => $oldRecords->pluck('tenant_id')->toArray(),
                    'approver_1' => $app1,
                    'approver_2' => $app2,
                ];
            }
        }

        return view('pengelola.pencairan-dana.create', compact('tenants', 'kantins', 'approvers', 'duplicateData'));
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
        return redirect()->route('pengelola.pencairan_dana.index', ['status' => $status])->with('success', $message);
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

    public function duplicateBatch($batch_id)
    {
        $oldRecords = PencairanDana::where('batch_id', $batch_id)->get();
        if ($oldRecords->isEmpty()) {
            return back()->with('error', 'Laporan tidak ditemukan.');
        }
        
        $newBatchId = 'REQ-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
        $createdCount = 0;
        
        foreach ($oldRecords as $old) {
            $newRecord = $old->replicate();
            $newRecord->batch_id = $newBatchId;
            $newRecord->status = 'draft';
            $newRecord->catatan_kaur = null;
            $newRecord->catatan_kabag = null;
            $newRecord->created_at = now();
            $newRecord->updated_at = now();
            $newRecord->save();
            
            // Replicate details
            $oldDetails = \App\Models\PencairanDanaDetail::where('pencairan_dana_id', $old->id)->get();
            foreach ($oldDetails as $detail) {
                $newDetail = $detail->replicate();
                $newDetail->pencairan_dana_id = $newRecord->id;
                $newDetail->save();
            }
            $createdCount++;
        }
        
        return redirect()->route('pengelola.pencairan_dana.show', $newBatchId)
            ->with('success', "{$createdCount} Laporan berhasil diduplikasi menjadi Draft baru. Silakan tinjau dan ajukan ulang.");
    }

    public function show($batch_id)
    {
        $pencairan_danas = PencairanDana::with(['tenant.kantin', 'pengelola'])
            ->where('batch_id', $batch_id)
            ->where('pengelola_id', auth()->id())
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
