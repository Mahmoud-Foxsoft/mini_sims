<script setup>
import { ref, computed, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/services/api";
import { useAuthStore } from "@/stores/authStore";
import { useWsStore } from "@/stores/wsStore";


const visible = defineModel("visible", { type: Boolean, default: false });

const toast = useToast();
const authStore = useAuthStore();

const localServices = ref([]);
const loading = ref(false);
const isSubmitting = ref(false);

const selectedService = ref(null);
const quantity = ref(1);

const displayServices = computed(() => {
    return localServices.value;
});

const maxQuantity = computed(() => {
    return authStore.maxCartAmount || 5;
});

const totalCost = computed(() => {
    if (!selectedService.value) return 0;
    return Number(selectedService.value.price) * quantity.value;
});

const fetchServices = async (force = false) => {
    if (!force && localServices.value.length) return;

    loading.value = true;
    try {
        const response = await apiRequest(`/v1/services`);
        localServices.value = response.services || [];
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

watch(visible, (isNowVisible) => {
    if (isNowVisible) {
        fetchServices();
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
        fetchServices(true)
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

        await apiRequest("/v1/orders", {
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
                    :options="displayServices"
                    optionLabel="name"
                    placeholder="Search and select a service"
                    filter
                    :loading="loading"
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
                        <div class="flex flex-col py-1">
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
