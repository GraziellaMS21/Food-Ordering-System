<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' - Meal and Deal' : 'Meal and Deal Food Hub' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-amber-50/40 text-stone-800 min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <header class="bg-white border-b border-amber-200/70 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-orange-500 flex items-center justify-center text-white shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-utensils text-lg"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-lg sm:text-xl tracking-tight text-stone-900 flex items-center gap-1">
                            Meal <span class="text-orange-600">&amp;</span> Deal
                        </span>
                        <span class="block text-xs font-semibold text-amber-600 uppercase tracking-wider">Food Hub Canteen</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="/" class="px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:text-orange-600 hover:bg-orange-50 transition">
                        Home
                    </a>
                    <a href="/menu" class="px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:text-orange-600 hover:bg-orange-50 transition">
                        Menu
                    </a>
                    <a href="/orders/track" class="px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:text-orange-600 hover:bg-orange-50 transition">
                        Track Order
                    </a>
                </nav>

                <!-- Actions / Cart / Auth -->
                <div class="flex items-center gap-3">
                    <a href="/cart" id="cart-button" class="relative inline-flex items-center gap-2 px-4 py-2 rounded-full bg-orange-600 hover:bg-orange-700 text-white font-semibold text-sm shadow-md shadow-orange-600/20 transition-all hover:shadow-lg">
                        <i class="fa-solid fa-cart-shopping text-sm"></i>
                        <span>Cart</span>
                        <?php $itemCount = cart_count(); ?>
                        <span id="cart-count-badge" class="<?= $itemCount > 0 ? 'inline-flex' : 'hidden' ?> items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-orange-600 bg-white rounded-full transition-transform duration-200">
                            <?= $itemCount ?>
                        </span>
                    </a>

                    <?php if ($staff = auth_user()): ?>
                        <a href="/admin" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100 hover:bg-amber-200 transition">
                            <span>Portal (<?= e($staff['name']) ?>)</span>
                        </a>
                    <?php else: ?>
                        <a href="/staff/login" class="text-xs font-medium text-stone-500 hover:text-stone-800 transition px-2 py-1">
                            Staff Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Global Flash Notifications -->
    <?php
        $flashes = get_flashes();
        $toastQueue = [];

        // Check for "Added ..." messages to show as toasts instead of banner
        if (!empty($flashes['success'])) {
            $filteredSuccess = [];
            foreach ($flashes['success'] as $sMsg) {
                if (str_starts_with($sMsg, 'Added ')) {
                    $toastQueue[] = ['message' => $sMsg, 'type' => 'success'];
                } else {
                    $filteredSuccess[] = $sMsg;
                }
            }
            if (empty($filteredSuccess)) {
                unset($flashes['success']);
            } else {
                $flashes['success'] = $filteredSuccess;
            }
        }
    ?>
    <?php if (!empty($flashes)): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
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
                    <div class="mb-2 p-4 rounded-xl border <?= $bgColor ?> flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-sm"><?= e($msg) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Main Content Area -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-stone-900 text-stone-400 mt-16 border-t border-stone-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white text-lg font-bold mb-2">Meal &amp; Deal Food Hub</h3>
                    <p class="text-sm text-stone-400 leading-relaxed mb-3">
                        Freshly cooked carinderia favorites, budget-friendly rice meals, snacks, and refreshing drinks. Order online for swift canteen pickup!
                    </p>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        Cash on Pickup Only
                    </span>
                </div>
                <div>
                    <h4 class="text-white text-sm font-semibold uppercase tracking-wider mb-3">Quick Navigation</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/" class="hover:text-amber-400 transition">Home</a></li>
                        <li><a href="/menu" class="hover:text-amber-400 transition">Browse Menu</a></li>
                        <li><a href="/cart" class="hover:text-amber-400 transition">View Shopping Cart</a></li>
                        <li><a href="/orders/track" class="hover:text-amber-400 transition">Track Pickup Order</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white text-sm font-semibold uppercase tracking-wider mb-3">Canteen Information</h4>
                    <p class="text-sm text-stone-400 leading-relaxed">
                        <strong>Pickup Location:</strong> Main Food Hub Counter<br>
                        <strong>Payment:</strong> Cash upon pickup<br>
                        <strong>Operating Hours:</strong> 8:00 AM – 6:00 PM
                    </p>
                    <div class="mt-4 pt-3 border-t border-stone-800">
                        <a href="/staff/login" class="inline-flex items-center gap-1.5 text-xs text-stone-500 hover:text-stone-300 transition">
                            <span>Staff Portal Sign In</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-stone-800/80 text-center text-xs text-stone-500">
                &copy; <?= date('Y') ?> Meal and Deal Food Hub. Built with vanilla PHP, MySQL, and Tailwind CSS.
            </div>
        </div>
    </footer>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"></div>

    <!-- Toast & Cart AJAX JavaScript -->
    <script>
        function showToast(message, type = 'success', actionUrl = '/cart', actionText = 'View Cart') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto flex items-center justify-between gap-3 p-4 rounded-2xl bg-stone-900/95 backdrop-blur-md text-white shadow-2xl border border-stone-700/80 transform translate-y-4 opacity-0 transition-all duration-300';

            const iconHtml = (type === 'error') 
                ? '<i class="fa-solid fa-triangle-exclamation text-base text-amber-300"></i>' 
                : '<i class="fa-solid fa-cart-plus text-base text-white"></i>';

            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl ${type === 'error' ? 'bg-rose-600' : 'bg-orange-600'} text-white flex items-center justify-center font-bold text-base shadow-sm shrink-0">
                        ${iconHtml}
                    </div>
                    <div class="text-xs font-semibold leading-snug">
                        ${message}
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    ${actionUrl ? `<a href="${actionUrl}" class="px-3 py-1.5 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition shadow-sm whitespace-nowrap">${actionText} <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i></a>` : ''}
                    <button type="button" class="text-stone-400 hover:text-white p-1 text-sm transition" aria-label="Close notification" onclick="this.closest('.pointer-events-auto').remove()"><i class="fa-solid fa-xmark"></i></button>
                </div>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            // Auto dismiss after 4 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 350);
            }, 4000);
        }

        // Intercept Add to Cart form submissions to stay on the page and display toast
        document.addEventListener('submit', function (e) {
            const form = e.target.closest('form[action="/cart/add"]');
            if (!form) return;

            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalContent = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75');
            }

            const formData = new FormData(form);

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok && res.status !== 400 && res.status !== 404) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    // Update header cart badge
                    const badge = document.getElementById('cart-count-badge');
                    if (badge) {
                        badge.textContent = data.cart_count;
                        badge.classList.remove('hidden');
                        badge.classList.add('inline-flex');
                        badge.classList.add('scale-125');
                        setTimeout(() => badge.classList.remove('scale-125'), 200);
                    }

                    // Show success toast notification
                    showToast(data.message, 'success', '/cart', 'View Cart');

                    // Temporary visual feedback on the button
                    if (submitBtn) {
                        submitBtn.innerHTML = `
                            <i class="fa-solid fa-check text-emerald-300"></i>
                            <span>Added!</span>
                        `;
                    }
                } else {
                    showToast(data.message || 'Could not add item to cart.', 'error');
                }
            })
            .catch(err => {
                // Fallback to regular form submission if fetch failed
                form.submit();
            })
            .finally(() => {
                setTimeout(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75');
                        submitBtn.innerHTML = originalContent;
                    }
                }, 1000);
            });
        });

        // Trigger any server-rendered toast notifications on load
        <?php if (!empty($toastQueue)): ?>
            document.addEventListener('DOMContentLoaded', function () {
                <?php foreach ($toastQueue as $t): ?>
                    showToast(<?= json_encode($t['message']) ?>, <?= json_encode($t['type']) ?>, '/cart', 'View Cart');
                <?php endforeach; ?>
            });
        <?php endif; ?>
    </script>

</body>
</html>
