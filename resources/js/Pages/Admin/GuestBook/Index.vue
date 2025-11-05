<template>
    <MainLayout>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Guest Book Management</h1>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Total Entries</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ statistics.total }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Approved</h3>
                    <p class="text-3xl font-bold text-green-600">{{ statistics.approved }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Pending Approval</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ statistics.pending }}</p>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button
                            @click="filterStatus = 'all'"
                            :class="[
                                'px-6 py-3 border-b-2 font-medium text-sm',
                                filterStatus === 'all'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            All ({{ statistics.total }})
                        </button>
                        <button
                            @click="filterStatus = 'pending'"
                            :class="[
                                'px-6 py-3 border-b-2 font-medium text-sm',
                                filterStatus === 'pending'
                                    ? 'border-yellow-500 text-yellow-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            Pending ({{ statistics.pending }})
                        </button>
                        <button
                            @click="filterStatus = 'approved'"
                            :class="[
                                'px-6 py-3 border-b-2 font-medium text-sm',
                                filterStatus === 'approved'
                                    ? 'border-green-500 text-green-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            Approved ({{ statistics.approved }})
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Guest Books List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div v-if="filteredEntries.length > 0" class="divide-y divide-gray-200">
                    <div
                        v-for="entry in filteredEntries"
                        :key="entry.id"
                        class="p-6 hover:bg-gray-50"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ entry.name }}</h3>
                                    <span
                                        :class="entry.is_approved
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-yellow-100 text-yellow-800'"
                                        class="px-3 py-1 rounded-full text-xs font-semibold"
                                    >
                                        {{ entry.is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ entry.email }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Submitted: {{ formatDate(entry.created_at) }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    v-if="!entry.is_approved"
                                    @click="approveEntry(entry.id)"
                                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm"
                                >
                                    Approve
                                </button>
                                <button
                                    v-else
                                    @click="unapproveEntry(entry.id)"
                                    class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 text-sm"
                                >
                                    Unapprove
                                </button>
                                <button
                                    @click="deleteEntry(entry.id)"
                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 leading-relaxed">{{ entry.message }}</p>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <p class="text-gray-500">No entries found</p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

const entries = ref([]);
const filterStatus = ref('all');

const statistics = computed(() => {
    const total = entries.value.length;
    const approved = entries.value.filter(e => e.is_approved).length;
    const pending = total - approved;

    return { total, approved, pending };
});

const filteredEntries = computed(() => {
    if (filterStatus.value === 'all') return entries.value;
    if (filterStatus.value === 'approved') return entries.value.filter(e => e.is_approved);
    if (filterStatus.value === 'pending') return entries.value.filter(e => !e.is_approved);
    return entries.value;
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const loadEntries = async () => {
    try {
        const response = await axios.get('/api/admin/guestbook');
        entries.value = response.data.data;
    } catch (error) {
        console.error('Failed to load guest book entries:', error);
    }
};

const approveEntry = async (id) => {
    try {
        await axios.post(`/api/admin/guestbook/${id}/approve`);
        await loadEntries();
    } catch (error) {
        console.error(error.response?.data?.message || 'Failed to approve entry');
    }
};

const unapproveEntry = async (id) => {
    if (!confirm('Are you sure you want to unapprove this entry?')) return;

    try {
        await axios.post(`/api/admin/guestbook/${id}/unapprove`);
        await loadEntries();
    } catch (error) {
        console.error(error.response?.data?.message || 'Failed to unapprove entry');
    }
};

const deleteEntry = async (id) => {
    if (!confirm('Are you sure you want to delete this entry? This action cannot be undone.')) {
        return;
    }

    try {
        await axios.delete(`/api/admin/guestbook/${id}`);
        await loadEntries();
    } catch (error) {
        console.error(error.response?.data?.message || 'Failed to delete entry');
    }
};

onMounted(() => {
    loadEntries();
});
</script>
