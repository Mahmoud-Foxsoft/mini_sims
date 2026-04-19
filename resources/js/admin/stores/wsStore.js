import { defineStore } from "pinia";
import { useToast } from "primevue";
import Pusher from "pusher-js";
import { ref, computed } from "vue";
import { useAuthStore } from "./authStore";
import Echo from "laravel-echo";

export const useWsStore = defineStore("wsStore", () => {
    const toast = useToast();
    const paymentLastUpdated = ref(null);
    const setPaymentLastUpdated = () => {
        paymentLastUpdated.value = Date.now();
    };
    const servicesLastUpdated = ref(null);
    const setServicesLastUpdated = () => {
        servicesLastUpdated.value = Date.now();
    };
    const messageReceived = ref(null);
    const setMessageReceived = (value) => {
        messageReceived.value = value;
    };

    const phoneRefundedLastUpdated = ref(null);
    const setPhoneRefundedLastUpdated = () => {
        phoneRefundedLastUpdated.value = Date.now();
    };

    const authStore = useAuthStore();
    const init = () => {
        const id = authStore.user?.id;
        if (!id) return;
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: "pusher",
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
            auth: {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("user_token")}`,
                    Accept: "application/json",
                },
            },
        });
        const globalChannel = window.Echo.private("global");
        const channel = window.Echo.private(`user.${id}`);
        globalChannel.listen(".services-updated", (event) => {
            setServicesLastUpdated();
        });
        channel
            .listen(".payment-finished", (event) => {
                toast.add({
                    severity: "success",
                    summary: "Payment Finished",
                    detail: `Your payment of ${event.payment.amount} was successful`,
                    life: 5000,
                });
                authStore.hydrate();
                setPaymentLastUpdated();
            })
            .listen(".message-received", (event) => {
                toast.add({
                    severity: "info",
                    summary: "New SMS",
                    detail: `New SMS received for Service ${event.message.order_item.service_name} from ${event.message.order_item.phone_number}`,
                    life: 5000,
                });
                setMessageReceived(event.message);
                authStore.maxCartAmount.value -= 1;
            })
            .listen(".phone-numbers-refunded", (event) => {
                const count = event.refund.count;
                const s = count > 1 ? "s" : "";
                toast.add({
                    severity: "info",
                    summary: "Phone Number Refunded",
                    detail: `${count} Phone number${s} was timeout refunded`,
                    life: 5000,
                });
                authStore.hydrate();
                setPhoneRefundedLastUpdated();
            });
    };

    const leave = () => {
        if (authStore.user?.id) {
            window.Echo.leave(`user.${authStore.user.id}`);
        }
    };

    return {
        paymentLastUpdated,
        messageReceived,
        phoneRefundedLastUpdated,
        servicesLastUpdated,
        init,
        leave,
    };
});
