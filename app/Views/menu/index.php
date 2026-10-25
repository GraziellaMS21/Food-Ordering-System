<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                <?= $activeCategory ? e($activeCategory['name']) : 'Canteen Menu' ?>
            </h1>
            <p class="text-sm text-stone-500 mt-1">
                <?= $activeCategory && !empty($activeCategory['description'])
                    ? e($activeCategory['description'])
                    : 'Freshly prepared carinderia meals and drinks ready for pickup.' ?>
            </p>
        </div>

        <!-- Search Bar -->
        <form action="/menu" method="GET" class="flex items-center gap-2 max-w-md w-full md:w-auto">
            <?php if ($activeCategoryId): ?>
                <input type="hidden" name="category" value="<?= (int)$activeCategoryId ?>">
            <?php endif; ?>
            <div class="relative w-full">
                <input
                    type="text"
                    name="search"
                    placeholder="Search dishes..."
                    value="<?= e($search ?? '') ?>"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm bg-white"
                >
                <i class="fa-solid fa-magnifying-glass text-stone-400 absolute left-3.5 top-3.5 text-xs"></i>
            </div>
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-semibold text-sm transition">
                Search
            </button>
            <?php if (!empty($search)): ?>
                <a href="/menu<?= $activeCategoryId ? '?category=' . (int)$activeCategoryId : '' ?>" class="text-xs text-stone-500 hover:text-stone-800 underline">
                    Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Category Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 scrollbar-none">
        <a href="/menu<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>"
           class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition <?= !$activeCategoryId ? 'bg-orange-600 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' ?>">
            All Items
        </a>
        <?php foreach ($categories as $cat): ?>
            <?php $isActive = $activeCategoryId === (int)$cat['id']; ?>
            <a href="/menu?category=<?= (int)$cat['id'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
               class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition <?= $isActive ? 'bg-orange-600 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' ?>">
                <?= e($cat['name']) ?>
                <span class="ml-1 text-xs opacity-75">(<?= (int)$cat['products_count'] ?>)</span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Product Grid -->
    <?php if (empty($products)): ?>
        <div class="bg-white rounded-3xl border border-stone-200 p-12 text-center max-w-lg mx-auto">
            <div class="mb-4">
                <i class="fa-solid fa-utensils text-5xl text-stone-300"></i>
            </div>
            <h3 class="text-lg font-bold text-stone-900 mb-1">No food items found</h3>
            <p class="text-sm text-stone-500 mb-6">We couldn't find any items matching your criteria. Try adjusting your search or category filter.</p>
            <a href="/menu" class="inline-flex px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-sm transition">
                View All Menu Items
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm hover:shadow-md transition flex flex-col overflow-hidden group">
                    <!-- Image or Food Placeholder -->
                    <a href="/menu/<?= (int)$product['id'] ?>" class="h-48 bg-gradient-to-tr from-amber-100 to-orange-50 flex items-center justify-center relative overflow-hidden block">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="text-5xl text-amber-600/70 group-hover:scale-110 transition duration-300">
                                <?php
                                    $catName = strtolower($product['category_name'] ?? '');
                                    $prodIcon = str_contains($catName, 'drink') ? 'fa-mug-hot' : (str_contains($catName, 'snack') ? 'fa-cookie-bite' : (str_contains($catName, 'rice') ? 'fa-bowl-rice' : 'fa-bowl-food'));
                                ?>
                                <i class="fa-solid <?= $prodIcon ?>"></i>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['category_name'])): ?>
                            <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-stone-800 text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                                <?= e($product['category_name']) ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-lg text-stone-900">
                                <a href="/menu/<?= (int)$product['id'] ?>" class="hover:text-orange-600 transition">
                                    <?= e($product['name']) ?>
                                </a>
                            </h3>
                            <p class="text-xs text-stone-500 mt-1 line-clamp-2 leading-relaxed">
                                <?= e($product['description'] ?? 'Delicious canteen dish freshly prepared.') ?>
                            </p>
                        </div>

                        <!-- Price and Add to Cart -->
                        <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between gap-3">
                            <div>
                                <span class="text-xs text-stone-400 font-medium">Price</span>
                                <div class="text-xl font-extrabold text-stone-900">
                                    <?= format_price($product['price']) ?>
                                </div>
                            </div>

                            <form action="/cart/add" method="POST" class="flex items-center gap-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs shadow-sm hover:shadow transition">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>Add</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
