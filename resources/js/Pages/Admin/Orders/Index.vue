<template>
    <MainLayout>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Order Management</h1>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input
                        v-model="filters.search"
                        @input="performSearch"
                        type="text"
                        placeholder="Search order number..."
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    />
                    <select
                        v-model="filters.status"
                        @change="performSearch"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select
                        v-model="filters.payment_method"
                        @change="performSearch"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Payment Methods</option>
                        <option value="prepaid">Credit/Debit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="cod">COD</option>
                    </select>
                    <button
                        @click="loadOrders"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                    >
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ order.order_number }}</div>
                                <div class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ order.user?.name || order.shipping_name }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                Rp {{ formatPrice(order.total) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ getPaymentMethodLabel(order.payment_method) }}
                                <span v-if="order.bank_type" class="block text-xs text-gray-500">{{ order.bank_type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="getStatusBadgeClass(order.status)"
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                >
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <button
                                    @click="viewOrderDetails(order)"
                                    class="text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    View
                                </button>
                                <button
                                    v-if="order.status !== 'cancelled'"
                                    @click="cancelOrder(order)"
                                    class="text-red-600 hover:text-red-800 font-medium"
                                >
                                    Cancel
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="!loading && orders.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No orders found</p>
                </div>

                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </div>

            <!-- Cancel Order Modal -->
            <div v-if="cancelModal.show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                    <h2 class="text-2xl font-bold mb-4">Cancel Order</h2>
                    <p class="text-gray-600 mb-4">Order akan dibatalkan dan stok dikembalikan. Berikan alasan pembatalan:</p>

                    <textarea
                        v-model="cancelModal.reason"
                        rows="4"
                        placeholder="Contoh: Transaksi mencurigakan, alamat tidak valid, dll."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"
                    ></textarea>

                    <div class="flex justify-end space-x-3">
                        <button
                            @click="cancelModal.show = false"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Close
                        </button>
                        <button
                            @click="confirmCancel"
                            :disabled="!cancelModal.reason || cancelModal.reason.length < 10"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400"
                        >
                            Confirm Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Details Modal -->
            <div v-if="selectedOrder" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
                <div class="bg-white rounded-lg p-8 max-w-3xl w-full mx-4 my-8">
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-2xl font-bold">Order Details</h2>
                        <button @click="selectedOrder = null" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Order Number</label>
                                <p class="mt-1 text-gray-900 font-semibold">{{ selectedOrder.order_number }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1">
                                    <span :class="getStatusBadgeClass(selectedOrder.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ selectedOrder.status }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Shipping Address</label>
                            <p class="mt-1 text-gray-900">{{ selectedOrder.shipping_name }}</p>
                            <p class="text-gray-600">{{ selectedOrder.shipping_address }}, {{ selectedOrder.shipping_city }}</p>
                            <p class="text-gray-600">{{ selectedOrder.shipping_province }} {{ selectedOrder.shipping_postal_code }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Order Items</label>
                            <div class="border rounded-lg divide-y">
                                <div v-for="item in selectedOrder.items" :key="item.id" class="p-4 flex justify-between">
                                    <div>
                                        <p class="font-medium">{{ item.product?.name || 'Product' }}</p>
                                        <p class="text-sm text-gray-600">Qty: {{ item.quantity }}</p>
                                    </div>
                                    <p class="font-medium">Rp {{ formatPrice(item.price * item.quantity) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal:</span>
                                <span>Rp {{ formatPrice(selectedOrder.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Tax:</span>
                                <span>Rp {{ formatPrice(selectedOrder.tax) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Shipping:</span>
                                <span>Rp {{ formatPrice(selectedOrder.shipping_fee) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg mt-2 pt-2 border-t">
                                <span>Total:</span>
                                <span>Rp {{ formatPrice(selectedOrder.total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

const orders = ref([]);
const loading = ref(false);
const selectedOrder = ref(null);
const filters = ref({
    search: '',
    status: '',
    payment_method: '',
});

const cancelModal = ref({
    show: false,
    order: null,
    reason: '',
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getPaymentMethodLabel = (method) => {
    const labels = {
        prepaid: 'Credit/Debit Card',
        paypal: 'PayPal',
        cod: 'Cash on Delivery',
    };
    return labels[method] || method;
};

const getStatusBadgeClass = (status) => {
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
        const params = {
            search: filters.value.search,
            status: filters.value.status,
            payment_method: filters.value.payment_method,
        };

        const response = await axios.get('/api/admin/orders', { params });
        orders.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to load orders:', error);
        alert('Failed to load orders');
    } finally {
        loading.value = false;
    }
};

const performSearch = () => {
    loadOrders();
};

const viewOrderDetails = async (order) => {
    try {
        const response = await axios.get(`/api/admin/orders/${order.id}`);
        selectedOrder.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to load order details:', error);
        alert('Failed to load order details');
    }
};

const cancelOrder = (order) => {
    cancelModal.value = {
        show: true,
        order,
        reason: '',
    };
};

const confirmCancel = async () => {
    try {
        await axios.patch(`/api/admin/orders/${cancelModal.value.order.id}/status`, {
            status: 'cancelled',
            notes: `Admin cancelled: ${cancelModal.value.reason}`,
        });

        alert('Order cancelled successfully');
        cancelModal.value.show = false;
        loadOrders();
    } catch (error) {
        console.error('Failed to cancel order:', error);
        alert(error.response?.data?.message || 'Failed to cancel order');
    }
};

onMounted(() => {
    loadOrders();
});
</script>
