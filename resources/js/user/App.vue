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
    <Toast />
</template>
