<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BPABS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Akun Baru</h2>
                <p class="text-gray-600">Buat akun BPABS Anda</p>
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

            <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Nama Lengkap
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="name" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat">
                        Alamat
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="alamat" 
                        name="alamat" 
                        required>{{ old('alamat') }}</textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="no_hp">
                        Nomor HP
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="no_hp" 
                        type="text" 
                        name="no_hp" 
                        value="{{ old('no_hp') }}" 
                        required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                        Username
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="username" 
                        type="text" 
                        name="username" 
                        value="{{ old('username') }}" 
                        required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="password" 
                        type="password" 
                        name="password" 
                        required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                        Konfirmasi Password
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required>
                </div>

                <div class="mt-6">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" 
                        type="submit">
                        Daftar
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700">Login</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>