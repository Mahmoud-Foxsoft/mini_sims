import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';

export const useCartStore = defineStore('cart', () => {
    // 1. State
    const items = ref(JSON.parse(localStorage.getItem('cart')) || []);
    const latestServices = ref({}); // Maps service IDs to their latest data
    const isCartVisible = ref(false);

    // 2. Watcher for persistence
    watch(items, (newItems) => {
        localStorage.setItem('cart', JSON.stringify(newItems));
    }, { deep: true });

    // 3. Actions
    const syncLatestPrices = (servicesArray) => {
        const catalog = {};
        servicesArray.forEach(service => {
            catalog[service.code] = service;
        });
        latestServices.value = catalog;
    };

    const addToCart = (service, quantity = 1) => {
        const code = service.code;
        const existingItem = items.value.find(item => item.code === code);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            items.value.push({ code, quantity });
        }
        
        // Temporarily cache the service name/price so it displays nicely 
        // even before the next API sync runs.
        if (!latestServices.value[code]) {
            latestServices.value[code] = service;
        }
        
        // isCartVisible.value = true; // Auto-open cart when adding items
    };

    const removeFromCart = (code) => {
        items.value = items.value.filter(item => item.code !== code);
    };

    const updateQuantity = (code, quantity) => {
        const item = items.value.find(i => i.code === code);
        if (item) {
            if (quantity <= 0) removeFromCart(code);
            else item.quantity = quantity;
        }
    };

    const clearCart = () => {
        items.value = [];
    };

    const toggleCart = () => isCartVisible.value = !isCartVisible.value;

    // 4. Getters
    const totalItems = computed(() => {
        return items.value.reduce((total, item) => total + item.quantity, 0);
    });

    const populatedCart = computed(() => {
        return items.value.map(item => {
            const details = latestServices.value[item.code] || {};
            // Make sure to handle cents/dollars depending on your API structure
            const price = details.price !== undefined ? details.price : 0; 
            
            return {
                ...item,
                name: details.name || 'Unknown Service',
                price: price,
                totalPrice: price * item.quantity
            };
        });
    });

    const cartTotal = computed(() => {
        return populatedCart.value.reduce((total, item) => total + item.totalPrice, 0);
    });

    return {
        items,
        isCartVisible,
        syncLatestPrices,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        toggleCart,
        totalItems,
        populatedCart,
        cartTotal
    };
});