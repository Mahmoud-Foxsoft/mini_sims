<script setup>
import { computed, onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/admin/services/api";
import { formatDate } from "@/admin/services/date";

const toast = useToast();

const loadingHome = ref(false);
const loadingReport = ref(false);

const homeTotals = ref([]);
const reportTotals = ref([]);
const reportLoaded = ref(false);

const today = new Date();
const reportFilters = ref({
    from: new Date(today.getFullYear(), today.getMonth(), 1),
    to: today,
});

const reportMeta = computed(() => {
    const from = formatDate(reportFilters.value.from);
    const to = formatDate(reportFilters.value.to);

    if (!from || !to) {
        return "";
    }

    return `${from} to ${to}`;
});

const fetchHomeTotals = async () => {
    loadingHome.value = true;
    try {
        const data = await apiRequest("/home");
        homeTotals.value = data.totals || [];
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load dashboard",
            detail: error.message,
            life: 4000,
        });
    } finally {
        loadingHome.value = false;
    }
};

const runReport = async () => {
    const from = formatDate(reportFilters.value.from);
    const to = formatDate(reportFilters.value.to);

    if (!from || !to) {
        toast.add({
            severity: "warn",
            summary: "Invalid range",
            detail: "Please select both from and to dates.",
            life: 3000,
        });
        return;
    }

    reportLoaded.value = false;
    reportTotals.value = [];
    loadingReport.value = true;
    try {
        const params = new URLSearchParams();
        params.set("filters[from]", from);
        params.set("filters[to]", to);

        const data = await apiRequest(`/reports?${params.toString()}`);
        reportTotals.value = data.totals || [];
        reportLoaded.value = true;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Report failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        loadingReport.value = false;
    }
};

onMounted(() => {
    fetchHomeTotals();
    runReport();
});
</script>

<template>
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-semibold">Admin Dashboard</h1>
            <p class="text-gray-600">Track monthly metrics and run period reports.</p>
        </div>

        <Card class="shadow-sm">
            <template #title>This Month</template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <Card
                        v-for="(total, index) in homeTotals"
                        :key="`home-${total.key}-${index}`"
                        class="shadow-sm border border-surface-200"
                    >
                        <template #content>
                            <p class="text-sm text-gray-500">{{ total.key }}</p>
                            <h3 class="text-xl font-semibold mt-2">{{ total.value }}</h3>
                        </template>
                    </Card>

                    <Card
                        v-if="loadingHome"
                        v-for="n in 6"
                        :key="`home-loading-${n}`"
                        class="shadow-sm border border-surface-200"
                    >
                        <template #content>
                            <Skeleton width="70%" height="1rem" class="mb-3" />
                            <Skeleton width="40%" height="1.5rem" />
                        </template>
                    </Card>
                </div>
            </template>
        </Card>

        <Card class="shadow-sm">
            <template #title>Reports Search</template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end mb-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-medium">From</label>
                        <Calendar
                            v-model="reportFilters.from"
                            dateFormat="yy-mm-dd"
                            showIcon
                            class="w-full"
                            inputClass="w-full"
                            placeholder="YYYY-MM-DD"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-medium">To</label>
                        <Calendar
                            v-model="reportFilters.to"
                            dateFormat="yy-mm-dd"
                            showIcon
                            class="w-full"
                            inputClass="w-full"
                            placeholder="YYYY-MM-DD"
                        />
                    </div>
                    <div>
                        <Button
                            label="Search"
                            icon="pi pi-search"
                            :loading="loadingReport"
                            @click="runReport"
                        />
                    </div>
                </div>

                <div v-if="reportLoaded" class="mb-3">
                    <p class="text-sm text-gray-500">
                        Report totals for <span class="font-medium text-gray-700">{{ reportMeta }}</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <Card
                        v-for="(total, index) in reportTotals"
                        :key="`report-${total.key}-${index}`"
                        class="shadow-sm border border-surface-200"
                    >
                        <template #content>
                            <p class="text-sm text-gray-500">{{ total.key }}</p>
                            <h3 class="text-xl font-semibold mt-2">{{ total.value }}</h3>
                        </template>
                    </Card>

                    <Card
                        v-if="loadingReport"
                        v-for="n in 7"
                        :key="`report-loading-${n}`"
                        class="shadow-sm border border-surface-200"
                    >
                        <template #content>
                            <Skeleton width="70%" height="1rem" class="mb-3" />
                            <Skeleton width="40%" height="1.5rem" />
                        </template>
                    </Card>
                </div>
            </template>
        </Card>
    </div>
</template>
