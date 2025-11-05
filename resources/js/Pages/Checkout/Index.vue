<template>
    <MainLayout>
        <div class="container-responsive py-6 sm:py-8 lg:py-12">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 sm:mb-8">Checkout</h1>

            <div v-if="loading" class="text-center py-8 sm:py-12">
                <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-sm sm:text-base text-gray-600">Loading cart...</p>
            </div>

            <div v-else-if="!cartItems || cartItems.length === 0" class="text-center py-8 sm:py-12">
                <svg class="mx-auto h-16 w-16 sm:h-24 sm:w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900">Your cart is empty</h3>
                <p class="mt-2 text-sm text-gray-500">Add items to your cart before checking out.</p>
                <div class="mt-6">
                    <Link href="/products" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm sm:text-base">
                        Browse Products
                    </Link>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                    <!-- Shipping Information -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">Shipping Information</h2>
                        <form @submit.prevent="placeOrder" class="space-y-3 sm:space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                    <input
                                        v-model="form.shipping_name"
                                        type="text"
                                        required
                                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input
                                        v-model="form.shipping_email"
                                        type="email"
                                        required
                                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                <input
                                    v-model="form.shipping_phone"
                                    type="tel"
                                    required
                                    class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Address *</label>
                                <textarea
                                    v-model="form.shipping_address"
                                    required
                                    rows="3"
                                    class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">City *</label>
                                    <input
                                        v-model="form.shipping_city"
                                        type="text"
                                        required
                                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Province *</label>
                                    <input
                                        v-model="form.shipping_province"
                                        type="text"
                                        required
                                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                                    <input
                                        v-model="form.shipping_postal_code"
                                        type="text"
                                        required
                                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Order Notes (Optional)</label>
                                <textarea
                                    v-model="form.notes"
                                    rows="3"
                                    placeholder="Any special instructions for your order..."
                                    class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>
                        </form>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">Payment Method</h2>
                        <div class="space-y-2 sm:space-y-3">
                            <!-- Credit/Debit Card (Prepaid) -->
                            <label class="flex items-start p-3 sm:p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                                   :class="{ 'border-blue-500 bg-blue-50': form.payment_method === 'prepaid' }">
                                <input
                                    type="radio"
                                    v-model="form.payment_method"
                                    value="prepaid"
                                    class="h-4 w-4 text-blue-600 mt-1 flex-shrink-0"
                                />
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="flex items-center flex-wrap gap-1">
                                        <svg class="h-5 w-5 sm:h-6 sm:w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span class="font-medium text-gray-900 text-sm sm:text-base">Credit/Debit Card</span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Pay securely with your credit or debit card</p>

                                    <!-- Bank Selection -->
                                    <div v-if="form.payment_method === 'prepaid'" class="mt-3">
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Bank/Card Type</label>
                                        <select
                                            v-model="form.bank_type"
                                            required
                                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">-- Select Bank --</option>
                                            <option value="visa">Visa</option>
                                            <option value="mastercard">Mastercard</option>
                                            <option value="bca">BCA Virtual Account</option>
                                            <option value="mandiri">Mandiri Virtual Account</option>
                                            <option value="bni">BNI Virtual Account</option>
                                            <option value="bri">BRI Virtual Account</option>
                                            <option value="permata">Permata Bank</option>
                                            <option value="cimb">CIMB Niaga</option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-2">💳 You'll be redirected to payment gateway after placing order</p>
                                    </div>
                                </div>
                            </label>

                            <!-- PayPal -->
                            <label class="flex items-start p-3 sm:p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                                   :class="{ 'border-blue-500 bg-blue-50': form.payment_method === 'paypal' }">
                                <input
                                    type="radio"
                                    v-model="form.payment_method"
                                    value="paypal"
                                    class="h-4 w-4 text-blue-600 mt-1 flex-shrink-0"
                                />
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center flex-wrap gap-1">
                                        <svg class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.067 8.478c.492.88.556 2.014.3 3.327-.74 3.806-3.276 5.12-6.514 5.12h-.5a.805.805 0 00-.794.68l-.04.22-.63 3.993-.028.15a.805.805 0 01-.794.68H7.72a.483.483 0 01-.477-.558L7.418 21h1.518l.95-6.02h1.385c4.678 0 7.75-2.203 8.796-6.502z"/>
                                            <path d="M2.379 0h5.304c.456 0 .848.331.925.784l1.538 9.745c.023.144-.09.271-.234.271h-2.35L9.031 2.24A.928.928 0 008.106 1.5H3.304a.925.925 0 00-.925.925v8.85H.925A.925.925 0 010 10.35V.925C0 .414.414 0 .925 0h1.454z"/>
                                        </svg>
                                        <span class="font-medium text-gray-900 text-sm sm:text-base">PayPal</span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Pay with your PayPal account</p>
                                </div>
                            </label>

                            <!-- Cash on Delivery -->
                            <label class="flex items-start p-3 sm:p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                                   :class="{
                                       'border-blue-500 bg-blue-50': form.payment_method === 'cod',
                                       'opacity-50 cursor-not-allowed': codCheckResult && !codCheckResult.cod_available
                                   }">
                                <input
                                    type="radio"
                                    v-model="form.payment_method"
                                    value="cod"
                                    :disabled="codCheckResult && !codCheckResult.cod_available"
                                    class="h-4 w-4 text-blue-600 mt-1 flex-shrink-0"
                                />
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div class="flex items-center flex-wrap gap-1">
                                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <span class="font-medium text-gray-900 text-sm sm:text-base">Cash on Delivery (COD)</span>
                                        </div>
                                        <button
                                            v-if="form.shipping_city"
                                            type="button"
                                            @click.stop="checkCodAvailability"
                                            :disabled="checkingCod"
                                            class="px-2 sm:px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400 whitespace-nowrap"
                                        >
                                            {{ checkingCod ? 'Checking...' : 'Check Availability' }}
                                        </button>
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Pay when you receive your order</p>
                                    <p class="text-xs text-amber-600 mt-1">⚠️ Same-city delivery only - Click "Check Availability"</p>

                                    <!-- COD Check Result -->
                                    <div v-if="codCheckResult" class="mt-3 p-2 sm:p-3 rounded-md text-xs sm:text-sm" :class="codCheckResult.cod_available ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                                        <div class="flex items-start gap-2">
                                            <svg v-if="codCheckResult.cod_available" class="h-4 w-4 sm:h-5 sm:w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <svg v-else class="h-4 w-4 sm:h-5 sm:w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div class="flex-1">
                                                <p class="font-semibold" :class="codCheckResult.cod_available ? 'text-green-800' : 'text-red-800'">
                                                    {{ codCheckResult.message }}
                                                </p>
                                                <p class="mt-1" :class="codCheckResult.cod_available ? 'text-green-700' : 'text-red-700'">
                                                    {{ codCheckResult.details }}
                                                </p>

                                                <!-- Show incompatible vendors -->
                                                <div v-if="!codCheckResult.cod_available && codCheckResult.incompatible_vendors.length > 0" class="mt-2">
                                                    <p class="font-semibold text-red-800 mb-1">Vendor dari kota berbeda:</p>
                                                    <ul class="text-red-700 space-y-1">
                                                        <li v-for="vendor in codCheckResult.incompatible_vendors" :key="vendor.vendor_id" class="flex items-start gap-1">
                                                            <span class="text-red-500">•</span>
                                                            <span class="break-words">{{ vendor.vendor_name }} ({{ vendor.vendor_city }}) - {{ vendor.products.length }} produk</span>
                                                        </li>
                                                    </ul>
                                                    <p class="text-red-700 mt-2 font-medium">
                                                        💡 Tip: Pilih metode pembayaran lain atau beli hanya dari vendor di kota {{ codCheckResult.user_city }}
                                                    </p>
                                                </div>

                                                <!-- Show compatible vendors -->
                                                <div v-if="codCheckResult.cod_available && codCheckResult.compatible_vendors.length > 0" class="mt-2">
                                                    <p class="font-semibold text-green-800 mb-1">Vendor tersedia (kota {{ codCheckResult.user_city }}):</p>
                                                    <ul class="text-green-700 space-y-1">
                                                        <li v-for="vendor in codCheckResult.compatible_vendors" :key="vendor.vendor_id" class="flex items-start gap-1">
                                                            <span class="text-green-500">✓</span>
                                                            <span class="break-words">{{ vendor.vendor_name }} - {{ vendor.products.length }} produk</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6 lg:sticky lg:top-4">
                        <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">Order Summary</h2>

                        <!-- Vendor Info -->
                        <div v-if="vendorsByCity.length > 0" class="mb-3 sm:mb-4 p-2 sm:p-3 bg-blue-50 rounded-md border border-blue-200">
                            <p class="text-xs font-semibold text-blue-900 mb-2">📍 Vendor Locations in Cart:</p>
                            <div class="space-y-1">
                                <div v-for="vendorGroup in vendorsByCity" :key="vendorGroup.city" class="text-xs text-blue-800 break-words">
                                    <span class="font-medium">{{ vendorGroup.city }}:</span>
                                    <span class="ml-1">{{ vendorGroup.vendors.join(', ') }}</span>
                                    <span class="text-blue-600 ml-1">({{ vendorGroup.productCount }} items)</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 sm:space-y-3 mb-3 sm:mb-4 max-h-48 sm:max-h-60 overflow-y-auto">
                            <div v-for="item in cartItems" :key="item.id" class="flex items-center gap-2 sm:gap-3 pb-2 sm:pb-3 border-b">
                                <div class="flex-shrink-0 w-12 h-12 sm:w-16 sm:h-16 bg-white border rounded flex items-center justify-center p-1">
                                    <img
                                        v-if="item.product.image_url"
                                        :src="item.product.image_url"
                                        :alt="item.product.name"
                                        class="max-w-full max-h-full object-contain"
                                    />
                                    <svg v-else class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ item.product.name }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ item.quantity }}</p>
                                    <p v-if="item.product.vendor" class="text-xs text-blue-600 truncate">
                                        {{ item.product.vendor.business_name }}
                                    </p>
                                </div>
                                <p class="text-xs sm:text-sm font-semibold text-gray-900 whitespace-nowrap">
                                    Rp {{ formatPrice(item.price * item.quantity) }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2 border-t pt-3 sm:pt-4">
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium">Rp {{ formatPrice(cartTotal) }}</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">Tax (11%)</span>
                                <span class="font-medium">Rp {{ formatPrice(tax) }}</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">Shipping Fee</span>
                                <span class="font-medium">Rp {{ formatPrice(shippingFee) }}</span>
                            </div>
                            <div class="flex justify-between text-base sm:text-lg font-bold border-t pt-2 mt-2">
                                <span>Total</span>
                                <span class="text-blue-600">Rp {{ formatPrice(total) }}</span>
                            </div>
                        </div>

                        <button
                            @click="placeOrder"
                            :disabled="processing || !form.payment_method"
                            class="w-full mt-4 sm:mt-6 bg-blue-600 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold text-sm sm:text-base"
                        >
                            {{ processing ? 'Processing...' : 'Place Order' }}
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-3 sm:mt-4">
                            By placing your order, you agree to our terms and conditions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';

const { success, error, warning } = useNotification();
const page = usePage();

const cartItems = ref([]);
const cartTotal = ref(0);
const loading = ref(true);
const processing = ref(false);
const checkingCod = ref(false);
const codCheckResult = ref(null);

const form = ref({
    shipping_name: '',
    shipping_email: '',
    shipping_phone: '',
    shipping_address: '',
    shipping_city: '',
    shipping_province: '',
    shipping_postal_code: '',
    payment_method: 'prepaid',
    bank_type: '',
    notes: '',
});

const shippingFee = 15000;

const tax = computed(() => {
    return Math.round(cartTotal.value * 0.11);
});

const total = computed(() => {
    return cartTotal.value + tax.value + shippingFee;
});

// Group vendors by city for display
const vendorsByCity = computed(() => {
    const cityGroups = {};

    cartItems.value.forEach(item => {
        if (item.product?.vendor?.user?.city) {
            const city = item.product.vendor.user.city;
            const vendorName = item.product.vendor.business_name;

            if (!cityGroups[city]) {
                cityGroups[city] = {
                    city: city,
                    vendors: new Set(),
                    productCount: 0
                };
            }

            cityGroups[city].vendors.add(vendorName);
            cityGroups[city].productCount += item.quantity;
        }
    });

    return Object.values(cityGroups).map(group => ({
        ...group,
        vendors: Array.from(group.vendors)
    }));
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const loadCart = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/cart');
        cartItems.value = response.data.cart.items || [];
        cartTotal.value = response.data.total || 0;

        // Autofill shipping data from user profile
        const user = page.props.auth.user;
        if (user) {
            form.value.shipping_name = user.name || '';
            form.value.shipping_email = user.email || '';
            form.value.shipping_phone = user.phone || '';
            form.value.shipping_address = user.address || '';
            form.value.shipping_city = user.city || '';
            form.value.shipping_province = user.province || '';
            form.value.shipping_postal_code = user.postal_code || '';
        }
    } catch (err) {
        console.error('Failed to load cart:', err);
        error('Gagal memuat keranjang belanja');
    } finally {
        loading.value = false;
    }
};

const checkCodAvailability = async () => {
    if (!form.value.shipping_city) {
        warning('Silakan isi kota pengiriman terlebih dahulu');
        return;
    }

    checkingCod.value = true;
    codCheckResult.value = null;

    try {
        const response = await axios.post('/api/cod/check', {
            shipping_city: form.value.shipping_city
        });

        codCheckResult.value = response.data;

        if (response.data.cod_available) {
            success('COD tersedia untuk pesanan Anda!');
        } else {
            warning('COD tidak tersedia. Silakan pilih metode pembayaran lain.');
            // Auto-deselect COD if not available
            if (form.value.payment_method === 'cod') {
                form.value.payment_method = 'prepaid';
            }
        }
    } catch (err) {
        console.error('Failed to check COD:', err);
        error('Gagal memeriksa ketersediaan COD');
    } finally {
        checkingCod.value = false;
    }
};

// Watch for city changes and reset COD check
watch(() => form.value.shipping_city, () => {
    codCheckResult.value = null;
    if (form.value.payment_method === 'cod') {
        form.value.payment_method = 'prepaid';
    }
});

const placeOrder = async () => {
    if (!form.value.payment_method) {
        error('Silakan pilih metode pembayaran');
        return;
    }

    // Validate bank selection for prepaid
    if (form.value.payment_method === 'prepaid' && !form.value.bank_type) {
        error('Silakan pilih bank/kartu yang akan digunakan');
        return;
    }

    // Validate COD selection
    if (form.value.payment_method === 'cod') {
        if (!codCheckResult.value) {
            warning('Silakan periksa ketersediaan COD terlebih dahulu');
            return;
        }
        if (!codCheckResult.value.cod_available) {
            error('COD tidak tersedia untuk kota Anda. Silakan pilih metode pembayaran lain.');
            return;
        }
    }

    processing.value = true;
    try {
        const response = await axios.post('/api/orders', form.value);

        success('Pesanan berhasil dibuat! Invoice telah dikirim ke email Anda.');

        // Redirect to order detail page
        setTimeout(() => {
            router.visit(`/orders/${response.data.order.id}`);
        }, 1500);

    } catch (err) {
        console.error('Failed to place order:', err);
        error(err.response?.data?.message || 'Gagal membuat pesanan');
    } finally {
        processing.value = false;
    }
};

onMounted(() => {
    loadCart();
});
</script>
