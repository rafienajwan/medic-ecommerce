<template>
    <VendorLayout :vendor="vendor">
        <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <Link href="/vendor/products" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Products
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 sm:p-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Edit Product</h1>

                <div v-if="loading" class="text-center py-12">
                    <p class="text-gray-500">Loading product...</p>
                </div>

                <form v-else @submit.prevent="submitForm" enctype="multipart/form-data">
                    <!-- Product Name -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                    </div>

                    <!-- Category & SKU -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.category_id"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Select Category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="errors.category_id" class="text-red-500 text-sm mt-1">{{ errors.category_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                SKU <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.sku"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="errors.sku" class="text-red-500 text-sm mt-1">{{ errors.sku }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="form.description"
                            required
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        ></textarea>
                        <p v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</p>
                    </div>

                    <!-- Price & Stock -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Price (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.price"
                                type="number"
                                required
                                min="0"
                                step="0.01"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="errors.price" class="text-red-500 text-sm mt-1">{{ errors.price }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Stock <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.stock"
                                type="number"
                                required
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="errors.stock" class="text-red-500 text-sm mt-1">{{ errors.stock }}</p>
                        </div>
                    </div>

                    <!-- Product Image -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Product Image
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <img v-if="imagePreview" :src="imagePreview" class="mx-auto h-48 w-48 object-contain rounded mb-4" />
                                <img v-else-if="currentImage" :src="currentImage" class="mx-auto h-48 w-48 object-contain rounded mb-4" />
                                <svg v-else class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>{{ imagePreview || currentImage ? 'Change image' : 'Upload a file' }}</span>
                                        <input
                                            ref="imageInput"
                                            type="file"
                                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                            @change="handleImageUpload"
                                            class="sr-only"
                                        />
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">JPEG, JPG, PNG, GIF, WebP up to 2MB</p>
                            </div>
                        </div>
                        <p v-if="errors.image" class="text-red-500 text-sm mt-1">{{ errors.image }}</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <button
                            type="button"
                            @click="$inertia.visit('/vendor/products')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:bg-gray-400 disabled:cursor-not-allowed"
                        >
                            {{ submitting ? 'Updating...' : 'Update Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import axios from 'axios';

const page = usePage();
const vendor = computed(() => page.props.auth?.user?.vendor);

const props = defineProps({
    productId: {
        type: [String, Number],
        required: true
    }
});

const form = ref({
    name: '',
    category_id: '',
    sku: '',
    description: '',
    price: '',
    stock: '',
    image: null,
});

const categories = ref([]);
const currentImage = ref(null);
const imagePreview = ref(null);
const imageInput = ref(null);
const errors = ref({});
const submitting = ref(false);
const loading = ref(true);

const loadProduct = async () => {
    try {
        const response = await axios.get(`/api/vendor/products`);
        const product = response.data.data.find(p => p.id == props.productId);

        if (product) {
            form.value = {
                name: product.name,
                category_id: product.category_id,
                sku: product.sku,
                description: product.description,
                price: product.price,
                stock: product.stock,
                image: null,
            };
            currentImage.value = product.image_url;
        } else {
            router.visit('/vendor/products');
        }
    } catch (err) {
        console.error('Failed to load product:', err);
        router.visit('/vendor/products');
    } finally {
        loading.value = false;
    }
};

const loadCategories = async () => {
    try {
        const response = await axios.get('/api/vendor/products/categories');
        categories.value = response.data.data;
    } catch (err) {
        console.error('Failed to load categories:', err);
    }
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            errors.value.image = 'Please upload only image files (JPEG, JPG, PNG, GIF, WebP)';
            imageInput.value.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            errors.value.image = 'Image size must be less than 2MB';
            imageInput.value.value = '';
            return;
        }

        errors.value.image = null;
        form.value.image = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submitForm = async () => {
    submitting.value = true;
    errors.value = {};

    try {
        const formData = new FormData();
        formData.append('name', form.value.name);
        formData.append('category_id', form.value.category_id);
        formData.append('sku', form.value.sku);
        formData.append('description', form.value.description);
        formData.append('price', form.value.price);
        formData.append('stock', form.value.stock);
        formData.append('_method', 'PUT');
        if (form.value.image) {
            formData.append('image', form.value.image);
        }

        await axios.post(`/api/vendor/products/${props.productId}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        router.visit('/vendor/products');
    } catch (err) {
        if (err.response?.data?.errors) {
            errors.value = err.response.data.errors;
        } else {
            errors.value.general = err.response?.data?.message || 'Failed to update product';
        }
    } finally {
        submitting.value = false;
    }
};

onMounted(() => {
    loadCategories();
    loadProduct();
});
</script>
