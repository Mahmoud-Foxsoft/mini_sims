<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { apiRequest } from '@/services/api';

const toast = useToast();
const router = useRouter();
const loading = ref(false);
const totals = ref([]);

const fetchTotals = async () => {
    loading.value = true;
    try {
        const data = await apiRequest('/home');
        totals.value = data.totals || [];
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Failed to load', detail: error.message, life: 4000 });
    } finally {
        loading.value = false;
    }
};

onMounted(fetchTotals);
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold">Welcome back</h1>
            <p class="text-gray-600">Track your activity and manage your services.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <Card v-for="(total, index) in totals" :key="total.key + index" class="shadow-sm">
                <template #content>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">{{ total.key }}</p>
                            <h3 class="text-xl font-semibold mt-2">{{ total.value }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-primary-100 text-primary-600">
                            <i :class="['pi', total.icon || 'pi-chart-bar', 'text-lg']" />
                        </div>
                    </div>
                    <router-link v-if="total.route" :to="'/' + total.route" class="text-primary text-sm mt-4 inline-flex items-center gap-2">
                        View details
                        <i class="pi pi-arrow-right text-xs" />
                    </router-link>
                </template>
            </Card>
            <Card v-if="loading" v-for="index in 3" :key="'loading-' + index" class="shadow-sm">
                <template #content>
                    <Skeleton width="70%" height="1rem" class="mb-3" />
                    <Skeleton width="40%" height="1.5rem" />
                </template>
            </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <Card class="lg:col-span-2 shadow-sm">
                <template #title>Next steps</template>
                <template #content>
                    <div class="grid gap-3">
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="font-medium">Create a payment</p>
                                <p class="text-sm text-gray-500">Top up your balance with a new payment.</p>
                            </div>
                            <Button label="Create" size="small" @click="router.push('/payments')" />
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="font-medium">Review recent orders</p>
                                <p class="text-sm text-gray-500">See the latest orders and status updates.</p>
                            </div>
                            <Button label="View" size="small" severity="secondary" @click="router.push('/orders')" />
                        </div>
                    </div>
                </template>
            </Card>
            <Card class="shadow-sm">
                <template #title>Support</template>
                <template #content>
                    <p class="text-sm text-gray-600">Need help? Reach out to the team for account support.</p>
                    <Button label="Contact support" class="mt-4" severity="secondary" />
                </template>
            </Card>
        </div>
    </div>
</template>
