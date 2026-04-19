import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { apiRequest } from "@/user/services/api";

const TOKEN_KEY = "user_token";
const USER_KEY = "user_profile";

export const useAuthStore = defineStore("authStore", () => {
    const token = ref(localStorage.getItem(TOKEN_KEY) || "");
    const user = ref(JSON.parse(localStorage.getItem(USER_KEY) || "null"));
    const baseMaxAmount = user.value?.max_pending_numbers || Number(import.meta.env.VITE_MAX_CART_AMOUNT) || 5;
    const maxCartAmount = ref(baseMaxAmount);
    const isAuthenticated = computed(() => Boolean(token.value));
    const setMaxCartAmount = (amountUsedByUser) => {
        const calculatedMax = baseMaxAmount - amountUsedByUser;
        maxCartAmount.value = calculatedMax < 0 ? 0 : calculatedMax;        
    };
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
            const profile = await apiRequest("/v1/me");
            setMaxCartAmount(profile.count_pending_numbers);
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
        setMaxCartAmount(data.count_pending_numbers);
        return data;
    };

    const register = async (payload) => {
        return apiRequest("/register", { method: "POST", body: payload });
    };

    const forgotPassword = async (payload) => {
        return apiRequest("/forgot-password", {
            method: "POST",
            body: payload,
        });
    };

    const resetPassword = async (payload) => {
        return apiRequest("/reset-password", { method: "POST", body: payload });
    };

    const verifyEmail = async (payload) => {
        return apiRequest("/verify-email", { method: "POST", body: payload });
    };

    const resendOtp = async (payload) => {
        return apiRequest("/resend-otp", { method: "POST", body: payload });
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
        const profile = await apiRequest("/v1/me");
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

    const rotateApiKey = async () => {
        return apiRequest("/api-key", { method: "POST" });
    };

    return {
        token,
        user,
        isAuthenticated,
        hydrate,
        login,
        register,
        forgotPassword,
        resetPassword,
        verifyEmail,
        resendOtp,
        logout,
        refreshToken,
        getProfile,
        updateProfile,
        rotateApiKey,
        maxCartAmount
    };
});
