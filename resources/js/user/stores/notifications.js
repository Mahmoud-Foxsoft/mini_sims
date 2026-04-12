import { defineStore } from "pinia";
// import { apiRequest } from "@/services/api";
import {
    notificationsSupported,
    requestFirebaseToken,
    subscribeToForegroundMessages,
} from "@/services/notifications";

const PROMPT_KEY = "user_notifications_prompt";
const TOKEN_KEY = "user_notifications_token";

export const useNotificationStore = defineStore("user-notifications", {
    state: () => ({
        permission:
            typeof window !== "undefined" && "Notification" in window
                ? Notification.permission
                : "default",
        promptDismissed:
            typeof window !== "undefined" &&
            window.localStorage.getItem(PROMPT_KEY) === "dismissed",
        token:
            typeof window !== "undefined"
                ? window.localStorage.getItem(TOKEN_KEY)
                : null,
        initialized: false,
        unsubscribes: [],
    }),
    getters: {
        canPrompt(state) {
            console.log(state.permission, state.promptDismissed);
            
            return (
                notificationsSupported() &&
                state.permission === "default" &&
                !state.promptDismissed
            );
        },
    },
    actions: {
        // 1. Pass the toast instance in here
        async bootstrap(toast) { 
            if (this.initialized || !notificationsSupported()) {
                return;
            }
            this.permission = Notification.permission;
            this.initialized = true;
            
            const unsubscribe = await subscribeToForegroundMessages(
                (payload) => {
                    const title = payload?.notification?.title ?? "New notification";
                    const body = payload?.notification?.body ?? "You have a new update";
                    
                    // 2. Safely use the passed toast instance
                    if (toast) {
                        toast.add({
                            severity: "info",
                            summary: title,
                            detail: body,
                            life: 4000,
                        });
                    }
                },
            );
            
            if (typeof unsubscribe === "function") {
                this.unsubscribes.push(unsubscribe);
            }
            if (this.permission === "granted") {
                await this.refreshToken();
            }
        },
        dismissPrompt() {
            this.promptDismissed = true;
            if (typeof window !== "undefined") {
                window.localStorage.setItem(PROMPT_KEY, "dismissed");
            }
        },
        // 3. Pass the toast instance in here as well
        async enableNotifications(toast) {
            if (!notificationsSupported()) {
                return;
            }
            try {
                const permission = await Notification.requestPermission();
                this.permission = permission;
                
                if (permission !== "granted") {
                    if (toast) {
                        toast.add({ severity: 'error', summary: 'Error', detail: "Notifications are disabled in your browser settings.", life: 4000 });
                    }
                    this.dismissPrompt();
                    return;
                }
                
                const token = await requestFirebaseToken();
                if (!token) {
                    if (toast) {
                        toast.add({ severity: 'error', summary: 'Error', detail: "Unable to register push notifications.", life: 4000 });
                    }
                    return;
                }
                
                console.log(token);
                await this.registerToken(token);
                this.dismissPrompt();
                
                if (toast) {
                    toast.add({ severity: 'success', summary: 'Success', detail: "Push notifications enabled.", life: 3000 });
                }
            } catch (error) {
                console.error("Notification permission failed", error);
                if (toast) {
                    toast.add({ severity: 'error', summary: 'Error', detail: "Unable to enable notifications.", life: 4000 });
                }
            }
        },
        async refreshToken() {
            try {
                const token = await requestFirebaseToken();
                if (token && token !== this.token) {
                    await this.registerToken(token);
                }
            } catch (error) {
                console.warn("Failed to refresh notification token", error);
            }
        },
        async registerToken(token) {
            // await apiRequest("/device-tokens", {
            //     method: "POST",
            //     body: { token, platform: "web" },
            // });
            this.token = token;
            if (typeof window !== "undefined") {
                window.localStorage.setItem(TOKEN_KEY, token);
            }
        },
        async unregisterToken() {
            if (!this.token) return;
            try {
                // await apiRequest("/device-tokens", {
                //     method: "DELETE",
                //     body: { token: this.token },
                // });
            } catch (error) {
                console.warn("Failed to remove notification token", error);
            } finally {
                if (typeof window !== "undefined") {
                    window.localStorage.removeItem(TOKEN_KEY);
                }
                this.token = null;
            }
        },
        cleanup() {
            this.unsubscribes.forEach((fn) => {
                if (typeof fn === "function") fn();
            });
            this.unsubscribes = [];
            this.initialized = false;
        },
    },
});