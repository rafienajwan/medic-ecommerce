<template>
    <MainLayout>
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Guest Book</h1>
            <p class="text-gray-600 mb-8">Share your experience with us!</p>

            <!-- Write Testimonial Form (Authenticated Users) -->
            <div v-if="$page.props.auth.user" class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Write a Testimonial</h2>
                <form @submit.prevent="submitTestimonial">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Your name"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="your.email@example.com"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea
                            v-model="form.message"
                            required
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Share your experience..."
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:bg-gray-400"
                    >
                        {{ submitting ? 'Submitting...' : 'Submit Testimonial' }}
                    </button>
                    <p class="text-sm text-gray-500 mt-2">
                        Your testimonial will be reviewed before appearing publicly.
                    </p>
                </form>
            </div>

            <!-- Login Prompt for Guests -->
            <div v-else class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <p class="text-blue-800">
                    <Link href="/login" class="font-semibold underline">Login</Link>
                    to share your testimonial with us!
                </p>
            </div>

            <!-- Approved Testimonials -->
            <div class="space-y-6">
                <h2 class="text-2xl font-semibold mb-4">What Our Customers Say</h2>

                <div v-if="guestbooks.data.length > 0" class="space-y-4">
                    <div
                        v-for="entry in guestbooks.data"
                        :key="entry.id"
                        class="bg-white rounded-lg shadow p-6"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">{{ entry.name }}</h3>
                                <p class="text-sm text-gray-500">{{ formatDate(entry.created_at) }}</p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                Verified
                            </span>
                        </div>
                        <p class="text-gray-700 leading-relaxed">{{ entry.message }}</p>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <p class="text-gray-500">No testimonials yet. Be the first to share your experience!</p>
                </div>

                <!-- Pagination -->
                <div v-if="guestbooks.links.length > 3" class="mt-8 flex justify-center space-x-2">
                    <Link
                        v-for="(link, index) in guestbooks.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 rounded-md',
                            link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                            !link.url ? 'cursor-not-allowed opacity-50' : ''
                        ]"
                    />
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

const props = defineProps({
    guestbooks: Object,
});

const form = ref({
    name: '',
    email: '',
    message: '',
});

const submitting = ref(false);

const submitTestimonial = async () => {
    submitting.value = true;

    try {
        await axios.post('/api/guestbook', form.value);

        // Success - reset form
        form.value = { name: '', email: '', message: '' };
    } catch (error) {
        console.error('Failed to submit testimonial:', error);
    } finally {
        submitting.value = false;
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>
