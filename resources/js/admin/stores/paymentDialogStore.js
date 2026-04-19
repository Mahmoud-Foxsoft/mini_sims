import { defineStore } from 'pinia';
import { ref } from 'vue';

export const usePaymentDialogStore = defineStore('paymentDialogStore', () => {
    const visible = ref(false);
    const refreshKey = ref(0);

    const open = () => {
        visible.value = true;
    };

    const close = () => {
        visible.value = false;
    };

    const notifyCreated = () => {
        refreshKey.value += 1;
    };

    return {
        visible,
        refreshKey,
        open,
        close,
        notifyCreated
    };
});
