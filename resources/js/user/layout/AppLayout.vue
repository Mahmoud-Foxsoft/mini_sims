<script setup>
import { computed, onMounted, onUnmounted } from "vue";
import { useLayout } from "@/layout/composables/layout";
import AppFooter from "./AppFooter.vue";
import AppSidebar from "./AppSidebar.vue";
import AppTopbar from "./AppTopbar.vue";
import CartDialog from "@/components/CartDialog.vue";
import { useToast } from "primevue";
import { useAuthStore } from "@/stores/authStore";
import Pusher from "pusher-js";
import Echo from "laravel-echo";

const { layoutConfig, layoutState, hideMobileMenu } = useLayout();
const toast = useToast();

const authStore = useAuthStore();
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
    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: "pusher",
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
        auth: {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                Accept: "application/json",
            },
        },
    });
    // Make sure we have a user before connecting
    if (authStore.user?.id) {
        const channel = window.Echo.private(`user.${authStore.user.id}`);

        channel
            .listen("order-placed", (event) => {
                console.log("Order event received!", event);
                toast.add({
                    severity: "success",
                    summary: "Order Success",
                    detail: `Order #${event.order.id} is processing.`,
                    life: 5000,
                });
            })
            .listen("payment-finished", (event) => {
                toast.add({
                    severity: "warn",
                    summary: "Number Cancelled",
                    detail: `Number ${event.phone} was cancelled.`,
                    life: 5000,
                });
            })
            .listen("message-received", (event) => {
                console.log("Message event received!", event);
                toast.add({
                    severity: "info",
                    summary: "New SMS",
                    detail: event.message,
                    life: 5000,
                });
            });
    }
});

onUnmounted(() => {
    // Always clean up the channel when the component is destroyed
    if (authStore.user?.id) {
        window.Echo.leave(`user.${authStore.user.id}`);
    }
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
    <CartDialog />
</template>
