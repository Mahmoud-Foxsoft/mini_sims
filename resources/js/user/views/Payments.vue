<script setup>
import { onMounted, ref, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/services/api";
import { formatDate } from "@/services/date";
import { usePaymentDialogStore } from "@/stores/paymentDialogStore";

const toast = useToast();
const loading = ref(false);
const payments = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);
const paymentDialogStore = usePaymentDialogStore();

const statusFilter = ref(null);
const createdDateFilter = ref(null);
const statusOptions = [
    { label: "All", value: null },
    { label: "Finished", value: "finished" },
    { label: "Waiting", value: "waiting" },
    { label: "Confirming", value: "confirming" },
    { label: "Sending", value: "sending" },
    { label: "Refunded", value: "refunded" },
];

const openCreate = () => {
    paymentDialogStore.open();
};

const buildQuery = (page) => {
    const params = new URLSearchParams();
    params.set("page", String(page));
    params.set("per_page", String(rows.value));
    if (statusFilter.value) {
        params.set("filters[status]", statusFilter.value);
    }
    const createdDate = formatDate(createdDateFilter.value);
    if (createdDate) {
        params.set("filters[created_date]", createdDate);
    }
    return params.toString();
};

const fetchPayments = async (page = 1) => {
    loading.value = true;
    try {
        const query = buildQuery(page);
        const data = await apiRequest(`/v1/payments?${query}`);
        payments.value = data.data || [];
        totalRecords.value = data.total || 0;
        rows.value = data.per_page || 20;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load payments",
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
    fetchPayments(event.page + 1);
};

const applyFilters = () => {
    first.value = 0;
    fetchPayments(1);
};

const clearFilters = () => {
    statusFilter.value = null;
    createdDateFilter.value = null;
    applyFilters();
};

const statusSeverity = (status) => {
    switch (status) {
        case "finished":
            return "success";
        case "waiting":
            return "warning";
        case "confirming":
            return "info";
        case "sending":
            return "info";
        case "refunded":
            return "danger";
        default:
            return "secondary";
    }
};

onMounted(() => fetchPayments());

watch(
    () => paymentDialogStore.refreshKey,
    () => {
        first.value = 0;
        fetchPayments(1);
    }
);
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Payments</h2>
                <p class="text-gray-600">Create and track payments.</p>
            </div>
            <Button label="New payment" icon="pi pi-plus" @click="openCreate" />
        </div>

        <Card class="shadow-sm">
            <template #content>
                <div class="flex flex-col gap-3 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Status</label>
                            <Dropdown
                                v-model="statusFilter"
                                :options="statusOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="All statuses"
                                class="w-full"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Created date</label>
                            <Calendar
                                v-model="createdDateFilter"
                                dateFormat="yy-mm-dd"
                                showIcon
                                class="w-full"
                                inputClass="w-full"
                                placeholder="YYYY-MM-DD"
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
                    :value="payments"
                    :loading="loading"
                    :paginator="true"
                    :rows="rows"
                    :totalRecords="totalRecords"
                    :first="first"
                    @page="onPage"
                >
                    <Column
                        field="transaction_id"
                        header="Transaction"
                        style="min-width: 14rem"
                    />
                    <Column
                        field="amount"
                        header="Amount"
                        style="min-width: 8rem"
                    />
                    <Column
                        field="currency"
                        header="Currency"
                        style="min-width: 8rem"
                    />
                    <Column
                        field="status"
                        header="Status"
                        style="min-width: 10rem"
                    >
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.status"
                                :severity="
                                    statusSeverity(slotProps.data.status)
                                "
                            />
                        </template>
                    </Column>
                    <Column
                        field="paid_amount"
                        header="Paid"
                        style="min-width: 10rem"
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
                                No Payments found.
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
                                Loading payments...
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
