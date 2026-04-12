<script setup>
import { useCartStore } from '@/stores/cart';

const cartStore = useCartStore();
</script>

<template>
    <button 
        type="button" 
        class="layout-topbar-action relative" 
        @click="cartStore.toggleCart"
    >
        <i class="pi pi-shopping-cart" />
        
        <Badge 
            v-if="cartStore.totalItems > 0"
            :value="cartStore.totalItems" 
            severity="danger" 
            class="absolute -bottom-1 -right-1 shadow-sm border border-white dark:border-gray-900"
            style="min-width: 1.25rem; height: 1.25rem; line-height: 1.25rem; font-size: 0.75rem;"
        />
    </button>
</template>