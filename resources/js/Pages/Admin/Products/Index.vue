<template>
    <MainLayout>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Product Management</h1>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input
                        v-model="filters.search"
                        @input="performSearch"
                        type="text"
                        placeholder="Search products..."
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    />
                    <select
                        v-model="filters.status"
                        @change="performSearch"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive (Takedown)</option>
                    </select>
                    <select
                        v-model="filters.vendor"
                        @change="performSearch"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Vendors</option>
                        <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                            {{ vendor.business_name }}
                        </option>
                    </select>
                    <button
                        @click="loadProducts"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                    >
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img :src="product.image_url || '/placeholder.png'" class="w-12 h-12 rounded object-cover mr-3" />
                                    <div>
                                        <div class="font-medium text-gray-900">{{ product.name }}</div>
                                        <div class="text-sm text-gray-500">SKU: {{ product.sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ product.vendor?.business_name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                Rp {{ formatPrice(product.price) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ product.stock }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                >
                                    {{ product.is_active ? 'Active' : 'Takedown' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <button
                                    v-if="product.is_active"
                                    @click="takedownProduct(product)"
                                    class="text-red-600 hover:text-red-800 font-medium"
                                >
                                    Takedown
                                </button>
                                <button
                                    v-else
                                    @click="restoreProduct(product)"
                                    class="text-green-600 hover:text-green-800 font-medium"
                                >
                                    Restore
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="!loading && products.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No products found</p>
                </div>

                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </div>

            <!-- Takedown Modal -->
            <div v-if="takedownModal.show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                    <h2 class="text-2xl font-bold mb-4">Takedown Product</h2>
                    <p class="text-gray-600 mb-4">Produk akan disembunyikan dari customer. Berikan alasan takedown:</p>

                    <textarea
                        v-model="takedownModal.reason"
                        rows="4"
                        placeholder="Contoh: Produk melanggar ketentuan, harga tidak wajar, dll."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"
                    ></textarea>

                    <div class="flex justify-end space-x-3">
                        <button
                            @click="takedownModal.show = false"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </button>
                        <button
                            @click="confirmTakedown"
                            :disabled="!takedownModal.reason || takedownModal.reason.length < 10"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400"
                        >
                            Confirm Takedown
                        </button>
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

const products = ref([]);
const vendors = ref([]);
const loading = ref(false);
const filters = ref({
    search: '',
    status: '',
    vendor: '',
});

const takedownModal = ref({
    show: false,
    product: null,
    reason: '',
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const loadProducts = async () => {
    loading.value = true;
    try {
        const params = {
            search: filters.value.search,
            is_active: filters.value.status === 'active' ? 1 : filters.value.status === 'inactive' ? 0 : '',
            vendor_id: filters.value.vendor,
        };

        const response = await axios.get('/api/admin/products', { params });
        products.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to load products:', error);
        alert('Failed to load products');
    } finally {
        loading.value = false;
    }
};

const loadVendors = async () => {
    try {
        const response = await axios.get('/api/admin/vendors');
        vendors.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to load vendors:', error);
    }
};

const performSearch = () => {
    loadProducts();
};

const takedownProduct = (product) => {
    takedownModal.value = {
        show: true,
        product,
        reason: '',
    };
};

const confirmTakedown = async () => {
    try {
        await axios.put(`/api/admin/products/${takedownModal.value.product.id}`, {
            is_active: false,
            takedown_reason: takedownModal.value.reason,
        });

        alert('Product taken down successfully');
        takedownModal.value.show = false;
        loadProducts();
    } catch (error) {
        console.error('Failed to takedown product:', error);
        alert(error.response?.data?.message || 'Failed to takedown product');
    }
};

const restoreProduct = async (product) => {
    if (!confirm(`Restore product "${product.name}"?`)) return;

    try {
        await axios.put(`/api/admin/products/${product.id}`, {
            is_active: true,
            takedown_reason: null,
        });

        alert('Product restored successfully');
        loadProducts();
    } catch (error) {
        console.error('Failed to restore product:', error);
        alert('Failed to restore product');
    }
};

onMounted(() => {
    loadProducts();
    loadVendors();
});
</script>
