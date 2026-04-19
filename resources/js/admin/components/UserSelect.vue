<script setup>
import { ref, watch, onMounted } from "vue";
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

const internalValue = ref(props.modelValue);
watch(() => props.modelValue, (newVal) => { internalValue.value = newVal; });
watch(internalValue, (newVal) => { emit("update:modelValue", newVal); });

const users = ref([]);
const loading = ref(false);
const totalRecords = ref(0);
const searchQuery = ref("");
const limit = 10;

let filterTimeout = null;

const loadUsers = async (page = 1, email = "") => {
    if (loading.value) return;
    loading.value = true;

    try {
        const params = new URLSearchParams({ page , search:true });
        if (email) {
            params.set("email", email); 
        }

        const response = await apiRequest(`/users?${params.toString()}`);
        const fetchedUsers = response.data || [];
        totalRecords.value = response.total || 0;

        if (page === 1) {
            users.value = Array.from({ length: totalRecords.value });
        }

        const start = (page - 1) * limit;
        const newUsers = [...users.value];
        fetchedUsers.forEach((user, index) => {
            newUsers[start + index] = user;
        });
        
        users.value = newUsers;
    } catch (error) {
        console.error("Failed to fetch users", error);
    } finally {
        loading.value = false;
    }
};

// Handle User Search Typing
const onFilter = (event) => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        searchQuery.value = event.value;
        loadUsers(1, searchQuery.value);
    }, 500); // 500ms debounce
};

// Handle Infinite Scroll Lazy Loading
const onLazyLoad = (event) => {
    const { first } = event; // 'first' is the index of the first visible item
    
    // Only load if the current chunk hasn't been fetched yet (is null)
    if (!users.value[first]) {
        const page = Math.floor(first / limit) + 1;
        loadUsers(page, searchQuery.value);
    }
};

onMounted(() => {
    loadUsers(1, "");
});
</script>

<template>
    <Dropdown
        v-model="internalValue"
        :options="users"
        optionLabel="name"
        optionValue="id"
        filter
        filterMatchMode="custom"
        scrollHeight="250px"
        :placeholder="placeholder"
        :loading="loading"
        class="w-full"
        :virtualScrollerOptions="{
            lazy: true,
            onLazyLoad: onLazyLoad,
            itemSize: 48,
            showLoader: true,
            loading: loading
        }"
        @filter="onFilter"
    >
        <template #option="slotProps">
            <div v-if="slotProps.option" class="flex flex-col">
                <span class="font-medium text-surface-900 dark:text-surface-0">{{ slotProps.option.name }}</span>
                <span class="text-xs text-gray-500">{{ slotProps.option.email }}</span>
            </div>
            <div v-else class="h-[48px] flex items-center text-gray-400">
                Loading...
            </div>
        </template>
    </Dropdown>
</template>