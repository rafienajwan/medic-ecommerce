<template>
    <MainLayout>
        <div class="container-responsive py-4 sm:py-6 lg:py-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 sm:mb-6">Products</h1>

            <!-- Search and Filters - Responsive -->
            <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row gap-3 sm:gap-4">
                <input
                    v-model="search"
                    @input="performSearch"
                    type="text"
                    placeholder="Search products..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                />

                <select
                    v-model="selectedCategory"
                    @change="filterByCategory"
                    class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                >
                    <option value="">All Categories</option>
                    <option value="alat-diagnostik">Alat Diagnostik</option>
                    <option value="alat-terapi">Alat Terapi</option>
                    <option value="perlengkapan-medis">Perlengkapan Medis</option>
                    <option value="obat-perawatan">Obat & Perawatan</option>
                    <option value="kesehatan-ibu-anak">Kesehatan Ibu & Anak</option>
                </select>
            </div>

            <!-- Products Grid - Responsive -->
            <div v-if="products.data.length > 0" class="grid-responsive-products">
                <div
                    v-for="product in products.data"
                    :key="product.id"
                    class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden flex flex-col h-full"
                >
                    <!-- Fixed Image Container -->
                    <div class="w-full h-48 bg-white flex items-center justify-center p-4 flex-shrink-0">
                        <img
                            :src="product.image_url || 'https://via.placeholder.com/400'"
                            :alt="product.name"
                            class="max-w-full max-h-full object-contain"
                        />
                    </div>
                    <div class="p-3 sm:p-4 flex flex-col flex-grow">
                        <!-- Product Name - Fixed Height with Line Clamp -->
                        <h3 class="font-semibold text-base sm:text-lg text-gray-900 mb-2 line-clamp-2 h-12 sm:h-14">
                            {{ product.name }}
                        </h3>

                        <!-- Product Description - Fixed Height with Line Clamp -->
                        <p class="text-xs sm:text-sm text-gray-600 mb-2 line-clamp-2 h-10">
                            {{ product.description }}
                        </p>

                        <!-- Vendor Location - NEW -->
                        <div v-if="product.vendor?.user?.city" class="mb-3 flex items-center gap-1 text-xs text-gray-500">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ product.vendor.user.city }}</span>
                        </div>

                        <!-- Price and Stock -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-lg sm:text-xl font-bold text-blue-600">
                                Rp {{ formatPrice(product.price) }}
                            </span>
                            <span class="text-xs sm:text-sm text-gray-500">
                                Stock: {{ product.stock }}
                            </span>
                        </div>

                        <!-- Button - Push to Bottom -->
                        <div class="mt-auto">
                            <Link
                                :href="`/products/${product.id}`"
                                class="block w-full bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md text-center hover:bg-blue-700 transition-colors text-sm sm:text-base"
                            >
                                View Details
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-12">
                <p class="text-gray-500 text-base sm:text-lg">No products found</p>
            </div>

            <!-- Pagination - Responsive -->
            <div v-if="products.links.length > 3" class="mt-6 sm:mt-8 flex flex-wrap justify-center gap-2">
                <Link
                    v-for="(link, index) in products.links"
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    :class="[
                        'px-3 sm:px-4 py-2 rounded-md text-sm sm:text-base',
                        link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                        !link.url ? 'cursor-not-allowed opacity-50' : ''
                    ]"
                />
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    products: Object,
});

const search = ref('');
const selectedCategory = ref('');

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const performSearch = () => {
    router.get('/products', {
        q: search.value,
        category: selectedCategory.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const filterByCategory = () => {
    router.get('/products', {
        q: search.value,
        category: selectedCategory.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>
