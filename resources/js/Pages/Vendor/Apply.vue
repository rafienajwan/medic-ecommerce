<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

const vendorStatus = ref(null);
const loading = ref(true);

const form = useForm({
    business_name: '',
    business_type: 'Toko',
    description: '',
    business_address: '',
    business_phone: '',
    business_email: '',
    tax_id: '',
    business_license: '',
});

const checkVendorStatus = async () => {
    try {
        const response = await axios.get('/api/vendor/status');
        vendorStatus.value = response.data;

        console.log('Vendor Status:', response.data);

        // If vendor exists and rejected, pre-fill form
        if (response.data.has_vendor && response.data.vendor) {
            const vendor = response.data.vendor;
            form.business_name = vendor.business_name || '';
            form.business_type = vendor.business_type || 'Toko';
            form.description = vendor.description || '';
            form.business_address = vendor.business_address || '';
            form.business_phone = vendor.business_phone || '';
            form.business_email = vendor.business_email || '';
            form.tax_id = vendor.tax_id || '';
            form.business_license = vendor.business_license || '';
        }
    } catch (error) {
        console.error('Error checking vendor status:', error);
    } finally {
        loading.value = false;
    }
};

const submitApplication = async () => {
    form.processing = true;

    try {
        if (vendorStatus.value?.has_vendor && vendorStatus.value.status === 'rejected') {
            // Update existing application
            const response = await axios.put('/api/vendor/update', form.data());
            alert(response.data.message || 'Permohonan berhasil diperbarui');
        } else {
            // New application
            const response = await axios.post('/api/vendor/apply', form.data());
            alert(response.data.message || 'Permohonan berhasil diajukan');
        }

        // Refresh status and reset form
        await checkVendorStatus();
        form.reset();

        // Scroll to top to show status message
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        if (error.response?.data?.errors) {
            Object.keys(error.response.data.errors).forEach(key => {
                form.setError(key, error.response.data.errors[key][0]);
            });
        } else {
            alert(error.response?.data?.message || 'Terjadi kesalahan');
        }
    } finally {
        form.processing = false;
    }
};

const goBack = () => {
    router.visit('/dashboard');
};

onMounted(() => {
    checkVendorStatus();
});
</script>

<template>
    <Head title="Daftar Jadi Vendor" />

    <MainLayout>
        <div class="min-h-screen bg-gray-50 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600">Memuat...</p>
                </div>

                <!-- Already Approved -->
                <div v-else-if="vendorStatus?.has_vendor && vendorStatus.status === 'approved'" class="bg-green-50 border-2 border-green-200 rounded-lg p-8 text-center">
                    <svg class="mx-auto h-16 w-16 text-green-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Akun Vendor Aktif!</h2>
                    <p class="text-gray-700 mb-6">Selamat! Akun vendor Anda sudah disetujui oleh admin.</p>
                    <Link href="/vendor/dashboard" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Buka Dashboard Vendor
                    </Link>
                </div>

                <!-- Pending Approval -->
                <div v-else-if="vendorStatus?.has_vendor && vendorStatus.status === 'pending'" class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-8 text-center">
                    <svg class="mx-auto h-16 w-16 text-yellow-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Menunggu Persetujuan</h2>
                    <p class="text-gray-700 mb-4">Permohonan vendor Anda sedang diproses oleh admin.</p>
                    <div class="bg-white rounded-lg p-4 text-left mt-6">
                        <h3 class="font-semibold text-gray-900 mb-2">Detail Permohonan:</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Nama Usaha:</dt>
                                <dd class="font-semibold">{{ vendorStatus.vendor.business_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Jenis Usaha:</dt>
                                <dd class="font-semibold">{{ vendorStatus.vendor.business_type }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Status:</dt>
                                <dd class="text-yellow-600 font-semibold">Menunggu Approval</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Rejected - Show Reason & Allow Re-apply -->
                <div v-else-if="vendorStatus?.has_vendor && vendorStatus.status === 'rejected'" class="space-y-6">
                    <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6">
                        <div class="flex items-start">
                            <svg class="h-6 w-6 text-red-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-red-900 mb-2">Permohonan Ditolak</h3>
                                <p class="text-red-800 font-medium mb-2">Alasan Penolakan:</p>
                                <p class="text-red-700 bg-red-100 p-3 rounded border border-red-200">
                                    {{ vendorStatus.vendor.rejection_reason }}
                                </p>
                                <p class="text-sm text-red-600 mt-3">
                                    Silakan perbaiki data sesuai alasan di atas dan kirim ulang permohonan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form to re-apply -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Perbaiki & Kirim Ulang Permohonan</h2>

                        <!-- Re-apply Form (same as new application) -->
                        <form @submit.prevent="submitApplication" class="space-y-6">
                            <!-- Business Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Usaha <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.business_name"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="PT. Alat Medis Sejahtera"
                                />
                                <p v-if="form.errors.business_name" class="mt-1 text-sm text-red-600">{{ form.errors.business_name }}</p>
                            </div>

                            <!-- Business Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Usaha <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.business_type"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="Toko">Toko</option>
                                    <option value="Distributor">Distributor</option>
                                    <option value="Manufacturer">Manufacturer</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <p v-if="form.errors.business_type" class="mt-1 text-sm text-red-600">{{ form.errors.business_type }}</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi Usaha
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Jelaskan tentang usaha Anda..."
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <!-- Business Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Usaha <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="form.business_address"
                                    rows="2"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Jl. Contoh No. 123, Jakarta"
                                ></textarea>
                                <p v-if="form.errors.business_address" class="mt-1 text-sm text-red-600">{{ form.errors.business_address }}</p>
                            </div>

                            <!-- Contact Info -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Telepon Usaha <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.business_phone"
                                        type="tel"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="021-1234567"
                                    />
                                    <p v-if="form.errors.business_phone" class="mt-1 text-sm text-red-600">{{ form.errors.business_phone }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Usaha <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.business_email"
                                        type="email"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="info@example.com"
                                    />
                                    <p v-if="form.errors.business_email" class="mt-1 text-sm text-red-600">{{ form.errors.business_email }}</p>
                                </div>
                            </div>

                            <!-- Tax ID & License -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        NPWP
                                    </label>
                                    <input
                                        v-model="form.tax_id"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="01.234.567.8-901.000"
                                    />
                                    <p v-if="form.errors.tax_id" class="mt-1 text-sm text-red-600">{{ form.errors.tax_id }}</p>
                                    <p class="mt-1 text-xs text-gray-500">Format: 01.234.567.8-901.000</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        SIUP / NIB
                                    </label>
                                    <input
                                        v-model="form.business_license"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="SIUP/001/2020 atau NIB123456"
                                    />
                                    <p v-if="form.errors.business_license" class="mt-1 text-sm text-red-600">{{ form.errors.business_license }}</p>
                                    <p class="mt-1 text-xs text-gray-500">Nomor izin usaha</p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-between items-center pt-6 border-t">
                                <button
                                    type="button"
                                    @click="goBack"
                                    class="text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200"
                                >
                                    ← Kembali
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                                >
                                    <span v-if="form.processing">Mengirim...</span>
                                    <span v-else>Kirim Ulang Permohonan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- New Application Form -->
                <div v-else class="bg-white rounded-lg shadow-lg p-8">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Daftar Jadi Vendor</h1>
                        <p class="text-gray-600">Mulai berjualan alat kesehatan di platform kami. Isi formulir di bawah untuk mengajukan permohonan.</p>
                    </div>

                    <!-- Application Form -->
                    <form @submit.prevent="submitApplication" class="space-y-6">
                        <!-- Business Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Usaha <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.business_name"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="PT. Alat Medis Sejahtera"
                            />
                            <p v-if="form.errors.business_name" class="mt-1 text-sm text-red-600">{{ form.errors.business_name }}</p>
                        </div>

                        <!-- Business Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Usaha <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.business_type"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="Toko">Toko</option>
                                <option value="Distributor">Distributor</option>
                                <option value="Manufacturer">Manufacturer</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <p v-if="form.errors.business_type" class="mt-1 text-sm text-red-600">{{ form.errors.business_type }}</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Usaha
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Jelaskan tentang usaha Anda..."
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <!-- Business Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Usaha <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="form.business_address"
                                rows="2"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Jl. Contoh No. 123, Jakarta"
                            ></textarea>
                            <p v-if="form.errors.business_address" class="mt-1 text-sm text-red-600">{{ form.errors.business_address }}</p>
                        </div>

                        <!-- Contact Info -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Telepon Usaha <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.business_phone"
                                    type="tel"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="021-1234567"
                                />
                                <p v-if="form.errors.business_phone" class="mt-1 text-sm text-red-600">{{ form.errors.business_phone }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Usaha <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.business_email"
                                    type="email"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="info@example.com"
                                />
                                <p v-if="form.errors.business_email" class="mt-1 text-sm text-red-600">{{ form.errors.business_email }}</p>
                            </div>
                        </div>

                        <!-- Tax ID & License -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    NPWP
                                </label>
                                <input
                                    v-model="form.tax_id"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="01.234.567.8-901.000"
                                />
                                <p v-if="form.errors.tax_id" class="mt-1 text-sm text-red-600">{{ form.errors.tax_id }}</p>
                                <p class="mt-1 text-xs text-gray-500">Format: 01.234.567.8-901.000</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    SIUP / NIB
                                </label>
                                <input
                                    v-model="form.business_license"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="SIUP/001/2020 atau NIB123456"
                                />
                                <p v-if="form.errors.business_license" class="mt-1 text-sm text-red-600">{{ form.errors.business_license }}</p>
                                <p class="mt-1 text-xs text-gray-500">Nomor izin usaha</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-between items-center pt-6 border-t">
                            <button
                                type="button"
                                @click="goBack"
                                class="text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200"
                            >
                                ← Kembali
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                            >
                                <span v-if="form.processing">Mengirim...</span>
                                <span v-else">Kirim Permohonan</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </MainLayout>
</template>
