<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BPABS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tambahkan Font Awesome untuk ikon mata -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <div class="text-center mb-8">
                <h1 class="text-6xl font-bold text-blue-800">BPABS</h1>
                <h2 class="text-2xl font-bold text-gray-800">Login BPABS</h2>
                <p class="text-gray-600">Masuk ke akun Anda</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                        Username
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="username" 
                        type="text" 
                        name="username" 
                        value="{{ old('username') }}" 
                        required 
                        autofocus>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <div class="relative">
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                            id="password" 
                            type="password" 
                            name="password" 
                            required>
                        <button type="button" 
                            class="absolute right-2 top-2 text-gray-500 hover:text-gray-700"
                            onclick="togglePassword()">
                            <i class="fas fa-eye" id="togglePassword"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" 
                        type="submit">
                        Login
                    </button>
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700">Register</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>