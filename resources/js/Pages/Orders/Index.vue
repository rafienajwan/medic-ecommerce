<template>
    <MainLayout>
        <div class="container-responsive py-6 sm:py-8 lg:py-12">
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Orders</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-2">Track and manage your orders</p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-8 sm:py-12">
                <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-sm sm:text-base text-gray-600">Loading orders...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="orders.length === 0" class="text-center py-8 sm:py-12 bg-white rounded-lg shadow">
                <svg class="mx-auto h-16 w-16 sm:h-24 sm:w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900">No orders yet</h3>
                <p class="mt-2 text-sm text-gray-500">Start shopping to see your orders here.</p>
                <div class="mt-6">
                    <Link href="/products" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm sm:text-base">
                        Browse Products
                    </Link>
                </div>
            </div>

            <!-- Orders List -->
            <div v-else class="space-y-3 sm:space-y-4">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="bg-white rounded-lg shadow hover:shadow-md transition-shadow"
                >
                    <div class="p-4 sm:p-6">
                        <!-- Order Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 pb-4 border-b">
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Order #{{ order.order_number }}</h3>
                                <p class="text-xs sm:text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                            </div>
                            <div class="flex flex-col sm:text-right gap-2">
                                <span :class="getStatusClass(order.status)" class="px-3 py-1 text-xs sm:text-sm font-semibold rounded-full text-center sm:text-right">
                                    {{ formatStatus(order.status) }}
                                </span>
                                <p class="text-xs sm:text-sm text-gray-500">{{ formatPaymentMethod(order.payment_method) }}</p>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="space-y-3 mb-4">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="flex items-center gap-3 sm:gap-4"
                            >
                                <div class="flex-shrink-0 w-12 h-12 sm:w-16 sm:h-16 bg-white border rounded flex items-center justify-center p-1">
                                    <img
                                        :src="item.product?.image_url || 'https://via.placeholder.com/100'"
                                        :alt="item.product_name"
                                        class="max-w-full max-h-full object-contain"
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm sm:text-base text-gray-900 truncate">{{ item.product_name }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-500">SKU: {{ item.product_sku }}</p>
                                    <p class="text-xs sm:text-sm text-gray-600">Qty: {{ item.quantity }} × Rp {{ formatPrice(item.price) }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-semibold text-sm sm:text-base text-gray-900 whitespace-nowrap">Rp {{ formatPrice(item.subtotal) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between text-xs sm:text-sm mb-2">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">Rp {{ formatPrice(order.subtotal) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs sm:text-sm mb-2">
                                <span class="text-gray-600">Tax (11%)</span>
                                <span class="text-gray-900">Rp {{ formatPrice(order.tax) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs sm:text-sm mb-3">
                                <span class="text-gray-600">Shipping Fee</span>
                                <span class="text-gray-900">Rp {{ formatPrice(order.shipping_fee) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-base sm:text-lg font-bold border-t pt-3">
                                <span class="text-gray-900">Total</span>
                                <span class="text-blue-600">Rp {{ formatPrice(order.total) }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-4 flex flex-col sm:flex-row gap-2 sm:gap-3">
                            <Link
                                :href="`/orders/${order.id}`"
                                class="flex-1 text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm sm:text-base"
                            >
                                View Details
                            </Link>
                            <button
                                v-if="order.invoice_path"
                                @click="downloadInvoice(order.id)"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50"
                            >
                                Download Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';

const { success, error } = useNotification();

const loading = ref(true);
const orders = ref([]);

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatStatus = (status) => {
    const statuses = {
        pending: 'Pending',
        processing: 'Processing',
        shipped: 'Shipped',
        delivered: 'Delivered',
        cancelled: 'Cancelled'
    };
    return statuses[status] || status;
};

const formatPaymentMethod = (method) => {
    const methods = {
        prepaid: 'Credit/Debit Card',
        paypal: 'PayPal',
        cod: 'Cash on Delivery',
    };
    return methods[method] || method;
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

const loadOrders = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/orders');
        orders.value = response.data.orders.data || [];
    } catch (err) {
        console.error('Failed to load orders:', err);
        error('Gagal memuat pesanan');
    } finally {
        loading.value = false;
    }
};

const downloadInvoice = async (orderId) => {
    try {
        window.open(`/api/orders/${orderId}/invoice`, '_blank');
        success('Invoice is being downloaded');
    } catch (err) {
        console.error('Failed to download invoice:', err);
        error('Gagal mengunduh invoice');
    }
};

onMounted(() => {
    loadOrders();
});
</script>
