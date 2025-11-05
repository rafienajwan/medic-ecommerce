<template>
    <MainLayout>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Reports & Analytics</h1>

            <!-- Date Range Selector -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input
                            v-model="dateRange.from"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input
                            v-model="dateRange.to"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select</label>
                        <select
                            @change="selectQuickRange"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Custom Range</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button
                            @click="loadReports"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                        >
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div v-if="report" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Total Revenue</h3>
                    <p class="text-3xl font-bold text-green-600">Rp {{ formatPrice(report.total_revenue) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Total Orders</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ report.total_orders }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Total Customers</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ report.total_customers }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Avg Order Value</h3>
                    <p class="text-3xl font-bold text-orange-600">Rp {{ formatPrice(report.avg_order_value) }}</p>
                </div>
            </div>

            <!-- Payment Methods Breakdown -->
            <div v-if="report" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Payment Methods</h3>
                    <div class="space-y-3">
                        <div v-for="(value, key) in report.payment_methods" :key="key" class="flex justify-between items-center">
                            <span class="text-gray-700 capitalize">{{ getPaymentMethodLabel(key) }}</span>
                            <span class="font-semibold">{{ value }} orders</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Order Status</h3>
                    <div class="space-y-3">
                        <div v-for="(value, key) in report.order_status" :key="key" class="flex justify-between items-center">
                            <span class="text-gray-700 capitalize">{{ key }}</span>
                            <span class="font-semibold">{{ value }} orders</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div v-if="report && report.top_products" class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">Top 10 Products</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sold</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(product, index) in report.top_products" :key="index">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ product.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ product.total_sold }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-green-600">Rp {{ formatPrice(product.revenue) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Vendors -->
            <div v-if="report && report.top_vendors" class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Top 10 Vendors</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(vendor, index) in report.top_vendors" :key="index">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ vendor.business_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ vendor.total_orders }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-green-600">Rp {{ formatPrice(vendor.revenue) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-gray-600">Loading report...</p>
            </div>

            <div v-if="!report && !loading" class="text-center py-12">
                <p class="text-gray-500">Select date range and click "Generate Report" to view analytics</p>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

const report = ref(null);
const loading = ref(false);
const dateRange = ref({
    from: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
    to: new Date().toISOString().split('T')[0],
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price || 0);
};

const getPaymentMethodLabel = (method) => {
    const labels = {
        prepaid: 'Credit/Debit Card',
        paypal: 'PayPal',
        cod: 'Cash on Delivery',
    };
    return labels[method] || method;
};

const selectQuickRange = (event) => {
    const value = event.target.value;
    const today = new Date();

    switch (value) {
        case 'today':
            dateRange.value.from = today.toISOString().split('T')[0];
            dateRange.value.to = today.toISOString().split('T')[0];
            break;
        case 'week':
            const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
            dateRange.value.from = weekStart.toISOString().split('T')[0];
            dateRange.value.to = new Date().toISOString().split('T')[0];
            break;
        case 'month':
            dateRange.value.from = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            dateRange.value.to = new Date().toISOString().split('T')[0];
            break;
        case 'year':
            dateRange.value.from = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            dateRange.value.to = new Date().toISOString().split('T')[0];
            break;
    }

    if (value) {
        loadReports();
    }
};

const loadReports = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/reports/monthly', {
            params: {
                from: dateRange.value.from,
                to: dateRange.value.to,
            }
        });
        report.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to load reports:', error);
        alert('Failed to load reports');
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadReports();
});
</script>
