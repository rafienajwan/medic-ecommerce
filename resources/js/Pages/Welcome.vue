<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Toko Alat Kesehatan - Medic E-Commerce" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Navigation - Responsive -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-14 sm:h-16">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 sm:h-8 sm:w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                        </svg>
                        <span class="ml-2 text-base sm:text-xl font-bold text-gray-900">Medic E-Commerce</span>
                    </div>

                    <div v-if="canLogin" class="flex items-center space-x-2 sm:space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="text-gray-700 hover:text-blue-600 px-2 sm:px-3 py-2 rounded-md text-xs sm:text-sm font-medium transition"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-gray-700 hover:text-blue-600 px-2 sm:px-3 py-2 rounded-md text-xs sm:text-sm font-medium transition"
                            >
                                Masuk
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="bg-blue-600 text-white hover:bg-blue-700 px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition"
                            >
                                Daftar
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section - Responsive -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 lg:py-20">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4 sm:mb-6">
                    Toko <span class="text-blue-600">Alat Kesehatan</span> Terpercaya
                </h1>
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 mb-6 sm:mb-10 max-w-3xl mx-auto px-4">
                    Sedia berbagai peralatan medis berkualitas untuk rumah sakit, klinik, dan kebutuhan kesehatan Anda.
                </p>

                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 px-4">
                    <Link
                        href="/products"
                        class="bg-blue-600 text-white hover:bg-blue-700 px-6 sm:px-8 py-3 rounded-lg text-base sm:text-lg font-semibold transition shadow-lg hover:shadow-xl"
                    >
                        Lihat Produk
                    </Link>

                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('register')"
                        class="bg-white text-blue-600 hover:bg-gray-50 border-2 border-blue-600 px-6 sm:px-8 py-3 rounded-lg text-base sm:text-lg font-semibold transition"
                    >
                        Mulai Belanja
                    </Link>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-20 grid gap-8 md:grid-cols-3">
                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="flex justify-center mb-6">
                        <div class="bg-blue-100 rounded-full p-4">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Produk Berkualitas</h3>
                    <p class="text-gray-600 text-center">
                        Semua produk dijamin original dan memiliki izin edar resmi dari Kemenkes RI
                    </p>
                </div>

                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="flex justify-center mb-6">
                        <div class="bg-green-100 rounded-full p-4">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Pembayaran Aman</h3>
                    <p class="text-gray-600 text-center">
                        Mendukung berbagai metode pembayaran: Prepaid, Postpaid, COD, dan PayPal
                    </p>
                </div>

                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="flex justify-center mb-6">
                        <div class="bg-purple-100 rounded-full p-4">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Pengiriman Cepat</h3>
                    <p class="text-gray-600 text-center">
                        Proses pesanan cepat dengan tracking real-time dan invoice otomatis
                    </p>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="mt-20">
                <h2 class="text-3xl font-bold text-gray-900 mb-10 text-center">Kategori Produk</h2>
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer">
                        <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <svg class="h-20 w-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Alat Diagnostik</h3>
                            <p class="text-gray-600">Tensimeter, Termometer, Stetoskop, dll</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer">
                        <div class="h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                            <svg class="h-20 w-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Alat Terapi</h3>
                            <p class="text-gray-600">Nebulizer, Alat Fisioterapi, dll</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer">
                        <div class="h-48 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                            <svg class="h-20 w-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Perlengkapan RS</h3>
                            <p class="text-gray-600">Kursi Roda, Tempat Tidur Pasien, dll</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="mt-20 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-12 text-center shadow-2xl">
                <h2 class="text-3xl font-bold text-white mb-4">
                    Siap untuk berbelanja?
                </h2>
                <p class="text-xl text-blue-100 mb-8">
                    Daftar sekarang dan dapatkan invoice otomatis untuk setiap transaksi
                </p>
                <div class="flex justify-center space-x-4">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('register')"
                        class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg text-lg font-semibold transition"
                    >
                        Daftar Gratis
                    </Link>
                    <Link
                        href="/products"
                        class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold transition"
                    >
                        Lihat Katalog
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white mt-20 border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Medic E-Commerce</h4>
                        <p class="text-gray-600 text-sm">
                            Toko alat kesehatan terpercaya sejak 2025. Menyediakan produk original dengan harga terbaik.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Tautan Cepat</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><Link href="/products" class="hover:text-blue-600">Produk</Link></li>
                            <li><a href="#" class="hover:text-blue-600">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-blue-600">Kontak</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Bantuan</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="#" class="hover:text-blue-600">FAQ</a></li>
                            <li><a href="#" class="hover:text-blue-600">Cara Berbelanja</a></li>
                            <li><a href="#" class="hover:text-blue-600">Kebijakan Privasi</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Hubungi Kami</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li>📧 info@medic-ecommerce.com</li>
                            <li>📱 +62 812-3456-7890</li>
                            <li>📍 Jakarta, Indonesia</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-200 text-center text-sm text-gray-600">
                    <p>&copy; 2025 Medic E-Commerce. All rights reserved.</p>
                    <p class="mt-2 text-xs">
                        <span class="text-blue-600 font-semibold">✨ BNSP Project</span> - Auto-logout 60 detik | PDF Invoice | Multiple Payment Methods
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
