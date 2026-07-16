<script setup>
import { ref, computed, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/user/services/api";
import { useAuthStore } from "@/user/stores/authStore";
import { useWsStore } from "@/user/stores/wsStore";


const visible = defineModel("visible", { type: Boolean, default: false });

const toast = useToast();
const authStore = useAuthStore();

const localServices = ref([]);
const loading = ref(false);
const totalRecords = ref(0);
const totalPages = ref(0);
const searchQuery = ref("");
const isSubmitting = ref(false);
const pageSize = 20;
const loadedPages = new Set();
const loadingPages = new Set();
let searchTimeout = null;
let requestGeneration = 0;

const selectedService = ref(null);
const quantity = ref(1);
const emits = defineEmits(["submit"]);
const maxQuantity = computed(() => {
    return authStore.maxCartAmount || 5;
});

const totalCost = computed(() => {
    if (!selectedService.value) return 0;
    return Number(selectedService.value.price) * quantity.value;
});

const initializePlaceholders = (count) => {
    localServices.value = Array.from({ length: count }, () => ({
        name: searchQuery.value,
        code: searchQuery.value,
        price: 0,
        _isPlaceholder: true,
    }));
};

const loadPage = async (page, generation = requestGeneration) => {
    if (page < 1 || (totalPages.value > 0 && page > totalPages.value)) return;

    const requestKey = `${generation}:${page}`;
    if (loadedPages.has(page) || loadingPages.has(requestKey)) return;

    loadingPages.add(requestKey);
    loading.value = true;

    try {
        const params = new URLSearchParams({
            page: String(page),
            per_page: String(pageSize),
        });

        if (searchQuery.value) params.set("search", searchQuery.value);

        const response = await apiRequest(`/v1/services?${params.toString()}`);
        if (generation !== requestGeneration) return;

        const pagination = response.pagination || {};
        totalRecords.value = Number(pagination.total || 0);
        totalPages.value = Number(pagination.last_page || 0);

        if (localServices.value.length !== totalRecords.value) {
            initializePlaceholders(totalRecords.value);
        }

        const nextServices = [...localServices.value];
        const start = (page - 1) * pageSize;
        (response.services || []).forEach((service, index) => {
            nextServices[start + index] = service;
        });

        localServices.value = nextServices;
        loadedPages.add(page);
    } catch (error) {
        if (generation !== requestGeneration) return;

        toast.add({
            severity: "error",
            summary: "Failed to load services",
            detail: error.message,
            life: 4000,
        });
    } finally {
        loadingPages.delete(requestKey);
        if (generation === requestGeneration) {
            loading.value = loadingPages.size > 0;
        }
    }
};

const resetAndLoad = (query = "") => {
    requestGeneration += 1;
    searchQuery.value = String(query).trim();
    localServices.value = [];
    totalRecords.value = 0;
    totalPages.value = 0;
    loadedPages.clear();
    loadingPages.clear();
    loadPage(1);
};

const onFilter = (event) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => resetAndLoad(event.value || ""), 300);
};

const onLazyLoad = (event) => {
    const firstPage = Math.floor(Number(event.first || 0) / pageSize) + 1;
    const lastIndex = Math.max(Number(event.last || pageSize) - 1, 0);
    const lastPage = Math.floor(lastIndex / pageSize) + 1;
    const upperPage = totalPages.value
        ? Math.min(lastPage, totalPages.value)
        : lastPage;

    for (let page = firstPage; page <= upperPage; page += 1) {
        loadPage(page);
    }
};

watch(visible, (isNowVisible) => {
    if (isNowVisible) {
        resetAndLoad();
        selectedService.value = null;
        quantity.value = 1;
    }
});

const wsStore = useWsStore();
watch(
    () => wsStore.servicesLastUpdated,
    (value) => {
        if (!value) return
        selectedService.value = null
        resetAndLoad(searchQuery.value)
    },
);

const handleQuantityChange = (newQuantity) => {
    if (newQuantity > maxQuantity.value) {
        toast.add({
            severity: "warn",
            summary: "Limit Reached",
            detail: `You cannot order more than ${maxQuantity.value} items.`,
            life: 3000,
        });
        return;
    }

    if (newQuantity < 1) return;
    quantity.value = newQuantity;
};

const handleCheckout = async () => {
    if (!selectedService.value) {
        toast.add({
            severity: "warn",
            summary: "Selection Required",
            detail: "Please select a service before confirming.",
            life: 3000,
        });
        return;
    }

    isSubmitting.value = true;
    try {
        const payload = {
            service_code: selectedService.value.code,
            quantity: quantity.value,
        };

        const response = await apiRequest("/v1/orders", {
            method: "POST",
            body: payload,
        });

        toast.add({
            severity: "success",
            summary: "Order Placed",
            detail: "Your service has been ordered successfully.",
            life: 4000,
        });

        visible.value = false;
        emits("submit",response);
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Checkout Failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Dialog
        v-model:visible="visible"
        header="Place Order"
        :dismissableMask="true"
        modal
        class="w-full max-w-md mx-4"
    >
        <div class="flex flex-col gap-5 mt-2">
            <div class="flex flex-col gap-2">
                <label
                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >Select Service</label
                >
                <Dropdown
                    v-model="selectedService"
                    :options="localServices"
                    optionLabel="name"
                    optionDisabled="_isPlaceholder"
                    placeholder="Search and select a service"
                    filter
                    filterMatchMode="contains"
                    :filterFields="['name', 'code']"
                    :loading="loading"
                    scrollHeight="280px"
                    :virtualScrollerOptions="{
                        lazy: true,
                        onLazyLoad,
                        itemSize: 62,
                        showLoader: false,
                        loading,
                    }"
                    @filter="onFilter"
                    class="w-full"
                >
                    <template #value="slotProps">
                        <div
                            v-if="slotProps.value"
                            class="flex items-center justify-between pr-2"
                        >
                            <span>{{ slotProps.value.name }}</span>
                        </div>
                        <span v-else>{{ slotProps.placeholder }}</span>
                    </template>

                    <template #option="slotProps">
                        <div
                            v-if="!slotProps.option._isPlaceholder"
                            class="flex flex-col py-1 transition-opacity duration-200"
                        >
                            <div
                                class="font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ slotProps.option.name }}
                            </div>
                            <div
                                class="text-sm text-gray-500 flex items-center gap-2 mt-1"
                            >
                                <span>Code: {{ slotProps.option.code }}</span>
                                <span>&bull;</span>
                                <span class="text-primary font-semibold"
                                    >${{
                                        Number(slotProps.option.price).toFixed(
                                            2,
                                        )
                                    }}</span
                                >
                            </div>
                        </div>
                        <div v-else class="flex h-[62px] flex-col justify-center gap-2 py-2">
                            <div class="h-3 w-2/3 animate-pulse rounded bg-surface-200 dark:bg-surface-700"></div>
                            <div class="h-2 w-1/2 animate-pulse rounded bg-surface-100 dark:bg-surface-800"></div>
                        </div>
                    </template>

                    <template #emptyfilter>
                        <div class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500">
                            <i v-if="loading" class="pi pi-spinner pi-spin"></i>
                            <span>{{ loading ? "Searching services..." : "No services found" }}</span>
                        </div>
                    </template>

                    <template #empty>
                        <div class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500">
                            <i v-if="loading" class="pi pi-spinner pi-spin"></i>
                            <span>{{ loading ? "Loading services..." : "No services available" }}</span>
                        </div>
                    </template>
                </Dropdown>
            </div>

            <div
                v-if="selectedService"
                class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm"
            >
                <span class="font-medium text-gray-900 dark:text-gray-100"
                    >Quantity</span
                >
                <div
                    class="flex items-center gap-3 bg-gray-100 dark:bg-gray-800 rounded-md p-1"
                >
                    <Button
                        icon="pi pi-minus"
                        text
                        size="small"
                        class="w-8 h-8 p-0"
                        @click="handleQuantityChange(quantity - 1)"
                        :disabled="quantity <= 1"
                    />
                    <span class="w-6 text-center font-semibold">{{
                        quantity
                    }}</span>
                    <Button
                        icon="pi pi-plus"
                        text
                        size="small"
                        class="w-8 h-8 p-0"
                        @click="handleQuantityChange(quantity + 1)"
                        :disabled="quantity >= maxQuantity"
                    />
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div
                    class="flex justify-between items-center mb-4 text-lg font-bold"
                >
                    <span>Total Cost:</span>
                    <span class="text-primary"
                        >${{ totalCost.toFixed(2) }}</span
                    >
                </div>

                <div class="flex gap-2">
                    <Button
                        label="Cancel"
                        severity="secondary"
                        outlined
                        class="w-1/3"
                        @click="visible = false"
                    />
                    <Button
                        label="Confirm Order"
                        icon="pi pi-check"
                        class="w-2/3"
                        :loading="isSubmitting"
                        @click="handleCheckout"
                        :disabled="!selectedService"
                    />
                </div>
            </div>
        </div>
    </Dialog>
</template>
