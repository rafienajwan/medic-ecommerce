<template>
    <MainLayout>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">User Management</h1>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input
                        v-model="filters.search"
                        @input="performSearch"
                        type="text"
                        placeholder="Search users..."
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    />
                    <select
                        v-model="filters.role"
                        @change="performSearch"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Roles</option>
                        <option value="customer">Customer</option>
                        <option value="vendor">Vendor</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button
                        @click="loadStatistics"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                    >
                        Refresh Statistics
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Total Users</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ statistics.total_users }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Customers</h3>
                    <p class="text-3xl font-bold text-green-600">{{ statistics.customers }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Vendors</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ statistics.vendors }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm mb-2">Admins</h3>
                    <p class="text-3xl font-bold text-red-600">{{ statistics.admins }}</p>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Joined
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ user.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="getRoleBadgeClass(user.role)"
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                >
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2">
                                    <button
                                        @click="viewUser(user)"
                                        class="text-blue-600 hover:text-blue-800"
                                    >
                                        View
                                    </button>
                                    <button
                                        @click="editUser(user)"
                                        class="text-green-600 hover:text-green-800"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        v-if="canDeleteUser(user)"
                                        @click="deleteUser(user)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="users.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No users found</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.total > pagination.per_page" class="mt-6 flex justify-center space-x-2">
                <button
                    v-for="page in totalPages"
                    :key="page"
                    @click="loadUsers(page)"
                    :class="[
                        'px-4 py-2 rounded-md',
                        page === pagination.current_page
                            ? 'bg-blue-600 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-100'
                    ]"
                >
                    {{ page }}
                </button>
            </div>

            <!-- User Detail Modal -->
            <div v-if="selectedUser" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4">
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-2xl font-bold">User Details</h2>
                        <button @click="selectedUser = null" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="mt-1 text-gray-900">{{ selectedUser.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-gray-900">{{ selectedUser.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <p class="mt-1">
                                <span :class="getRoleBadgeClass(selectedUser.role)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ selectedUser.role }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Joined</label>
                            <p class="mt-1 text-gray-900">{{ formatDate(selectedUser.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

const users = ref([]);
const statistics = ref(null);
const selectedUser = ref(null);
const filters = ref({
    search: '',
    role: '',
});
const pagination = ref({
    current_page: 1,
    per_page: 10,
    total: 0,
});

const totalPages = computed(() => {
    return Math.ceil(pagination.value.total / pagination.value.per_page);
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getRoleBadgeClass = (role) => {
    const classes = {
        admin: 'bg-red-100 text-red-800',
        vendor: 'bg-purple-100 text-purple-800',
        customer: 'bg-green-100 text-green-800',
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

const canDeleteUser = (user) => {
    // Cannot delete admins or yourself
    return user.role !== 'admin';
};

const loadUsers = async (page = 1) => {
    try {
        const params = {
            page,
            search: filters.value.search,
            role: filters.value.role,
        };

        const response = await axios.get('/api/admin/users', { params });
        users.value = response.data.data;
        pagination.value = response.data.pagination;
    } catch (error) {
        console.error('Failed to load users:', error);
    }
};

const loadStatistics = async () => {
    try {
        const response = await axios.get('/api/admin/users/statistics');
        statistics.value = response.data.data;
    } catch (error) {
        console.error('Failed to load statistics:', error);
    }
};

const performSearch = () => {
    loadUsers(1);
};

const viewUser = async (user) => {
    try {
        const response = await axios.get(`/api/admin/users/${user.id}`);
        selectedUser.value = response.data.data;
    } catch (error) {
        console.error('Failed to load user details');
    }
};

const editUser = async (user) => {
    const newRole = prompt(`Change role for ${user.name} (customer/vendor/admin):`, user.role);
    if (!newRole || newRole === user.role) return;

    if (!['customer', 'vendor', 'admin'].includes(newRole)) {
        console.error('Invalid role. Must be: customer, vendor, or admin');
        return;
    }

    try {
        await axios.put(`/api/admin/users/${user.id}`, {
            role: newRole,
        });
        await loadUsers(pagination.value.current_page);
        await loadStatistics();
    } catch (error) {
        console.error(error.response?.data?.message || 'Failed to update user');
    }
};

const deleteUser = async (user) => {
    if (!confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
        return;
    }

    try {
        await axios.delete(`/api/admin/users/${user.id}`);
        await loadUsers(pagination.value.current_page);
        await loadStatistics();
    } catch (error) {
        console.error(error.response?.data?.message || 'Failed to delete user');
    }
};

onMounted(() => {
    loadUsers();
    loadStatistics();
});
</script>
