<template>
    <MainLayout>
        <!-- Login Required Modal -->
        <div
            v-if="showLoginModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="showLoginModal = false"
        >
            <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 shadow-xl">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Login Required</h3>
                    <p class="text-gray-600 mb-6">Anda harus login terlebih dahulu untuk menambahkan produk ke keranjang.</p>
                    <div class="flex gap-3">
                        <button
                            @click="showLoginModal = false"
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300"
                        >
                            Batal
                        </button>
                        <button
                            @click="redirectToLogin"
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                        >
                            Login Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-responsive py-4 sm:py-6 lg:py-8">
            <!-- Back Button -->
            <div class="mb-4">
                <button
                    @click="router.visit('/products')"
                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Products
                </button>
            </div>

            <!-- Product Details - Responsive -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 sm:mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 p-4 sm:p-6 lg:p-8">
                    <!-- Product Image - Fixed Container -->
                    <div>
                        <div class="w-full h-64 sm:h-80 lg:h-96 bg-white flex items-center justify-center p-4 sm:p-6 border rounded-lg">
                            <img
                                :src="product.image_url || 'https://via.placeholder.com/600'"
                                :alt="product.name"
                                class="max-w-full max-h-full object-contain"
                            />
                        </div>
                    </div>

                    <!-- Product Info - Responsive -->
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">{{ product.name }}</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">{{ product.description }}</p>

                        <div class="mb-4 sm:mb-6">
                            <div class="text-2xl sm:text-3xl font-bold text-blue-600 mb-2">
                                Rp {{ formatPrice(product.price) }}
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm">
                                <span :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ product.stock > 0 ? `In Stock: ${product.stock}` : 'Out of Stock' }}
                                </span>
                                <span class="text-gray-500 hidden sm:inline">|</span>
                                <span class="text-gray-600">Category: {{ product.category?.name }}</span>
                            </div>
                        </div>

                        <!-- Quantity - Responsive -->
                        <div class="mb-4 sm:mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input
                                v-model.number="quantity"
                                type="number"
                                min="1"
                                :max="product.stock"
                                class="w-20 sm:w-24 px-3 sm:px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                            />
                        </div>

                        <!-- Add to Cart Button - Responsive -->
                        <button
                            @click="addToCart"
                            :disabled="product.stock === 0"
                            class="w-full bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-base sm:text-lg font-semibold"
                        >
                            {{ product.stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
                        </button>

                        <!-- Vendor Information - Enhanced -->
                        <div class="mt-4 sm:mt-6 p-4 sm:p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                            <h3 class="font-semibold mb-3 text-sm sm:text-base text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Vendor Information
                            </h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="font-semibold text-base sm:text-lg text-gray-900">
                                        {{ product.vendor?.business_name || 'N/A' }}
                                    </p>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                        {{ product.vendor?.description || 'Supplier alat medis profesional' }}
                                    </p>
                                </div>
                                <div v-if="product.vendor?.user?.city" class="flex items-center gap-2 pt-2 border-t border-blue-200">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-sm sm:text-base font-medium text-gray-700">
                                        {{ product.vendor.user.city }}, {{ product.vendor.user.province || 'Indonesia' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Reviews Section - Responsive -->
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 lg:p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Customer Reviews</h2>
                    <div v-if="reviewStats" class="flex items-center gap-6">
                        <div class="flex items-center">
                            <div class="flex text-yellow-400">
                                <span v-for="i in 5" :key="i" class="text-2xl">
                                    {{ i <= Math.round(reviewStats.average_rating) ? '★' : '☆' }}
                                </span>
                            </div>
                            <span class="ml-2 text-xl font-semibold">{{ reviewStats.average_rating?.toFixed(1) || '0.0' }}</span>
                        </div>
                        <span class="text-gray-600">{{ reviewStats.total_reviews }} reviews</span>
                    </div>
                </div>

                <!-- Write Review (Authenticated Users) -->
                <div v-if="$page.props.auth.user" class="mb-8 p-6 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Write a Review</h3>
                    <form @submit.prevent="submitReview">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                            <div class="flex items-center gap-2">
                                <button
                                    v-for="i in 5"
                                    :key="i"
                                    type="button"
                                    @click="reviewForm.rating = i"
                                    class="text-3xl focus:outline-none"
                                    :class="i <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300'"
                                >
                                    ★
                                </button>
                                <span class="ml-2 text-sm text-gray-600">({{ reviewForm.rating }}/5)</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                            <textarea
                                v-model="reviewForm.review"
                                required
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Share your experience with this product..."
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            :disabled="submittingReview || reviewForm.rating === 0"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:bg-gray-400"
                        >
                            {{ submittingReview ? 'Submitting...' : 'Submit Review' }}
                        </button>
                    </form>
                </div>

                <!-- Reviews List -->
                <div class="space-y-6">
                    <div v-if="reviews.length > 0" class="space-y-4">
                        <div
                            v-for="review in reviews"
                            :key="review.id"
                            class="border-b border-gray-200 pb-4 last:border-b-0"
                        >
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-gray-900">{{ review.user?.name || 'Anonymous' }}</h4>
                                        <span v-if="review.verified_purchase" class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                            Verified Purchase
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex text-yellow-400 text-sm">
                                            <span v-for="i in 5" :key="i">
                                                {{ i <= review.rating ? '★' : '☆' }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ formatDate(review.created_at) }}</span>
                                    </div>
                                </div>
                                <div v-if="$page.props.auth.user?.id === review.user_id" class="flex gap-2">
                                    <button
                                        @click="editReview(review)"
                                        class="text-blue-600 hover:text-blue-800 text-sm"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deleteReview(review.id)"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                            <p class="text-gray-700">{{ review.review }}</p>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No reviews yet. Be the first to review this product!
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';

const { success, error } = useNotification();
const page = usePage();

const props = defineProps({
    product: Object,
});

const quantity = ref(1);
const reviews = ref([]);
const reviewStats = ref(null);
const submittingReview = ref(false);
const showLoginModal = ref(false);
const reviewForm = ref({
    rating: 0,
    review: '',
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const addToCart = async () => {
    // Check if user is authenticated
    if (!page.props.auth.user) {
        showLoginModal.value = true;
        return;
    }

    try {
        const response = await axios.post('/api/cart', {
            product_id: props.product.id,
            quantity: quantity.value,
        });

        // Show success notification
        success(`${props.product.name} berhasil ditambahkan ke keranjang!`);
        quantity.value = 1; // Reset quantity
    } catch (err) {
        // If unauthorized, show login modal
        if (err.response?.status === 401) {
            showLoginModal.value = true;
        } else {
            // Show error notification
            error(err.response?.data?.message || 'Gagal menambahkan produk ke keranjang');
        }
    }
};

const redirectToLogin = () => {
    router.visit('/login');
};

const loadReviews = async () => {
    try {
        const response = await axios.get(`/api/products/${props.product.id}/reviews`);
        reviews.value = response.data.data.reviews;
        reviewStats.value = response.data.data.stats;
    } catch (error) {
        console.error('Failed to load reviews:', error);
    }
};

const submitReview = async () => {
    if (reviewForm.value.rating === 0) {
        // No alert - just don't submit
        return;
    }

    submittingReview.value = true;

    try {
        await axios.post('/api/reviews', {
            product_id: props.product.id,
            rating: reviewForm.value.rating,
            review: reviewForm.value.review,
        });

        reviewForm.value = { rating: 0, review: '' };
        // Success - silently reload reviews
        await loadReviews();
    } catch (error) {
        // Silently fail - no alert
        console.error('Failed to submit review:', error);
    } finally {
        submittingReview.value = false;
    }
};

const editReview = async (review) => {
    const newReview = prompt('Edit your review:', review.review);
    if (newReview === null) return;

    const newRating = prompt('Edit your rating (1-5):', review.rating);
    if (newRating === null) return;

    try {
        await axios.put(`/api/reviews/${review.id}`, {
            rating: parseInt(newRating),
            review: newReview,
        });
        // Success - silently reload
        await loadReviews();
    } catch (error) {
        console.error('Failed to update review:', error);
    }
};

const deleteReview = async (reviewId) => {
    if (!confirm('Are you sure you want to delete this review?')) return;

    try {
        await axios.delete(`/api/reviews/${reviewId}`);
        // Success - silently reload
        await loadReviews();
    } catch (error) {
        console.error('Failed to delete review:', error);
    }
};

onMounted(() => {
    loadReviews();
});
</script>
