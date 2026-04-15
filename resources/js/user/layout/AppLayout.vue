<script setup>
import { computed, onMounted, onUnmounted } from "vue";
import { useLayout } from "@/layout/composables/layout";
import AppFooter from "./AppFooter.vue";
import AppSidebar from "./AppSidebar.vue";
import AppTopbar from "./AppTopbar.vue";
import { useWsStore } from "@/stores/wsStore";

const { layoutConfig, layoutState, hideMobileMenu } = useLayout();

const wsStore = useWsStore();
const containerClass = computed(() => {
    return {
        "layout-overlay": layoutConfig.menuMode === "overlay",
        "layout-static": layoutConfig.menuMode === "static",
        "layout-overlay-active": layoutState.overlayMenuActive,
        "layout-mobile-active": layoutState.mobileMenuActive,
        "layout-static-inactive": layoutState.staticMenuInactive,
    };
});

onMounted(() => {
   wsStore.init();
});

onUnmounted(() => {
    wsStore.leave();
});
</script>

<template>
    <div class="layout-wrapper" :class="containerClass">
        <AppTopbar />
        <AppSidebar />
        <div class="layout-main-container">
            <div class="layout-main">
                <router-view />
            </div>
            <AppFooter />
        </div>
        <div class="layout-mask" @click="hideMobileMenu" />
    </div>
    <ConfirmDialog group="confirm-dialog"></ConfirmDialog>
    <ConfirmPopup group="confirm-popup"></ConfirmPopup>
</template>
