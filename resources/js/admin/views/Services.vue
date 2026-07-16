<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/admin/services/api";

const toast = useToast();

const loading = ref(false);
const saving = ref(false);
const deletingId = ref(null);
const services = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);

const nameFilter = ref(null);
const codeFilter = ref(null);
const priceFilter = ref(null);

const dialogVisible = ref(false);
const editingServiceId = ref(null);
const form = ref({
    name: "",
    code: "",
    price: 0,
});

const buildQuery = (page) => {
    const params = new URLSearchParams();
    params.set("page", String(page));
    params.set("per_page", String(rows.value));

    if (nameFilter.value) {
        params.set("filters[name]", nameFilter.value);
    }
    if (codeFilter.value) {
        params.set("filters[code]", codeFilter.value);
    }
    if (priceFilter.value !== null && priceFilter.value !== "") {
        params.set("filters[price]", String(priceFilter.value));
    }

    return params.toString();
};

const fetchServices = async (page = 1) => {
    loading.value = true;

    try {
        const query = buildQuery(page);
        const response = await apiRequest(`/services?${query}`);
        services.value = response.services || [];
        totalRecords.value = response.pagination?.total || 0;
        rows.value = response.pagination?.per_page || rows.value;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load services",
            detail: error.message,
            life: 4000,
        });
    } finally {
        loading.value = false;
    }
};

const applyFilters = () => {
    first.value = 0;
    fetchServices(1);
};

const clearFilters = () => {
    nameFilter.value = null;
    codeFilter.value = null;
    priceFilter.value = null;
    applyFilters();
};

const onPage = (event) => {
    first.value = event.first;
    rows.value = event.rows;
    fetchServices(event.page + 1);
};

const resetForm = () => {
    editingServiceId.value = null;
    form.value = {
        name: "",
        code: "",
        price: 0,
    };
};

const openCreateDialog = () => {
    resetForm();
    dialogVisible.value = true;
};

const openEditDialog = (service) => {
    editingServiceId.value = service.id;
    form.value = {
        name: service.name,
        code: service.code,
        price: Number(service.price),
    };
    dialogVisible.value = true;
};

const closeDialog = () => {
    dialogVisible.value = false;
    resetForm();
};

const saveService = async () => {
    saving.value = true;

    try {
        const payload = {
            name: form.value.name,
            code: form.value.code,
            price_cents: Math.round(Number(form.value.price || 0) * 100),
        };

        if (editingServiceId.value) {
            await apiRequest(`/services/${editingServiceId.value}`, {
                method: "PUT",
                body: payload,
            });
        } else {
            await apiRequest("/services", {
                method: "POST",
                body: payload,
            });
        }

        toast.add({
            severity: "success",
            summary: editingServiceId.value ? "Service updated" : "Service created",
            detail: "The service catalog was saved successfully.",
            life: 3000,
        });

        closeDialog();
        fetchServices();
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Save failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        saving.value = false;
    }
};

const deleteService = async (service) => {
    deletingId.value = service.id;

    try {
        await apiRequest(`/services/${service.id}`, {
            method: "DELETE",
        });

        toast.add({
            severity: "success",
            summary: "Service deleted",
            detail: `${service.name} was removed successfully.`,
            life: 3000,
        });

        fetchServices();
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Delete failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        deletingId.value = null;
    }
};

onMounted(() => fetchServices());
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h2 class="text-xl font-semibold">Services</h2>
                <p class="text-gray-600">
                    Manage the local FoxSims-backed service catalog.
                </p>
            </div>

            <Button label="Add Service" icon="pi pi-plus" @click="openCreateDialog" />
        </div>

        <Card class="shadow-sm">
            <template #content>
                <div class="flex flex-col gap-3 mb-6">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-end"
                    >
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-sm">Service Name</label>
                            <InputText
                                v-model="nameFilter"
                                placeholder="Search by name..."
                                class="w-full"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-sm">Code</label>
                            <InputText
                                v-model="codeFilter"
                                placeholder="e.g., 39"
                                class="w-full"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-sm">Price</label>
                            <InputNumber
                                v-model="priceFilter"
                                placeholder="0.00"
                                mode="decimal"
                                :minFractionDigits="2"
                                class="w-full"
                                inputClass="w-full"
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
                </div>

                <DataTable
                    lazy
                    :value="services"
                    :loading="loading"
                    paginator
                    :rows="rows"
                    :rowsPerPageOptions="[10, 20, 50]"
                    :totalRecords="totalRecords"
                    :first="first"
                    @page="onPage"
                    responsiveLayout="scroll"
                >
                    <Column field="name" header="Service Name" style="min-width: 14rem">
                        <template #body="{ data }">
                            <span class="font-medium">{{ data.name }}</span>
                        </template>
                    </Column>

                    <Column field="code" header="Code" style="min-width: 8rem">
                        <template #body="{ data }">
                            <span class="text-gray-600 uppercase">{{ data.code }}</span>
                        </template>
                    </Column>

                    <Column field="price" header="Price" style="min-width: 8rem">
                        <template #body="{ data }">
                            ${{ Number(data.price).toFixed(2) }}
                        </template>
                    </Column>

                    <Column header="Actions" style="min-width: 12rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-pencil"
                                    label="Edit"
                                    size="small"
                                    severity="secondary"
                                    @click="openEditDialog(data)"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    label="Delete"
                                    size="small"
                                    severity="danger"
                                    :loading="deletingId === data.id"
                                    @click="deleteService(data)"
                                />
                            </div>
                        </template>
                    </Column>

                    <template #empty>
                        <div
                            v-if="!loading"
                            class="flex flex-col items-center justify-center p-8 text-gray-500"
                        >
                            <i class="pi pi-box text-4xl mb-4 text-gray-400"></i>
                            <p class="text-lg font-medium">No Services found.</p>
                            <p class="text-sm text-center">
                                Try adjusting your filters or add a new service.
                            </p>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center justify-center p-8 text-gray-500"
                        >
                            <i class="pi pi-spinner pi-spin text-4xl mb-4 text-blue-500 dark:text-blue-400"></i>
                            <p class="text-lg font-medium">Loading services...</p>
                            <p class="text-sm">Please wait while we fetch your data.</p>
                        </div>
                    </template>
                </DataTable>
            </template>
        </Card>

        <Dialog
            v-model:visible="dialogVisible"
            :header="editingServiceId ? 'Edit Service' : 'Create Service'"
            modal
            class="w-full max-w-lg mx-4"
            @hide="closeDialog"
        >
            <div class="flex flex-col gap-4 mt-2">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm">Name</label>
                    <InputText v-model="form.name" placeholder="Service name" />
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm">Code</label>
                    <InputText v-model="form.code" placeholder="Provider service code" />
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm">Price</label>
                    <InputNumber
                        v-model="form.price"
                        mode="decimal"
                        :min="0"
                        :minFractionDigits="2"
                        inputClass="w-full"
                    />
                </div>
            </div>

            <template #footer>
                <div class="flex items-center justify-end gap-2">
                    <Button label="Cancel" severity="secondary" @click="closeDialog" />
                    <Button
                        :label="editingServiceId ? 'Save Changes' : 'Create Service'"
                        :loading="saving"
                        @click="saveService"
                    />
                </div>
            </template>
        </Dialog>
    </div>
</template>
