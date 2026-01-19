<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Counter;
use App\Models\User;
use App\Models\Queue;
use App\Traits\HasConfigVersion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    use HasConfigVersion;

    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Statistics
        $totalQueues = Queue::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        $finishedQueues = Queue::where('status', 'finished')
            ->whereDate('finished_at', '>=', $startDate)
            ->whereDate('finished_at', '<=', $endDate)
            ->count();

        // Per Service Statistics
        $services = Service::all();
        $serviceStats = $services->map(function ($service) use ($startDate, $endDate) {
            return [
                'name' => $service->name,
                'hex_color' => $service->hex_color,
                'prefix' => $service->prefix,
                'total' => Queue::where('service_id', $service->id)
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->count(),
                'finished' => Queue::where('service_id', $service->id)
                    ->where('status', 'finished')
                    ->whereDate('finished_at', '>=', $startDate)
                    ->whereDate('finished_at', '<=', $endDate)
                    ->count(),
            ];
        });

        return view('admin.index', [
            'serviceCount' => $services->count(),
            'counterCount' => Counter::count(),
            'userCount' => User::count(),
            'activeCounterCount' => Counter::where('status', 'busy')->count(),
            'totalQueues' => $totalQueues,
            'finishedQueues' => $finishedQueues,
            'serviceStats' => $serviceStats,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    // User Management
    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,staff'
        ]);

        $data['password'] = bcrypt($data['password']);
        User::create($data);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,staff'
        ]);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return back()->with('success', 'User berhasil diperbarui.');
    }


    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus diri sendiri.');
        }
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    // Service Management
    public function services()
    {
        $services = Service::all();
        return view('admin.services', compact('services'));
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:5',
            'color_class' => 'required|string',
            'hex_color' => 'required|string|max:7',
            'description' => 'nullable|string'
        ]);

        Service::create($data);
        $this->updateConfigVersion();
        return back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function updateService(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:5',
            'color_class' => 'required|string',
            'hex_color' => 'required|string|max:7',
            'description' => 'nullable|string'
        ]);

        $service->update($data);
        $this->updateConfigVersion();
        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function deleteService(Service $service)
    {
        if ($service->counters()->count() > 0) {
            return back()->with('error', 'Layanan tidak bisa dihapus karena masih digunakan oleh loket.');
        }
        $service->delete();
        $this->updateConfigVersion();
        return back()->with('success', 'Layanan berhasil dihapus.');
    }

    // Counter Management
    public function counters()
    {
        $counters = Counter::with('service')->get();
        $services = Service::all();
        return view('admin.counters', compact('counters', 'services'));
    }

    public function storeCounter(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'status' => 'required|in:active,inactive'
        ]);

        Counter::create($data);
        $this->updateConfigVersion();
        return back()->with('success', 'Loket berhasil ditambahkan.');
    }

    public function updateCounter(Request $request, Counter $counter)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'status' => 'required|in:active,inactive'
        ]);

        $counter->update($data);
        $this->updateConfigVersion();
        return back()->with('success', 'Loket berhasil diperbarui.');
    }

    public function deleteCounter(Counter $counter)
    {
        if ($counter->occupied_by) {
            return back()->with('error', 'Loket tidak bisa dihapus karena sedang digunakan.');
        }
        $counter->delete();
        $this->updateConfigVersion();
        return back()->with('success', 'Loket berhasil dihapus.');
    }

    // Quota Management
    public function quotas()
    {
        $services = Service::all();
        $today = now()->format('Y-m-d');
        return view('admin.quotas', compact('services', 'today'));
    }

    public function updateQuota(Request $request)
    {
        $request->validate([
            'quotas' => 'required|array',
            'quotas.*' => 'required|integer|min:1'
        ]);

        foreach ($request->quotas as $serviceId => $quota) {
            Service::where('id', $serviceId)->update([
                'daily_quota' => $quota,
                'quota_date' => now()->format('Y-m-d')
            ]);
        }

        $this->updateConfigVersion();
        return back()->with('success', 'Kuota harian berhasil diperbarui untuk hari ini.');
    }

    public function resetQuota()
    {
        Service::query()->update([
            'daily_quota' => null,
            'quota_date' => null
        ]);

        $this->updateConfigVersion();
        return back()->with('success', 'Seluruh kuota harian berhasil direset.');
    }
}
