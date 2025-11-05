<template>
    <MainLayout>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Products Management -->
                <Link
                    href="/admin/products"
                    class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-900">Products</h3>
                            <p class="text-gray-600">Manage inventory</p>
                        </div>
                    </div>
                </Link>

                <!-- Orders Management -->
                <Link
                    href="/admin/orders"
                    class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-900">Orders</h3>
                            <p class="text-gray-600">Process orders</p>
                        </div>
                    </div>
                </Link>

                <!-- User Management -->
                <Link
                    href="/admin/users"
                    class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-900">Users</h3>
                            <p class="text-gray-600">Manage users</p>
                        </div>
                    </div>
                </Link>

                <!-- Vendors Management -->
                <Link
                    href="/admin/vendors"
                    class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-4">
                        <div class="bg-yellow-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-900">Vendors</h3>
                            <p class="text-gray-600">Approve vendors</p>
                        </div>
                    </div>
                </Link>

                <!-- Guest Book Management -->
                <Link
                    href="/admin/guestbook"
                    class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-4">
                        <div class="bg-pink-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-900">Guest Book</h3>
                            <p class="text-gray-600">Moderate testimonials</p>
                        </div>
                    </div>
                </Link>

                <!-- Reports -->
                <Link
                    href="/admin/reports"
                    class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center mb-4">
                        <div class="bg-red-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-900">Reports</h3>
                            <p class="text-gray-600">View analytics</p>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Quick Stats -->
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm mb-2">Total Products</p>
                        <p class="text-3xl font-bold text-blue-600">-</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm mb-2">Pending Orders</p>
                        <p class="text-3xl font-bold text-yellow-600">-</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm mb-2">Total Users</p>
                        <p class="text-3xl font-bold text-green-600">-</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm mb-2">Revenue (MTD)</p>
                        <p class="text-3xl font-bold text-purple-600">-</p>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
</script>
