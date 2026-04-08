<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { apiRequest } from '@/services/api';

const toast = useToast();
const loading = ref(false);
const orders = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(10);

const fetchOrders = async (page = 1) => {
    loading.value = true;
    try {
        const data = await apiRequest(`/v1/orders?page=${page}`);
        orders.value = data.data || [];
        totalRecords.value = data.total || 0;
        rows.value = data.per_page || 10;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Failed to load orders', detail: error.message, life: 4000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    first.value = event.first;
    rows.value = event.rows;
    const page = event.page + 1;
    fetchOrders(page);
};

const statusSeverity = (status) => {
    switch (status) {
        case 'completed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'cancelled':
            return 'danger';
        case 'partially_completed':
            return 'info';
        default:
            return 'secondary';
    }
};

onMounted(() => fetchOrders());
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Orders</h2>
                <p class="text-gray-600">Track your recent orders.</p>
            </div>
        </div>

        <Card class="shadow-sm">
            <template #content>
                <DataTable
                    :value="orders"
                    :loading="loading"
                    :paginator="true"
                    :rows="rows"
                    :totalRecords="totalRecords"
                    :first="first"
                    @page="onPage"
                >
                    <Column field="id" header="Order ID" style="min-width: 12rem" />
                    <Column field="total_cent_price" header="Total (cents)" style="min-width: 10rem" />
                    <Column field="status" header="Status" style="min-width: 10rem">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status" :severity="statusSeverity(slotProps.data.status)" />
                        </template>
                    </Column>
                    <Column field="created_at" header="Created" style="min-width: 12rem" />
                </DataTable>
            </template>
        </Card>
    </div>
</template>
