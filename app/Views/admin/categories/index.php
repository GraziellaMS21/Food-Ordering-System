<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Menu Categories</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Organize your canteen food and beverage offerings into categories</p>
        </div>

        <a href="/admin/categories/create" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-sm hover:shadow transition">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add New Category</span>
        </a>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <h2 class="font-bold text-base text-stone-900">Existing Categories (<?= count($categories) ?>)</h2>
        </div>

        <?php if (empty($categories)): ?>
            <div class="p-12 text-center text-stone-400 text-sm">
                No categories created yet.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 uppercase font-bold tracking-wider border-b border-stone-100">
                        <tr>
                            <th class="px-5 py-3">ID</th>
                            <th class="px-5 py-3">Category Name</th>
                            <th class="px-5 py-3">Description</th>
                            <th class="px-5 py-3">Products</th>
                            <th class="px-5 py-3">Created</th>
                            <th class="px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700 font-medium">
                        <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="px-5 py-3.5 text-stone-400 font-mono">#<?= (int)$cat['id'] ?></td>
                                <td class="px-5 py-3.5 font-bold text-stone-900">
                                    <?= e($cat['name']) ?>
                                </td>
                                <td class="px-5 py-3.5 text-stone-500">
                                    <?= e($cat['description'] ?? '—') ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="/admin/products?category=<?= (int)$cat['id'] ?>" class="inline-flex items-center gap-1 font-bold text-orange-600 hover:underline">
                                        <?= (int)$cat['products_count'] ?> products
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-stone-400 text-[11px]">
                                    <?= date('M d, Y', strtotime($cat['created_at'])) ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="/admin/categories/<?= (int)$cat['id'] ?>/edit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold text-[11px] transition">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            <span>Edit</span>
                                        </a>
                                        <form action="/admin/categories/<?= (int)$cat['id'] ?>/delete" method="POST" style="display:inline" data-confirm-title="Delete Category" data-confirm-message="Delete this category? Products must be removed first." data-confirm-variant="danger" data-confirm-confirm-text="Delete Category">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>


</div>
