<template>
    <MainLayout>
        <div class="container-responsive py-6 sm:py-8 lg:py-12">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 sm:mb-8">Shopping Cart</h1>

            <!-- Empty Cart State -->
            <div v-if="!cartItems || cartItems.length === 0" class="text-center py-8 sm:py-12">
                <svg class="mx-auto h-16 w-16 sm:h-24 sm:w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900">Your cart is empty</h3>
                <p class="mt-2 text-sm text-gray-500">Start shopping to add items to your cart.</p>
                <div class="mt-6">
                    <Link href="/products" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm sm:text-base">
                        Browse Products
                    </Link>
                </div>
            </div>

            <!-- Cart Items -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                <!-- Items List -->
                <div class="lg:col-span-2 space-y-3 sm:space-y-4">
                    <div v-for="item in cartItems" :key="item.id" class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0 w-full sm:w-20 lg:w-24 h-20 lg:h-24 bg-white border rounded-lg flex items-center justify-center p-2">
                                <img
                                    v-if="item.product.image_url"
                                    :src="item.product.image_url"
                                    :alt="item.product.name"
                                    class="max-w-full max-h-full object-contain"
                                />
                                <svg v-else class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <!-- Product Details -->
                            <div class="flex-grow min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ item.product.name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-500 truncate">{{ item.product.category?.name }}</p>
                                <p class="text-base sm:text-lg font-bold text-indigo-600 mt-1 sm:mt-2">
                                    Rp {{ formatPrice(item.price) }}
                                </p>
                            </div>

                            <!-- Quantity Controls and Remove - Mobile -->
                            <div class="flex items-center justify-between sm:justify-end gap-4">
                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="decreaseQuantity(item)"
                                        class="px-2 sm:px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm"
                                        :disabled="updating"
                                    >
                                        -
                                    </button>
                                    <span class="px-3 sm:px-4 py-1 bg-gray-100 rounded text-sm sm:text-base min-w-[2.5rem] text-center">{{ item.quantity }}</span>
                                    <button
                                        @click="increaseQuantity(item)"
                                        class="px-2 sm:px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm"
                                        :disabled="updating"
                                    >
                                        +
                                    </button>
                                </div>

                                <!-- Remove Button -->
                                <button
                                    @click="removeItem(item.id)"
                                    class="text-red-600 hover:text-red-800"
                                    :disabled="updating"
                                >
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6 lg:sticky lg:top-4">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm sm:text-base text-gray-600">
                                <span>Subtotal ({{ totalItems }} items)</span>
                                <span class="font-medium">Rp {{ formatPrice(cartTotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm sm:text-base text-gray-600">
                                <span>Shipping</span>
                                <span class="text-xs sm:text-sm">At checkout</span>
                            </div>
                        </div>

                        <div class="border-t pt-4 mb-6">
                            <div class="flex justify-between text-lg sm:text-xl font-bold">
                                <span>Total</span>
                                <span class="text-indigo-600">Rp {{ formatPrice(cartTotal) }}</span>
                            </div>
                        </div>

                        <Link
                            href="/checkout"
                            class="block w-full bg-indigo-600 text-white text-center py-2.5 sm:py-3 rounded-lg hover:bg-indigo-700 font-semibold text-sm sm:text-base"
                        >
                            Proceed to Checkout
                        </Link>

                        <Link
                            href="/products"
                            class="block w-full mt-3 bg-gray-200 text-gray-800 text-center py-2.5 sm:py-3 rounded-lg hover:bg-gray-300 font-semibold text-sm sm:text-base"
                        >
                            Continue Shopping
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';
import { useNotification } from '@/composables/useNotification';

const { success, error } = useNotification();

const cartItems = ref([]);
const cartTotal = ref(0);
const totalItems = ref(0);
const updating = ref(false);

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const loadCart = async () => {
    try {
        const response = await axios.get('/api/cart');
        cartItems.value = response.data.cart.items || [];
        cartTotal.value = response.data.total || 0;
        totalItems.value = response.data.items_count || 0;
    } catch (err) {
        console.error('Failed to load cart:', err);
        error('Gagal memuat keranjang belanja');
    }
};

const updateQuantity = async (itemId, quantity) => {
    if (quantity < 1) return;

    updating.value = true;
    try {
        await axios.patch(`/api/cart/${itemId}`, { quantity });
        await loadCart();
        success('Jumlah produk berhasil diperbarui');
    } catch (err) {
        console.error('Failed to update quantity:', err);
        error('Gagal memperbarui jumlah produk');
    } finally {
        updating.value = false;
    }
};

const increaseQuantity = (item) => {
    updateQuantity(item.id, item.quantity + 1);
};

const decreaseQuantity = (item) => {
    if (item.quantity > 1) {
        updateQuantity(item.id, item.quantity - 1);
    }
};

const removeItem = async (itemId) => {
    if (!confirm('Hapus produk ini dari keranjang?')) return;

    updating.value = true;
    try {
        await axios.delete(`/api/cart/${itemId}`);
        await loadCart();
        success('Produk berhasil dihapus dari keranjang');
    } catch (err) {
        console.error('Failed to remove item:', err);
        error('Gagal menghapus produk dari keranjang');
    } finally {
        updating.value = false;
    }
};

onMounted(() => {
    loadCart();
});
</script>
