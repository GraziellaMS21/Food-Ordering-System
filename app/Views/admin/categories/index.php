<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Menu Categories</h1>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Organize your canteen food and beverage offerings into categories</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Create Category Form -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-stone-900 border-b border-stone-100 pb-3">Add New Category</h2>

            <form action="/admin/categories/store" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Category Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                        placeholder="e.g. Desserts, Specials"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                    >
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Description (Optional)
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Short summary of items in this category"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-sm hover:shadow transition inline-flex items-center justify-center gap-1.5"
                >
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Create Category</span>
                </button>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <h2 class="font-bold text-base text-stone-900">Existing Categories (<?= count($categories) ?>)</h2>
            </div>

            <?php if (empty($categories)): ?>
                <div class="p-10 text-center text-stone-400 text-sm">
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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
