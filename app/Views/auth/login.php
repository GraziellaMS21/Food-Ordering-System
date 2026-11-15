<div class="max-w-md mx-auto px-4 py-12 sm:py-20">
    <div class="bg-white rounded-3xl border border-stone-200/80 shadow-md p-6 sm:p-8">
        <!-- Logo / Icon -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-600 flex items-center justify-center text-white mx-auto shadow-md shadow-orange-500/20 mb-3">
                <i class="fa-solid fa-user-shield text-xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Staff Portal Sign In</h1>
            <p class="text-xs text-stone-500 mt-1">Authorized canteen personnel and administrators only</p>
        </div>

        <form action="/staff/login" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="username" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                    Username
                </label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    autocomplete="username"
                    placeholder="Enter your username"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium"
                >
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-lg shadow-orange-600/20 transition-all hover:shadow-xl inline-flex items-center justify-center gap-1.5"
                >
                    <span>Sign In to Portal</span>
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </div>
        </form>

        <p class="text-center text-xs text-stone-500 mt-4">
            New staff? <a href="/staff/register" class="text-orange-600 font-bold hover:underline">Create an account</a>
        </p>
    </div>
</div>
