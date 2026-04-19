<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/admin/services/api";

const toast = useToast();
const loading = ref(false);
const users = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);

const nameFilter = ref("");
const emailFilter = ref("");
const blockedFilter = ref(null);
const blockedOptions = [
    { label: "All", value: null },
    { label: "Blocked", value: "true" },
    { label: "Active", value: "false" },
];

const editDialogVisible = ref(false);
const savingEdit = ref(false);
const editingUser = ref(null);
const editForm = ref({
    is_blocked: false,
    max_pending_numbers: null,
});

const transactionDialogVisible = ref(false);
const savingTransaction = ref(false);
const transactionUser = ref(null);
const transactionForm = ref({
    type: "credit",
    amount: null,
    description: "",
    reference_id: "",
});

const buildQuery = (page) => {
    const params = new URLSearchParams();
    params.set("page", String(page));
    params.set("per_page", String(rows.value));

    if (nameFilter.value.trim()) {
        params.set("filters[name]", nameFilter.value.trim());
    }

    if (emailFilter.value.trim()) {
        params.set("filters[email]", emailFilter.value.trim());
    }

    if (blockedFilter.value !== null) {
        params.set("filters[is_blocked]", blockedFilter.value);
    }

    return params.toString();
};

const fetchUsers = async (page = 1) => {
    loading.value = true;
    try {
        const query = buildQuery(page);
        const data = await apiRequest(`/users?${query}`);
        users.value = data.data || [];
        totalRecords.value = data.total || 0;
        rows.value = data.per_page || 20;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load users",
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
    fetchUsers(event.page + 1);
};

const applyFilters = () => {
    first.value = 0;
    fetchUsers(1);
};

const clearFilters = () => {
    nameFilter.value = "";
    emailFilter.value = "";
    blockedFilter.value = null;
    applyFilters();
};

const openEditDialog = (user) => {
    editingUser.value = user;
    editForm.value = {
        is_blocked: Boolean(user.is_blocked),
        max_pending_numbers: user.max_pending_numbers,
    };
    editDialogVisible.value = true;
};

const saveUser = async () => {
    if (!editingUser.value) return;

    savingEdit.value = true;
    try {
        const payload = {
            is_blocked: editForm.value.is_blocked,
        };

        if (editForm.value.max_pending_numbers !== null) {
            payload.max_pending_numbers = editForm.value.max_pending_numbers;
        }

        await apiRequest(`/users/${editingUser.value.id}`, {
            method: "PUT",
            body: payload,
        });

        toast.add({
            severity: "success",
            summary: "User updated",
            detail: "User settings were saved successfully.",
            life: 3000,
        });

        editDialogVisible.value = false;
        const currentPage = Math.floor(first.value / rows.value) + 1;
        fetchUsers(currentPage);
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Update failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        savingEdit.value = false;
    }
};

const openTransactionDialog = (user) => {
    transactionUser.value = user;
    transactionForm.value = {
        type: "credit",
        amount: null,
        description: "",
        reference_id: "",
    };
    transactionDialogVisible.value = true;
};

const createTransaction = async () => {
    if (!transactionUser.value) return;

    const amount = Number(transactionForm.value.amount || 0);
    if (!Number.isFinite(amount) || amount <= 0) {
        toast.add({
            severity: "warn",
            summary: "Invalid amount",
            detail: "Enter an amount greater than zero.",
            life: 3000,
        });
        return;
    }

    savingTransaction.value = true;
    try {
        await apiRequest("/transactions", {
            method: "POST",
            body: {
                user_id: transactionUser.value.id,
                type: transactionForm.value.type,
                amount_cents: Math.round(amount * 100),
                description: transactionForm.value.description,
                reference_id: transactionForm.value.reference_id || null,
            },
        });

        toast.add({
            severity: "success",
            summary: "Transaction created",
            detail: "Wallet transaction was added successfully.",
            life: 3000,
        });

        transactionDialogVisible.value = false;
        const currentPage = Math.floor(first.value / rows.value) + 1;
        fetchUsers(currentPage);
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Transaction failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        savingTransaction.value = false;
    }
};

onMounted(() => fetchUsers());
</script>

<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold">Users</h2>
            <p class="text-gray-600">
                Manage user access, limits, and wallet adjustments.
            </p>
        </div>

        <Card class="shadow-sm">
            <template #content>
                <div class="flex flex-col gap-3 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Name</label>
                            <InputText
                                v-model="nameFilter"
                                placeholder="Search by name"
                                class="w-full"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Email</label>
                            <InputText
                                v-model="emailFilter"
                                placeholder="Search by email"
                                class="w-full"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium">Status</label>
                            <Dropdown
                                v-model="blockedFilter"
                                :options="blockedOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="All"
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
                    :value="users"
                    :loading="loading"
                    :paginator="true"
                    :rows="rows"
                    :totalRecords="totalRecords"
                    :first="first"
                    @page="onPage"
                >
                    <Column field="id" header="ID" style="min-width: 6rem" />
                    <Column field="name" header="Name" style="min-width: 12rem" />
                    <Column field="email" header="Email" style="min-width: 14rem" />
                    <Column header="Balance" style="min-width: 10rem">
                        <template #body="slotProps">
                            ${{ (Number(slotProps.data.balance_cents || 0) / 100).toFixed(2) }}
                        </template>
                    </Column>
                    <Column field="max_pending_numbers" header="Max Pending" style="min-width: 10rem" />
                    <Column field="is_blocked" header="Blocked" style="min-width: 8rem">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.is_blocked ? 'Blocked' : 'Active'"
                                :severity="slotProps.data.is_blocked ? 'danger' : 'success'"
                            />
                        </template>
                    </Column>
                    <Column header="Actions" style="min-width: 14rem">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <Button
                                    label="Edit"
                                    icon="pi pi-pencil"
                                    size="small"
                                    severity="secondary"
                                    @click="openEditDialog(slotProps.data)"
                                />
                                <Button
                                    label="Add Tx"
                                    icon="pi pi-wallet"
                                    size="small"
                                    @click="openTransactionDialog(slotProps.data)"
                                />
                            </div>
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
                            <p class="text-lg font-medium">No users found.</p>
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
                            <p class="text-lg font-medium">Loading users...</p>
                            <p class="text-sm">
                                Please wait while we fetch your data.
                            </p>
                        </div>
                    </template>
                </DataTable>
            </template>
        </Card>

        <Dialog
            v-model:visible="editDialogVisible"
            modal
            header="Update User"
            :style="{ width: '32rem' }"
        >
            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-sm text-gray-500">
                        {{ editingUser?.name }} ({{ editingUser?.email }})
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox v-model="editForm.is_blocked" binary inputId="user-blocked" />
                    <label for="user-blocked" class="font-medium">Blocked</label>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium">Max pending numbers</label>
                    <InputNumber
                        v-model="editForm.max_pending_numbers"
                        :min="0"
                        :useGrouping="false"
                        class="w-full"
                        inputClass="w-full"
                    />
                </div>
            </div>

            <template #footer>
                <Button
                    label="Cancel"
                    severity="secondary"
                    text
                    @click="editDialogVisible = false"
                />
                <Button label="Save" :loading="savingEdit" @click="saveUser" />
            </template>
        </Dialog>

        <Dialog
            v-model:visible="transactionDialogVisible"
            modal
            header="Add Wallet Transaction"
            :style="{ width: '34rem' }"
        >
            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-sm text-gray-500">
                        {{ transactionUser?.name }} ({{ transactionUser?.email }})
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Current Balance:
                        <span class="font-medium text-gray-700">
                            ${{ (Number(transactionUser?.balance_cents || 0) / 100).toFixed(2) }}
                        </span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex flex-col gap-2">
                        <label class="font-medium">Type</label>
                        <Dropdown
                            v-model="transactionForm.type"
                            :options="[
                                { label: 'Credit', value: 'credit' },
                                { label: 'Debit', value: 'debit' },
                            ]"
                            optionLabel="label"
                            optionValue="value"
                            class="w-full"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-medium">Amount (USD)</label>
                        <InputNumber
                            v-model="transactionForm.amount"
                            mode="currency"
                            currency="USD"
                            :min="0.01"
                            :minFractionDigits="2"
                            :maxFractionDigits="2"
                            class="w-full"
                            inputClass="w-full"
                        />
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium">Description</label>
                    <InputText
                        v-model="transactionForm.description"
                        placeholder="Manual wallet adjustment"
                        class="w-full"
                    />
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium">Reference ID (optional)</label>
                    <InputText
                        v-model="transactionForm.reference_id"
                        placeholder="ticket-1234"
                        class="w-full"
                    />
                </div>
            </div>

            <template #footer>
                <Button
                    label="Cancel"
                    severity="secondary"
                    text
                    @click="transactionDialogVisible = false"
                />
                <Button
                    label="Create"
                    :loading="savingTransaction"
                    @click="createTransaction"
                />
            </template>
        </Dialog>
    </div>
</template>
