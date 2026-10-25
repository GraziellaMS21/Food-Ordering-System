<?php $user = auth_user(); ?>

<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Staff Overview</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">
                Hello <strong><?= e($user['name'] ?? 'Staff') ?></strong>! Here is today's real-time canteen ordering activity.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="/admin/orders" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-sm transition">
                <span>Manage Orders</span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
            <a href="/admin/products/create" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 text-stone-800 font-bold text-xs shadow-sm transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>New Product</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Pending -->
        <a href="/admin/orders?status=pending" class="block bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm hover:border-amber-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Pending</span>
                <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-hourglass-half"></i>
                </span>
            </div>
            <div class="mt-3 text-3xl font-black text-stone-900"><?= (int)$stats['pending'] ?></div>
            <div class="mt-1 text-[11px] text-stone-500 font-medium">Awaiting kitchen review</div>
        </a>

        <!-- Preparing -->
        <a href="/admin/orders?status=preparing" class="block bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm hover:border-indigo-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Preparing</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-fire-burner"></i>
                </span>
            </div>
            <div class="mt-3 text-3xl font-black text-stone-900"><?= (int)$stats['preparing'] ?></div>
            <div class="mt-1 text-[11px] text-stone-500 font-medium">Currently in the kitchen</div>
        </a>

        <!-- Ready for Pickup -->
        <a href="/admin/orders?status=ready" class="block bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm hover:border-emerald-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Ready for Pickup</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-bell"></i>
                </span>
            </div>
            <div class="mt-3 text-3xl font-black text-stone-900"><?= (int)$stats['ready'] ?></div>
            <div class="mt-1 text-[11px] text-stone-500 font-medium">Waiting at counter</div>
        </a>

        <!-- Today's Cash -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-700">Today's Cash Paid</span>
                <span class="w-8 h-8 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-peso-sign"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl sm:text-3xl font-black text-stone-900">
                <?= format_price($stats['revenue_today']) ?>
            </div>
            <div class="mt-1 text-[11px] text-stone-500 font-medium">
                <?= (int)$stats['total_orders_today'] ?> orders today (<?= (int)$stats['completed_today'] ?> completed)
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-base text-stone-900">Recent Incoming Orders</h2>
                <p class="text-xs text-stone-500">Live order queue from canteen customers</p>
            </div>
            <a href="/admin/orders" class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 hover:text-orange-700">
                <span>View all orders</span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <div class="p-10 text-center text-stone-400 text-sm">
                No orders have been placed yet.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 uppercase font-bold tracking-wider border-b border-stone-100">
                        <tr>
                            <th class="px-5 py-3">Order #</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Order Status</th>
                            <th class="px-5 py-3">Payment</th>
                            <th class="px-5 py-3">Time</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700 font-medium">
                        <?php foreach ($recentOrders as $ord): ?>
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="px-5 py-3.5 font-mono font-bold text-stone-900">
                                    <a href="/admin/orders/<?= (int)$ord['id'] ?>" class="text-orange-600 hover:underline">
                                        <?= e($ord['order_number']) ?>
                                    </a>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-stone-900"><?= e($ord['customer_name']) ?></div>
                                    <div class="text-[11px] text-stone-400"><?= e($ord['customer_contact'] ?? 'No contact') ?></div>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-stone-900">
                                    <?= format_price($ord['total_amount']) ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <?php
                                        $st = $ord['order_status'];
                                        $stClass = match ($st) {
                                            'pending' => 'bg-amber-100 text-amber-800',
                                            'confirmed' => 'bg-sky-100 text-sky-800',
                                            'preparing' => 'bg-indigo-100 text-indigo-800',
                                            'ready' => 'bg-emerald-100 text-emerald-800 animate-pulse',
                                            'completed' => 'bg-stone-100 text-stone-600',
                                            'cancelled' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-stone-100 text-stone-700',
                                        };
                                    ?>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $stClass ?>">
                                        <?= e($st) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <?php if ($ord['payment_status'] === 'paid'): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-check"></i> Paid
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                                            Unpaid
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-stone-400 text-[11px]">
                                    <?= date('h:i A', strtotime($ord['created_at'])) ?>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/admin/orders/<?= (int)$ord['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-stone-900 hover:bg-orange-600 text-white font-bold text-[11px] transition">
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
