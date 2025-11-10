<template>
    <VendorLayout :vendor="vendor">
        <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Vendor Profile</h1>
                <p class="text-gray-600 mt-1">Update your business information</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 sm:p-8">
                <form @submit.prevent="updateProfile">
                    <!-- Business Name -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Business Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.business_name"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="PT. Alat Medis Sejahtera"
                        />
                        <p v-if="errors.business_name" class="text-red-500 text-sm mt-1">{{ errors.business_name }}</p>
                    </div>

                    <!-- Business Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Business Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.business_type"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="Toko">Toko</option>
                            <option value="Distributor">Distributor</option>
                            <option value="Manufacturer">Manufacturer</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <p v-if="errors.business_type" class="text-red-500 text-sm mt-1">{{ errors.business_type }}</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Business Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Describe your business..."
                        ></textarea>
                        <p v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</p>
                    </div>

                    <!-- Business Address -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Business Address <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="form.business_address"
                            rows="2"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Jl. Contoh No. 123, Jakarta"
                        ></textarea>
                        <p v-if="errors.business_address" class="text-red-500 text-sm mt-1">{{ errors.business_address }}</p>
                    </div>

                    <!-- City & Province -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Business City
                            </label>
                            <input
                                v-model="form.business_city"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Jakarta"
                            />
                            <p v-if="errors.business_city" class="text-red-500 text-sm mt-1">{{ errors.business_city }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Business Province
                            </label>
                            <input
                                v-model="form.business_province"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="DKI Jakarta"
                            />
                            <p v-if="errors.business_province" class="text-red-500 text-sm mt-1">{{ errors.business_province }}</p>
                        </div>
                    </div>

                    <!-- Postal Code -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Postal Code
                        </label>
                        <input
                            v-model="form.business_postal_code"
                            type="text"
                            maxlength="5"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="12345"
                        />
                        <p v-if="errors.business_postal_code" class="text-red-500 text-sm mt-1">{{ errors.business_postal_code }}</p>
                    </div>

                    <!-- Contact Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Business Phone <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.business_phone"
                                type="tel"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="021-1234567"
                            />
                            <p v-if="errors.business_phone" class="text-red-500 text-sm mt-1">{{ errors.business_phone }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Business Email <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.business_email"
                                type="email"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="info@example.com"
                            />
                            <p v-if="errors.business_email" class="text-red-500 text-sm mt-1">{{ errors.business_email }}</p>
                        </div>
                    </div>

                    <!-- Tax ID & License -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                NPWP
                            </label>
                            <input
                                v-model="form.tax_id"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="01.234.567.8-901.000"
                            />
                            <p v-if="errors.tax_id" class="text-red-500 text-sm mt-1">{{ errors.tax_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                SIUP / NIB
                            </label>
                            <input
                                v-model="form.business_license"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="SIUP/001/2020 atau NIB123456"
                            />
                            <p v-if="errors.business_license" class="text-red-500 text-sm mt-1">{{ errors.business_license }}</p>
                        </div>
                    </div>

                    <!-- Status Info (Read-only) -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Account Status</h3>
                        <div class="flex items-center space-x-2">
                            <span
                                :class="{
                                    'bg-green-100 text-green-800': vendor.status === 'approved',
                                    'bg-yellow-100 text-yellow-800': vendor.status === 'pending',
                                    'bg-red-100 text-red-800': vendor.status === 'rejected'
                                }"
                                class="px-3 py-1 rounded-full text-sm font-semibold"
                            >
                                {{ vendor.status.toUpperCase() }}
                            </span>
                        </div>
                        <p v-if="vendor.rejection_reason" class="text-red-600 text-sm mt-2">
                            <strong>Rejection Reason:</strong> {{ vendor.rejection_reason }}
                        </p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <button
                            type="button"
                            @click="$inertia.visit('/vendor/dashboard')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:bg-gray-400 disabled:cursor-not-allowed"
                        >
                            {{ submitting ? 'Updating...' : 'Update Profile' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import axios from 'axios';

const page = usePage();
const vendor = computed(() => page.props.auth?.user?.vendor);

const form = ref({
    business_name: '',
    business_type: 'Toko',
    description: '',
    business_address: '',
    business_city: '',
    business_province: '',
    business_postal_code: '',
    business_phone: '',
    business_email: '',
    tax_id: '',
    business_license: '',
});

const errors = ref({});
const submitting = ref(false);

const loadVendorData = () => {
    if (vendor.value) {
        form.value = {
            business_name: vendor.value.business_name || '',
            business_type: vendor.value.business_type || 'Toko',
            description: vendor.value.description || '',
            business_address: vendor.value.business_address || '',
            business_city: vendor.value.business_city || '',
            business_province: vendor.value.business_province || '',
            business_postal_code: vendor.value.business_postal_code || '',
            business_phone: vendor.value.business_phone || '',
            business_email: vendor.value.business_email || '',
            tax_id: vendor.value.tax_id || '',
            business_license: vendor.value.business_license || '',
        };
    }
};

const updateProfile = async () => {
    submitting.value = true;
    errors.value = {};

    try {
        await axios.put('/api/vendor/profile', form.value);

        alert('Vendor profile updated successfully!');

        // Reload page to refresh vendor data
        router.reload();
    } catch (err) {
        if (err.response?.data?.errors) {
            errors.value = err.response.data.errors;
        } else {
            alert(err.response?.data?.message || 'Failed to update profile');
        }
    } finally {
        submitting.value = false;
    }
};

onMounted(() => {
    loadVendorData();
});
</script>
