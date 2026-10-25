<!-- Hero Section -->
<section class="bg-gradient-to-b from-amber-100/80 via-amber-50/50 to-transparent py-12 md:py-20 border-b border-amber-200/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-100 text-orange-800 text-xs font-bold tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    Hot &amp; Freshly Cooked Everyday
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-stone-900 tracking-tight leading-tight">
                    Your Favorite Canteen Meals, <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-amber-600">Ready on Time.</span>
                </h1>
                <p class="text-lg text-stone-600 max-w-2xl leading-relaxed">
                    Browse today's home-cooked ulam, sizzling rice meals, merienda snacks, and cold beverages. Order in advance and grab your meal hot and fresh at the counter.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="/menu" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-base shadow-lg shadow-orange-600/25 transition-all hover:-translate-y-0.5">
                        <span>Browse Full Menu</span>
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </a>
                    <a href="/orders/track" class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl bg-white hover:bg-stone-50 text-stone-800 font-bold text-base border border-stone-200 shadow-sm transition">
                        Track Existing Order
                    </a>
                </div>

                <!-- Feature Badges -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-amber-200/60 max-w-lg">
                    <div>
                        <div class="text-xl font-extrabold text-orange-600">Fast</div>
                        <div class="text-xs text-stone-500 font-medium">Pickup Ready</div>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-orange-600">Fresh</div>
                        <div class="text-xs text-stone-500 font-medium">Cooked Daily</div>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-orange-600">Cash</div>
                        <div class="text-xs text-stone-500 font-medium">Pay on Counter</div>
                    </div>
                </div>
            </div>

            <!-- Hero Graphic Card -->
            <div class="lg:col-span-5">
                <div class="relative bg-gradient-to-tr from-amber-600 to-orange-500 p-8 rounded-3xl text-white shadow-2xl shadow-orange-500/20">
                    <div class="absolute -top-3 -right-3 bg-stone-900 text-amber-400 text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider shadow">
                        Carinderia Special
                    </div>
                    <div class="space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                        <h2 class="text-2xl font-bold">How it Works</h2>
                        <ol class="space-y-3 text-sm text-amber-100">
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-white text-orange-600 flex items-center justify-center font-bold text-xs shrink-0">1</span>
                                <span>Choose your favorite dishes from our live menu.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-white text-orange-600 flex items-center justify-center font-bold text-xs shrink-0">2</span>
                                <span>Checkout with your name and contact details.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-white text-orange-600 flex items-center justify-center font-bold text-xs shrink-0">3</span>
                                <span>Head to the counter, pay cash, and claim your meal!</span>
                            </li>
                        </ol>
                        <div class="pt-2">
                            <a href="/menu" class="block text-center py-2.5 px-4 rounded-xl bg-white text-orange-600 font-bold text-sm hover:bg-orange-50 transition shadow">
                                Order Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Categories Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-stone-900">Explore by Category</h2>
            <p class="text-sm text-stone-500 mt-1">Select a category to view dishes</p>
        </div>
        <a href="/menu" class="inline-flex items-center gap-1.5 text-sm font-bold text-orange-600 hover:text-orange-700">
            <span>See all</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <?php foreach ($categories as $cat): ?>
            <a href="/menu?category=<?= (int)$cat['id'] ?>" class="group block p-5 rounded-2xl bg-white border border-stone-200/80 shadow-sm hover:border-orange-500 hover:shadow-md transition">
                <div class="text-2xl mb-2">
                    <?php
                        $catIcon = match (strtolower($cat['name'])) {
                            'rice meals' => 'fa-bowl-rice text-amber-600',
                            'viands' => 'fa-drumstick-bite text-orange-600',
                            'snacks' => 'fa-cookie-bite text-amber-500',
                            'drinks' => 'fa-mug-hot text-sky-600',
                            default => 'fa-utensils text-stone-600',
                        };
                    ?>
                    <i class="fa-solid <?= $catIcon ?>"></i>
                </div>
                <h3 class="font-bold text-stone-900 group-hover:text-orange-600 transition"><?= e($cat['name']) ?></h3>
                <p class="text-xs text-stone-500 mt-1 line-clamp-1"><?= e($cat['description'] ?? 'Delicious items') ?></p>
                <div class="mt-3 text-xs font-semibold text-orange-600">
                    <?= (int)$cat['products_count'] ?> <?= (int)$cat['products_count'] === 1 ? 'item' : 'items' ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-stone-900">Today's Featured Dishes</h2>
            <p class="text-sm text-stone-500 mt-1">Freshly prepared and ready to order</p>
        </div>
        <a href="/menu" class="inline-flex items-center gap-1.5 text-sm font-bold text-orange-600 hover:text-orange-700">
            <span>View Full Menu</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-12 bg-white rounded-2xl border border-stone-200 p-8">
            <p class="text-stone-500">No menu items are currently available. Please check back shortly.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm hover:shadow-md transition flex flex-col overflow-hidden">
                    <!-- Image or Food Icon Placeholder -->
                    <div class="h-44 bg-gradient-to-tr from-amber-100 to-orange-50 flex items-center justify-center relative overflow-hidden">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="text-4xl text-amber-600/70">
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
                    </div>

                    <!-- Details -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-lg text-stone-900">
                                <a href="/menu/<?= (int)$product['id'] ?>" class="hover:text-orange-600 transition">
                                    <?= e($product['name']) ?>
                                </a>
                            </h3>
                            <p class="text-xs text-stone-500 mt-1 line-clamp-2 leading-relaxed">
                                <?= e($product['description'] ?? 'Delicious canteen dish cooked fresh.') ?>
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-stone-400 font-medium">Price</span>
                                <div class="text-xl font-extrabold text-stone-900">
                                    <?= format_price($product['price']) ?>
                                </div>
                            </div>

                            <form action="/cart/add" method="POST" class="inline-flex">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs shadow-sm hover:shadow transition">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    <span>Add to Cart</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
