<div class="mx-auto space-y-6">
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
                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'confirmed' => 'bg-sky-100 text-sky-800 border-sky-200',
                        'preparing' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                        'ready' => 'bg-emerald-100 text-emerald-800 border-emerald-200 animate-pulse',
                        'completed' => 'bg-stone-100 text-stone-600 border-stone-200',
                        'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
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

                <?php if (!empty($order['needs_refund'])): ?>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border bg-rose-50 text-rose-700 border-rose-300">
                        <i class="fa-solid fa-triangle-exclamation"></i> Needs Refund
                    </span>
                <?php endif; ?>
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
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Order Status</h3>

                <input type="hidden" id="expected_updated_at" name="expected_updated_at" value="<?= e($order['updated_at']) ?>">

                <?php if ($order['payment_status'] !== 'paid' && $order['order_status'] !== 'cancelled'): ?>
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 font-semibold">
                        Record payment first.
                    </div>
                <?php endif; ?>

                <?php
                    // Determine primary next status (exclude cancel)
                    $primaryNext = null;
                    foreach ($allowedNextStatuses as $ns) {
                        if ($ns !== 'cancelled') { $primaryNext = $ns; break; }
                    }
                    $canCancel = in_array('cancelled', $allowedNextStatuses, true);
                ?>

                <?php if ($primaryNext): ?>
                    <form action="/admin/orders/status" method="POST" data-confirm-title="Update Status" data-confirm-message="Move order to <?= e(ucwords(str_replace('_',' ',$primaryNext))) ?>?" data-confirm-variant="confirm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                        <input type="hidden" name="status" value="<?= e($primaryNext) ?>">
                        <input type="hidden" name="expected_updated_at" value="<?= e($order['updated_at']) ?>">
                        <?php
                            $label = match ($primaryNext) {
                                'preparing' => 'Start Preparing',
                                'ready' => 'Mark Ready for Pickup',
                                'completed' => 'Mark as Picked Up (Completed)',
                                default => ucwords(str_replace('_', ' ', $primaryNext)),
                            };
                        ?>
                        <?php
                            $btnColor = match ($primaryNext) {
                                'preparing' => 'bg-indigo-600 hover:bg-indigo-700',
                                'ready' => 'bg-emerald-600 hover:bg-emerald-700',
                                'completed' => 'bg-emerald-600 hover:bg-emerald-700',
                                default => 'bg-stone-900 hover:bg-orange-600',
                            };
                        ?>
                        <button type="submit" class="px-4 py-2.5 rounded-xl <?= $btnColor ?> text-white font-bold text-xs shadow-sm hover:shadow transition inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                            <span><?= e($label) ?></span>
                        </button>
                    </form>
                <?php elseif ($order['order_status'] === 'completed' || $order['order_status'] === 'cancelled'): ?>
                    <div class="p-3 bg-stone-100 rounded-xl text-xs text-stone-500 font-semibold">
                        This order is <?= e($order['order_status']) ?> and has reached a terminal status.
                    </div>
                <?php endif; ?>

                <?php if ($canCancel): ?>
                    <form action="/admin/orders/status" method="POST" data-confirm-title="Cancel Order" data-confirm-message="This order will be cancelled.<?= !empty($order['needs_refund']) || $order['payment_status']==='paid' ? ' It is already paid and will be flagged for refund.' : '' ?>" data-confirm-variant="danger" data-confirm-confirm-text="Cancel Order">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                        <input type="hidden" name="status" value="cancelled">
                        <input type="hidden" name="expected_updated_at" value="<?= e($order['updated_at']) ?>">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold text-xs transition inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-ban text-xs"></i>
                            <span>Cancel Order</span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- 2. Cash Payment Recording -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Cash Payment Collection</h3>

                <?php if (!$isPaid && $order['order_status'] !== 'cancelled'): ?>
                    <div class="space-y-3" id="payment-panel">
                        <div class="text-xs text-stone-600">
                            Total Due: <span class="font-extrabold text-stone-900"><?= format_price($order['total_amount']) ?></span>
                        </div>
                        <form action="/admin/orders/payment" method="POST" id="payment-form" onsubmit="return handlePaymentSubmit(event);">
                            <?= csrf_field() ?>
                            <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                            <input type="hidden" name="expected_updated_at" value="<?= e($order['updated_at']) ?>">

                            <label class="block text-[11px] font-bold text-stone-600 mb-1">Cash Received</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="cash_received"
                                id="cash_received"
                                class="w-full px-3 py-2 rounded-xl border border-stone-300 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="0.00"
                                oninput="computeChange()"
                            >

                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <?php
                                    $total = (float) $order['total_amount'];
                                    $quickAmounts = [$total, 50, 100, 200, 500, 1000];
                                    // unique sorted
                                    $quickAmounts = array_values(array_unique($quickAmounts));
                                    sort($quickAmounts);
                                ?>
                                <?php foreach ($quickAmounts as $qa): ?>
                                    <button type="button" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-[10px] font-bold text-stone-700" onclick="setCash(<?= json_encode($qa) ?>)">
                                        <?= format_price($qa) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-3 text-sm">
                                Change: <span id="change-display" class="font-extrabold">₱0.00</span>
                                <span id="insufficient" class="text-rose-600 text-xs font-bold hidden">Insufficient cash</span>
                            </div>

                            <button
                                type="submit"
                                id="confirm-pay-btn"
                                disabled
                                class="mt-3 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm hover:shadow transition inline-flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <i class="fa-solid fa-money-bill-wave text-xs"></i>
                                <span>Confirm Payment</span>
                            </button>
                        </form>
                    </div>
                <?php elseif ($isPaid): ?>
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 space-y-1">
                        <div class="font-bold flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i> Payment Recorded</div>
                        <div>Cash received: <?= format_price($order['cash_received']) ?></div>
                        <div>Change given: <?= format_price($order['change_given']) ?></div>
                        <div>Paid at: <?= $order['paid_at'] ? date('M d, Y h:i A', strtotime($order['paid_at'])) : '—' ?></div>
                        <div>Received by: <?= e($order['received_by'] ? 'Staff #' . $order['received_by'] : '—') ?></div>
                    </div>
                <?php else: ?>
                    <div class="p-3 bg-stone-100 rounded-xl text-xs text-stone-500 font-semibold">
                        Order cancelled.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status History Timeline -->
        <div class="py-6 border-b border-stone-100">
            <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider mb-3">Status History</h3>
            <?php $history = $order['status_history'] ?? []; ?>
            <?php if (empty($history)): ?>
                <div class="text-xs text-stone-400">No status changes recorded yet.</div>
            <?php else: ?>
                <ol class="space-y-2">
                    <?php foreach ($history as $h): ?>
                        <li class="text-xs text-stone-700">
                            <span class="font-bold"><?= e($h['previous_status'] ?? '—') ?></span>
                            <i class="fa-solid fa-arrow-right text-[9px] mx-1"></i>
                            <span class="font-bold"><?= e($h['new_status']) ?></span>
                            <span class="text-stone-400 ml-2"><?= $h['changed_by_name'] ? e($h['changed_by_name']) : 'System' ?></span>
                            <span class="text-stone-400 ml-2"><?= date('M d, Y h:i A', strtotime($h['created_at'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
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

<script>
(function() {
    var total = <?= json_encode((float)$order['total_amount']) ?>;
    var cashInput = document.getElementById('cash_received');
    var changeDisplay = document.getElementById('change-display');
    var insufficient = document.getElementById('insufficient');
    var btn = document.getElementById('confirm-pay-btn');

    window.setCash = function(val) {
        if (cashInput) {
            cashInput.value = Number(val).toFixed(2);
            computeChange();
        }
    };

    window.computeChange = function() {
        if (!cashInput) return;
        var cash = parseFloat(cashInput.value);
        if (isNaN(cash)) cash = 0;
        var change = cash - total;
        if (changeDisplay) {
            changeDisplay.textContent = '₱' + (change >= 0 ? change.toFixed(2) : '0.00');
            changeDisplay.style.color = change >= 0 ? '#059669' : '#dc2626';
        }
        if (insufficient) {
            if (cash > 0 && cash < total) insufficient.classList.remove('hidden');
            else insufficient.classList.add('hidden');
        }
        if (btn) {
            btn.disabled = !(cash >= total && cash > 0);
        }
    };

    window.handlePaymentSubmit = function(e) {
        if (document.getElementById('payment-form').dataset.confirmed === '1') return true;
        e.preventDefault();
        var cash = parseFloat(cashInput.value);
        var change = cash - total;
        showConfirm({
            title: 'Confirm Payment',
            message: 'Received ₱' + cash.toFixed(2) + ', change ₱' + change.toFixed(2) + '. Confirm?',
            confirmText: 'Confirm Payment',
            onConfirm: function() {
                document.getElementById('payment-form').dataset.confirmed = '1';
                document.getElementById('payment-form').submit();
            }
        });
        return false;
    };
})();
</script>
