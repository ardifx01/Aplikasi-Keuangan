<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <div class="mb-2">
        <h1 class="text-3xl font-bold text-main tracking-tight drop-shadow-sm mb-4">Perencanaan Pendapatan</h1>
    </div>

    <?php if (session('success')) : ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session('error')) : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-end gap-2 mb-6">
        <button id="btnAddBudget" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-main text-white rounded-lg shadow hover:bg-highlight transition h-11">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah
        </button>
    </div>
    <?= view('Budgets/modal_add_budget', ['categories' => $categories]) ?>
    <?= view('Budgets/modal_edit_budget', ['categories' => $categories]) ?>
    <?= view('Budgets/modal_detail_budget', ['isAdmin' => $isAdmin]) ?>

    <div class="overflow-x-auto rounded-lg shadow border border-gray-200 bg-white">
        <table class="min-w-full border border-gray-300">
            <thead class="bg-main/90">
                <tr>
                    <th class="py-3 px-2 w-12 text-center text-xs font-bold text-white uppercase tracking-wider border-b border-r border-gray-300">No.</th>
                    <th class="py-3 px-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b border-r border-gray-300">Kategori Pendapatan</th>
                    <th class="py-3 px-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b border-r border-gray-300">Periode</th>
                    <th class="py-3 px-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b border-r border-gray-300">Target Pendapatan</th>
                    <th class="py-3 px-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b border-r border-gray-300">Realisasi</th>
                    <th class="py-3 px-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b border-r border-gray-300">Status</th>
                    <?php if ($isAdmin): ?>
                    <th class="py-3 px-4 text-left text-xs font-bold text-white uppercase tracking-wider border-b border-r border-gray-300">User</th>
                    <?php endif; ?>
                    <th class="py-3 px-2 w-40 text-center text-xs font-bold text-white uppercase tracking-wider border-b border-r border-gray-300">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($budgets)) : ?>
                    <tr>
                        <td colspan="<?= (isset($isAdmin) && $isAdmin) ? '8' : '7' ?>" class="py-4 px-4 text-center text-gray-400">
                            Belum ada data perencanaan pendapatan
                        </td>
                    </tr>
                <?php else : ?>
                    <?php 
                    $no = 1;
                    foreach ($budgets as $budget) : 
                        $usage = isset($budgetModel) ? $budgetModel->getCurrentUsage($budget['category_id'], $budget['periode']) : 0;
                        $percentage = $budget['jumlah_anggaran'] > 0 ? ($usage / $budget['jumlah_anggaran']) * 100 : 0;
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-2 px-2 text-center text-sm text-gray-700 font-medium border-b border-r border-gray-200"><?= $no++ ?></td>
                        <td class="py-2 px-4 text-sm border-b border-r border-gray-200">
                            <?= esc($budget['nama_kategori']) ?>
                        </td>
                        <td class="py-2 px-4 text-sm text-gray-800 border-b border-r border-gray-200">
                            <?= date('F Y', strtotime($budget['periode'] . '-01')) ?>
                        </td>
                        <td class="py-2 px-4 text-sm font-bold border-b border-r border-gray-200">
                            Rp <?= number_format($budget['jumlah_anggaran'], 0, ',', '.') ?>
                        </td>
                        <td class="py-2 px-4 text-sm font-bold border-b border-r border-gray-200">
                            Rp <?= number_format($usage, 0, ',', '.') ?>
                        </td>
                        <td class="py-2 px-4 border-b border-r border-gray-200">
                            <div class="flex items-center">
                                <div class="relative w-full h-2 bg-gray-200 rounded">
                                    <div class="absolute top-0 left-0 h-full rounded <?= $percentage >= 100 ? 'bg-green-500' : 'bg-blue-500' ?>" style="width: <?= min($percentage, 100) ?>%"></div>
                                </div>
                                <span class="ml-2 text-sm <?= $percentage >= 100 ? 'text-green-600' : 'text-blue-600' ?>"><?= number_format($percentage, 1) ?>%</span>
                            </div>
                        </td>
                        <?php if ($isAdmin): ?>
                        <td class="py-2 px-4 text-sm text-gray-700 border-b border-r border-gray-200">
                            <?= esc($budget['username']) ?>
                        </td>
                        <?php endif; ?>
                        <td class="py-2 px-2 text-center border-b border-r border-gray-200">
                            <div class="flex justify-center gap-1">
                                <button type="button"
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded hover:bg-blue-600"
                                    title="Detail"
                                    onclick='toggleDetailBudgetModal(true, {
                                        "id": <?= $budget["id"] ?>,
                                        "nama_kategori": "<?= esc($budget["nama_kategori"]) ?>",
                                        "periode": "<?= $budget["periode"] ?>",
                                        "jumlah_anggaran": <?= $budget["jumlah_anggaran"] ?>,
                                        "current_usage": <?= $usage ?>,
                                        "username": "<?= esc($budget["username"] ?? "") ?>",
                                        "created_at": "<?= $budget["created_at"] ?>",
                                        "updated_at": "<?= $budget["updated_at"] ?>"
                                    })'>
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </button>
                                <button type="button"
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold text-white bg-yellow-500 rounded hover:bg-yellow-600"
                                    title="Ubah"
                                    onclick='toggleEditBudgetModal(true, <?= json_encode($budget, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)'>
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Ubah
                                </button>
                                <button type="button"
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded hover:bg-red-600"
                                    title="Hapus"
                                    onclick='toggleDeleteBudgetModal(true, <?= json_encode($budget, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)'>
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (isset($pager) && isset($total_budgets) && $total_budgets > $perPage): ?>
    <div class="mt-4 flex justify-center">
        <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
            <?= $pager->makeLinks($pager->getCurrentPage(), $perPage, $total_budgets, 'default_full') ?>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
