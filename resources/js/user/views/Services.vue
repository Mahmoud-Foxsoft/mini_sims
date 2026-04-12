<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/services/api";
import { useCartStore } from "@/stores/cart"; // <-- Import the cart store

const toast = useToast();
const cartStore = useCartStore(); // <-- Initialize the store
const loading = ref(false);
const services = ref([]);

// Filters
const nameFilter = ref(null);
const codeFilter = ref(null);
const priceFilter = ref(null);
const availableFilter = ref(null);

const availableOptions = [
    { label: "All", value: null },
    { label: "Available", value: true },
    { label: "Unavailable", value: false },
];

const buildQuery = () => {
    const params = new URLSearchParams();
    
    if (nameFilter.value) {
        params.set("filters[name]", nameFilter.value);
    }
    if (codeFilter.value) {
        params.set("filters[code]", codeFilter.value);
    }
    if (priceFilter.value !== null && priceFilter.value !== '') {
        params.set("filters[price]", String(priceFilter.value));
    }
    if (availableFilter.value !== null) {
        params.set("filters[available]", String(availableFilter.value));
    }
    
    return params.toString();
};

const fetchServices = async () => {
    loading.value = true;
    try {
        const query = buildQuery();
        const response = await apiRequest(`/v1/services?${query}`);
        
        const rawServices = response.services || [];
        
        // Add a local _cartQty property to each service for the UI controls
        services.value = rawServices.map(service => ({
            ...service,
            _cartQty: 1 
        }));

        // Sync the latest prices to the cart store immediately
        cartStore.syncLatestPrices(services.value);

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
    fetchServices();
};

const clearFilters = () => {
    nameFilter.value = null;
    codeFilter.value = null;
    priceFilter.value = null;
    availableFilter.value = null;
    fetchServices();
};

const getAvailabilitySeverity = (isAvailable) => {
    return isAvailable ? "success" : "secondary";
};

// Handle adding to cart and resetting the row's quantity counter
const handleAddToCart = (service) => {
    cartStore.addToCart(service, service._cartQty);
    
    toast.add({
        severity: "success",
        summary: "Added to Cart",
        detail: `${service._cartQty}x ${service.name} added to your cart.`,
        life: 3000,
    });

    // Reset the counter back to 1 for the next time they want to add
    service._cartQty = 1; 
};

onMounted(() => fetchServices());
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Services</h2>
                <p class="text-gray-600">Browse and manage available phone services.</p>
            </div>
        </div>

        <Card class="shadow-sm">
            <template #content>
                <div class="flex flex-col gap-3 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
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
                                placeholder="e.g., nf" 
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
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-sm">Availability</label>
                            <Dropdown
                                v-model="availableFilter"
                                :options="availableOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Any Status"
                                class="w-full"
                            />
                        </div>
                        <div class="flex items-center gap-2">
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
                    :value="services"
                    :loading="loading"
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

                    <Column field="available" header="Status" style="min-width: 10rem">
                        <template #body="{ data }">
                            <Tag
                                :value="data.available ? 'Available' : 'Unavailable'"
                                :severity="getAvailabilitySeverity(data.available)"
                            />
                        </template>
                    </Column>

                    <Column header="Actions" alignFrozen="right" style="min-width: 10rem">
                        <template #body="{ data }">
                            <div v-if="data.available" class="flex flex-col items-center gap-2 w-full max-w-[8rem]">
                                <div class="flex items-center justify-between w-full bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 p-1">
                                    <Button 
                                        icon="pi pi-minus" 
                                        text 
                                        size="small" 
                                        class="w-8 h-8 p-0" 
                                        :disabled="data._cartQty <= 1"
                                        @click="data._cartQty > 1 ? data._cartQty-- : null" 
                                    />
                                    <span class="font-semibold text-sm">{{ data._cartQty }}</span>
                                    <Button 
                                        icon="pi pi-plus" 
                                        text 
                                        size="small" 
                                        class="w-8 h-8 p-0" 
                                        @click="data._cartQty++" 
                                    />
                                </div>
                                
                                <Button 
                                    label="Add" 
                                    icon="pi pi-shopping-cart" 
                                    size="small"
                                    class="w-full"
                                    @click="handleAddToCart(data)"
                                />
                            </div>
                            <div v-else class="text-sm text-gray-400 italic flex items-center justify-center h-full">
                                Not Available
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
                                Try adjusting your filters or check back later.
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
    </div>
</template>