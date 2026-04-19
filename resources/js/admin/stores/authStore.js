import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { apiRequest } from "@/admin/services/api";

const TOKEN_KEY = "admin_token";
const USER_KEY = "admin_profile";

export const useAuthStore = defineStore("authStore", () => {
    const token = ref(localStorage.getItem(TOKEN_KEY) || "");
    const user = ref(JSON.parse(localStorage.getItem(USER_KEY) || "null"));
    const isAuthenticated = computed(() => Boolean(token.value));
    const setSession = (newToken, newUser) => {
        token.value = newToken || "";
        user.value = newUser || null;
        if (token.value) localStorage.setItem(TOKEN_KEY, token.value);
        else localStorage.removeItem(TOKEN_KEY);

        if (user.value)
            localStorage.setItem(USER_KEY, JSON.stringify(user.value));
        else localStorage.removeItem(USER_KEY);
    };

    const hydrate = async () => {
        if (!token.value) return;
        try {
            const profile = await apiRequest("/me");
            setSession(token.value, profile);
        } catch (error) {
            setSession("", null);
        }
    };

    const login = async (payload) => {
        const data = await apiRequest("/login", {
            method: "POST",
            body: payload,
        });
        setSession(data.token, data);
        return data;
    };

    const logout = async () => {
        try {
            await apiRequest("/logout", { method: "POST" });
        } catch (error) {
            // ignore
        }
        setSession("", null);
    };

    const refreshToken = async () => {
        const data = await apiRequest("/refresh-token", { method: "POST" });
        setSession(data.token, data);
        return data;
    };

    const getProfile = async () => {
        const profile = await apiRequest("/me");
        setSession(token.value, profile);
        return profile;
    };

    const updateProfile = async (payload) => {
        const updated = await apiRequest("/me", {
            method: "PUT",
            body: payload,
        });
        setSession(token.value, updated);
        return updated;
    };


    return {
        token,
        user,
        isAuthenticated,
        hydrate,
        login,
        logout,
        refreshToken,
        getProfile,
        updateProfile,
    };
});
