

<?php $__env->startSection('title', 'Dashboard Loket'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body bg-dark text-white d-flex justify-content-between align-items-center rounded">
                    <div>
                        <h2 class="mb-0"><?php echo e($counter->name); ?></h2>
                        <div class="d-flex align-items-center mt-1">
                            <span class="text-info me-3">Melayani Utama:
                                <strong><?php echo e($counter->service->name); ?></strong></span>
                            <!-- Ganti Layanan Dropdown -->
                            <form action="<?php echo e(route('counter.update-service')); ?>" method="POST" class="d-flex gap-2">
                                <?php echo csrf_field(); ?>
                                <select name="service_id" class="form-select form-select-sm" style="width: auto;"
                                    onchange="this.form.submit()">
                                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>" <?php echo e($counter->service_id == $s->id ? 'selected' : ''); ?>>
                                            <?php echo e($s->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="mb-1">Petugas: <strong><?php echo e(Auth::user()->name); ?></strong></div>
                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Logout / Nonaktifkan Loket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendaftaran Antrian Baru (Multiple Services) -->
        <div class="col-md-12 mb-4">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Daftar Antrian Baru</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Klik salah satu layanan di bawah untuk mencetak nomor antrian baru bagi pelanggan.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <form action="<?php echo e(route('queue.register', $service->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-primary btn-lg px-4">
                                    <i class="bi bi-plus-circle me-1"></i> <?php echo e($service->name); ?> (<?php echo e($service->prefix); ?>)
                                </button>
                            </form>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4 shadow-sm" style="min-height: 300px;">
                <div class="card-header bg-secondary text-white text-center py-3">
                    <h5 class="mb-0">PANGGILAN SAAT INI</h5>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <?php if($currentQueue): ?>
                        <p class="text-muted mb-0">Nomor Antrian</p>
                        <div class="display-1 fw-bold text-primary mb-4">
                            <?php echo e($currentQueue->queue_number); ?>

                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <form action="<?php echo e(route('counter.finish', $currentQueue->id)); ?>" method="POST" class="flex-grow-1">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-success btn-lg w-100 py-3">
                                    <i class="bi bi-check-circle me-1"></i> SELESAI
                                </button>
                            </form>
                            <form action="<?php echo e(route('counter.next', $currentQueue->id)); ?>" method="POST" class="flex-grow-1">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow">
                                    <i class="bi bi-chevron-double-right me-1"></i> ANTRIAN SELANJUTNYA
                                </button>
                            </form>
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <form action="<?php echo e(route('counter.recall', $currentQueue->id)); ?>" method="POST" class="flex-grow-1">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-info btn-sm w-100 py-2">
                                    <i class="bi bi-megaphone-fill me-1"></i> PANGGIL ULANG (RECALL)
                                </button>
                            </form>
                            <form action="<?php echo e(route('counter.skip', $currentQueue->id)); ?>" method="POST" class="flex-grow-1">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-warning btn-sm w-100 py-2 text-dark">
                                    <i class="bi bi-skip-forward me-1"></i> LEWATI
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="text-muted mb-4">
                            <i class="bi bi-person-dash display-4 d-block mb-3"></i>
                            <h4>Tidak ada antrian aktif</h4>
                        </div>
                        <form action="<?php echo e(route('counter.call-next')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-primary btn-xl py-3 px-5 w-100 shadow">
                                <i class="bi bi-megaphone me-2"></i> <strong>PANGGIL SELANJUTNYA</strong>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm" style="min-height: 300px;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Daftar Tunggu - <?php echo e($counter->service->name); ?></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Nomor Antrian</th>
                                    <th>Waktu Daftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $waitingQueues = \App\Models\Queue::where('service_id', $counter->service_id)
                                        ->where('status', 'waiting')
                                        ->orderBy('number', 'asc')
                                        ->get();
                                ?>
                                <?php $__empty_1 = true; $__currentLoopData = $waitingQueues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="ps-3 text-muted"><?php echo e($loop->iteration); ?></td>
                                        <td><span class="badge bg-info text-dark fs-6"><?php echo e($q->queue_number); ?></span></td>
                                        <td><?php echo e($q->created_at->format('H:i:s')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox d-block display-6 mb-2"></i>
                                            Tidak ada antrian menunggu untuk layanan ini
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
        <script>
            // Auto Print Trigger
            <?php if(session('print_queue_id')): ?>
                (function () {
                    const printUrl = "<?php echo e(route('queue.print', session('print_queue_id'))); ?>";
                    const printWindow = window.open(printUrl, 'Cetak Antrian', 'width=300,height=400');
                    if (printWindow) {
                        // The child window handles printing and closing itself
                        console.log("Print window opened for ID:", "<?php echo e(session('print_queue_id')); ?>");
                    } else {
                        alert("Gagal membuka jendela cetak. Pastikan pop-up diperbolehkan di browser ini.");
                    }
                })();
            <?php endif; ?>
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\930102850\.gemini\antigravity\scratch\antrian_app\resources\views/counter/index.blade.php ENDPATH**/ ?>