<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/admin/services/api";
import { formatDate } from "@/admin/services/date";
import UserSelect from "@/admin/components/UserSelect.vue";

const toast = useToast();
const loading = ref(false);
const orders = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(10);

const userFilter = ref(null);
const statusFilter = ref(null);
const createdDateFilter = ref(null);
const statusOptions = [
    { label: "All", value: null },
    { label: "Pending", value: "pending" },
    { label: "Completed", value: "completed" },
    { label: "Partially completed", value: "partially_completed" },
    { label: "Cancelled", value: "cancelled" },
];

const buildQuery = (page) => {
    const params = new URLSearchParams();
    params.set("page", String(page));
    params.set("per_page", String(rows.value));
    if (userFilter.value) {
        params.set("filters[user_id]", userFilter.value);
    }
    if (statusFilter.value) {
        params.set("filters[status]", statusFilter.value);
    }
    const createdDate = formatDate(createdDateFilter.value);
    if (createdDate) {
        params.set("filters[created_at]", createdDate);
    }
    return params.toString();
};

const fetchOrders = async (page = 1) => {
    loading.value = true;
    try {
        const query = buildQuery(page);
        const data = await apiRequest(`/orders?${query}`);
        orders.value = data.data || [];
        totalRecords.value = data.total || 0;
        rows.value = data.per_page || 10;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load orders",
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
    const page = event.page + 1;
    fetchOrders(page);
};

const applyFilters = () => {
    first.value = 0;
    fetchOrders(1);
};

const clearFilters = () => {
    statusFilter.value = null;
    createdDateFilter.value = null;
    applyFilters();
};

const statusSeverity = (status) => {
    switch (status) {
        case "completed":
            return "success";
        case "pending":
            return "warning";
        case "cancelled":
            return "danger";
        case "partially_completed":
            return "info";
        default:
            return "secondary";
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
                <div class="flex flex-col gap-3 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">User</label>
                            <UserSelect v-model="userFilter" placeholder="Search a user..." />
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
                    :value="orders"
                    :loading="loading"
                    :paginator="true"
                    :rows="rows"
                    :totalRecords="totalRecords"
                    :first="first"
                    @page="onPage"
                >
                    <Column
                        field="id"
                        header="Order ID"
                        style="min-width: 12rem"
                    />
                    <Column field="user" header="User" style="min-width: 12rem">
                        <template #body="slotProps">
                            {{ slotProps.data.user.name }} ({{
                                slotProps.data.user.email
                            }})
                        </template>
                    </Column>
                    <Column
                        field="total_cent_price"
                        header="Total Price"
                        style="min-width: 10rem"
                    >
                        <template #body="slotProps">
                            $
                            {{
                                (slotProps.data.total_cent_price / 100).toFixed(
                                    2,
                                )
                            }}
                        </template>
                    </Column>
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
                    <template #empty>
                        <div
                            v-if="!loading"
                            class="flex flex-col items-center justify-center p-8 text-gray-500"
                        >
                            <i
                                class="pi pi-inbox text-4xl mb-4 text-gray-400"
                            ></i>
                            <p class="text-lg font-medium">No Orders found.</p>
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
                            <p class="text-lg font-medium">Loading orders...</p>
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
