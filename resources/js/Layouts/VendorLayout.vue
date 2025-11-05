<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Notification Toast -->
        <NotificationToast />

        <!-- Vendor Navigation -->
        <nav class="bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-blue-100 hover:text-white hover:bg-blue-500 focus:outline-none mr-3"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Logo & Brand -->
                        <div class="flex items-center space-x-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <div>
                                <h1 class="text-white font-bold text-lg">Vendor Dashboard</h1>
                                <p class="text-blue-100 text-xs">{{ vendor?.business_name }}</p>
                            </div>
                        </div>

                        <!-- Desktop Navigation -->
                        <div class="hidden md:flex md:ml-10 space-x-1">
                            <Link href="/vendor/dashboard" :class="isActive('/vendor/dashboard')" class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link href="/vendor/products" :class="isActive('/vendor/products')" class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Products
                            </Link>
                            <Link href="/vendor/orders" :class="isActive('/vendor/orders')" class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Orders
                                <span v-if="pendingOrders > 0" class="ml-2 px-2 py-0.5 text-xs bg-yellow-400 text-yellow-900 rounded-full">
                                    {{ pendingOrders }}
                                </span>
                            </Link>
                        </div>
                    </div>

                    <!-- Right side menu -->
                    <div class="flex items-center space-x-3">
                        <!-- Back to Store -->
                        <Link href="/" class="hidden sm:flex items-center px-3 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-500 rounded-md transition-colors">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Store
                        </Link>

                        <!-- User Menu -->
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 text-white hover:text-blue-100 bg-blue-500 hover:bg-blue-600 px-3 py-2 rounded-md transition-colors">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium hidden sm:inline">{{ $page.props.auth.user.name }}</span>
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div v-show="userMenuOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-20 border border-gray-200">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $page.props.auth.user.email }}</p>
                                </div>
                                <Link href="/vendor/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Vendor Profile
                                </Link>
                                <Link href="/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Customer Dashboard
                                </Link>
                                <div class="border-t border-gray-200"></div>
                                <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Navigation Menu -->
                <div v-show="mobileMenuOpen" class="md:hidden pb-4 space-y-1">
                    <Link href="/vendor/dashboard" :class="isActiveMobile('/vendor/dashboard')" class="block px-3 py-2 rounded-md text-base font-medium">
                        <svg class="inline h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </Link>
                    <Link href="/vendor/products" :class="isActiveMobile('/vendor/products')" class="block px-3 py-2 rounded-md text-base font-medium">
                        <svg class="inline h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Products
                    </Link>
                    <Link href="/vendor/orders" :class="isActiveMobile('/vendor/orders')" class="block px-3 py-2 rounded-md text-base font-medium">
                        <svg class="inline h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Orders
                        <span v-if="pendingOrders > 0" class="ml-2 px-2 py-0.5 text-xs bg-yellow-400 text-yellow-900 rounded-full">
                            {{ pendingOrders }}
                        </span>
                    </Link>
                    <Link href="/" class="block px-3 py-2 rounded-md text-base font-medium text-blue-100 hover:text-white hover:bg-blue-500">
                        <svg class="inline h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Back to Store
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash.error" class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <p class="text-red-700">{{ $page.props.flash.error }}</p>
            </div>
        </div>

        <div v-if="$page.props.flash.success" class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <p class="text-green-700">{{ $page.props.flash.success }}</p>
            </div>
        </div>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600 text-sm">
                <p>&copy; {{ new Date().getFullYear() }} Vendor Dashboard - Medic Shop</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import NotificationToast from '@/Components/NotificationToast.vue';

const props = defineProps({
    vendor: Object,
    pendingOrders: {
        type: Number,
        default: 0
    }
});

const userMenuOpen = ref(false);
const mobileMenuOpen = ref(false);
const page = usePage();

const currentPath = computed(() => page.url);

const isActive = (path) => {
    const active = currentPath.value === path || currentPath.value.startsWith(path + '/');
    return active ? 'bg-blue-500 text-white' : 'text-blue-100 hover:text-white hover:bg-blue-500';
};

const isActiveMobile = (path) => {
    const active = currentPath.value === path || currentPath.value.startsWith(path + '/');
    return active ? 'bg-blue-500 text-white' : 'text-blue-100 hover:text-white hover:bg-blue-500';
};

const logout = () => {
    router.post('/logout', {}, {
        onSuccess: () => {
            router.visit('/');
        }
    });
};
</script>
