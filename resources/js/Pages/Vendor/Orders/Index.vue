<template>
    <VendorLayout :vendor="vendor" :pending-orders="stats.pending">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Order Management</h1>
                <p class="text-gray-600 mt-1">Manage and process customer orders</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Processing</p>
                    <p class="text-2xl font-bold text-blue-600">{{ stats.processing }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Shipped</p>
                    <p class="text-2xl font-bold text-purple-600">{{ stats.shipped }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Delivered</p>
                    <p class="text-2xl font-bold text-green-600">{{ stats.delivered }}</p>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div v-if="loading" class="p-8 text-center text-gray-500">
                    Loading orders...
                </div>
                <div v-else-if="orders.data.length === 0" class="p-8 text-center text-gray-500">
                    <p>No orders yet for your products.</p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="order in orders.data" :key="order.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">#{{ order.order_id }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ order.customer_name }}</div>
                                    <div class="text-sm text-gray-500">{{ order.customer_email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded object-contain bg-gray-100" :src="order.product_image || 'https://via.placeholder.com/100'" :alt="order.product_name">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ order.product_name }}</div>
                                            <div class="text-xs text-gray-500">{{ order.product_sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ order.quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ formatPrice(order.amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button
                                        @click="viewOrderDetail(order)"
                                        class="text-blue-600 hover:text-blue-900 font-medium mr-3"
                                    >
                                        Detail
                                    </button>
                                    <button
                                        @click="openStatusModal(order)"
                                        class="text-green-600 hover:text-green-900 font-medium"
                                    >
                                        Update
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="orders.data.length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <p class="text-sm text-gray-700">
                            Showing {{ orders.from }} to {{ orders.to }} of {{ orders.total }} orders
                        </p>
                        <div class="flex gap-2">
                            <button
                                v-for="link in orders.links"
                                :key="link.label"
                                @click="changePage(link.url)"
                                :disabled="!link.url"
                                v-html="link.label"
                                :class="[
                                    link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                class="px-3 py-1 border border-gray-300 rounded text-sm"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Detail Modal -->
        <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeDetailModal">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Order Details #{{ selectedOrder?.order_id }}</h3>
                        <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="selectedOrder" class="space-y-4">
                        <!-- Customer Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Customer Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Name</p>
                                    <p class="font-medium">{{ selectedOrder.customer_name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Email</p>
                                    <p class="font-medium">{{ selectedOrder.customer_email }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-gray-500">Phone</p>
                                    <p class="font-medium">{{ selectedOrder.customer_phone || 'N/A' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-gray-500">Shipping Address</p>
                                    <p class="font-medium">{{ selectedOrder.shipping_address }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Product Details</h4>
                            <div class="flex items-center space-x-4">
                                <img :src="selectedOrder.product_image || 'https://via.placeholder.com/100'" class="h-20 w-20 object-contain rounded bg-white" />
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ selectedOrder.product_name }}</p>
                                    <p class="text-sm text-gray-500">SKU: {{ selectedOrder.product_sku }}</p>
                                    <p class="text-sm text-gray-500">Quantity: {{ selectedOrder.quantity }}</p>
                                    <p class="font-semibold text-blue-600 mt-1">Rp {{ formatPrice(selectedOrder.amount) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Order Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Status</p>
                                    <span :class="getStatusClass(selectedOrder.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1">
                                        {{ selectedOrder.status }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-gray-500">Payment Method</p>
                                    <p class="font-medium">{{ formatPaymentMethod(selectedOrder.payment_method) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Order Date</p>
                                    <p class="font-medium">{{ formatDate(selectedOrder.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Last Updated</p>
                                    <p class="font-medium">{{ formatDate(selectedOrder.updated_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button @click="closeDetailModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Close
                            </button>
                            <button @click="openStatusModalFromDetail" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Update Status
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status Modal -->
        <div v-if="showStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeStatusModal">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Update Order Status</h3>
                    <p class="text-gray-600 mb-4">Order #{{ selectedOrder?.order_id }}</p>

                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="newStatus === 'pending' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300'">
                            <input type="radio" v-model="newStatus" value="pending" class="mr-3" />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Pending</p>
                                <p class="text-sm text-gray-500">Order received, awaiting processing</p>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="newStatus === 'processing' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="radio" v-model="newStatus" value="processing" class="mr-3" />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Processing</p>
                                <p class="text-sm text-gray-500">Order is being prepared</p>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="newStatus === 'shipped' ? 'border-purple-500 bg-purple-50' : 'border-gray-300'">
                            <input type="radio" v-model="newStatus" value="shipped" class="mr-3" />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Shipped</p>
                                <p class="text-sm text-gray-500">Order has been shipped</p>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="newStatus === 'delivered' ? 'border-green-500 bg-green-50' : 'border-gray-300'">
                            <input type="radio" v-model="newStatus" value="delivered" class="mr-3" />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Delivered</p>
                                <p class="text-sm text-gray-500">Order has been delivered</p>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="newStatus === 'cancelled' ? 'border-red-500 bg-red-50' : 'border-gray-300'">
                            <input type="radio" v-model="newStatus" value="cancelled" class="mr-3" />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Cancelled</p>
                                <p class="text-sm text-gray-500">Order has been cancelled</p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button @click="closeStatusModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button
                            @click="updateOrderStatus"
                            :disabled="updating || newStatus === selectedOrder?.status"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                        >
                            {{ updating ? 'Updating...' : 'Update Status' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import axios from 'axios';

const page = usePage();
const vendor = computed(() => page.props.auth?.user?.vendor);

const orders = ref({
    data: [],
    links: [],
    from: 0,
    to: 0,
    total: 0,
});
const loading = ref(true);
const showDetailModal = ref(false);
const showStatusModal = ref(false);
const selectedOrder = ref(null);
const newStatus = ref('');
const updating = ref(false);

const stats = computed(() => {
    const data = orders.value.data;
    return {
        total: data.length,
        pending: data.filter(o => o.status === 'pending').length,
        processing: data.filter(o => o.status === 'processing').length,
        shipped: data.filter(o => o.status === 'shipped').length,
        delivered: data.filter(o => o.status === 'delivered').length,
    };
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatPaymentMethod = (method) => {
    const methods = {
        'prepaid': 'Credit/Debit Card',
        'cod': 'Cash on Delivery (COD)',
        'paypal': 'PayPal'
    };
    return methods[method] || method || 'N/A';
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

const loadOrders = async (url = '/api/vendor/orders') => {
    loading.value = true;
    try {
        const response = await axios.get(url);
        orders.value = response.data;
    } catch (err) {
        console.error('Failed to load orders:', err);
    } finally {
        loading.value = false;
    }
};

const changePage = (url) => {
    if (url) {
        loadOrders(url);
    }
};

const viewOrderDetail = (order) => {
    selectedOrder.value = order;
    showDetailModal.value = true;
};

const closeDetailModal = () => {
    showDetailModal.value = false;
    selectedOrder.value = null;
};

const openStatusModal = (order) => {
    selectedOrder.value = order;
    newStatus.value = order.status;
    showStatusModal.value = true;
};

const openStatusModalFromDetail = () => {
    newStatus.value = selectedOrder.value.status;
    showDetailModal.value = false;
    showStatusModal.value = true;
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    if (!showDetailModal.value) {
        selectedOrder.value = null;
    }
    newStatus.value = '';
};

const updateOrderStatus = async () => {
    if (!selectedOrder.value || updating.value) return;

    updating.value = true;
    try {
        await axios.patch(`/api/vendor/orders/${selectedOrder.value.order_id}/status`, {
            status: newStatus.value
        });

        // Update local data
        const index = orders.value.data.findIndex(o => o.order_id === selectedOrder.value.order_id);
        if (index !== -1) {
            orders.value.data[index].status = newStatus.value;
            orders.value.data[index].updated_at = new Date().toISOString();
        }

        alert('Order status updated successfully!');
        closeStatusModal();

        // Reload orders to get fresh data
        await loadOrders();
    } catch (err) {
        console.error('Failed to update order status:', err);
        alert('Failed to update order status: ' + (err.response?.data?.message || err.message));
    } finally {
        updating.value = false;
    }
};

onMounted(() => {
    loadOrders();
});
</script>
