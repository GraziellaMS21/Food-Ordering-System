<div class="max-w-md mx-auto px-4 py-12 sm:py-20">
    <div class="bg-white rounded-3xl border border-stone-200/80 shadow-md p-6 sm:p-8">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-600 flex items-center justify-center text-white mx-auto shadow-md shadow-orange-500/20 mb-3">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Create Staff Account</h1>
            <p class="text-xs text-stone-500 mt-1">Add a new canteen staff member</p>
        </div>

        <form action="/staff/register" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter full name"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
            </div>

            <div>
                <label for="username" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Username</label>
                <input type="text" id="username" name="username" required placeholder="Choose a username"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required placeholder="Choose a password"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repeat password"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-lg shadow-orange-600/20 transition-all hover:shadow-xl inline-flex items-center justify-center gap-1.5">
                    <span>Create Account</span>
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </div>
        </form>

        <p class="text-center text-xs text-stone-500 mt-4">
            Already have an account? <a href="/staff/login" class="text-orange-600 font-bold hover:underline">Sign in</a>
        </p>
    </div>
</div>