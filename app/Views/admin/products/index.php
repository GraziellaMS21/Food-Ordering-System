<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Products Management</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Manage canteen menu dishes, prices, and availability</p>
        </div>

        <a href="/admin/products/create" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-sm hover:shadow transition">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add New Product</span>
        </a>
    </div>

    <!-- Category Filter Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <a href="/admin/products" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= !$selectedCategoryId ? 'bg-orange-600 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' ?>">
            All Categories
        </a>
        <?php foreach ($categories as $cat): ?>
            <?php $isActive = $selectedCategoryId === (int)$cat['id']; ?>
            <a href="/admin/products?category=<?= (int)$cat['id'] ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $isActive ? 'bg-orange-600 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' ?>">
                <?= e($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
        <?php if (empty($products)): ?>
            <div class="p-12 text-center text-stone-400 text-sm">
                No products found in this category.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 uppercase font-bold tracking-wider border-b border-stone-100">
                        <tr>
                            <th class="px-5 py-3">Item</th>
                            <th class="px-5 py-3">Category</th>
                            <th class="px-5 py-3">Price</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700 font-medium">
                        <?php foreach ($products as $prod): ?>
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-100 to-orange-50 flex items-center justify-center shrink-0 overflow-hidden">
                                            <?php if (!empty($prod['image'])): ?>
                                                <img src="<?= e($prod['image']) ?>" alt="<?= e($prod['name']) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <i class="fa-solid fa-bowl-food text-xl text-stone-400"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-stone-900 text-sm"><?= e($prod['name']) ?></div>
                                            <div class="text-[11px] text-stone-400 line-clamp-1 max-w-xs">
                                                <?= e($prod['description'] ?? 'No description') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-stone-100 text-stone-700">
                                        <?= e($prod['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-stone-900 text-sm">
                                    <?= format_price($prod['price']) ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <?php if ((int)$prod['is_available'] === 1): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Sold Out
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="/admin/products/<?= (int)$prod['id'] ?>/edit" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold text-[11px] transition">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="/admin/products/<?= (int)$prod['id'] ?>/delete" method="POST" onsubmit="return confirm('Delete this menu item?');" class="inline">
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
