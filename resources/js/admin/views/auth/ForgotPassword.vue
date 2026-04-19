<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "primevue/usetoast";
import { useAuthStore } from "@/user/stores/authStore";
import { getRecaptchaToken, renderRecaptcha, resetRecaptcha, clearRecaptcha } from "@/user/services/recaptcha";

const router = useRouter();
const toast = useToast();
const authStore = useAuthStore();

const email = ref("");
const loading = ref(false);

// Mount and unmount logic for the widget
onMounted(() => {
    renderRecaptcha('recaptcha-container');
});

onUnmounted(() => {
    clearRecaptcha();
});

const submit = async () => {
    loading.value = true;

    // Grab the token synchronously
    const recaptchaToken = getRecaptchaToken();

    // Prevent submission if the user hasn't completed the captcha
    if (!recaptchaToken) {
        toast.add({ severity: 'warn', summary: 'Verification required', detail: 'Please complete the reCAPTCHA.', life: 4000 });
        loading.value = false;
        return;
    }

    try {
        await authStore.forgotPassword({
            email: email.value,
            recaptcha_token: recaptchaToken, // Pass the real token
        });
        toast.add({
            severity: "success",
            summary: "Email sent",
            detail: "Check your inbox for reset link.",
            life: 4000,
        });
        router.push({ name: "login" });
    } catch (error) {
        // Reset the widget if the request fails
        resetRecaptcha();

        toast.add({
            severity: "error",
            summary: "Request failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div
        class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen max-w-dvw overflow-hidden"
    >
        <div class="flex flex-col items-center justify-center">
            <div
                style="
                    border-radius: 56px;
                    padding: 0.3rem;
                    background: linear-gradient(
                        180deg,
                        var(--primary-color) 10%,
                        rgba(33, 150, 243, 0) 30%
                    );
                "
            >
                <div
                    class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20"
                    style="border-radius: 53px"
                >
                    <div class="text-center mb-8">
                        <div
                            class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4"
                        >
                            Reset your password
                        </div>
                        <span class="text-muted-color font-medium"
                            >We will send you a reset link</span
                        >
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <label
                            for="forgot-email"
                            class="w-full text-start block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2"
                            >Email</label
                        >
                        <InputText
                            id="forgot-email"
                            v-model="email"
                            type="email"
                            placeholder="Email address"
                            class="w-full md:w-[30rem] mb-6"
                        />

                        <div class="flex justify-center mb-6 mt-2 w-full">
                            <div id="recaptcha-container"></div>
                        </div>

                        <Button
                            label="Send reset link"
                            class="w-full"
                            :loading="loading"
                            @click="submit"
                        />
                        <div class="text-sm text-center mt-6">
                            <router-link
                                to="/auth/login"
                                class="text-primary font-medium"
                                >Back to login</router-link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
