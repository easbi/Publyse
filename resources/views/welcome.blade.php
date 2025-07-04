<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Publyse - Sistem Pemeriksaan Publikasi</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fb7185 0%, #f97316 50%, #eab308 100%);
            min-height: 100vh;
        }

        .hero-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: rgba(255, 255, 255, 1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(234, 88, 12, 0.5);
        }

        .btn-secondary {
            border: 2px solid #f97316;
            color: #f97316;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #f97316;
            color: white;
            transform: translateY(-2px);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(249, 115, 22, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(249, 115, 22, 0.8);
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .hero-section {
                margin: 0 1rem;
            }

            .feature-card {
                margin: 0 0.5rem;
            }
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="p-4 sm:p-6 relative z-10">
            <div class="max-w-7xl mx-auto">
                <nav class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl flex items-center justify-center shadow-lg animate-float">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Publyse</h1>
                    </div>

                    <!-- Navigation -->
                    <div class="flex flex-wrap justify-center sm:justify-end space-x-3 sm:space-x-4">
                        <a href="{{ route('login') }}" class="text-white hover:text-orange-200 transition-colors duration-300 px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">Masuk</a>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 sm:px-6 py-8">
            <div class="max-w-7xl mx-auto w-full">
                <!-- Hero Section -->
                <div class="hero-section rounded-2xl p-6 sm:p-8 lg:p-12 mb-8 sm:mb-12 text-center">
                    <div class="max-w-4xl mx-auto">
                        <h2 class="text-3xl sm:text-4xl lg:text-6xl font-bold text-white mb-4 sm:mb-6 leading-tight">
                            Sistem Pemeriksaan<br class="hidden sm:block">
                            <span>Publikasi</span>
                        </h2>
                        <p class="text-lg sm:text-xl lg:text-2xl text-white text-opacity-90 mb-8 sm:mb-12 max-w-3xl mx-auto leading-relaxed">
                            Platform digital untuk memverifikasi, mereview dan memeriksa publikasi berdasarkan kaidah pemeriksaan publikasi BPS.
                        </p>

                        <!-- Login Form Center -->
                        <div class="max-w-md mx-auto glass-card rounded-2xl shadow-2xl p-6 sm:p-8">
                            <h3 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-6 text-center">Akses Publyse</h3>
                            <div class="space-y-4">
                                <a href="{{ route('login') }}" class="w-full btn-primary text-white font-semibold py-3 px-6 rounded-xl flex items-center justify-center space-x-2 text-base sm:text-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    <span>Masuk ke Publyse</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 mb-8 sm:mb-12">
                    <div class="feature-card rounded-2xl p-6 sm:p-8 group">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3">Real Experience</h3>
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                Sistem pemeriksaan yang memberikan experience pemeriksaan seperti memeriksa buku/publikasi realistis.
                            </p>
                        </div>
                    </div>

                    <div class="feature-card rounded-2xl p-6 sm:p-8 group">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3">Pemeriksaan Mendalam</h3>
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                Bisa digunakan untuk memeriksa publikasi hingga bagian yang detail dan mendalam.
                            </p>
                        </div>
                    </div>

                    <div class="feature-card rounded-2xl p-6 sm:p-8 group">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3">Laporan Terstruktur</h3>
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                Menyajikan laporan pemeriksaan yang terstruktur dan mudah dipahami dengan rekomendasi peningkatan.
                            </p>
                        </div>
                    </div>

                    <div class="feature-card rounded-2xl p-6 sm:p-8 group">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3">Proses Cepat</h3>
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                Pemrosesan yang efisien dan cepat untuk memberikan hasil pemeriksaan publikasi dalam waktu singkat.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="glass-card rounded-2xl p-6 sm:p-8 mb-8 sm:mb-12">
                    <h3 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-8">Statistik Publyse</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                        <div class="text-center">
                            <div class="text-3xl sm:text-4xl font-bold gradient-text mb-2">1,234</div>
                            <div class="text-gray-600 text-sm sm:text-base">Publikasi Terverifikasi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl sm:text-4xl font-bold gradient-text mb-2">456</div>
                            <div class="text-gray-600 text-sm sm:text-base">Pengguna Aktif</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl sm:text-4xl font-bold gradient-text mb-2">89%</div>
                            <div class="text-gray-600 text-sm sm:text-base">Tingkat Akurasi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl sm:text-4xl font-bold gradient-text mb-2">24/7</div>
                            <div class="text-gray-600 text-sm sm:text-base">Layanan Tersedia</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="p-6 sm:p-8 text-center relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="glass-card rounded-2xl p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Publyse</span>
                        </div>
                        <p class="text-gray-600 text-sm sm:text-base">
                            © 2024 Publyse. Sistem Pemeriksaan Publikasi BPS Kota Padang Panjang.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
