<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import { apiRequest } from "@/admin/services/api";

const toast = useToast();
const confirm = useConfirm();

const loading = ref(false);
const admins = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);

const searchFilter = ref("");

const createDialogVisible = ref(false);
const updateDialogVisible = ref(false);
const saving = ref(false);
const editingAdmin = ref(null);

const createForm = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const updateForm = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const buildQuery = (page) => {
    const params = new URLSearchParams();
    params.set("page", String(page));
    params.set("per_page", String(rows.value));

    if (searchFilter.value.trim()) {
        params.set("filters[search]", searchFilter.value.trim());
    }

    return params.toString();
};

const fetchAdmins = async (page = 1) => {
    loading.value = true;
    try {
        const query = buildQuery(page);
        const data = await apiRequest(`/admins?${query}`);
        admins.value = data.data || [];
        totalRecords.value = data.total || 0;
        rows.value = data.per_page || 20;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load admins",
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
    fetchAdmins(event.page + 1);
};

const applyFilters = () => {
    first.value = 0;
    fetchAdmins(1);
};

const clearFilters = () => {
    searchFilter.value = "";
    applyFilters();
};

const openCreateDialog = () => {
    createForm.value = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    };
    createDialogVisible.value = true;
};

const createAdmin = async () => {
    saving.value = true;
    try {
        await apiRequest("/admins", {
            method: "POST",
            body: createForm.value,
        });

        toast.add({
            severity: "success",
            summary: "Admin created",
            detail: "New admin account created successfully.",
            life: 3000,
        });

        createDialogVisible.value = false;
        fetchAdmins(1);
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Create failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        saving.value = false;
    }
};

const openUpdateDialog = (admin) => {
    editingAdmin.value = admin;
    updateForm.value = {
        name: admin.name || "",
        email: admin.email || "",
        password: "",
        password_confirmation: "",
    };
    updateDialogVisible.value = true;
};

const updateAdmin = async () => {
    if (!editingAdmin.value) return;

    saving.value = true;
    try {
        const payload = {
            name: updateForm.value.name,
            email: updateForm.value.email,
        };

        if (updateForm.value.password) {
            payload.password = updateForm.value.password;
            payload.password_confirmation = updateForm.value.password_confirmation;
        }

        await apiRequest(`/admins/${editingAdmin.value.id}`, {
            method: "PUT",
            body: payload,
        });

        toast.add({
            severity: "success",
            summary: "Admin updated",
            detail: "Admin account updated successfully.",
            life: 3000,
        });

        updateDialogVisible.value = false;
        const currentPage = Math.floor(first.value / rows.value) + 1;
        fetchAdmins(currentPage);
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Update failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        saving.value = false;
    }
};

const deleteAdmin = (event, admin) => {
    confirm.require({
        group: "confirm-popup",
        target: event.currentTarget,
        message: `Delete admin ${admin.email}?`,
        icon: "pi pi-exclamation-triangle",
        acceptClass: "p-button-danger",
        accept: async () => {
            try {
                await apiRequest(`/admins/${admin.id}`, { method: "DELETE" });
                toast.add({
                    severity: "success",
                    summary: "Admin deleted",
                    detail: "Admin account was deleted successfully.",
                    life: 3000,
                });
                const currentPage = Math.floor(first.value / rows.value) + 1;
                fetchAdmins(currentPage);
            } catch (error) {
                toast.add({
                    severity: "error",
                    summary: "Delete failed",
                    detail: error.message,
                    life: 4000,
                });
            }
        },
    });
};

onMounted(() => fetchAdmins());
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Admins</h2>
                <p class="text-gray-600">Create and manage admin accounts.</p>
            </div>
            <Button label="New Admin" icon="pi pi-plus" @click="openCreateDialog" />
        </div>

        <Card class="shadow-sm">
            <template #content>
                <div class="flex flex-col md:flex-row items-end gap-3 mb-4">
                    <div class="flex flex-col gap-2 w-full md:max-w-md">
                        <label class="font-medium">Search</label>
                        <InputText
                            v-model="searchFilter"
                            placeholder="Search by name or email"
                            class="w-full"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <Button label="Apply" icon="pi pi-filter" @click="applyFilters" />
                        <Button
                            label="Clear"
                            icon="pi pi-times"
                            severity="secondary"
                            @click="clearFilters"
                        />
                    </div>
                </div>

                <DataTable
                    lazy
                    :value="admins"
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
                    <Column header="Created" style="min-width: 10rem">
                        <template #body="slotProps">
                            {{ new Date(slotProps.data.created_at).toLocaleDateString() }}
                        </template>
                    </Column>
                    <Column header="Actions" style="min-width: 12rem">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-pencil"
                                    size="small"
                                    severity="secondary"
                                    @click="openUpdateDialog(slotProps.data)"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    size="small"
                                    severity="danger"
                                    @click="deleteAdmin($event, slotProps.data)"
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
                            <p class="text-lg font-medium">No admins found.</p>
                            <p class="text-sm">
                                Try adjusting your search or check back later.
                            </p>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center justify-center p-8 text-gray-500"
                        >
                            <i
                                class="pi pi-spinner pi-spin text-4xl mb-4 text-blue-500 dark:text-blue-400"
                            ></i>
                            <p class="text-lg font-medium">Loading admins...</p>
                            <p class="text-sm">
                                Please wait while we fetch your data.
                            </p>
                        </div>
                    </template>
                </DataTable>
            </template>
        </Card>

        <Dialog
            v-model:visible="createDialogVisible"
            modal
            header="Create Admin"
            :style="{ width: '34rem' }"
        >
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-medium">Name</label>
                    <InputText v-model="createForm.name" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium">Email</label>
                    <InputText v-model="createForm.email" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium">Password</label>
                    <Password
                        v-model="createForm.password"
                        :feedback="false"
                        toggleMask
                        class="w-full"
                        inputClass="w-full"
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium">Confirm Password</label>
                    <Password
                        v-model="createForm.password_confirmation"
                        :feedback="false"
                        toggleMask
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
                    @click="createDialogVisible = false"
                />
                <Button label="Create" :loading="saving" @click="createAdmin" />
            </template>
        </Dialog>

        <Dialog
            v-model:visible="updateDialogVisible"
            modal
            header="Update Admin"
            :style="{ width: '34rem' }"
        >
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-medium">Name</label>
                    <InputText v-model="updateForm.name" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium">Email</label>
                    <InputText v-model="updateForm.email" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium">New Password (optional)</label>
                    <Password
                        v-model="updateForm.password"
                        :feedback="false"
                        toggleMask
                        class="w-full"
                        inputClass="w-full"
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium">Confirm Password</label>
                    <Password
                        v-model="updateForm.password_confirmation"
                        :feedback="false"
                        toggleMask
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
                    @click="updateDialogVisible = false"
                />
                <Button label="Save" :loading="saving" @click="updateAdmin" />
            </template>
        </Dialog>
    </div>
</template>
