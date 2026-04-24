<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jurnal Operasional Keamanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <img src="{{ asset('logo-aica.png') }}" alt="Logo" class="h-14 w-auto mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Sistem Informasi Jurnal</h1>
            <p class="text-black">Operasional Keamanan</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-black text-sm font-semibold mb-2" for="username">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-black">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input type="text" name="username" id="username" required
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        placeholder="Masukkan username">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-black text-sm font-semibold mb-2" for="password">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-black">
                        <i class="bi bi-key-fill"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        placeholder="Masukan Password">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-blue-900 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition duration-300 flex items-center justify-center gap-2">
                Login
            </button>
        </form>
    </div>

</body>
</html>