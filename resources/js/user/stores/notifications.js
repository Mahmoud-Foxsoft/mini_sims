import { defineStore } from "pinia";
import { apiRequest } from "@/services/api";
import { useToast } from "primevue/usetoast";

import {
    notificationsSupported,
    requestFirebaseToken,
    subscribeToForegroundMessages,
} from "@/services/notifications";

const toast = useToast();

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
        isAuthenticated: false,
        unsubscribes: [],
    }),
    getters: {
        canPrompt(state) {
            return (
                state.isAuthenticated &&
                notificationsSupported() &&
                state.permission === "default" &&
                !state.promptDismissed
            );
        },
    },
    actions: {
        setAuthenticated(value) {
            this.isAuthenticated = value;
        },
        async bootstrap() {
            if (this.initialized || !notificationsSupported()) {
                return;
            }
            this.permission = Notification.permission;
            this.initialized = true;
            const unsubscribe = await subscribeToForegroundMessages(
                (payload) => {
                    const title =
                        payload?.notification?.title ?? "New notification";
                    const body =
                        payload?.notification?.body ?? "You have a new update";
                    toast.add({
                        severity: "info",
                        summary: title,
                        detail: body,
                        life: 4000,
                    });
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
        async enableNotifications() {
            if (!notificationsSupported()) {
                return;
            }
            try {
                const permission = await Notification.requestPermission();
                this.permission = permission;
                if (permission !== "granted") {
                    useToast().error(
                        "Notifications are disabled in your browser settings.",
                    );
                    this.dismissPrompt();
                    return;
                }
                const token = await requestFirebaseToken();
                if (!token) {
                    useToast().error("Unable to register push notifications.");
                    return;
                }
                console.log(token);
                await this.registerToken(token);
                this.dismissPrompt();
                useToast().success("Push notifications enabled.");
            } catch (error) {
                console.error("Notification permission failed", error);
                useToast().error("Unable to enable notifications.");
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
