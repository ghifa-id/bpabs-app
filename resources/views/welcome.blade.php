<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BPABS - Sistem Pengelolaan Air Bersih</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="antialiased">
    <div class="relative min-h-screen bg-gradient-to-br from-blue-100 to-blue-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-blue-600">BPABS</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                    <i class="fas fa-user"></i>Login
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                    <i class="fas fa-right-to-bracket mr-2"></i>Login
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                                        <i class="fas fa-user-plus mr-2"></i>Register
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h2 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl mb-8">
                    Sistem Pengelolaan<br>
                    <span class="text-blue-600">Air Bersih</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-12">
                    Solusi modern untuk pengelolaan dan pemantauan air bersih yang efisien, transparan, dan terpercaya.
                </p>
                <div class="flex justify-center space-x-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg text-lg">
                                <i class="fas fa-gauge-high mr-2"></i>Akses Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg text-lg">
                                <i class="fas fa-right-to-bracket mr-2"></i>Mulai Sekarang
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <!-- ... existing code ... -->

<!-- Features Section -->
<div class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h3 class="text-3xl font-bold text-gray-900">Fitur Utama</h3>
            <p class="mt-4 text-xl text-gray-600">Nikmati kemudahan pengelolaan air bersih dengan fitur-fitur modern kami</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1: Monitoring Real-time -->
            <div class="p-6 bg-blue-50 rounded-xl hover:shadow-lg transition-shadow duration-300">
                <div class="text-blue-600 text-2xl mb-4 flex justify-center">
                    <i class="fas fa-chart-line text-4xl"></i>
                </div>
                <h4 class="text-xl font-semibold text-gray-900 mb-2 text-center">Monitoring Real-time</h4>
                <p class="text-gray-600 text-center">Pantau penggunaan air secara real-time dengan data yang akurat dan terperinci. Dapatkan grafik dan analisis penggunaan air Anda.</p>
            </div>
            
            <!-- Feature 2: Pembayaran Digital -->
            <div class="p-6 bg-blue-50 rounded-xl hover:shadow-lg transition-shadow duration-300">
                <div class="text-blue-600 text-2xl mb-4 flex justify-center">
                    <i class="fas fa-wallet text-4xl"></i>
                </div>
                <h4 class="text-xl font-semibold text-gray-900 mb-2 text-center">Pembayaran Digital</h4>
                <p class="text-gray-600 text-center">Lakukan pembayaran dengan mudah dan aman melalui berbagai metode pembayaran digital yang terintegrasi.</p>
            </div>

        </div>
    </div>
</div>
<!-- ... existing code ... -->
                    <!-- Feature 2 -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; {{ date('Y') }} BPABS. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>