<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Link -->
    <a href="/admin/orders" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-600 hover:text-orange-600 transition">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Back to Order Queue</span>
    </a>

    <!-- Order Header Card -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-100">
            <div>
                <span class="text-xs font-bold text-orange-600 uppercase tracking-widest">Order Management</span>
                <h1 class="text-2xl sm:text-3xl font-black text-stone-900 font-mono mt-1">
                    <?= e($order['order_number']) ?>
                </h1>
                <p class="text-xs text-stone-400 mt-1">
                    Created on <?= date('F d, Y \a\t h:i A', strtotime($order['created_at'])) ?>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Status Badge -->
                <?php
                    $st = $order['order_status'];
                    $stClass = match ($st) {
                        'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                        'confirmed' => 'bg-sky-100 text-sky-800 border-sky-300',
                        'preparing' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                        'ready' => 'bg-emerald-100 text-emerald-800 border-emerald-300 animate-pulse',
                        'completed' => 'bg-stone-100 text-stone-600 border-stone-300',
                        'cancelled' => 'bg-rose-100 text-rose-800 border-rose-300',
                        default => 'bg-stone-100 text-stone-700 border-stone-300',
                    };
                ?>
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border <?= $stClass ?>">
                    Status: <?= e(str_replace('_', ' ', $st)) ?>
                </span>

                <!-- Payment Badge -->
                <?php $isPaid = $order['payment_status'] === 'paid'; ?>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border <?= $isPaid ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-300' ?>">
                    <?php if ($isPaid): ?>
                        <i class="fa-solid fa-circle-check"></i> Paid
                    <?php else: ?>
                        <i class="fa-solid fa-hourglass-half"></i> Payment: Unpaid
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <!-- Customer & Order Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 py-6 border-b border-stone-100 text-xs">
            <div>
                <span class="text-stone-400 font-semibold block uppercase tracking-wider">Customer Name</span>
                <span class="font-bold text-stone-900 text-sm mt-0.5 block"><?= e($order['customer_name']) ?></span>
            </div>
            <div>
                <span class="text-stone-400 font-semibold block uppercase tracking-wider">Contact Number</span>
                <span class="font-bold text-stone-900 text-sm mt-0.5 block">
                    <?= e($order['customer_contact'] ?? 'None provided') ?>
                </span>
            </div>
            <div>
                <span class="text-stone-400 font-semibold block uppercase tracking-wider">Fulfillment &amp; Payment</span>
                <span class="font-bold text-stone-900 text-sm mt-0.5 block">Pickup (Cash)</span>
            </div>
            <?php if (!empty($order['notes'])): ?>
                <div class="sm:col-span-3 bg-amber-50/70 p-3 rounded-xl border border-amber-200/60">
                    <span class="text-amber-800 font-bold block uppercase tracking-wider text-[11px]">Customer Special Request / Notes:</span>
                    <p class="text-stone-700 text-xs mt-1 italic"><?= e($order['notes']) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Staff Action Panel: Status Update & Payment -->
        <div class="py-6 border-b border-stone-100 grid grid-cols-1 md:grid-cols-2 gap-6 bg-stone-50/60 -mx-6 sm:-mx-8 px-6 sm:px-8">
            <!-- 1. Order Status Transition -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Kitchen / Pickup Status Transition</h3>

                <?php if (!empty($allowedNextStatuses)): ?>
                    <form action="/admin/orders/status" method="POST" class="flex flex-wrap items-center gap-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

                        <select name="status" class="px-3 py-2 rounded-xl border border-stone-300 text-xs font-bold bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <?php foreach ($allowedNextStatuses as $nextSt): ?>
                                <option value="<?= e($nextSt) ?>">
                                    Change to: <?= ucwords(str_replace('_', ' ', $nextSt)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-stone-900 hover:bg-orange-600 text-white font-bold text-xs transition">
                            <i class="fa-solid fa-arrows-rotate text-xs"></i>
                            <span>Update Status</span>
                        </button>
                    </form>
                    <p class="text-[11px] text-stone-500">Allowed transitions from current status: <?= implode(', ', array_map('ucwords', $allowedNextStatuses)) ?></p>
                <?php else: ?>
                    <div class="p-3 bg-stone-100 rounded-xl text-xs text-stone-500 font-semibold">
                        This order is marked as <?= e($st) ?> and has reached a terminal status.
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. Cash Payment Recording -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Cash Payment Collection</h3>

                <?php if (!$isPaid): ?>
                    <form action="/admin/orders/payment" method="POST" onsubmit="return confirm('Confirm that cash payment of <?= format_price($order['total_amount']) ?> has been received at the counter?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                        <button
                            type="submit"
                            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm hover:shadow transition inline-flex items-center gap-1.5"
                        >
                            <i class="fa-solid fa-money-bill-wave text-xs"></i>
                            <span>Record Cash Received (<?= format_price($order['total_amount']) ?>)</span>
                        </button>
                    </form>
                    <p class="text-[11px] text-stone-500">Only click after the customer physically hands over the cash.</p>
                <?php else: ?>
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                        <span>Payment Confirmed &amp; Recorded in Cash</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ordered Items Table -->
        <div class="pt-6 space-y-4">
            <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Ordered Food Items</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 uppercase font-bold tracking-wider border-b border-stone-100">
                        <tr>
                            <th class="px-4 py-2.5">Item Name</th>
                            <th class="px-4 py-2.5">Unit Price</th>
                            <th class="px-4 py-2.5 text-center">Quantity</th>
                            <th class="px-4 py-2.5 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700 font-medium">
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="px-4 py-3 font-bold text-stone-900">
                                    <?= e($item['product_name']) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?= format_price($item['unit_price']) ?>
                                </td>
                                <td class="px-4 py-3 text-center font-bold">
                                    <?= (int)$item['quantity'] ?>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-stone-900">
                                    <?= format_price($item['subtotal']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="border-t-2 border-stone-200 bg-stone-50 font-bold">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right text-stone-900 uppercase tracking-wider text-xs">
                                Total Amount:
                            </td>
                            <td class="px-4 py-3 text-right text-orange-600 text-base font-extrabold">
                                <?= format_price($order['total_amount']) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
