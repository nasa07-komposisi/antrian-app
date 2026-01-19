

<?php $__env->startSection('title', 'Manajemen Loket'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-dark fw-bold">Tambah Loket Baru</div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.counters.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Nama Loket</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Loket 5" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Layanan Utama</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">-- Pilih Layanan --</option>
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?> (<?php echo e($service->prefix); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Awal</label>
                            <select name="status" class="form-select">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-100 fw-bold">Simpan Loket</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Daftar Loket</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Nama Loket</th>
                                <th>Layanan Utama</th>
                                <th>Status Fisik</th>
                                <th class="text-end px-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $counters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $counter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="align-middle fw-bold px-3"><?php echo e($counter->name); ?></td>
                                    <td class="align-middle">
                                        <span class="badge" style="background-color: <?php echo e($counter->service->hex_color); ?>">
                                            <?php echo e($counter->service->name); ?>

                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span
                                            class="badge <?php echo e($counter->status == 'inactive' ? 'bg-secondary' : 'bg-success'); ?>">
                                            <?php echo e(strtoupper($counter->status)); ?>

                                        </span>
                                    </td>
                                    <td class="text-end px-3 align-middle">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editCounter<?php echo e($counter->id); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="<?php echo e(route('admin.counters.delete', $counter->id)); ?>" method="POST"
                                            class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus loket ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editCounter<?php echo e($counter->id); ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="<?php echo e(route('admin.counters.update', $counter->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Loket: <?php echo e($counter->name); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Loket</label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="<?php echo e($counter->name); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Layanan Utama</label>
                                                        <select name="service_id" class="form-select" required>
                                                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($service->id); ?>" <?php echo e($counter->service_id == $service->id ? 'selected' : ''); ?>>
                                                                    <?php echo e($service->name); ?> (<?php echo e($service->prefix); ?>)
                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status Fisik</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" <?php echo e($counter->status == 'active' ? 'selected' : ''); ?>>Aktif</option>
                                                            <option value="inactive" <?php echo e($counter->status == 'inactive' ? 'selected' : ''); ?>>Nonaktif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-info fw-bold">Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\930102850\.gemini\antigravity\scratch\antrian_app\resources\views/admin/counters.blade.php ENDPATH**/ ?>