<script setup>
import { onMounted } from "vue";
import { useAuthStore } from "@/stores/authStore";

const authStore = useAuthStore();

onMounted(() => {
    authStore.hydrate();
});
</script>

<template>
    <router-view />
    <Toast class="!w-full !max-w-full !left-0 !right-0 px-2 sm:!w-[25rem] sm:!left-auto sm:!right-5 sm:px-0" />
</template>
