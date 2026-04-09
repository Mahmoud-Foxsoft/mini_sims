<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/services/api";
import { formatDate } from "@/services/date";

const toast = useToast();
const loading = ref(false);
const phoneNumbers = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);

const expandedRows = ref({});
const serviceFilter = ref("");
const phoneFilter = ref("");
const statusFilter = ref(null);
const statusOptions = [
    { label: "All", value: null },
    { label: "Active", value: "active" },
    { label: "Completed", value: "completed" },
    { label: "Timeout refunded", value: "timeout_refunded" },
    { label: "Cancelled", value: "cancelled" },
    { label: "Pending", value: "pending" },
];

const buildQuery = (page) => {
    const params = new URLSearchParams();
    params.set("page", String(page));
    params.set("per_page", String(rows.value));
    if (serviceFilter.value?.trim()) {
        params.set("filters[service_name]", serviceFilter.value.trim());
    }
    if (phoneFilter.value?.trim()) {
        params.set("filters[phone_number]", phoneFilter.value.trim());
    }
    if (statusFilter.value) {
        params.set("filters[status]", statusFilter.value);
    }
    return params.toString();
};

const fetchNumbers = async (page = 1) => {
    loading.value = true;
    try {
        const query = buildQuery(page);
        const data = await apiRequest(`/v1/phone-numbers?${query}`);
        phoneNumbers.value = data.data || [];
        totalRecords.value = data.total || 0;
        rows.value = data.per_page || 20;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load numbers",
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
    fetchNumbers(event.page + 1);
};

const applyFilters = () => {
    first.value = 0;
    expandedRows.value = {};
    fetchNumbers(1);
};

const clearFilters = () => {
    serviceFilter.value = "";
    phoneFilter.value = "";
    statusFilter.value = null;
    applyFilters();
};

const toggleRow = (row) => {
    if (!row?.id) return;
    const next = { ...expandedRows.value };
    if (next[row.id]) {
        delete next[row.id];
    } else {
        next[row.id] = true;
    }
    expandedRows.value = next;
};

const statusSeverity = (status) => {
    switch (status) {
        case "active":
            return "success";
        case "completed":
            return "info";
        case "timeout_refunded":
            return "warning";
        case "cancelled":
            return "danger";
        default:
            return "secondary";
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
                <div class="flex flex-col gap-3 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Service</label>
                            <InputText
                                v-model="serviceFilter"
                                placeholder="Service name"
                                class="w-full"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Phone number</label>
                            <InputText
                                v-model="phoneFilter"
                                placeholder="Phone number"
                                class="w-full"
                            />
                        </div>
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
                    :value="phoneNumbers"
                    :loading="loading"
                    :paginator="true"
                    :rows="rows"
                    :totalRecords="totalRecords"
                    :first="first"
                    v-model:expandedRows="expandedRows"
                    dataKey="id"
                    @page="onPage"
                >
                    <Column expander style="width: 3rem" />
                    <Column style="width: 4rem">
                        <template #body="slotProps">
                            <div
                                :id="'msgCount_' + slotProps.data.id"
                                class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold transition-colors duration-300"
                                :class="
                                    slotProps.data.isActive
                                        ? 'bg-green-500 text-white'
                                        : 'bg-gray-100 dark:bg-gray-800 dark:text-gray-200'
                                "
                            >
                                {{ slotProps.data.messages.length }}
                            </div>
                        </template>
                    </Column>
                    <Column
                        field="service_name"
                        header="Service"
                        style="min-width: 12rem"
                    />
                    <Column
                        field="phone_number"
                        header="Phone number"
                        style="min-width: 12rem"
                    >
                        <template #body="slotProps">
                            <Button
                                link
                                class="p-0"
                                @click.stop="toggleRow(slotProps.data)"
                            >
                                <span class="text-primary-600 underline">{{
                                    slotProps.data.phone_number
                                }}</span>
                            </Button>
                        </template>
                    </Column>
                    <Column
                        field="price_cents"
                        header="Price (cents)"
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
                    <Column header="Created" style="min-width: 12rem">
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.created_at) }}
                        </template>
                    </Column>
                    <template #expansion="slotProps">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h4 class="font-semibold mb-2">Messages</h4>
                            <div
                                v-if="
                                    slotProps.data.messages &&
                                    slotProps.data.messages.length
                                "
                                class="flex flex-col gap-2"
                            >
                                <div
                                    v-for="message in slotProps.data.messages"
                                    :key="message.id"
                                    class="p-3 border border-gray-200 rounded-md"
                                >
                                    <p class="text-sm">{{ message.message }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ message.created_at }}
                                    </p>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">
                                No messages yet.
                            </div>
                        </div>
                    </template>
                    <template #empty>
                        <div
                            v-if="!loading"
                            class="flex flex-col items-center justify-center p-8 text-gray-500"
                        >
                            <i
                                class="pi pi-inbox text-4xl mb-4 text-gray-400"
                            ></i>
                            <p class="text-lg font-medium">
                                No Phone Numbers found.
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
                                Loading phone numbers...
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
