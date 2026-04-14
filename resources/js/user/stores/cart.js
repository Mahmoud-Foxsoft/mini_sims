import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';

export const useCartStore = defineStore('cart', () => {
    // 1. State
    const items = ref(JSON.parse(localStorage.getItem('cart')) || []);
    const latestServices = ref({}); 
    const isCartVisible = ref(false);
    
    // Parse Vite env variable to ensure it's a number (fallback to 5)
    const baseMaxAmount = Number(import.meta.env.VITE_MAX_CART_AMOUNT) || 5;
    const maxCartAmount = ref(baseMaxAmount);

    // 2. Watcher for persistence
    watch(items, (newItems) => {
        localStorage.setItem('cart', JSON.stringify(newItems));
    }, { deep: true });

    // 3. Actions
    
    const setMaxCartAmount = (amountUsedByUser) => {
        // 1. Set the new global limit
        const calculatedMax = baseMaxAmount - amountUsedByUser;
        maxCartAmount.value = calculatedMax < 0 ? 0 : calculatedMax;
        
        // 2. Check if the current total items exceed this new limit
        let currentTotal = items.value.reduce((sum, item) => sum + item.quantity, 0);
        let excess = currentTotal - maxCartAmount.value;

        if (excess > 0) {
            // 3. Shave off excess quantity starting from the last added item
            for (let i = items.value.length - 1; i >= 0; i--) {
                if (excess <= 0) break; // Stop when we've removed enough

                if (items.value[i].quantity <= excess) {
                    // Remove the whole item if its quantity is less than or equal to the excess
                    excess -= items.value[i].quantity;
                    items.value[i].quantity = 0; 
                } else {
                    // Just reduce the quantity if it can absorb the rest of the excess
                    items.value[i].quantity -= excess;
                    excess = 0;
                }
            }
            
            // 4. Clean up any items that hit 0 quantity
            items.value = items.value.filter(item => item.quantity > 0);
        }
    };

    const syncLatestPrices = (servicesArray) => {
        const catalog = {};
        servicesArray.forEach(service => {
            catalog[service.code] = service;
        });
        latestServices.value = catalog;
    };

    const addToCart = (service, quantity = 1) => {
        const code = service.code;
        
        // Calculate how much room is left in the entire cart
        const currentTotal = items.value.reduce((sum, item) => sum + item.quantity, 0);
        const availableSpace = maxCartAmount.value - currentTotal;
        
        // Stop if cart is completely full
        if (availableSpace <= 0) return; 

        // Only add up to the available space
        const amountToAdd = quantity > availableSpace ? availableSpace : quantity;

        const existingItem = items.value.find(item => item.code === code);
        
        if (existingItem) {
            existingItem.quantity += amountToAdd;
        } else {
            items.value.push({ code, quantity: amountToAdd });
        }
        
        if (!latestServices.value[code]) {
            latestServices.value[code] = service;
        }
    };

    const removeFromCart = (code) => {
        items.value = items.value.filter(item => item.code !== code);
    };

    const updateQuantity = (code, quantity) => {
        const item = items.value.find(i => i.code === code);
        if (!item) return;

        if (quantity <= 0) {
            removeFromCart(code);
            return;
        }

        // Calculate total of ALL OTHER items to see how much space THIS item has
        const totalOfOtherItems = items.value
            .filter(i => i.code !== code)
            .reduce((sum, i) => sum + i.quantity, 0);
            
        const availableSpace = maxCartAmount.value - totalOfOtherItems;

        // Cap the item at the available space, or the requested quantity
        item.quantity = quantity > availableSpace ? availableSpace : quantity;
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
            const price = details.price !== undefined ? details.price : 0; 
            
            return {
                ...item,
                name: details.name || 'Unknown Service',
                price: price,
                totalPrice: price * item.quantity,
                // We map this for UI constraints (e.g. disabling the + button)
                max_cart_amount: maxCartAmount.value 
            };
        });
    });

    const cartTotal = computed(() => {
        return populatedCart.value.reduce((total, item) => total + item.totalPrice, 0);
    });

    return {
        items,
        isCartVisible,
        maxCartAmount,
        setMaxCartAmount,
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