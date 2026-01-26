<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounterAssignmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'staff') {
            return redirect()->route('admin.index')->with('error', 'Admin tidak diperbolehkan memilih loket.');
        }

        // If user already occupies a counter, redirect to dashboard
        if ($user->occupiedCounter) {
            return redirect()->route('counter.index');
        }

        // Get counters that are not occupied
        $availableCounters = Counter::whereNull('occupied_by')
            ->where('status', 'active')
            ->get();

        return view('counter.select', compact('availableCounters'));
    }

    public function select(Request $request)
    {
        $request->validate([
            'counter_id' => 'required|exists:counters,id'
        ]);

        $counter = Counter::findOrFail($request->counter_id);

        if ($counter->occupied_by) {
            return back()->with('error', 'Loket ini sudah digunakan oleh user lain.');
        }

        $counter->update([
            'occupied_by' => Auth::id(),
            'status' => 'busy'
        ]);

        return redirect()->route('counter.index')->with('success', 'Loket berhasil dipilih.');
    }
}
