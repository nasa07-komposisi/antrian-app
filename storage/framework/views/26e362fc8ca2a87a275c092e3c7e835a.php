

<?php $__env->startSection('title', 'Pilih Loket'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="mb-0">PILIH LOKET TUGAS</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-center text-muted">Selamat datang, <strong><?php echo e(Auth::user()->name); ?></strong>. Silakan
                        pilih loket yang akan Anda aktifkan.</p>

                    <?php if($availableCounters->isEmpty()): ?>
                        <div class="alert alert-warning text-center">
                            Maaf, saat ini tidak ada loket yang tersedia atau semua loket sudah digunakan.
                        </div>
                    <?php else: ?>
                        <form action="<?php echo e(route('counter.select.post')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="list-group mb-4">
                                <?php $__currentLoopData = $availableCounters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $counter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-3" type="radio" name="counter_id"
                                                value="<?php echo e($counter->id); ?>" required>
                                            <div>
                                                <h5 class="mb-0"><?php echo e($counter->name); ?></h5>
                                                <small class="text-muted">Melayani: <?php echo e($counter->service->name); ?></small>
                                            </div>
                                        </div>
                                        <span class="badge bg-success">Tersedia</span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Aktifkan Loket</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <div class="mt-3 text-center">
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-link text-danger">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\930102850\.gemini\antigravity\scratch\antrian_app\resources\views/counter/select.blade.php ENDPATH**/ ?>