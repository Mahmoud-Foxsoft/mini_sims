<script setup>
import { computed, onMounted, ref } from "vue";
import { apiRequest } from "@/admin/services/api";

const props = defineProps({
    modelValue: {
        type: [String, Number, Object],
        default: null,
    },
    placeholder: {
        type: String,
        default: "Select a user",
    },
});

const emit = defineEmits(["update:modelValue"]);

// 1. FIXED: Use a computed property for v-model instead of error-prone double watchers
const internalValue = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const users = ref([]);
const loading = ref(false);
const totalRecords = ref(0);
const totalPages = ref(0);
const searchQuery = ref("");
const limit = 20;

// Track loaded and loading pages to prevent duplicate API spam when scrolling fast
const loadedPages = new Set();
const loadingPages = new Set();
let filterTimeout = null;

const normalizeQuery = (value = "") => String(value).trim();

// 2. FIXED: PrimeVue's local filter will hide `null` items and crash. 
// We must initialize dummy objects that match the current search query so they stay visible in the DOM.
const initVirtualRows = (count) => {
    users.value = Array.from({ length: count }, () => ({
        id: null,
        name: searchQuery.value, 
        email: searchQuery.value,
        _isPlaceholder: true, // Custom flag to render a skeleton loader
    }));
};

const loadPage = async (page = 1, email = "") => {
    if (page < 1) return;
    if (totalPages.value > 0 && page > totalPages.value) return;
    
    // 3. FIXED: Prevent race conditions by checking if the page is already fetched or currently fetching
    if (loadedPages.has(page) || loadingPages.has(page)) return;

    loadingPages.add(page);
    loading.value = true;

    try {
        const params = new URLSearchParams();
        params.set("select", "1");
        params.set("page", String(page));
        params.set("per_page", String(limit));

        if (email.trim()) {
            params.set("email", email.trim());
        }

        const response = await apiRequest(`/users?${params.toString()}`);

        const fetchedUsers = response.data || [];
        totalRecords.value = response.total || 0;
        totalPages.value =
            Number(response.last_page || 0) ||
            (totalRecords.value > 0 ? Math.ceil(totalRecords.value / limit) : 0);

        if (!users.value.length || users.value.length !== totalRecords.value) {
            initVirtualRows(totalRecords.value);
        }

        const start = (page - 1) * limit;
        const nextUsers = [...users.value];

        fetchedUsers.forEach((user, index) => {
            nextUsers[start + index] = user;
        });

        users.value = nextUsers;
        loadedPages.add(page); // Mark page as successfully loaded
    } catch {
        // silent, keep dropdown usable
    } finally {
        loadingPages.delete(page);
        // Only remove the loading spinner if no other pages are currently loading
        if (loadingPages.size === 0) {
            loading.value = false;
        }
    }
};

const resetAndLoad = (email = "") => {
    const normalized = normalizeQuery(email);
    searchQuery.value = normalized;
    totalRecords.value = 0;
    totalPages.value = 0;
    users.value = [];
    loadedPages.clear();  // Reset tracking state
    loadingPages.clear(); // Reset tracking state
    
    loadPage(1, normalized);
};

const onFilter = (event) => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        const nextQuery = normalizeQuery(event.value || "");
        resetAndLoad(nextQuery);
    }, 300);
};

const onLazyLoad = (event) => {
    const first = Number(event.first || 0);
    const last = Number(event.last || first + limit);

    if (totalRecords.value > 0 && first >= totalRecords.value) {
        return;
    }

    const firstPage = Math.floor(first / limit) + 1;
    const lastPage = Math.floor(Math.max(last - 1, 0) / limit) + 1;
    const maxPage = totalPages.value;

    if (maxPage > 0 && firstPage > maxPage) {
        return;
    }

    const upperPage = maxPage > 0 ? Math.min(lastPage, maxPage) : lastPage;

    // The loadPage concurrency check now makes this loop safe
    for (let page = firstPage; page <= upperPage; page += 1) {
        loadPage(page, searchQuery.value);
    }
};

onMounted(() => {
    if (users.value.length === 0) {
        resetAndLoad("");
    }
});
</script>

<template>
    <Dropdown
        v-model="internalValue"
        :options="users"
        optionLabel="name"
        optionValue="id"
        filter
        filterMatchMode="contains"
        :filterFields="['name','email']"
        scrollHeight="250px"
        :placeholder="placeholder"
        :loading="loading"
        class="w-full"
        :virtualScrollerOptions="{
            lazy: true,
            onLazyLoad,
            itemSize: 48,
            showLoader: false,
            loading,
        }"
        @filter="onFilter"
    >
        <template #option="slotProps">
            <div v-if="slotProps.option && !slotProps.option._isPlaceholder" class="flex flex-col">
                <span class="font-medium text-surface-900 dark:text-surface-0">{{ slotProps.option.name }}</span>
                <span class="text-xs text-gray-500">{{ slotProps.option.email }}</span>
            </div>
            
            <div v-else class="flex flex-col justify-center h-[48px] w-full gap-2">
                <div class="h-3 bg-surface-200 dark:bg-surface-700 rounded w-3/4 animate-pulse"></div>
                <div class="h-2 bg-surface-100 dark:bg-surface-800 rounded w-1/2 animate-pulse"></div>
            </div>
        </template>
    </Dropdown>
</template>