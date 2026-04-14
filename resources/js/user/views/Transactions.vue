<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/services/api";
import { formatDate } from "@/services/date";

const toast = useToast();
const loading = ref(false);
const transactions = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);
const creditSum = ref(0);
const debitSum = ref(0);

const typeFilter = ref(null);
const referenceFilter = ref("");
const typeOptions = [
    { label: "All", value: null },
    { label: "Credit", value: "credit" },
    { label: "Debit", value: "debit" },
];

const buildQuery = (page) => {
    const params = new URLSearchParams();
    params.set("page", String(page));
    params.set("per_page", String(rows.value));
    if (typeFilter.value) {
        params.set("filters[type]", typeFilter.value);
    }
    if (referenceFilter.value?.trim()) {
        params.set("filters[reference]", referenceFilter.value.trim());
    }
    return params.toString();
};

const fetchTransactions = async (page = 1) => {
    loading.value = true;
    try {
        const query = buildQuery(page);
        const data = await apiRequest(`/v1/transactions?${query}`);
        creditSum.value = data.credit_sum_cents || 0;
        debitSum.value = data.debit_sum_cents || 0;
        const paginator = data.transactions || {};
        transactions.value = paginator.data || [];
        totalRecords.value = paginator.total || 0;
        rows.value = paginator.per_page || 20;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load transactions",
            detail: error.message,
            life: 4000,
        });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    first.value = event.first;
    rows.value = event.rows;
    fetchTransactions(event.page + 1);
};

const applyFilters = () => {
    first.value = 0;
    fetchTransactions(1);
};

const clearFilters = () => {
    typeFilter.value = null;
    referenceFilter.value = "";
    applyFilters();
};

const typeSeverity = (type) => (type === "credit" ? "success" : "danger");

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
                    <h3 class="text-xl font-semibold mt-2">
                        {{ creditSum }} cents
                    </h3>
                </template>
            </Card>
            <Card class="shadow-sm">
                <template #content>
                    <p class="text-gray-500 text-sm">Total debits</p>
                    <h3 class="text-xl font-semibold mt-2">
                        {{ debitSum }} cents
                    </h3>
                </template>
            </Card>
        </div>

        <Card class="shadow-sm">
            <template #content>
                <div class="flex flex-col gap-3 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Type</label>
                            <Dropdown
                                v-model="typeFilter"
                                :options="typeOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="All types"
                                class="w-full"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Reference</label>
                            <InputText
                                v-model="referenceFilter"
                                placeholder="Reference ID"
                                class="w-full"
                            />
                        </div>
                        <div class="flex items-end gap-2">
                            <Button
                                label="Apply"
                                icon="pi pi-filter"
                                @click="applyFilters"
                            />
                            <Button
                                label="Clear"
                                icon="pi pi-times"
                                severity="secondary"
                                @click="clearFilters"
                            />
                        </div>
                    </div>
                </div>
                <DataTable
                    lazy
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
                            <Tag
                                :value="slotProps.data.type"
                                :severity="typeSeverity(slotProps.data.type)"
                            />
                        </template>
                    </Column>
                    <Column
                        field="amount_cents"
                        header="Amount"
                        style="min-width: 10rem"
                    >
                        <template #body="slotProps">
                            $ {{ (slotProps.data.amount_cents / 100).toFixed(2) }}
                        </template>
                </Column>
                    <Column
                        field="description"
                        header="Description"
                        style="min-width: 14rem"
                    />
                    <Column
                        field="reference_id"
                        header="Reference"
                        style="min-width: 12rem"
                    />
                    <Column header="Created" style="min-width: 12rem">
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.created_at) }}
                        </template>
                    </Column>
                    <template #empty>
                        <div
                            v-if="!loading"
                            class="flex flex-col items-center justify-center p-8 text-gray-500"
                        >
                            <i
                                class="pi pi-inbox text-4xl mb-4 text-gray-400"
                            ></i>
                            <p class="text-lg font-medium">
                                No Transactions found.
                            </p>
                            <p class="text-sm">
                                Try adjusting your filters or check back later.
                            </p>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center justify-center p-8 text-gray-500"
                        >
                            <i
                                class="pi pi-spinner pi-spin text-4xl mb-4 text-blue-500 dark:text-blue-400"
                            ></i>
                            <p class="text-lg font-medium">
                                Loading transactions...
                            </p>
                            <p class="text-sm">
                                Please wait while we fetch your data.
                            </p>
                        </div>
                    </template>
                </DataTable>
            </template>
        </Card>
    </div>
</template>
