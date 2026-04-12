<script setup>
import { ref } from 'vue';
import { useCartStore } from '@/stores/cart';
import { useToast } from 'primevue/usetoast';
import { apiRequest } from '@/services/api';

const cartStore = useCartStore();
const toast = useToast();
const isCheckingOut = ref(false);

const handleCheckout = async () => {
    if (cartStore.items.length === 0) return;
    
    isCheckingOut.value = true;
    try {
        // Send ONLY IDs and Quantities to the backend for verification
        const payload = {
            cart: cartStore.items.map(item => ({
                service_code: item.code,
                quantity: item.quantity
            }))
        };

        // Adjust route to match your actual checkout endpoint
        await apiRequest('/v1/orders', {
            method: 'POST',
            body: payload
        });

        toast.add({
            severity: 'success',
            summary: 'Order Placed',
            detail: 'Your services have been ordered successfully.',
            life: 4000
        });

        cartStore.clearCart();
        cartStore.isCartVisible = false; // Close dialog
        
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Checkout Failed',
            detail: error.message,
            life: 4000
        });
    } finally {
        isCheckingOut.value = false;
    }
};
</script>

<template>
    <Dialog 
        v-model:visible="cartStore.isCartVisible" 
        header="Your Cart" 
        :draggable="true"
        class="w-full max-w-md mx-4"
    >
        <div v-if="cartStore.items.length === 0" class="flex flex-col items-center py-8 text-gray-500">
            <i class="pi pi-shopping-cart text-4xl mb-3 text-gray-300"></i>
            <p>Your cart is empty.</p>
        </div>

        <div v-else class="flex flex-col gap-4 mt-2">
            <div class="flex flex-col gap-3 max-h-[60vh] overflow-y-auto px-1">
                <div 
                    v-for="item in cartStore.populatedCart" 
                    :key="item.code" 
                    class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm"
                >
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ item.name }}</span>
                        <span class="text-sm text-gray-500">${{ Number(item.price).toFixed(2) }} each</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-md p-1">
                            <Button icon="pi pi-minus" text size="small" class="w-6 h-6 p-0" @click="cartStore.updateQuantity(item.code, item.quantity - 1)" />
                            <span class="w-4 text-center text-sm font-semibold">{{ item.quantity }}</span>
                            <Button icon="pi pi-plus" text size="small" class="w-6 h-6 p-0" @click="cartStore.updateQuantity(item.code, item.quantity + 1)" />
                        </div>
                        
                        <div class="flex flex-col items-end min-w-[3rem]">
                            <span class="font-semibold text-primary">${{ Number(item.totalPrice).toFixed(2) }}</span>
                            <Button 
                                icon="pi pi-trash" 
                                text 
                                severity="danger" 
                                size="small" 
                                class="h-6 p-0" 
                                @click="cartStore.removeFromCart(item.id)" 
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                <div class="flex justify-between items-center mb-4 text-lg font-bold">
                    <span>Total:</span>
                    <span class="text-primary-600">${{ Number(cartStore.cartTotal).toFixed(2) }}</span>
                </div>
                
                <div class="flex gap-2">
                    <Button label="Clear Cart" severity="secondary" outlined class="w-full" @click="cartStore.clearCart" />
                    <Button label="Checkout" icon="pi pi-check" class="w-full" :loading="isCheckingOut" @click="handleCheckout" />
                </div>
            </div>
        </div>
    </Dialog>
</template>