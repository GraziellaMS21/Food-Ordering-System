<div class="max-w-2xl mx-auto space-y-6">
    <!-- Back Link -->
    <a href="/admin/products" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-600 hover:text-orange-600 transition">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Back to Products</span>
    </a>

    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight">Add New Menu Item</h1>
            <p class="text-xs text-stone-500 mt-1">Enter the details for this canteen product</p>
        </div>

        <form action="/admin/products/store" method="POST" enctype="multipart/form-data" class="space-y-5">
            <?= csrf_field() ?>

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                    Product Name <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    placeholder="e.g. Chicken Inasal with Rice"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Category
                    </label>
                    <select
                        id="category_id"
                        name="category_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium bg-white"
                    >
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="price" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Price (PHP ₱) <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        required
                        step="0.01"
                        min="0"
                        placeholder="e.g. 65.00"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                    >
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                    Description
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Describe ingredients or serving details"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                ></textarea>
            </div>

            <!-- Image File or URL -->
            <div class="space-y-3 p-4 bg-stone-50 rounded-xl border border-stone-200">
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                    Product Image (Optional)
                </label>
                <div>
                    <label for="image" class="block text-[11px] font-medium text-stone-500 mb-1">Upload Image File (JPG, PNG, WebP up to 2MB):</label>
                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/png, image/jpeg, image/webp"
                        class="block w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100"
                    >
                </div>
                <div class="pt-2 border-t border-stone-200/70">
                    <label for="image_url" class="block text-[11px] font-medium text-stone-500 mb-1">Or enter direct Image URL:</label>
                    <input
                        type="text"
                        id="image_url"
                        name="image_url"
                        placeholder="https://example.com/image.jpg"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs bg-white"
                    >
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <input
                    type="checkbox"
                    id="is_available"
                    name="is_available"
                    value="1"
                    checked
                    class="w-4 h-4 text-orange-600 rounded border-stone-300 focus:ring-orange-500"
                >
                <label for="is_available" class="text-sm font-semibold text-stone-800">
                    Item is available for customer ordering
                </label>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="/admin/products" class="px-4 py-2.5 rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-bold transition">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-sm hover:shadow transition"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Save Product</span>
                </button>
            </div>
        </form>
    </div>
</div>
