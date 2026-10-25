<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Header -->
    <div class="mb-8">
        <a href="/cart" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-600 hover:text-orange-600 mb-2 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back to Cart</span>
        </a>
        <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Checkout Order</h1>
        <p class="text-sm text-stone-500 mt-1">Provide your pickup contact details to confirm your canteen order</p>
    </div>

    <form action="/orders" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <?= csrf_field() ?>

        <!-- Customer Information Form -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-lg font-bold text-stone-900 mb-1">Customer Information</h2>
                <p class="text-xs text-stone-500">We'll use this name at the counter when calling your order.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="customer_name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        required
                        placeholder="e.g. Maria Santos"
                        class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                    >
                </div>

                <div>
                    <label for="customer_contact" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Mobile / Contact Number <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="tel"
                        id="customer_contact"
                        name="customer_contact"
                        required
                        placeholder="e.g. 0917-123-4567"
                        class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                    >
                    <p class="text-[11px] text-stone-400 mt-1">Needed for pickup tracking and order verification.</p>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Order Notes / Special Requests (Optional)
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        placeholder="e.g. Extra sauce, no onions, pack separately..."
                        class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                    ></textarea>
                </div>
            </div>

            <!-- Payment Method Card -->
            <div class="pt-4 border-t border-stone-100">
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">
                    Payment Method
                </label>
                <div class="p-4 rounded-xl border-2 border-orange-500 bg-orange-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-bold text-lg">
                            <i class="fa-solid fa-money-bill-wave text-base"></i>
                        </div>
                        <div>
                            <div class="font-bold text-stone-900 text-sm">Cash on Counter Pickup</div>
                            <div class="text-xs text-stone-600">Pay in cash directly to our cashier when picking up your food.</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-orange-700 bg-orange-200/70 px-2.5 py-1 rounded-full">
                        Selected
                    </span>
                </div>
            </div>
        </div>

        <!-- Order Summary & Submit -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 sm:p-8 space-y-6 sticky top-24">
            <h2 class="text-lg font-bold text-stone-900 border-b border-stone-100 pb-3">Items in Order</h2>

            <div class="divide-y divide-stone-100 max-h-64 overflow-y-auto pr-1">
                <?php foreach ($cartItems as $item): ?>
                    <div class="py-2.5 flex items-center justify-between text-sm">
                        <div>
                            <span class="font-bold text-stone-900"><?= (int)$item['quantity'] ?>x</span>
                            <span class="font-medium text-stone-700 ml-1"><?= e($item['name']) ?></span>
                            <div class="text-xs text-stone-400"><?= format_price($item['price']) ?> each</div>
                        </div>
                        <div class="font-bold text-stone-800">
                            <?= format_price($item['subtotal']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="border-t border-stone-100 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-stone-600">
                    <span>Fulfillment</span>
                    <span class="font-semibold text-stone-900">Pickup Counter</span>
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-stone-100">
                    <span class="text-base font-bold text-stone-900">Total to Pay</span>
                    <span class="text-2xl font-extrabold text-orange-600"><?= format_price($total) ?></span>
                </div>
            </div>

            <button
                type="submit"
                class="w-full py-4 px-6 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-extrabold text-base shadow-xl shadow-orange-600/25 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2"
            >
                <span>Confirm &amp; Place Pickup Order</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>

            <div class="text-center text-xs text-stone-400 leading-relaxed">
                By placing this order, you agree to claim and pay for your items at the canteen counter.
            </div>
        </div>
    </form>
</div>
