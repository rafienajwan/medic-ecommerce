<template>
    <VendorLayout :vendor="vendor">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Products</h1>
                    <p class="text-gray-600 mt-1">Manage your product inventory</p>
                </div>
                <Link
                    href="/vendor/products/create"
                    class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center gap-2"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Product
                </Link>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Active</p>
                    <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Inactive</p>
                    <p class="text-2xl font-bold text-gray-600">{{ stats.inactive }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Low Stock</p>
                    <p class="text-2xl font-bold text-red-600">{{ stats.low_stock }}</p>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div v-if="loading" class="p-8 text-center text-gray-500">
                    Loading...
                </div>
                <div v-else-if="products.data.length === 0" class="p-8 text-center text-gray-500">
                    <p>No products yet. Add your first product to get started!</p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in products.data" :key="product.id">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 flex-shrink-0">
                                            <img class="h-12 w-12 rounded object-contain bg-gray-100" :src="product.image_url || 'https://via.placeholder.com/100'" :alt="product.name">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 line-clamp-1">{{ product.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ product.sku }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ product.category?.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ formatPrice(product.price) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="product.stock < 10 ? 'text-red-600 font-medium' : 'text-gray-900'" class="text-sm">
                                        {{ product.stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ product.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link
                                        :href="`/vendor/products/${product.id}/edit`"
                                        class="text-blue-600 hover:text-blue-900 mr-3"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="toggleStatus(product)"
                                        class="text-gray-600 hover:text-gray-900 mr-3"
                                    >
                                        {{ product.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button
                                        @click="deleteProduct(product)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products.data.length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <p class="text-sm text-gray-700">
                            Showing {{ products.from }} to {{ products.to }} of {{ products.total }} products
                        </p>
                        <div class="flex gap-2">
                            <button
                                v-for="link in products.links"
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
    </VendorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import axios from 'axios';

const page = usePage();
const vendor = computed(() => page.props.auth?.user?.vendor);

const products = ref({
    data: [],
    links: [],
    from: 0,
    to: 0,
    total: 0,
});
const loading = ref(true);

const stats = computed(() => {
    const total = products.value.data.length;
    const active = products.value.data.filter(p => p.is_active).length;
    const inactive = total - active;
    const low_stock = products.value.data.filter(p => p.stock < 10).length;

    return { total, active, inactive, low_stock };
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const loadProducts = async (url = '/api/vendor/products') => {
    loading.value = true;
    try {
        const response = await axios.get(url);
        products.value = response.data;
    } catch (err) {
        console.error('Failed to load products:', err);
    } finally {
        loading.value = false;
    }
};

const changePage = (url) => {
    if (url) {
        loadProducts(url);
    }
};

const toggleStatus = async (product) => {
    if (!confirm(`Are you sure you want to ${product.is_active ? 'deactivate' : 'activate'} this product?`)) {
        return;
    }

    try {
        await axios.patch(`/api/vendor/products/${product.id}`, {
            is_active: !product.is_active
        });
        await loadProducts();
    } catch (err) {
        alert('Failed to update product status');
    }
};

const deleteProduct = async (product) => {
    if (!confirm(`Are you sure you want to delete "${product.name}"? This action cannot be undone.`)) {
        return;
    }

    try {
        await axios.delete(`/api/vendor/products/${product.id}`);
        await loadProducts();
    } catch (err) {
        alert('Failed to delete product');
    }
};

onMounted(() => {
    loadProducts();
});
</script>
