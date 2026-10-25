<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Back Link -->
    <a href="/menu" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-600 hover:text-orange-600 mb-6 transition">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        <span>Back to Menu</span>
    </a>

    <div class="bg-white rounded-3xl border border-stone-200/80 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-2">
        <!-- Image / Visual -->
        <div class="h-72 md:h-full bg-gradient-to-tr from-amber-100 to-orange-50 flex items-center justify-center p-8 relative">
            <?php if (!empty($product['image'])): ?>
                <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="text-7xl text-amber-600/70">
                    <?php
                        $catName = strtolower($product['category_name'] ?? '');
                        $prodIcon = str_contains($catName, 'drink') ? 'fa-mug-hot' : (str_contains($catName, 'snack') ? 'fa-cookie-bite' : (str_contains($catName, 'rice') ? 'fa-bowl-rice' : 'fa-bowl-food'));
                    ?>
                    <i class="fa-solid <?= $prodIcon ?>"></i>
                </div>
            <?php endif; ?>

            <?php if (!empty($product['category_name'])): ?>
                <span class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm text-stone-800 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                    <?= e($product['category_name']) ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Details -->
        <div class="p-6 md:p-10 flex flex-col justify-between">
            <div class="space-y-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                        <?= e($product['name']) ?>
                    </h1>
                    <div class="mt-2 flex items-center gap-2">
                        <?php if ((int)$product['is_available'] === 1): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available Now
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Sold Out / Unavailable
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-3xl font-extrabold text-stone-900">
                    <?= format_price($product['price']) ?>
                </div>

                <p class="text-sm text-stone-600 leading-relaxed">
                    <?= e($product['description'] ?? 'No description provided for this delicious canteen item.') ?>
                </p>

                <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200/60 text-xs text-amber-900 space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-amber-600"></i>
                        <span>Order Note:</span>
                    </p>
                    <p>All items are prepared fresh for pickup at the canteen counter. Payment is cash only upon pickup.</p>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <div class="pt-8 border-t border-stone-100 mt-6">
                <?php if ((int)$product['is_available'] === 1): ?>
                    <form action="/cart/add" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

                        <div class="flex items-center gap-4">
                            <label for="quantity" class="text-xs font-bold text-stone-700 uppercase tracking-wider">
                                Quantity:
                            </label>
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="1"
                                min="1"
                                max="50"
                                class="w-20 px-3 py-2 rounded-xl border border-stone-300 text-center font-bold text-stone-900 focus:outline-none focus:ring-2 focus:ring-orange-500"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3.5 px-6 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-lg shadow-orange-600/20 transition-all hover:shadow-xl flex items-center justify-center gap-2"
                        >
                            <i class="fa-solid fa-cart-plus"></i>
                            <span>Add to Cart</span>
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center py-3 bg-stone-100 rounded-xl text-stone-500 font-semibold text-sm">
                        This item is currently unavailable
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
