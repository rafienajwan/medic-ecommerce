<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

const vendors = ref([]);
const loading = ref(true);
const selectedVendor = ref(null);
const showRejectModal = ref(false);
const rejectReason = ref('');
const rejectError = ref('');
const activeTab = ref('pending'); // pending, approved, rejected, all

const fetchVendors = async () => {
    loading.value = true;
    try {
        let url = '/api/admin/vendors';
        if (activeTab.value !== 'all') {
            url += `?status=${activeTab.value}`;
        }
        const response = await axios.get(url);
        vendors.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error fetching vendors:', error);
    } finally {
        loading.value = false;
    }
};

const approveVendor = async (vendorId) => {
    if (!confirm('Yakin ingin menyetujui vendor ini?')) return;

    try {
        const response = await axios.post(`/api/admin/vendors/${vendorId}/approve`);
        alert(response.data.message || 'Vendor berhasil disetujui!');
        await fetchVendors();
    } catch (error) {
        console.error('Error approving vendor:', error);
        if (error.response?.status === 419) {
            alert('Session expired. Halaman akan di-refresh.');
            window.location.reload();
        } else {
            alert(error.response?.data?.message || 'Gagal menyetujui vendor. Silakan coba lagi.');
        }
    }
};

const openRejectModal = (vendor) => {
    selectedVendor.value = vendor;
    rejectReason.value = '';
    rejectError.value = '';
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    selectedVendor.value = null;
    rejectReason.value = '';
    rejectError.value = '';
};

const submitReject = async () => {
    if (!rejectReason.value || rejectReason.value.length < 10) {
        rejectError.value = 'Alasan penolakan minimal 10 karakter';
        return;
    }

    try {
        const response = await axios.post(`/api/admin/vendors/${selectedVendor.value.id}/reject`, {
            reason: rejectReason.value
        });
        alert(response.data.message || 'Vendor berhasil ditolak!');
        closeRejectModal();
        await fetchVendors();
    } catch (error) {
        console.error('Error rejecting vendor:', error);
        if (error.response?.status === 419) {
            rejectError.value = 'Session expired. Halaman akan di-refresh.';
            setTimeout(() => window.location.reload(), 2000);
        } else {
            rejectError.value = error.response?.data?.message || 'Gagal menolak vendor. Silakan coba lagi.';
        }
    }
};

const changeTab = (tab) => {
    activeTab.value = tab;
    fetchVendors();
};

onMounted(() => {
    fetchVendors();
});
</script>

<template>
    <Head title="Manajemen Vendor - Admin" />

    <MainLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Vendor</h1>
                    <p class="mt-2 text-gray-600">Review dan kelola permohonan vendor</p>
                </div>

                <!-- Tabs -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button
                                @click="changeTab('pending')"
                                :class="activeTab === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-6 py-4 border-b-2 font-medium text-sm"
                            >
                                Menunggu Approval
                                <span v-if="activeTab === 'pending' && !loading" class="ml-2 bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">
                                    {{ vendors.length }}
                                </span>
                            </button>
                            <button
                                @click="changeTab('approved')"
                                :class="activeTab === 'approved' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-6 py-4 border-b-2 font-medium text-sm"
                            >
                                Disetujui
                            </button>
                            <button
                                @click="changeTab('rejected')"
                                :class="activeTab === 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-6 py-4 border-b-2 font-medium text-sm"
                            >
                                Ditolak
                            </button>
                            <button
                                @click="changeTab('all')"
                                :class="activeTab === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-6 py-4 border-b-2 font-medium text-sm"
                            >
                                Semua
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600">Memuat data...</p>
                </div>

                <!-- Empty State -->
                <div v-else-if="vendors.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-gray-600 text-lg">Tidak ada vendor {{ activeTab === 'all' ? '' : activeTab }}</p>
                </div>

                <!-- Vendor List -->
                <div v-else class="space-y-4">
                    <div
                        v-for="vendor in vendors"
                        :key="vendor.id"
                        class="bg-white rounded-lg shadow hover:shadow-lg transition p-6"
                    >
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="text-xl font-bold text-gray-900">{{ vendor.business_name }}</h3>
                                    <span
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': vendor.status === 'pending',
                                            'bg-green-100 text-green-800': vendor.status === 'approved',
                                            'bg-red-100 text-red-800': vendor.status === 'rejected'
                                        }"
                                        class="px-3 py-1 rounded-full text-xs font-semibold"
                                    >
                                        {{ vendor.status === 'pending' ? 'Pending' : vendor.status === 'approved' ? 'Approved' : 'Rejected' }}
                                    </span>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600">Pemilik: <span class="font-semibold text-gray-900">{{ vendor.user?.name }}</span></p>
                                        <p class="text-gray-600">Email: <span class="font-semibold text-gray-900">{{ vendor.user?.email }}</span></p>
                                        <p class="text-gray-600">Jenis: <span class="font-semibold text-gray-900">{{ vendor.business_type }}</span></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Telepon: <span class="font-semibold text-gray-900">{{ vendor.business_phone }}</span></p>
                                        <p class="text-gray-600">Email Bisnis: <span class="font-semibold text-gray-900">{{ vendor.business_email }}</span></p>
                                        <p class="text-gray-600">NPWP: <span class="font-semibold text-gray-900">{{ vendor.tax_id || '-' }}</span></p>
                                    </div>
                                </div>

                                <p class="mt-3 text-gray-700">{{ vendor.description || 'Tidak ada deskripsi' }}</p>
                                <p class="mt-2 text-sm text-gray-600">
                                    <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ vendor.business_address }}
                                </p>

                                <!-- Rejection Reason -->
                                <div v-if="vendor.status === 'rejected' && vendor.rejection_reason" class="mt-4 bg-red-50 border border-red-200 rounded p-3">
                                    <p class="text-sm font-semibold text-red-900 mb-1">Alasan Penolakan:</p>
                                    <p class="text-sm text-red-800">{{ vendor.rejection_reason }}</p>
                                </div>

                                <!-- Approval Info -->
                                <div v-if="vendor.status === 'approved'" class="mt-4 text-sm text-gray-500">
                                    Disetujui oleh {{ vendor.approver?.name || 'Admin' }} pada {{ new Date(vendor.approved_at).toLocaleDateString('id-ID') }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div v-if="vendor.status === 'pending'" class="ml-6 flex flex-col gap-2">
                                <button
                                    @click="approveVendor(vendor.id)"
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition whitespace-nowrap"
                                >
                                    ✓ Setujui
                                </button>
                                <button
                                    @click="openRejectModal(vendor)"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition whitespace-nowrap"
                                >
                                    ✗ Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Tolak Permohonan Vendor</h3>
                <p class="text-gray-600 mb-4">
                    Anda akan menolak permohonan dari <strong>{{ selectedVendor?.business_name }}</strong>
                </p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="rejectReason"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Jelaskan alasan penolakan secara detail (minimal 10 karakter)..."
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ rejectReason.length }}/10 karakter minimal</p>
                    <p v-if="rejectError" class="mt-1 text-sm text-red-600">{{ rejectError }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="closeRejectModal"
                        class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitReject"
                        :disabled="rejectReason.length < 10"
                        class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                    >
                        Tolak Permohonan
                    </button>
                </div>
            </div>
        </div>

    </MainLayout>
</template>
