<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Counter;
use App\Models\Service;
use App\Traits\HasConfigVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounterController extends Controller
{
    use HasConfigVersion;

    public function index()
    {
        $user = Auth::user();
        $counter = $user->occupiedCounter;

        if (!$counter) {
            return redirect()->route('counter.select')->with('error', 'Anda harus memilih loket terlebih dahulu.');
        }

        $currentQueue = Queue::where('counter_id', $counter->id)
            ->where('status', 'calling')
            ->first();

        $services = Service::all();

        return view('counter.index', compact('counter', 'currentQueue', 'services'));
    }

    public function callNext()
    {
        $user = Auth::user();
        $counter = $user->occupiedCounter;

        if (!$counter) {
            return back()->with('error', 'Loket tidak terdeteksi.');
        }

        // Check if there is already a calling queue
        $existing = Queue::where('counter_id', $counter->id)
            ->where('status', 'calling')
            ->first();

        if ($existing) {
            return back()->with('error', 'Selesaikan antrian saat ini sebelum memanggil yang lain.');
        }

        // Get next queue for THIS counter's service
        $next = Queue::where('service_id', $counter->service_id)
            ->where('status', 'waiting')
            ->orderBy('number', 'asc')
            ->first();

        if ($next) {
            $next->update([
                'counter_id' => $counter->id,
                'status' => 'calling',
                'called_at' => now()
            ]);
            return back()->with('success', "Memanggil nomor: " . $next->queue_number);
        }

        return back()->with('info', 'Tidak ada antrian menunggu.');
    }

    public function finish(Queue $queue)
    {
        $queue->update([
            'status' => 'finished',
            'finished_at' => now()
        ]);

        return back()->with('success', 'Antrian selesai.');
    }

    public function skip(Queue $queue)
    {
        $queue->update([
            'status' => 'skipped'
        ]);

        return back()->with('success', 'Antrian dilewati.');
    }

    public function recall(Queue $queue)
    {
        $queue->update([
            'called_at' => now()
        ]);

        return back()->with('success', "Memanggil kembali nomor: " . $queue->queue_number);
    }

    public function next(Queue $queue)
    {
        // 1. Finish the current queue
        $queue->update([
            'status' => 'finished',
            'finished_at' => now()
        ]);

        // 2. Call the next one
        return $this->callNext();
    }

    public function updateService(Request $request)
    {
        $user = Auth::user();
        $counter = $user->occupiedCounter;

        if (!$counter) {
            return back()->with('error', 'Loket tidak terdeteksi.');
        }

        $data = $request->validate([
            'service_id' => 'required|exists:services,id'
        ]);

        $counter->update([
            'service_id' => $data['service_id']
        ]);

        $this->updateConfigVersion();

        return back()->with('success', 'Layanan loket berhasil diubah.');
    }
}
