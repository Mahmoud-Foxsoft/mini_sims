<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { apiRequest } from '@/services/api';

const toast = useToast();
const loading = ref(false);
const phoneNumbers = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);

const fetchNumbers = async (page = 1) => {
    loading.value = true;
    try {
        const data = await apiRequest(`/v1/phone-numbers?page=${page}`);
        phoneNumbers.value = data.data || [];
        totalRecords.value = data.total || 0;
        rows.value = data.per_page || 20;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Failed to load numbers', detail: error.message, life: 4000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    first.value = event.first;
    rows.value = event.rows;
    fetchNumbers(event.page + 1);
};

const statusSeverity = (status) => {
    switch (status) {
        case 'active':
            return 'success';
        case 'completed':
            return 'info';
        case 'timeout_refunded':
            return 'warning';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
};

onMounted(() => fetchNumbers());
</script>

<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold">Phone Numbers</h2>
            <p class="text-gray-600">All purchased numbers and services.</p>
        </div>

        <Card class="shadow-sm">
            <template #content>
                <DataTable
                    :value="phoneNumbers"
                    :loading="loading"
                    :paginator="true"
                    :rows="rows"
                    :totalRecords="totalRecords"
                    :first="first"
                    @page="onPage"
                >
                    <Column field="service_name" header="Service" style="min-width: 12rem" />
                    <Column field="phone_number" header="Phone number" style="min-width: 12rem" />
                    <Column field="price_cents" header="Price (cents)" style="min-width: 8rem" />
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
