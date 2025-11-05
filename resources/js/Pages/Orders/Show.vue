<template>
    <MainLayout>
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <Link href="/orders" class="text-blue-600 hover:text-blue-800">
                    ← Back to Orders
                </Link>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-6">Order Details</h1>

            <div v-if="order" class="space-y-6">
                <!-- Order Summary Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-xl font-semibold mb-2">Order #{{ order.order_number }}</h2>
                            <p class="text-gray-600">{{ formatDate(order.created_at) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a
                                :href="`/api/orders/${order.id}/invoice`"
                                target="_blank"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-semibold inline-flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download Invoice
                            </a>
                            <span
                                :class="getStatusClass(order.status)"
                                class="px-4 py-2 rounded-full text-sm font-semibold"
                            >
                                {{ order.status.toUpperCase() }}
                            </span>
                        </div>
                    </div>

                    <!-- Cancel Order Button -->
                    <div v-if="canCancelOrder(order)" class="mb-6">
                        <button
                            @click="cancelOrder"
                            :disabled="cancelling"
                            class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 disabled:bg-gray-400"
                        >
                            {{ cancelling ? 'Cancelling...' : 'Cancel Order' }}
                        </button>
                        <p class="text-sm text-gray-500 mt-2">
                            You can cancel this order while it's still pending or processing.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 border-t pt-6">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Shipping Address</h3>
                            <p class="text-gray-600">{{ order.shipping_address }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Payment</h3>
                            <p class="text-gray-600 mb-1">Method: {{ formatPaymentMethod(order.payment_method) }}</p>
                            <p class="text-gray-600">
                                Status:
                                <span :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'">
                                    {{ order.payment_status || 'pending' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Order Items</h3>
                    <div class="space-y-4">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex items-center gap-4 pb-4 border-b last:border-b-0"
                        >
                            <img
                                :src="item.product?.image_url || 'https://via.placeholder.com/100'"
                                :alt="item.product?.name"
                                class="w-20 h-20 object-cover rounded"
                            />
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ item.product?.name }}</h4>
                                <p class="text-sm text-gray-600">Quantity: {{ item.quantity }}</p>
                                <p class="text-sm text-gray-600">Price: Rp {{ formatPrice(item.price) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">
                                    Rp {{ formatPrice(item.quantity * item.price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">Rp {{ formatPrice(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Tax (11%)</span>
                            <span class="text-gray-900">Rp {{ formatPrice(order.tax) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Shipping Fee</span>
                            <span class="text-gray-900">Rp {{ formatPrice(order.shipping_fee) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-lg font-bold border-t pt-3 mt-3">
                            <span>Total Amount</span>
                            <span class="text-blue-600">Rp {{ formatPrice(order.total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Order Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-3 h-3 bg-blue-600 rounded-full mt-1"></div>
                            <div>
                                <p class="font-semibold">Order Placed</p>
                                <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
                            </div>
                        </div>
                        <div v-if="order.status !== 'pending'" class="flex items-start gap-4">
                            <div class="w-3 h-3 bg-blue-600 rounded-full mt-1"></div>
                            <div>
                                <p class="font-semibold">Status: {{ order.status }}</p>
                                <p class="text-sm text-gray-600">{{ formatDate(order.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white rounded-lg shadow p-12 text-center">
                <p class="text-gray-500">Loading order details...</p>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

const props = defineProps({
    orderId: [String, Number],
});

const order = ref(null);
const cancelling = ref(false);

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatPaymentMethod = (method) => {
    const methods = {
        'prepaid': 'Credit/Debit Card',
        'paypal': 'PayPal',
        'cod': 'Cash on Delivery (COD)'
    };
    return methods[method] || method || 'N/A';
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

const canCancelOrder = (order) => {
    return order.status === 'pending' || order.status === 'processing';
};

const cancelOrder = async () => {
    if (!confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        return;
    }

    cancelling.value = true;

    try {
        await axios.post(`/api/orders/${props.orderId}/cancel`);
        await loadOrder();
    } catch (error) {
        console.error(error.response?.data?.message || 'Failed to cancel order');
    } finally {
        cancelling.value = false;
    }
};

const loadOrder = async () => {
    try {
        const response = await axios.get(`/api/orders/${props.orderId}`);
        order.value = response.data.data;
    } catch (error) {
        console.error('Failed to load order:', error);
    }
};

onMounted(() => {
    loadOrder();
});
</script>
