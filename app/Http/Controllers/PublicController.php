<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Queue;
use App\Models\Counter;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $counters = Counter::with([
            'service',
            'queues' => function ($q) {
                $q->where('status', 'calling')->latest('called_at');
            }
        ])->get();

        return view('public.index', compact('counters'));
    }

    public function getQueueStatus()
    {
        $counters = Counter::with([
            'service',
            'queues' => function ($q) {
                $q->where('status', 'calling')->latest('called_at');
            }
        ])->get();

        $data = $counters->map(function ($counter) {
            $waitingCount = Queue::where('service_id', $counter->service_id)
                ->where('status', 'waiting')
                ->count();

            $finishedToday = Queue::where('service_id', $counter->service_id)
                ->where('status', 'finished')
                ->whereDate('finished_at', today())
                ->count();

            $current = $counter->queues->first();

            return [
                'counter_name' => $counter->name,
                'service_name' => $counter->service?->name ?? '-',
                'prefix' => $counter->service?->prefix ?? '',
                'current_queue' => $current?->queue_number ?? '---',
                'current_number' => $current?->number ?? 0,
                'queue_id' => $current?->id ?? null,
                'called_at' => $current?->called_at ?? null,
                'status' => $counter->status,
                'waiting_count' => $waitingCount,
                'finished_today' => $finishedToday
            ];
        });

        // Also get all services summary (even those not handled by any counter right now)
        $servicesSummary = Service::all()->map(function ($service) {
            return [
                'name' => $service->name,
                'hex_color' => $service->hex_color,
                'finished_count' => Queue::where('service_id', $service->id)
                    ->where('status', 'finished')
                    ->whereDate('finished_at', today())
                    ->count()
            ];
        });

        return response()->json([
            'counters' => $data,
            'summary' => $servicesSummary,
            'config_version' => \Illuminate\Support\Facades\Storage::disk('local')->get('config_version.txt') ?? 0
        ]);
    }

    public function registerQueue(Service $service)
    {
        $today = now()->format('Y-m-d');

        // Check if quota is set for today
        if ($service->quota_date !== $today || is_null($service->daily_quota)) {
            return back()->with('error', "Kuota untuk layanan '{$service->name}' belum diatur untuk hari ini. Hubungi Admin.");
        }

        $todayCount = Queue::where('service_id', $service->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= $service->daily_quota) {
            return back()->with('error', "Maaf, kuota antrian untuk layanan '{$service->name}' telah habis (Maks: {$service->daily_quota}).");
        }

        $lastNumber = Queue::where('service_id', $service->id)
            ->whereDate('created_at', today())
            ->max('number') ?? 0;

        $number = $lastNumber + 1;
        $queueNumber = $service->prefix . str_pad($number, 3, '0', STR_PAD_LEFT);

        $queue = Queue::create([
            'service_id' => $service->id,
            'queue_number' => $queueNumber,
            'number' => $number,
            'status' => 'waiting'
        ]);

        return back()->with('success', "Nomor Antrian Baru: $queueNumber")->with('print_queue_id', $queue->id);
    }

    public function printTicket(Queue $queue)
    {
        return view('print.queue', compact('queue'));
    }

    public function getConfigVersion()
    {
        $version = \Illuminate\Support\Facades\Storage::disk('local')->get('config_version.txt') ?? 0;
        return response()->json(['config_version' => $version]);
    }
}
