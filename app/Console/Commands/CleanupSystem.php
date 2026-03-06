<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup abandoned counters and finish old queues from previous days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting system cleanup...');

        // 1. Release abandoned counters (inactive for > 20 minutes)
        // Note: HandleInactivity middleware touches the 'updated_at' on every request
        $inactiveTime = now()->subMinutes(20);

        $abandonedCounters = \App\Models\Counter::whereNotNull('occupied_by')
            ->where('updated_at', '<', $inactiveTime)
            ->get();

        foreach ($abandonedCounters as $counter) {
            $this->line("Releasing abandoned counter: {$counter->name}");
            $counter->update([
                'occupied_by' => null,
                'status' => 'offline'
            ]);
        }

        // 2. Finish old queues (anything created before today that isn't finished)
        $oldQueues = \App\Models\Queue::whereDate('created_at', '<', today())
            ->whereNotIn('status', ['finished', 'skipped'])
            ->get();

        foreach ($oldQueues as $queue) {
            $this->line("Auto-finishing old queue: {$queue->queue_number}");
            $queue->update([
                'status' => 'finished',
                'finished_at' => now()
            ]);
        }

        $this->info('Cleanup completed successfully.');
    }
}
