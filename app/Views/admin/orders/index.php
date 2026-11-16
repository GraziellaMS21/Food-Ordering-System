<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Order Management</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Review live orders, update kitchen preparation stages, and record cash payments</p>
        </div>

        <!-- Search Form -->
        <form action="/admin/orders" method="GET" class="flex items-center gap-2 max-w-sm w-full">
            <?php if (!empty($selectedStatus)): ?>
                <input type="hidden" name="status" value="<?= e($selectedStatus) ?>">
            <?php endif; ?>
            <input
                type="text"
                name="search"
                placeholder="Search order # or customer..."
                value="<?= e($search ?? '') ?>"
                class="w-full px-3.5 py-2 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-xs bg-white"
            >
            <button type="submit" class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold transition">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Search</span>
            </button>
            <?php if (!empty($search)): ?>
                <a href="/admin/orders<?= !empty($selectedStatus) ? '?status=' . urlencode($selectedStatus) : '' ?>" class="text-[11px] text-stone-500 hover:text-stone-800 underline">
                    Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Status Filter Tabs -->
    <?php
        $statuses = [
            '' => 'All Orders',
            'confirmed' => 'Confirmed',
            'preparing' => 'Preparing',
            'ready' => 'Ready for Pickup',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    ?>

    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <?php
            $isAwaiting = ($selectedPaymentStatus === 'unpaid');
        ?>
        <a href="/admin/orders" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap <?= (!$isAwaiting && ($selectedStatus === '' || $selectedStatus === null)) ? 'bg-orange-600 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' ?>">
            All Orders
        </a>
        <a href="/admin/orders?payment_status=unpaid" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap inline-flex items-center gap-1.5 <?= $isAwaiting ? 'bg-amber-600 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' ?>">
            Awaiting Payment
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold <?= $isAwaiting ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' ?>"><?= (int)($awaitingPaymentCount ?? 0) ?></span>
        </a>
        <?php foreach ($statuses as $stKey => $stLabel): ?>
            <?php if ($stKey === '') continue; ?>
            <?php
                $isActive = ($selectedStatus === $stKey);
            ?>
            <a
                href="/admin/orders?status=<?= $stKey ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap inline-flex items-center gap-1.5 <?= $isActive ? 'bg-orange-600 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' ?>"
            >
                <?= $stLabel ?>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold <?= $isActive ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-700' ?>"><?= (int)(($statusCounts ?? [])[$stKey] ?? 0) ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
        <?php if (empty($orders)): ?>
            <div class="p-12 text-center text-stone-400 text-sm">
                No orders found matching the filter criteria.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 uppercase font-bold tracking-wider border-b border-stone-100">
                        <tr>
                            <th class="px-5 py-3.5">Order #</th>
                            <th class="px-5 py-3.5">Customer</th>
                            <th class="px-5 py-3.5">Total Amount</th>
                            <th class="px-5 py-3.5">Order Status</th>
                            <th class="px-5 py-3.5">Payment</th>
                            <th class="px-5 py-3.5">Placed At</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700 font-medium">
                        <?php foreach ($orders as $ord): ?>
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="px-5 py-4 font-mono font-bold text-stone-900">
                                    <a href="/admin/orders/<?= (int)$ord['id'] ?>" class="text-orange-600 hover:underline">
                                        <?= e($ord['order_number']) ?>
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-stone-900"><?= e($ord['customer_name']) ?></div>
                                    <div class="text-[11px] text-stone-400"><?= e($ord['customer_contact'] ?? 'No contact number') ?></div>
                                </td>
                                <td class="px-5 py-4 font-extrabold text-stone-900 text-sm">
                                    <?= format_price($ord['total_amount']) ?>
                                </td>
                                <td class="px-5 py-4">
                                    <?php
                                        $st = $ord['order_status'];
                                        $stClass = match ($st) {
                                            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'confirmed' => 'bg-sky-100 text-sky-800 border-sky-200',
                                            'preparing' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                            'ready' => 'bg-emerald-100 text-emerald-800 border-emerald-200 animate-pulse',
                                            'completed' => 'bg-stone-100 text-stone-600 border-stone-200',
                                            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            default => 'bg-stone-100 text-stone-700 border-stone-200',
                                        };
                                    ?>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border <?= $stClass ?>">
                                        <?= e(str_replace('_', ' ', $st)) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <?php if ($ord['payment_status'] === 'paid'): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-check"></i> Paid
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                                            Unpaid (Cash)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-stone-400 text-[11px]">
                                    <?= date('M d, h:i A', strtotime($ord['created_at'])) ?>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <?php
                                            $st = $ord['order_status'];
                                            $paid = $ord['payment_status'] === 'paid';
                                        ?>
                                        <!-- Quick actions -->
                                        <?php if ($st === 'pending' && !$paid): ?>
                                            <button type="button" title="Record payment" aria-label="Record payment" onclick="openPaymentModal(<?= (int)$ord['id'] ?>)" class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center" >
                                                <i class="fa-solid fa-money-bill-wave"></i>
                                            </button>
                                            <form action="/admin/orders/status" method="POST" style="display:inline" data-confirm-title="Cancel Order" data-confirm-message="Cancel this order?<?= $paid ? ' It is paid and will be flagged for refund.' : '' ?>" data-confirm-variant="danger" data-confirm-confirm-text="Cancel Order">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="order_id" value="<?= (int)$ord['id'] ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" title="Cancel order" aria-label="Cancel order" class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($st === 'confirmed' && $paid): ?>
                                            <form action="/admin/orders/status" method="POST" style="display:inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="order_id" value="<?= (int)$ord['id'] ?>">
                                                <input type="hidden" name="status" value="preparing">
                                                <button type="submit" title="Start preparing" aria-label="Start preparing" class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-fire-burner"></i>
                                                </button>
                                            </form>
                                            <form action="/admin/orders/status" method="POST" style="display:inline" data-confirm-title="Cancel Order" data-confirm-message="Cancel this order? It is paid and will be flagged for refund." data-confirm-variant="danger" data-confirm-confirm-text="Cancel Order">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="order_id" value="<?= (int)$ord['id'] ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" title="Cancel order" aria-label="Cancel order" class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($st === 'preparing'): ?>
                                            <form action="/admin/orders/status" method="POST" style="display:inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="order_id" value="<?= (int)$ord['id'] ?>">
                                                <input type="hidden" name="status" value="ready">
                                                <button type="submit" title="Mark ready for pickup" aria-label="Mark ready for pickup" class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-bell"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($st === 'ready'): ?>
                                            <form action="/admin/orders/status" method="POST" style="display:inline" data-confirm-title="Mark as Picked Up" data-confirm-message="Mark this order as completed (picked up)?" data-confirm-variant="confirm" data-confirm-confirm-text="Mark Completed">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="order_id" value="<?= (int)$ord['id'] ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" title="Mark as picked up (completed)" aria-label="Mark as picked up" class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Edit -->
                                        <?php $editable = in_array($st, ['pending','confirmed'], true); ?>
                                        <a href="/admin/orders/<?= (int)$ord['id'] ?>" title="<?= $editable ? 'Edit order' : 'Order can no longer be edited' ?>" aria-label="Edit order" class="w-10 h-10 rounded-lg flex items-center justify-center <?= $editable ? 'bg-stone-100 text-stone-700 hover:bg-stone-200' : 'bg-stone-50 text-stone-300 cursor-not-allowed' ?>" <?= $editable ? '' : 'aria-disabled="true"' ?>>
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <!-- Delete -->
                                        <?php $deletable = ($st === 'cancelled'); ?>
                                        <?php if ($deletable): ?>
                                            <form action="/admin/orders/<?= (int)$ord['id'] ?>/delete" method="POST" style="display:inline" data-confirm-title="Delete Order" data-confirm-message="Delete this cancelled order?" data-confirm-variant="danger" data-confirm-confirm-text="Delete Order">
                                                <?= csrf_field() ?>
                                                <button type="submit" title="Delete order" aria-label="Delete order" class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" title="Only cancelled orders can be deleted" aria-label="Delete order (disabled)" disabled aria-disabled="true" class="w-10 h-10 rounded-lg bg-stone-50 text-stone-300 cursor-not-allowed flex items-center justify-center">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
setTimeout(function(){ window.location.reload(); }, 20000);
</script>

<!-- Cash Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 sm:p-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-extrabold text-stone-900"><i class="fa-solid fa-money-bill-wave text-emerald-600 mr-2"></i>Record Cash Payment</h2>
            <button type="button" onclick="closePaymentModal()" class="text-stone-400 hover:text-stone-700" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="paymentOrderInfo" class="mb-4 text-xs space-y-1"></div>
        <form id="paymentModalForm" action="/admin/orders/payment" method="POST" onsubmit="return handlePaymentSubmitModal(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="order_id" id="payment_order_id">
            <div class="text-xs text-stone-600 mb-2">Total Due: <span class="font-extrabold text-stone-900" id="payment_total"></span></div>
            <label class="block text-[11px] font-bold text-stone-600 mb-1">Cash Received</label>
            <input type="number" step="0.01" min="0" name="cash_received" id="payment_cash" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500" oninput="computePaymentChange()">
            <div class="flex flex-wrap gap-1.5 mt-2" id="payment_quick"></div>
            <div class="mt-3 text-sm">Change: <span id="payment_change" class="font-extrabold">₱0.00</span> <span id="payment_insufficient" class="text-rose-600 text-xs font-bold hidden">Insufficient cash</span></div>
            <div class="flex flex-col sm:flex-row gap-2 mt-6">
                <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-sm hover:bg-stone-50 min-h-[44px]">Cancel</button>
                <button type="submit" id="payment_confirm_btn" disabled class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm min-h-[44px] disabled:opacity-50">Confirm Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
var __ORDERS__ = <?= json_encode($orders) ?>;
function openPaymentModal(id) {
    var order = null;
    for (var i=0;i<__ORDERS__.length;i++){ if (__ORDERS__[i].id == id) { order = __ORDERS__[i]; break; } }
    if (!order) return;
    document.getElementById('payment_order_id').value = order.id;
    document.getElementById('payment_total').textContent = '₱' + parseFloat(order.total_amount).toFixed(2);
    var info = '<div class="font-mono font-bold">' + order.order_number + '</div>';
    info += '<div class="text-stone-500">' + order.customer_name + (order.customer_contact ? ' · ' + order.customer_contact : '') + '</div>';
    info += '<div class="text-stone-400 text-[11px]">Placed ' + order.created_at + '</div>';
    info += '<div class="font-extrabold mt-1">Total: ₱' + parseFloat(order.total_amount).toFixed(2) + '</div>';
    document.getElementById('paymentOrderInfo').innerHTML = info;
    var q = [parseFloat(order.total_amount), 50,100,200,500,1000];
    var seen = {}; var btns = '';
    q.sort(function(a,b){return a-b;}).forEach(function(v){
        if (seen[v]) return; seen[v]=true;
        btns += '<button type="button" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-[10px] font-bold text-stone-700" onclick="setPaymentCash(' + v + ')">₱' + v.toFixed(2) + '</button>';
    });
    document.getElementById('payment_quick').innerHTML = btns;
    document.getElementById('payment_cash').value = '';
    computePaymentChange();
    document.getElementById('paymentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(function(){ document.getElementById('payment_cash').focus(); }, 50);
}
function closePaymentModal(){ document.getElementById('paymentModal').classList.add('hidden'); document.body.style.overflow=''; }
var __paymentTotal = 0;
function setPaymentCash(v){ document.getElementById('payment_cash').value = v.toFixed(2); computePaymentChange(); }
function computePaymentChange(){
    var totalText = document.getElementById('payment_total').textContent.replace('₱','');
    var total = parseFloat(totalText);
    var cash = parseFloat(document.getElementById('payment_cash').value);
    if (isNaN(cash)) cash = 0;
    var change = cash - total;
    var disp = document.getElementById('payment_change');
    disp.textContent = '₱' + (change >= 0 ? change.toFixed(2) : '0.00');
    disp.style.color = change >= 0 ? '#059669' : '#dc2626';
    var ins = document.getElementById('payment_insufficient');
    if (cash > 0 && cash < total) ins.classList.remove('hidden'); else ins.classList.add('hidden');
    document.getElementById('payment_confirm_btn').disabled = !(cash >= total && cash > 0);
    __paymentTotal = total;
}
function handlePaymentSubmitModal(e){
    var form = document.getElementById('paymentModalForm');
    if (form.dataset.confirmed === '1') return true;
    e.preventDefault();
    var cash = parseFloat(document.getElementById('payment_cash').value);
    var change = cash - __paymentTotal;
    showConfirm({
        title: 'Confirm Payment',
        message: 'Received ₱' + cash.toFixed(2) + ', change ₱' + change.toFixed(2) + '. Confirm?',
        confirmText: 'Confirm Payment',
        onConfirm: function(){ form.dataset.confirmed='1'; form.submit(); }
    });
    return false;
}
</script>
