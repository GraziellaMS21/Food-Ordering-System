<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">Your Cart</h1>
            <p class="text-sm text-stone-500 mt-1">Review your selected meals before proceeding to checkout</p>
        </div>
        <a href="/menu" class="text-sm font-semibold text-orange-600 hover:text-orange-700 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Add more items</span>
        </a>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="bg-white rounded-3xl border border-stone-200 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="mb-4">
                <i class="fa-solid fa-cart-shopping text-6xl text-stone-300"></i>
            </div>
            <h3 class="text-xl font-bold text-stone-900 mb-2">Your cart is empty</h3>
            <p class="text-sm text-stone-500 mb-6 leading-relaxed">
                Looks like you haven't added any canteen favorites yet. Explore today's freshly prepared dishes and drinks!
            </p>
            <a href="/menu" class="inline-flex px-6 py-3.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md shadow-orange-600/20 transition">
                Browse Menu
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Items List -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
                <div class="divide-y divide-stone-100">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 <?= !$item['is_available'] ? 'bg-rose-50/50' : '' ?>">
                            <div class="flex items-center gap-4">
                                <!-- Thumbnail -->
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-tr from-amber-100 to-orange-50 flex items-center justify-center shrink-0 overflow-hidden">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <i class="fa-solid fa-bowl-food text-2xl text-stone-400"></i>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <h3 class="font-bold text-stone-900 text-base">
                                        <a href="/menu/<?= (int)$item['id'] ?>" class="hover:text-orange-600 transition">
                                            <?= e($item['name']) ?>
                                        </a>
                                    </h3>
                                    <div class="text-xs text-stone-500 mt-0.5">
                                        <?= e($item['category_name']) ?> &bull; <?= format_price($item['price']) ?> each
                                    </div>
                                    <?php if (!$item['is_available']): ?>
                                        <div class="mt-1 text-xs font-bold text-rose-600">
                                            &times; Sold Out / Unavailable
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Quantity Controls & Subtotal -->
                            <div class="flex items-center justify-between sm:justify-end gap-6 pt-2 sm:pt-0 border-t sm:border-0 border-stone-100">
                                <!-- Update form -->
                                <form action="/cart/update" method="POST" class="flex items-center gap-1.5">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                                    <input
                                        type="number"
                                        name="quantity"
                                        value="<?= (int)$item['quantity'] ?>"
                                        min="1"
                                        max="99"
                                        class="w-16 px-2 py-1.5 rounded-lg border border-stone-300 text-center font-bold text-stone-900 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none"
                                    >
                                    <button
                                        type="submit"
                                        title="Update quantity"
                                        class="px-2 py-1.5 rounded-lg text-stone-600 hover:text-stone-900 hover:bg-stone-100 text-xs font-semibold flex items-center gap-1"
                                    >
                                        <i class="fa-solid fa-arrows-rotate text-[11px]"></i>
                                        <span>Update</span>
                                    </button>
                                </form>

                                <!-- Subtotal -->
                                <div class="text-right min-w-[5rem]">
                                    <div class="font-bold text-stone-900 text-base">
                                        <?= format_price($item['subtotal']) ?>
                                    </div>
                                </div>

                                <!-- Remove form -->
                                <form action="/cart/remove" method="POST" onsubmit="return confirm('Remove this item from your cart?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                                    <button
                                        type="submit"
                                        class="text-stone-400 hover:text-rose-600 p-2 rounded-lg transition"
                                        title="Remove item"
                                    >
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 sticky top-24 space-y-6">
                <h2 class="text-lg font-bold text-stone-900 border-b border-stone-100 pb-3">Order Summary</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-stone-600">
                        <span>Items Total</span>
                        <span class="font-semibold text-stone-800"><?= format_price($total) ?></span>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <span>Fulfillment</span>
                        <span class="font-semibold text-stone-800">Canteen Pickup</span>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <span>Payment Method</span>
                        <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs">Cash on Pickup</span>
                    </div>
                    <div class="border-t border-stone-100 pt-3 flex justify-between items-baseline">
                        <span class="text-base font-bold text-stone-900">Total to Pay</span>
                        <span class="text-2xl font-extrabold text-orange-600"><?= format_price($total) ?></span>
                    </div>
                </div>

                <?php if ($hasUnavailableItems): ?>
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700">
                        Please remove unavailable items before proceeding to checkout.
                    </div>
                <?php endif; ?>

                <div>
                    <a
                        href="<?= $hasUnavailableItems ? '#' : '/checkout' ?>"
                        class="w-full block text-center py-3.5 px-4 rounded-xl <?= $hasUnavailableItems ? 'bg-stone-300 cursor-not-allowed text-stone-500' : 'bg-orange-600 hover:bg-orange-700 text-white shadow-lg shadow-orange-600/25' ?> font-bold text-sm transition flex items-center justify-center gap-2"
                    >
                        <span>Proceed to Checkout</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="text-xs text-stone-400 text-center leading-relaxed">
                    Orders are prepared once submitted. Pay at the canteen counter when collecting your meal.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
