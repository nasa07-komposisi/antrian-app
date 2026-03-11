<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Counter;
use App\Models\User;
use App\Models\Queue;
use App\Models\Announcement;
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

        // Statistics (Purely Global)
        $totalQueues = Queue::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        $finishedQueues = Queue::where('status', 'finished')
            ->whereDate('finished_at', '>=', $startDate)
            ->whereDate('finished_at', '<=', $endDate)
            ->count();

        // Calling Queues (Real-time active)
        $callingQueues = Queue::with(['service', 'counter.occupiedBy'])
            ->where('status', 'calling')
            ->get();

        // Active Staff (Real-time)
        $activeCounters = Counter::with(['occupiedBy', 'service'])
            ->whereNotNull('occupied_by')
            ->get();

        // Master Data for Dashboard
        $services = Service::all();
        $counterCount = Counter::count();
        $userCount = User::where('role', 'staff')->count();
        $activeCounterCount = Counter::whereNotNull('occupied_by')
            ->where('last_seen_at', '>=', now()->subSeconds(40))
            ->count();
        $serviceCount = $services->count();

        // Per Service Statistics (Horizontal Adaptive Row)
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
            'totalQueues' => $totalQueues,
            'finishedQueues' => $finishedQueues,
            'callingQueues' => $callingQueues,
            'activeCounters' => $activeCounters,
            'serviceStats' => $serviceStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'serviceCount' => $serviceCount,
            'counterCount' => $counterCount,
            'userCount' => $userCount,
            'activeCounterCount' => $activeCounterCount
        ]);
    }

    public function performance(Request $request)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $selectedServiceIds = $request->input('service_ids', []);
        if (!is_array($selectedServiceIds)) {
            $selectedServiceIds = array_filter(explode(',', $selectedServiceIds));
        }

        $services = Service::all();

        // Staff Performance Statistics
        $performanceQuery = Queue::with(['service', 'user'])
            ->where('status', 'finished')
            ->whereNotNull('user_id')
            ->whereHas('user', function ($q) {
                $q->where('role', 'staff');
            })
            ->whereNotNull('called_at')
            ->whereNotNull('finished_at')
            ->whereDate('finished_at', '>=', $startDate)
            ->whereDate('finished_at', '<=', $endDate);

        if (!empty($selectedServiceIds)) {
            $performanceQuery->whereIn('service_id', $selectedServiceIds);
        }

        $staffPerformance = $performanceQuery->get()
            ->groupBy('user_id')
            ->map(function ($queues) {
                $totalMinutes = $queues->sum(function ($q) {
                    return $q->called_at->diffInMinutes($q->finished_at);
                });
                $count = $queues->count();
                $servicesHandled = $queues->pluck('service.name')->unique()->implode(', ');

                return [
                    'service_name' => $servicesHandled,
                    'user_name' => $queues->first()->user->name,
                    'served' => $count,
                    'avg_minutes' => $count > 0 ? round($totalMinutes / $count, 1) : 0,
                ];
            })->sortByDesc('served')->values();

        return view('admin.performance', compact(
            'staffPerformance',
            'services',
            'startDate',
            'endDate',
            'selectedServiceIds'
        ));
    }

    public function forceFinish(Queue $queue)
    {
        $queue->update([
            'status' => 'finished',
            'finished_at' => now(),
            'user_id' => auth()->id()
        ]);

        $this->updateConfigVersion();

        return back()->with('success', "Antrian {$queue->queue_number} berhasil diselesaikan paksa oleh Admin.");
    }

    public function logs()
    {
        $logs = \App\Models\CounterLog::with(['user', 'counter'])
            ->orderBy('login_at', 'desc')
            ->paginate(20);

        return view('admin.logs', compact('logs'));
    }

    public function getRealtimeData(Request $request)
    {
        $startDate = $request->input('start_date', today()->format('Y-m-d'));
        $endDate = $request->input('end_date', today()->format('Y-m-d'));

        $callingQueues = Queue::with(['service', 'counter.occupiedBy'])
            ->where('status', 'calling')
            ->get();

        $activeCounters = Counter::with(['occupiedBy', 'service'])
            ->whereNotNull('occupied_by')
            ->get();

        $totalQueues = Queue::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        $finishedQueues = Queue::where('status', 'finished')
            ->whereDate('finished_at', '>=', $startDate)
            ->whereDate('finished_at', '<=', $endDate)
            ->count();

        $activeCounterCount = Counter::whereNotNull('occupied_by')
            ->where('last_seen_at', '>=', now()->subSeconds(40))
            ->count();

        return response()->json([
            'totalQueues' => $totalQueues,
            'finishedQueues' => $finishedQueues,
            'activeCounterCount' => $activeCounterCount,
            'html_calling' => view('admin.partials.calling_queues', compact('callingQueues'))->render(),
            'html_staff' => view('admin.partials.active_staff', compact('activeCounters'))->render(),
            'announcements' => Announcement::where('is_active', true)->pluck('content'),
            'config_version' => \Illuminate\Support\Facades\Storage::disk('local')->get('config_version.txt') ?? 0
        ]);
    }

    // Announcement Management
    public function announcements()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        return view('admin.announcements', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        Announcement::create($data);
        $this->updateConfigVersion();

        return back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $announcement->update($data);
        $this->updateConfigVersion();

        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        $announcement->delete();
        $this->updateConfigVersion();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
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

    public function downloadUserTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_user_antrian.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['nama', 'email', 'password', 'role']);
            fputcsv($file, ['Budi Santoso', 'budi@example.com', 'password123', 'staff']);
            fputcsv($file, ['Siti Aminah', 'siti@example.com', 'secret321', 'admin']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        // Skip header
        fgetcsv($handle);

        $imported = 0;
        $errors = [];
        $line = 1;

        while (($data = fgetcsv($handle)) !== FALSE) {
            $line++;
            if (count($data) < 4)
                continue;

            $name = trim($data[0]);
            $email = trim($data[1]);
            $password = trim($data[2]);
            $role = strtolower(trim($data[3]));

            if (empty($name) || empty($email) || empty($password)) {
                $errors[] = "Baris $line: Data tidak lengkap.";
                continue;
            }

            if (!in_array($role, ['admin', 'staff'])) {
                $role = 'staff'; // Default
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Baris $line: Email $email sudah digunakan.";
                continue;
            }

            try {
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt($password),
                    'role' => $role
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris $line: Gagal menyimpan (" . $e->getMessage() . ")";
            }
        }

        fclose($handle);

        $msg = "$imported user berhasil diimpor.";
        if (count($errors) > 0) {
            $msg .= " Terdapat beberapa error: " . implode(", ", array_slice($errors, 0, 3));
            if (count($errors) > 3)
                $msg .= " ...dan lainnya.";
            return back()->with('warning', $msg);
        }

        return back()->with('success', $msg);
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
