<div class="mx-auto space-y-6">
    <!-- Back Link -->
    <a href="/admin/categories" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-600 hover:text-orange-600 transition">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Back to Categories</span>
    </a>

    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight">Edit Category</h1>
            <p class="text-xs text-stone-500 mt-1">Update the details for this menu category</p>
        </div>

        <form action="/admin/categories/<?= (int)$category['id'] ?>/update" method="POST" class="space-y-5">
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
                    value="<?= e($category['name']) ?>"
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
                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                ><?= e($category['description'] ?? '') ?></textarea>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="/admin/categories" class="px-4 py-2.5 rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-bold transition">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-sm hover:shadow transition"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
