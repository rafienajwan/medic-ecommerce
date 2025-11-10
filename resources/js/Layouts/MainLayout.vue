<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Notification Toast -->
        <NotificationToast />

        <!-- Session Timer Warning (appears when < 2 minutes) -->
        <div
            v-if="$page.props.auth.user && (maxIdleTime - idleTime) < 120 && (maxIdleTime - idleTime) > 0"
            class="fixed top-0 left-0 right-0 bg-yellow-500 text-white px-4 py-2 text-center z-50 animate-pulse text-sm"
        >
            <strong>Peringatan:</strong> Sesi akan berakhir dalam {{ Math.max(0, maxIdleTime - idleTime) }} detik. Klik di mana saja untuk memperpanjang sesi.
        </div>

        <!-- Navigation - Responsive -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-14 sm:h-16">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none mr-2"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <Link href="/" class="flex items-center">
                            <span class="text-lg sm:text-2xl font-bold text-blue-600">🏥 Medic Shop</span>
                        </Link>

                        <!-- Desktop menu -->
                        <div class="hidden md:flex md:ml-8 space-x-4">
                            <Link href="/products" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm lg:text-base">
                                Products
                            </Link>
                            <Link href="/guestbook" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm lg:text-base">
                                Guest Book
                            </Link>
                            <Link v-if="$page.props.auth.user?.role === 'admin'" href="/admin" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm lg:text-base">
                                Admin
                            </Link>
                        </div>
                    </div>

                    <!-- Right side menu -->
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <template v-if="$page.props.auth.user">
                            <Link href="/cart" class="relative text-gray-700 hover:text-blue-600 p-2">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </Link>

                            <div class="relative">
                                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-1 sm:space-x-2 text-gray-700 hover:text-blue-600 p-1 sm:p-2">
                                    <span class="text-xs sm:text-sm lg:text-base max-w-[80px] sm:max-w-none truncate">{{ $page.props.auth.user.name }}</span>
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div v-show="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                    <Link href="/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</Link>
                                    <Link href="/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</Link>

                                    <!-- Show Vendor Dashboard if user has APPROVED vendor account -->
                                    <Link v-if="$page.props.auth.user?.vendor?.status === 'approved'" href="/vendor/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Vendor Dashboard
                                    </Link>

                                    <!-- Show "Application Status" if vendor exists but not approved yet -->
                                    <Link v-else-if="$page.props.auth.user?.vendor" href="/vendor/apply" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <span v-if="$page.props.auth.user.vendor.status === 'pending'" class="flex items-center">
                                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                            Vendor Application
                                        </span>
                                        <span v-else-if="$page.props.auth.user.vendor.status === 'rejected'" class="flex items-center">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                            Vendor Application
                                        </span>
                                    </Link>

                                    <!-- Show Become a Vendor if not admin and doesn't have vendor account -->
                                    <Link v-else-if="$page.props.auth.user?.role !== 'admin'" href="/vendor/apply" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Become a Vendor
                                    </Link>

                                    <Link href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</Link>
                                    <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <Link href="/login" class="text-gray-700 hover:text-blue-600 text-xs sm:text-sm lg:text-base px-2 sm:px-3">Login</Link>
                            <Link href="/register" class="bg-blue-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-blue-700 text-xs sm:text-sm lg:text-base">Register</Link>
                        </template>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div v-show="mobileMenuOpen" class="md:hidden pb-4">
                    <div class="space-y-1">
                        <Link href="/products" class="block px-3 py-2 text-base text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded">
                            Products
                        </Link>
                        <Link href="/guestbook" class="block px-3 py-2 text-base text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded">
                            Guest Book
                        </Link>
                        <Link v-if="$page.props.auth.user?.role === 'admin'" href="/admin" class="block px-3 py-2 text-base text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded">
                            Admin
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages - Responsive -->
        <div v-if="$page.props.flash.error" class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-50 border-l-4 border-red-500 p-3 sm:p-4 rounded text-sm sm:text-base">
                <p class="text-red-700">{{ $page.props.flash.error }}</p>
            </div>
        </div>

        <div v-if="$page.props.flash.success" class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-50 border-l-4 border-green-500 p-3 sm:p-4 rounded text-sm sm:text-base">
                <p class="text-green-700">{{ $page.props.flash.success }}</p>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600">
                <p>&copy; {{ new Date().getFullYear() }} Medical Equipment E-Commerce Platform</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useIdleDetector } from '@/composables/useIdleDetector';
import NotificationToast from '@/Components/NotificationToast.vue';

const userMenuOpen = ref(false);
const mobileMenuOpen = ref(false);
const page = usePage();

// Debug: Log auth user data
console.log('Auth User:', page.props.auth?.user);
console.log('Vendor:', page.props.auth?.user?.vendor);
console.log('Vendor Status:', page.props.auth?.user?.vendor?.status);

// Check if user is authenticated
const isAuthenticated = computed(() => !!page.props.auth?.user);

// Initialize idle detector ONLY for authenticated users
let idleTime = ref(0);
let maxIdleTime = ref(60); // 1 minute idle timeout
let idleDetectorInstance = null;

// Initialize idle detector ONLY for authenticated users
if (isAuthenticated.value) {
    console.log('MainLayout: User is authenticated, preparing idle detector');
    idleDetectorInstance = useIdleDetector();
    idleTime = idleDetectorInstance.idleTime;
    maxIdleTime = idleDetectorInstance.maxIdleTime;
} else {
    console.log('MainLayout: User not authenticated, skipping idle detector');
}

const logout = () => {
    // Cleanup idle detector before logout
    if (idleDetectorInstance && typeof idleDetectorInstance.cleanup === 'function') {
        idleDetectorInstance.cleanup();
    }

    router.post('/logout', {}, {
        onSuccess: () => {
            router.visit('/');  // Redirect to landing page instead of login
        }
    });
};

// Close menu when clicking outside + Initialize idle detector
onMounted(() => {
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
            userMenuOpen.value = false;
        }
    });

    // Initialize idle detector ONLY if user is authenticated
    if (isAuthenticated.value && idleDetectorInstance && typeof idleDetectorInstance.init === 'function') {
        console.log('MainLayout: Initializing idle detector (1 min timeout)');
        idleDetectorInstance.init();
    } else {
        console.log('MainLayout: Skipping idle detector initialization - user not authenticated');
    }
});
</script>
