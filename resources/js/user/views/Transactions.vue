<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { apiRequest } from '@/services/api';

const toast = useToast();
const loading = ref(false);
const transactions = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);
const creditSum = ref(0);
const debitSum = ref(0);

const fetchTransactions = async (page = 1) => {
    loading.value = true;
    try {
        const data = await apiRequest(`/v1/transactions?page=${page}`);
        creditSum.value = data.credit_sum_cents || 0;
        debitSum.value = data.debit_sum_cents || 0;
        const paginator = data.transactions || {};
        transactions.value = paginator.data || [];
        totalRecords.value = paginator.total || 0;
        rows.value = paginator.per_page || 20;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Failed to load transactions', detail: error.message, life: 4000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    first.value = event.first;
    rows.value = event.rows;
    fetchTransactions(event.page + 1);
};

const typeSeverity = (type) => (type === 'credit' ? 'success' : 'danger');

onMounted(() => fetchTransactions());
</script>

<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold">Transactions</h2>
            <p class="text-gray-600">Wallet credits and debits.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Card class="shadow-sm">
                <template #content>
                    <p class="text-gray-500 text-sm">Total credits</p>
                    <h3 class="text-xl font-semibold mt-2">{{ creditSum }} cents</h3>
                </template>
            </Card>
            <Card class="shadow-sm">
                <template #content>
                    <p class="text-gray-500 text-sm">Total debits</p>
                    <h3 class="text-xl font-semibold mt-2">{{ debitSum }} cents</h3>
                </template>
            </Card>
        </div>

        <Card class="shadow-sm">
            <template #content>
                <DataTable
                    :value="transactions"
                    :loading="loading"
                    :paginator="true"
                    :rows="rows"
                    :totalRecords="totalRecords"
                    :first="first"
                    @page="onPage"
                >
                    <Column field="id" header="ID" style="min-width: 6rem" />
                    <Column field="type" header="Type" style="min-width: 8rem">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.type" :severity="typeSeverity(slotProps.data.type)" />
                        </template>
                    </Column>
                    <Column field="amount_cents" header="Amount (cents)" style="min-width: 10rem" />
                    <Column field="description" header="Description" style="min-width: 14rem" />
                    <Column field="reference_id" header="Reference" style="min-width: 12rem" />
                    <Column field="created_at" header="Created" style="min-width: 12rem" />
                </DataTable>
            </template>
        </Card>
    </div>
</template>
