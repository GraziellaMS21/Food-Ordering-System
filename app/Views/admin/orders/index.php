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
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'preparing' => 'Preparing',
            'ready' => 'Ready for Pickup',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    ?>
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <?php foreach ($statuses as $stKey => $stLabel): ?>
            <?php
                $isActive = ($selectedStatus === $stKey) || ($stKey === '' && empty($selectedStatus));
            ?>
            <a
                href="/admin/orders<?= $stKey ? '?status=' . $stKey : '' ?><?= !empty($search) ? ($stKey ? '&' : '?') . 'search=' . urlencode($search) : '' ?>"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap <?= $isActive ? 'bg-orange-600 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' ?>"
            >
                <?= $stLabel ?>
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
                                <td class="px-5 py-4 text-right">
                                    <a
                                        href="/admin/orders/<?= (int)$ord['id'] ?>"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-stone-900 hover:bg-orange-600 text-white font-bold text-[11px] transition"
                                    >
                                        <span>Manage</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
