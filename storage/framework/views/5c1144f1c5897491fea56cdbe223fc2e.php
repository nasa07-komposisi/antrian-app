

<?php $__env->startSection('title', 'Manajemen Kuota Harian'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-uppercase fw-bold letter-spacing-1">
                        <i class="bi bi-shield-check me-2 text-warning"></i> Pengaturan Kuota Antrian
                    </h5>
                    <span class="badge bg-primary px-3 py-2"><?php echo e(\Carbon\Carbon::parse($today)->format('d F Y')); ?></span>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Penting:</strong> Kuota harus diatur setiap hari. Antrian tidak dapat dicetak jika kuota
                        untuk hari ini belum ditentukan.
                    </div>

                    <form action="<?php echo e(route('admin.quotas.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start px-4">Jenis Layanan</th>
                                        <th>Status Saat Ini</th>
                                        <th style="width: 200px;">Batas Kuota Hari Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-start px-4">
                                                <div class="d-flex align-items-center">
                                                    <div style="width: 12px; height: 12px; background: <?php echo e($svc->hex_color); ?>; border-radius: 50%;"
                                                        class="me-2"></div>
                                                    <span class="fw-bold"><?php echo e($svc->name); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($svc->quota_date == $today): ?>
                                                    <span class="badge bg-success px-3">Sudah Diatur: <?php echo e($svc->daily_quota); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger px-3">Belum Diatur</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="quotas[<?php echo e($svc->id); ?>]"
                                                        class="form-control text-center fw-bold"
                                                        value="<?php echo e($svc->quota_date == $today ? $svc->daily_quota : ''); ?>"
                                                        min="1" placeholder="0" required>
                                                    <span class="input-group-text">Orang</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-center gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                <i class="bi bi-save me-2"></i> SIMPAN & AKTIFKAN ANTRIAN
                            </button>
                        </div>
                    </form>

                    <form action="<?php echo e(route('admin.quotas.reset')); ?>" method="POST" class="mt-3"
                        onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh kuota harian? Antrian tidak akan bisa dicetak sampai kuota diatur kembali.')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Kuota Hari Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\930102850\.gemini\antigravity\scratch\antrian_app\resources\views/admin/quotas.blade.php ENDPATH**/ ?>