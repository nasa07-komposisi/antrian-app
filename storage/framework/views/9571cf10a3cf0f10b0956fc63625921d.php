

<?php $__env->startSection('title', 'Manajemen User'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Tambah User Baru</div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.users.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="staff">Petugas Loket</option>
                                <option value="admin">Super Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan User</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Daftar User</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-end px-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="align-middle px-3"><?php echo e($user->name); ?></td>
                                    <td class="align-middle"><?php echo e($user->email); ?></td>
                                    <td class="align-middle">
                                        <span class="badge <?php echo e($user->role == 'admin' ? 'bg-danger' : 'bg-info'); ?>">
                                            <?php echo e(strtoupper($user->role)); ?>

                                        </span>
                                    </td>
                                    <td class="text-end px-3">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editUser<?php echo e($user->id); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if($user->id !== auth()->id()): ?>
                                            <form action="<?php echo e(route('admin.users.delete', $user->id)); ?>" method="POST"
                                                class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Hapus user ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editUser<?php echo e($user->id); ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit User: <?php echo e($user->name); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama</label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="<?php echo e($user->name); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control"
                                                            value="<?php echo e($user->email); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                                                        <input type="password" name="password" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Role</label>
                                                        <select name="role" class="form-select">
                                                            <option value="staff" <?php echo e($user->role == 'staff' ? 'selected' : ''); ?>>
                                                                Petugas Loket</option>
                                                            <option value="admin" <?php echo e($user->role == 'admin' ? 'selected' : ''); ?>>
                                                                Super Admin</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\930102850\.gemini\antigravity\scratch\antrian_app\resources\views/admin/users.blade.php ENDPATH**/ ?>