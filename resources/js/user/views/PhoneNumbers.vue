<script setup>
import { onMounted, onUnmounted, ref, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import { apiRequest } from "@/services/api";

const toast = useToast();
const confirm = useConfirm();
const loading = ref(false);
const phoneNumbers = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);

// --- Added for Bulk Selection ---
const selectedNumbers = ref([]);
const isDeletingBulk = ref(false);

// --- Custom "Select All" Logic ---
const selectablePhoneNumbers = computed(() => {
    return phoneNumbers.value.filter(num => num.status !== 'pending');
});

const isAllSelected = computed(() => {
    if (!selectablePhoneNumbers.value.length) return false;
    // Check if every selectable number's ID is currently in the selectedNumbers array
    const selectedIds = selectedNumbers.value.map(n => n.id);
    return selectablePhoneNumbers.value.every(n => selectedIds.includes(n.id));
});

const toggleAll = () => {
    if (isAllSelected.value) {
        selectedNumbers.value = []; // Deselect all
    } else {
        selectedNumbers.value = [...selectablePhoneNumbers.value]; // Select only the completed ones
    }
};

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

const now = ref(Date.now());
let timeInterval = null;
const cancelLoading = ref({});

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
    selectedNumbers.value = []; // Clear selection when filtering
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
        case "active": return "success";
        case "completed": return "info";
        case "timeout_refunded": return "warning";
        case "cancelled": return "danger";
        default: return "secondary";
    }
};

const canCancel = (createdAt) => {
    if (!createdAt) return false;
    const createdMs = new Date(createdAt).getTime();
    const diffMinutes = (now.value - createdMs) / (1000 * 60);
    return diffMinutes >= 2;
};

const handleCancel = (event, id) => {       
    confirm.require({
        group: 'confirm-popup',
        target: event.currentTarget,
        message: 'Are you sure you want to cancel this number?',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptLabel: 'Yes, Cancel',
        rejectLabel: 'No',
        accept: async () => {
            cancelLoading.value[id] = true;
            try {
                await apiRequest(`/v1/phone-numbers/${id}/cancel`, { method: 'POST' });
                toast.add({ severity: 'success', summary: 'Cancelled', detail: 'Phone number cancelled.', life: 3000 });
                
                // Refresh data
                const currentPage = Math.floor(first.value / rows.value) + 1;
                fetchNumbers(currentPage);
            } catch (error) {
                toast.add({ severity: 'error', summary: 'Cancel Failed', detail: error.message, life: 4000 });
            } finally {
                cancelLoading.value[id] = false;
            }
        }
    });
};

const handleBulkDelete = () => {
    if (!selectedNumbers.value.length) return;

    confirm.require({
        group: 'confirm-dialog',
        message: `Are you sure you want to delete ${selectedNumbers.value.length} completed numbers? This action cannot be undone.`,
        header: 'Confirm Deletion',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: async () => {
            isDeletingBulk.value = true;
            const ids = selectedNumbers.value.map(num => num.id);

            try {
                await apiRequest(`/phone-numbers/delete`, {
                    method: 'DELETE',
                    body: {ids}
                });

                toast.add({
                    severity: 'success',
                    summary: 'Deleted',
                    detail: 'Selected numbers were successfully deleted.',
                    life: 3000
                });

                selectedNumbers.value = [];
                const currentPage = Math.floor(first.value / rows.value) + 1;
                fetchNumbers(currentPage);
            } catch (error) {
                toast.add({
                    severity: 'error',
                    summary: 'Deletion Failed',
                    detail: error.message,
                    life: 4000
                });
            } finally {
                isDeletingBulk.value = false;
            }
        }
    });
};

onMounted(() => {
    fetchNumbers();
    timeInterval = setInterval(() => { now.value = Date.now(); }, 10000);
});

onUnmounted(() => {
    if (timeInterval) clearInterval(timeInterval);
});
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Phone Numbers</h2>
                <p class="text-gray-600">All purchased numbers and services.</p>
            </div>
            <Button v-if="selectedNumbers.length > 0" :label="`Delete Selected (${selectedNumbers.length})`"
                icon="pi pi-trash" severity="danger" :loading="isDeletingBulk" @click="handleBulkDelete" />
        </div>

        <Card class="shadow-sm">
            <template #content>
                <div class="flex flex-col gap-3 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Service</label>
                            <InputText v-model="serviceFilter" placeholder="Service name" class="w-full" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Phone number</label>
                            <InputText v-model="phoneFilter" placeholder="Phone number" class="w-full" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Status</label>
                            <Dropdown v-model="statusFilter" :options="statusOptions" optionLabel="label"
                                optionValue="value" placeholder="All statuses" class="w-full" />
                        </div>
                        <div class="flex items-end gap-2">
                            <Button label="Apply" icon="pi pi-filter" @click="applyFilters" />
                            <Button label="Clear" icon="pi pi-times" severity="secondary" @click="clearFilters" />
                        </div>
                    </div>
                </div>

                <DataTable lazy :value="phoneNumbers" :loading="loading" :paginator="true" :rows="rows"
                    :totalRecords="totalRecords" :first="first" v-model:expandedRows="expandedRows" dataKey="id"
                    @page="onPage">

                    <Column headerStyle="width: 3rem">
                        <template #header>
                            <Checkbox :modelValue="isAllSelected" :binary="true" @update:modelValue="toggleAll"
                                :disabled="selectablePhoneNumbers.length === 0"
                                v-tooltip.top="selectablePhoneNumbers.length === 0 ? 'No completed numbers to select' : 'Select all completed'" />
                        </template>
                        <template #body="slotProps">
                            <Checkbox v-model="selectedNumbers" :value="slotProps.data"
                                :disabled="slotProps.data.status === 'pending'" />
                        </template>
                    </Column>

                    <Column expander style="width: 3rem" />
                    <Column style="width: 4rem">
                        <template #body="slotProps">
                            <div :id="'msgCount_' + slotProps.data.id"
                                class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold transition-colors duration-300"
                                :class="slotProps.data.isActive
                                    ? 'bg-green-500 text-white'
                                    : 'bg-gray-100 dark:bg-gray-900 dark:text-gray-200'
                                    ">
                                {{ slotProps.data.messages.length }}
                            </div>
                        </template>
                    </Column>
                    <Column field="service_name" header="Service" style="min-width: 12rem" />
                    <Column field="phone_number" header="Phone number" style="min-width: 12rem">
                        <template #body="slotProps">
                            <Button link class="p-0" @click.stop="toggleRow(slotProps.data)">
                                <span class="text-primary-600 underline">{{ slotProps.data.phone_number }}</span>
                            </Button>
                        </template>
                    </Column>
                    <Column field="price_cents" header="Price" style="min-width: 8rem">
                        <template #body="slotProps">
                            $ {{ (slotProps.data.price_cents / 100).toFixed(2) }}
                        </template>
                    </Column>
                    <Column field="status" header="Status" style="min-width: 10rem">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status" :severity="statusSeverity(slotProps.data.status)" />
                        </template>
                    </Column>
                    <Column header="Created" style="min-width: 12rem">
                        <template #body="slotProps">
                            {{
                                new Intl.DateTimeFormat("en-CA", {
                                    year: "numeric", month: "2-digit", day: "2-digit",
                                    hour: "2-digit", minute: "2-digit", hour12: true,
                                }).format(new Date(slotProps.data.created_at)).replace(", ", " ")
                            }}
                        </template>
                    </Column>

                    <Column header="Actions" style="min-width: 8rem" alignFrozen="right">
                        <template #body="slotProps">
                            <div class="flex items-center">
                                <Button v-if="slotProps.data.status === 'pending'" label="Cancel" severity="danger"
                                    size="small" icon="pi pi-times" :disabled="!canCancel(slotProps.data.created_at)"
                                    :loading="cancelLoading[slotProps.data.id]"
                                    :title="!canCancel(slotProps.data.created_at) ? 'Available 2 minutes after purchase' : ''"
                                    @click="handleCancel($event,slotProps.data.id)" />
                            </div>
                        </template>
                    </Column>

                    <template #expansion="slotProps">
                        <div class="p-4 bg-gray-50 dark:bg-gray-950 rounded-lg">
                            <h4 class="font-semibold mb-2">Messages</h4>
                            <div v-if="slotProps.data.messages && slotProps.data.messages.length"
                                class="flex flex-col gap-2">
                                <div v-for="message in slotProps.data.messages" :key="message.id"
                                    class="p-3 border border-gray-200 dark:border-gray-700 rounded-md">
                                    <p class="text-sm">{{ message.message }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{
                                            new Intl.DateTimeFormat("en-CA", {
                                                year: "numeric", month: "2-digit", day: "2-digit",
                                                hour: "2-digit", minute: "2-digit", hour12: true,
                                            }).format(new Date(message.created_at)).replace(", ", " ")
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">
                                No messages yet.
                            </div>
                        </div>
                    </template>
                    <template #empty>
                        <div v-if="!loading" class="flex flex-col items-center justify-center p-8 text-gray-500">
                            <i class="pi pi-inbox text-4xl mb-4 text-gray-400"></i>
                            <p class="text-lg font-medium">No Phone Numbers found.</p>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center p-8 text-gray-500">
                            <i class="pi pi-spinner pi-spin text-4xl mb-4 text-blue-500 dark:text-blue-400"></i>
                            <p class="text-lg font-medium">Loading phone numbers...</p>
                        </div>
                    </template>
                </DataTable>
            </template>
        </Card>
    </div>
</template>