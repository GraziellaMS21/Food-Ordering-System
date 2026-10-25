<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Header -->
    <div class="text-center max-w-xl mx-auto mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">Track Your Order</h1>
        <p class="text-sm text-stone-500 mt-2">
            Enter your order reference number or tracking token to check current kitchen preparation and pickup status.
        </p>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 mb-8 max-w-2xl mx-auto">
        <form action="/orders/track" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="order_number" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Order Number
                    </label>
                    <input
                        type="text"
                        id="order_number"
                        name="order_number"
                        placeholder="e.g. MD-20261025-XXXX"
                        value="<?= e($orderNumber ?? '') ?>"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-mono uppercase"
                    >
                </div>
                <div>
                    <label for="contact" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Contact Number (Verification)
                    </label>
                    <input
                        type="text"
                        id="contact"
                        name="contact"
                        placeholder="e.g. 09171234567"
                        value="<?= e($contact ?? '') ?>"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                    >
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <button
                    type="submit"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-sm hover:shadow transition flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Look Up Order Status</span>
                </button>

                <?php if ($searched): ?>
                    <a href="/orders/track" class="text-xs text-stone-500 hover:text-stone-800 underline">
                        Reset Search
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if ($order): ?>
        <!-- Order Tracking Card -->
        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-md p-6 sm:p-10 space-y-8">
            <!-- Order Header & Status Badge -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-100">
                <div>
                    <div class="text-xs font-bold text-orange-600 uppercase tracking-wider">Order Reference</div>
                    <h2 class="text-2xl sm:text-3xl font-black text-stone-900 font-mono mt-0.5">
                        <?= e($order['order_number']) ?>
                    </h2>
                    <div class="text-xs text-stone-400 mt-1">
                        Placed on <?= date('M d, Y - h:i A', strtotime($order['created_at'])) ?> &bull; Customer: <?= e($order['customer_name']) ?>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Order Status Badge -->
                    <?php
                        $status = $order['order_status'];
                        $badgeStyle = match ($status) {
                            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'confirmed' => 'bg-sky-100 text-sky-800 border-sky-300',
                            'preparing' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                            'ready' => 'bg-emerald-100 text-emerald-800 border-emerald-300 animate-pulse',
                            'completed' => 'bg-stone-100 text-stone-700 border-stone-300',
                            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-300',
                            default => 'bg-stone-100 text-stone-800 border-stone-200',
                        };
                    ?>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border <?= $badgeStyle ?>">
                        ● Status: <?= e(ucwords(str_replace('_', ' ', $status))) ?>
                    </span>

                    <!-- Payment Status Badge -->
                    <?php
                        $paymentPaid = $order['payment_status'] === 'paid';
                    ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border <?= $paymentPaid ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' ?>">
                        <?php if ($paymentPaid): ?>
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>Paid</span>
                        <?php else: ?>
                            <i class="fa-solid fa-hourglass-half text-amber-600"></i>
                            <span>Payment: Unpaid (Cash on Pickup)</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <!-- Visual Progression Timeline -->
            <?php if ($status !== 'cancelled'): ?>
                <?php
                    $steps = [
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'preparing' => 'Preparing',
                        'ready' => 'Ready for Pickup',
                        'completed' => 'Completed',
                    ];
                    $stepKeys = array_keys($steps);
                    $currentIndex = array_search($status, $stepKeys, true);
                    if ($currentIndex === false) $currentIndex = 0;
                ?>
                <div class="py-4">
                    <div class="grid grid-cols-5 gap-2 text-center">
                        <?php foreach ($steps as $key => $label): ?>
                            <?php
                                $stepIdx = array_search($key, $stepKeys, true);
                                $isDone = $stepIdx <= $currentIndex;
                                $isCurrent = $stepIdx === $currentIndex;
                            ?>
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm mb-2 transition-all <?= $isCurrent ? 'bg-orange-600 text-white ring-4 ring-orange-200 shadow-md scale-110' : ($isDone ? 'bg-emerald-600 text-white' : 'bg-stone-100 text-stone-400') ?>">
                                    <?php if ($isDone): ?>
                                        <?php if ($isCurrent): ?>
                                            <i class="fa-solid fa-circle text-[8px]"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-check text-xs"></i>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?= ($stepIdx + 1) ?>
                                    <?php endif; ?>
                                </div>
                                <span class="text-[11px] sm:text-xs font-bold leading-tight <?= $isCurrent ? 'text-orange-600' : ($isDone ? 'text-stone-800' : 'text-stone-400') ?>">
                                    <?= $label ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($status === 'ready'): ?>
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800">
                        <i class="fa-solid fa-bell text-2xl text-emerald-600 shrink-0"></i>
                        <div class="text-sm font-semibold">
                            Your food is ready for pickup! Please proceed to the canteen counter with order number <strong><?= e($order['order_number']) ?></strong> to claim and pay.
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark text-rose-600 text-lg"></i>
                    <span>This order was cancelled. If you have questions, please speak directly with canteen staff.</span>
                </div>
            <?php endif; ?>

            <!-- Order Items Breakdown -->
            <div>
                <h3 class="text-sm font-bold text-stone-900 mb-3 uppercase tracking-wider text-xs">Ordered Items</h3>
                <div class="divide-y divide-stone-100 border-t border-b border-stone-100">
                    <?php foreach ($items as $item): ?>
                        <div class="py-3 flex justify-between items-center text-sm">
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

                <div class="pt-4 flex justify-between items-baseline">
                    <span class="text-base font-bold text-stone-900">Total Order Amount</span>
                    <span class="text-2xl font-extrabold text-orange-600"><?= format_price($order['total_amount']) ?></span>
                </div>
            </div>
        </div>
    <?php elseif (!$searched): ?>
        <div class="text-center py-12 bg-white rounded-3xl border border-stone-200 max-w-md mx-auto p-8 shadow-sm">
            <div class="mb-3 text-stone-300">
                <i class="fa-solid fa-magnifying-glass text-5xl"></i>
            </div>
            <h3 class="font-bold text-stone-900 mb-1">Check Order Progress</h3>
            <p class="text-xs text-stone-500 leading-relaxed">
                Enter your order number from your confirmation screen or receipts to see when your food will be ready for pickup.
            </p>
        </div>
    <?php endif; ?>
</div>
