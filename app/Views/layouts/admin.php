<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' - Staff Portal' : 'Staff Portal - Meal and Deal Food Hub' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-stone-100 text-stone-800 min-h-screen flex flex-col md:flex-row">

    <?php $currentUser = auth_user(); ?>

    <!-- Sidebar Navigation -->
    <aside class="w-full md:w-64 bg-stone-900 text-stone-200 shrink-0 md:min-h-screen flex flex-col justify-between border-r border-stone-800">
        <div>
            <!-- Header Brand -->
            <div class="p-6 border-b border-stone-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-600 flex items-center justify-center text-white font-black text-sm shadow">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-sm tracking-tight text-white leading-tight">Meal &amp; Deal</div>
                        <div class="text-[10px] uppercase font-bold tracking-wider text-orange-400">Staff Portal</div>
                    </div>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1 text-sm font-semibold">
                <?php
                    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
                    $isActive = function(string $prefix) use ($currentUri) {
                        return str_starts_with($currentUri, $prefix);
                    };
                ?>
                <a href="/admin" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($currentUri === '/admin') ? 'bg-orange-600 text-white' : 'text-stone-300 hover:bg-stone-800 hover:text-white' ?>">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <a href="/admin/orders" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $isActive('/admin/orders') ? 'bg-orange-600 text-white' : 'text-stone-300 hover:bg-stone-800 hover:text-white' ?>">
                    <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                    <span>Orders</span>
                </a>

                <a href="/admin/products" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $isActive('/admin/products') ? 'bg-orange-600 text-white' : 'text-stone-300 hover:bg-stone-800 hover:text-white' ?>">
                    <i class="fa-solid fa-bowl-food w-5 text-center"></i>
                    <span>Products</span>
                </a>

                <a href="/admin/categories" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $isActive('/admin/categories') ? 'bg-orange-600 text-white' : 'text-stone-300 hover:bg-stone-800 hover:text-white' ?>">
                    <i class="fa-solid fa-layer-group w-5 text-center"></i>
                    <span>Categories</span>
                </a>

                <div class="pt-4 mt-4 border-t border-stone-800">
                    <a href="/" target="_blank" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-stone-400 hover:text-white hover:bg-stone-800 text-xs transition">
                        <i class="fa-solid fa-arrow-up-right-from-square w-4 text-center"></i>
                        <span>View Customer Store &nearr;</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- User Info & Logout -->
        <div class="p-4 border-t border-stone-800 bg-stone-950/50">
            <?php if ($currentUser): ?>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="font-bold text-xs text-white"><?= e($currentUser['name']) ?></div>
                        <div class="text-[10px] text-stone-400 capitalize">@<?= e($currentUser['username']) ?> &bull; <?= e($currentUser['role']) ?></div>
                    </div>
                    <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded <?= ($currentUser['role'] === 'admin') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-stone-800 text-stone-300' ?>">
                        <?= e($currentUser['role']) ?>
                    </span>
                </div>
            <?php endif; ?>

            <form action="/staff/logout" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="w-full py-2 px-3 rounded-lg bg-stone-800 hover:bg-rose-900/60 hover:text-rose-200 text-stone-400 text-xs font-semibold transition text-left flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Flash Alerts -->
        <?php $flashes = get_flashes(); ?>
        <?php if (!empty($flashes)): ?>
            <div class="p-4 sm:p-6 pb-0">
                <?php foreach ($flashes as $type => $messages): ?>
                    <?php
                        $bgColor = match ($type) {
                            'success' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                            'error' => 'bg-rose-50 text-rose-800 border-rose-300',
                            'warning' => 'bg-amber-50 text-amber-800 border-amber-300',
                            default => 'bg-sky-50 text-sky-800 border-sky-300',
                        };
                    ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="mb-3 p-4 rounded-xl border <?= $bgColor ?> text-sm font-semibold shadow-sm flex items-center justify-between">
                            <span><?= e($msg) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <?= $content ?>
        </main>
    </div>

</body>
</html>
