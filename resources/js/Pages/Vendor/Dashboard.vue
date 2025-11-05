<template>
    <VendorLayout :vendor="vendor" :pending-orders="stats.pending_orders">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Vendor Dashboard</h1>
                <p class="text-gray-600 mt-2">Manage your products and orders</p>
            </div>

            <!-- Vendor Status Banner -->
            <div v-if="vendor" class="mb-6">
                <div v-if="vendor.status === 'pending'" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Your vendor application is pending approval. You'll be able to add products once approved.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-else-if="vendor.status === 'approved'" class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>{{ vendor.business_name }}</strong> - Your vendor account is active and approved.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-else-if="vendor.status === 'rejected'" class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Your vendor application was rejected. Reason: {{ vendor.rejection_reason || 'No reason provided' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_products }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ stats.active_products }} active</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_orders }}</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ stats.pending_orders }} pending</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">Rp {{ formatPrice(stats.total_revenue) }}</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">This month</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Low Stock</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.low_stock_products }}</p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Stock &lt; 10 units</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button
                        @click="addProduct"
                        :disabled="!vendor || vendor.status !== 'approved'"
                        class="bg-blue-600 text-white px-6 py-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Product
                    </button>

                    <button
                        @click="viewProducts"
                        class="bg-white border-2 border-gray-300 text-gray-700 px-6 py-4 rounded-lg hover:bg-gray-50 flex items-center justify-center gap-2"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Manage Products
                    </button>

                    <button
                        @click="viewOrders"
                        class="bg-white border-2 border-gray-300 text-gray-700 px-6 py-4 rounded-lg hover:bg-gray-50 flex items-center justify-center gap-2"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        View Orders
                    </button>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Recent Products</h2>
                </div>
                <div v-if="loading" class="p-6 text-center text-gray-500">
                    Loading...
                </div>
                <div v-else-if="products.length === 0" class="p-6 text-center text-gray-500">
                    <p>No products yet. Add your first product to get started!</p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in products" :key="product.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded object-cover" :src="product.image_url || 'https://via.placeholder.com/100'" :alt="product.name">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ product.sku }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ formatPrice(product.price) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="product.stock < 10 ? 'text-red-600' : 'text-gray-900'" class="text-sm font-medium">
                                        {{ product.stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ product.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="editProduct(product.id)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                    <button @click="toggleProductStatus(product.id)" class="text-gray-600 hover:text-gray-900">
                                        {{ product.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Recent Orders</h2>
                </div>
                <div v-if="loadingOrders" class="p-6 text-center text-gray-500">
                    Loading...
                </div>
                <div v-else-if="orders.length === 0" class="p-6 text-center text-gray-500">
                    <p>No orders yet.</p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="order in orders" :key="order.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ order.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ order.user?.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ order.product_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ formatPrice(order.amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(order.created_at) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import axios from 'axios';

const props = defineProps({
    vendor: Object,
});

const loading = ref(true);
const loadingOrders = ref(true);
const products = ref([]);
const orders = ref([]);
const stats = ref({
    total_products: 0,
    active_products: 0,
    total_orders: 0,
    pending_orders: 0,
    total_revenue: 0,
    low_stock_products: 0,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const loadDashboardData = async () => {
    loading.value = true;
    loadingOrders.value = true;

    try {
        // Load products
        const productsResponse = await axios.get('/api/vendor/products');
        products.value = productsResponse.data.data.slice(0, 5); // Get latest 5

        // Load orders
        const ordersResponse = await axios.get('/api/vendor/orders');
        orders.value = ordersResponse.data.data.slice(0, 5); // Get latest 5

        // Load stats
        const statsResponse = await axios.get('/api/vendor/dashboard/stats');
        stats.value = {
            total_products: productsResponse.data.data.length,
            active_products: productsResponse.data.data.filter(p => p.is_active).length,
            ...statsResponse.data,
            low_stock_products: productsResponse.data.data.filter(p => p.stock < 10).length,
        };
    } catch (err) {
        console.error('Failed to load dashboard:', err);
    } finally {
        loading.value = false;
        loadingOrders.value = false;
    }
};

const addProduct = () => {
    router.visit('/vendor/products/create');
};

const viewProducts = () => {
    router.visit('/vendor/products');
};

const viewOrders = () => {
    router.visit('/vendor/orders');
};

const editProduct = (productId) => {
    router.visit(`/vendor/products/${productId}/edit`);
};

const toggleProductStatus = async (productId) => {
    const product = products.value.find(p => p.id === productId);
    if (!product) return;

    if (!confirm(`Are you sure you want to ${product.is_active ? 'deactivate' : 'activate'} this product?`)) {
        return;
    }

    try {
        await axios.patch(`/api/vendor/products/${productId}`, {
            is_active: !product.is_active
        });
        await loadDashboardData();
    } catch (err) {
        console.error('Failed to toggle product status:', err);
        alert('Gagal mengubah status produk');
    }
};

onMounted(() => {
    if (props.vendor && props.vendor.status === 'approved') {
        loadDashboardData();
    } else {
        loading.value = false;
        loadingOrders.value = false;
    }
});
</script>
