<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Success Banner -->
    <div class="bg-white rounded-3xl border border-stone-200/80 shadow-md p-6 sm:p-10 text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
            <i class="fa-solid fa-circle-check text-3xl"></i>
        </div>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 uppercase tracking-wider mb-2">
            Order Submitted Successfully
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
            Thank You, <?= e($order['customer_name']) ?>!
        </h1>
        <p class="text-sm text-stone-600 mt-2 max-w-lg mx-auto">
            Your pickup order has been received by the kitchen. Please present your order number when claiming and paying at the counter.
        </p>

        <!-- Big Order Number Box -->
        <div class="mt-8 p-6 bg-gradient-to-tr from-amber-50 to-orange-50 border-2 border-orange-200 rounded-2xl max-w-md mx-auto text-center">
            <div class="text-xs font-bold text-orange-600 uppercase tracking-widest">Your Order Reference Number</div>
            <div class="text-3xl sm:text-4xl font-black text-stone-900 tracking-wider my-2 font-mono">
                <?= e($order['order_number']) ?>
            </div>
            <div class="text-xs text-stone-500 font-medium">
                Placed on <?= date('M d, Y - h:i A', strtotime($order['created_at'])) ?>
            </div>
        </div>

        <!-- Secure Tracking Link Box -->
        <div class="mt-6 p-4 bg-stone-50 rounded-2xl border border-stone-200 text-left max-w-xl mx-auto">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-link text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="font-bold text-stone-900 text-sm">Bookmark your secure tracking link</div>
                    <p class="text-xs text-stone-500 mt-0.5">
                        You can follow live order preparation progress using this private tracking link anytime:
                    </p>
                    <div class="mt-2 flex items-center gap-2">
                        <input
                            type="text"
                            readonly
                            value="<?= (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : '') ?>/orders/track?token=<?= e($trackingToken) ?>"
                            class="w-full text-xs font-mono bg-white p-2 rounded-lg border border-stone-200 text-stone-700 select-all"
                            onclick="this.select()"
                        >
                        <a
                            href="/orders/track?token=<?= e($trackingToken) ?>"
                            class="px-3 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5"
                        >
                            <span>Track Live</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Breakdown Card -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <h2 class="text-lg font-bold text-stone-900 border-b border-stone-100 pb-3">Order Details</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-xs text-stone-400 block font-medium">Customer Name</span>
                <span class="font-bold text-stone-900"><?= e($order['customer_name']) ?></span>
            </div>
            <div>
                <span class="text-xs text-stone-400 block font-medium">Contact Number</span>
                <span class="font-bold text-stone-900"><?= e($order['customer_contact'] ?? 'None provided') ?></span>
            </div>
            <div>
                <span class="text-xs text-stone-400 block font-medium">Fulfillment &amp; Payment</span>
                <span class="font-bold text-stone-900">Counter Pickup (Cash Only)</span>
            </div>
            <div>
                <span class="text-xs text-stone-400 block font-medium">Payment Status</span>
                <span class="inline-flex items-center gap-1 font-bold text-amber-700">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Unpaid (Pay upon pickup)
                </span>
            </div>
            <?php if (!empty($order['notes'])): ?>
                <div class="sm:col-span-2">
                    <span class="text-xs text-stone-400 block font-medium">Notes</span>
                    <span class="text-stone-700 italic"><?= e($order['notes']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="border-t border-stone-100 pt-4">
            <h3 class="text-sm font-bold text-stone-900 mb-3 uppercase tracking-wider text-xs">Ordered Items</h3>
            <div class="divide-y divide-stone-100">
                <?php foreach ($items as $item): ?>
                    <div class="py-2.5 flex justify-between items-center text-sm">
                        <div>
                            <span class="font-bold text-stone-900"><?= (int)$item['quantity'] ?>x</span>
                            <span class="font-medium text-stone-800 ml-1"><?= e($item['product_name']) ?></span>
                            <span class="text-xs text-stone-400 ml-1">(<?= format_price($item['unit_price']) ?> each)</span>
                        </div>
                        <div class="font-bold text-stone-900">
                            <?= format_price($item['subtotal']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="border-t border-stone-200 mt-4 pt-4 flex justify-between items-baseline">
                <span class="text-base font-bold text-stone-900">Total Amount to Pay</span>
                <span class="text-2xl font-extrabold text-orange-600"><?= format_price($order['total_amount']) ?></span>
            </div>
        </div>

        <div class="pt-6 border-t border-stone-100 flex flex-wrap items-center justify-between gap-4">
            <a href="/menu" class="text-sm font-semibold text-stone-600 hover:text-stone-900 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Back to Menu</span>
            </a>
            <a
                href="/orders/track?token=<?= e($trackingToken) ?>"
                class="px-6 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md shadow-orange-600/20 transition flex items-center gap-2"
            >
                <span>View Live Order Status</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>
