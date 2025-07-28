<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('https://cdn-icons-png.freepik.com/512/4990/4990622.png') }}" type="image/x-icon">
    <style>
        /* Liquid Glass Effect */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Password Strength */
        .strength {
            height: 6px;
            border-radius: 3px;
            margin-top: 4px;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">

    <div class="glass relative rounded-3xl shadow-2xl p-8 w-full max-w-md">
        <!-- เอฟเฟกต์แสง -->
        <div
            class="absolute inset-0 bg-gradient-to-tr from-white/30 via-transparent to-transparent opacity-30 rounded-3xl">
        </div>

        <div class="relative z-10">
            <!-- Title -->
            <div class="text-center mb-6">
                <h1 class="text-4xl font-extrabold text-white drop-shadow-md">POS System</h1>
                <p class="text-white/80 mt-1">Log in to continue</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-white font-semibold">Email</label>
                    <input type="email" id="email" name="email" required
                        class="mt-1 w-full rounded-xl border border-white/30 bg-white/20 text-white placeholder-white/60
                        focus:border-pink-400 focus:ring-pink-400 px-4 py-2"
                        placeholder="you@example.com">
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-white font-semibold">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="mt-1 w-full rounded-xl border border-white/30 bg-white/20 text-white placeholder-white/60
                            focus:border-pink-400 focus:ring-pink-400 px-4 py-2"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 
                    text-white font-bold rounded-xl shadow-lg hover:scale-105 transform transition">
                    Log in
                </button>
            </form>

        </div>
    </div>
</body>

</html>
