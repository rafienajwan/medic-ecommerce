<template>
    <MainLayout>
        <div class="container-responsive py-6 sm:py-8 lg:py-12">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 sm:mb-8">Dashboard</h1>

            <!-- Welcome Message -->
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <h2 class="text-xl sm:text-2xl font-semibold mb-3 sm:mb-4">Welcome, {{ $page.props.auth.user.name }}! 👋</h2>
                <p class="text-sm sm:text-base text-gray-600 mb-4">
                    You are logged in as <span class="font-semibold">{{ $page.props.auth.user.role }}</span>.
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <Link
                    href="/products"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-3 sm:mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="ml-3 text-lg sm:text-xl font-semibold">Browse Products</h3>
                    </div>
                    <p class="text-sm sm:text-base text-blue-100">Explore our medical equipment catalog</p>
                </Link>

                <Link
                    href="/orders"
                    class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-3 sm:mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="ml-3 text-lg sm:text-xl font-semibold">My Orders</h3>
                    </div>
                    <p class="text-sm sm:text-base text-green-100">Track your orders and purchases</p>
                </Link>

                <Link
                    href="/cart"
                    class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-3 sm:mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="ml-3 text-lg sm:text-xl font-semibold">Shopping Cart</h3>
                    </div>
                    <p class="text-sm sm:text-base text-purple-100">Review items in your cart</p>
                </Link>
            </div>

            <!-- Role-specific Content -->
            <div v-if="$page.props.auth.user.role === 'admin'" class="mb-6 sm:mb-8">
                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 sm:p-6">
                    <div class="flex items-center mb-3 sm:mb-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="ml-3 text-base sm:text-lg font-semibold text-red-800">Admin Access</h3>
                    </div>
                    <p class="text-sm sm:text-base text-red-700 mb-4">You have administrator privileges.</p>
                    <Link
                        href="/admin"
                        class="inline-block bg-red-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-red-700 text-sm sm:text-base"
                    >
                        Go to Admin Panel
                    </Link>
                </div>
            </div>

            <div v-if="$page.props.auth.user.role === 'customer'" class="mb-6 sm:mb-8">
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-blue-800 mb-2">Become a Vendor</h3>
                    <p class="text-sm sm:text-base text-blue-700 mb-4">Want to sell your products on our platform?</p>
                    <Link
                        href="/vendor/apply"
                        class="inline-block bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-700 text-sm sm:text-base"
                    >
                        Apply Now
                    </Link>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold mb-4">Account Information</h3>
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b gap-1 sm:gap-0">
                        <span class="text-sm sm:text-base text-gray-600">Name:</span>
                        <span class="text-sm sm:text-base font-semibold break-words">{{ $page.props.auth.user.name }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b gap-1 sm:gap-0">
                        <span class="text-sm sm:text-base text-gray-600">Email:</span>
                        <span class="text-sm sm:text-base font-semibold break-all">{{ $page.props.auth.user.email }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b gap-1 sm:gap-0">
                        <span class="text-sm sm:text-base text-gray-600">Role:</span>
                        <span class="text-sm sm:text-base font-semibold capitalize">{{ $page.props.auth.user.role }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between py-2 gap-1 sm:gap-0">
                        <span class="text-sm sm:text-base text-gray-600">Account Status:</span>
                        <span class="text-sm sm:text-base text-green-600 font-semibold">Active</span>
                    </div>
                </div>
                <div class="mt-6">
                    <Link
                        href="/profile"
                        class="inline-block bg-gray-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-gray-700 text-sm sm:text-base"
                    >
                        Edit Profile
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
</script>
