<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "primevue/usetoast";
import { useAuthStore } from "@/stores/authStore";
import { getRecaptchaToken } from "@/services/recaptcha";

const router = useRouter();
const toast = useToast();
const authStore = useAuthStore();

const name = ref("");
const email = ref("");
const password = ref("");
const passwordConfirmation = ref("");
const accepted = ref(false);
const loading = ref(false);
const formErrors = ref({});
const formMessage = ref("");

const submit = async () => {
    loading.value = true;
    formErrors.value = {};
    formMessage.value = "";
    try {
        const recaptchaToken = await getRecaptchaToken("user_register");
        await authStore.register({
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
            terms: accepted.value,
            recaptcha_token: 'ghggf',
        });
        toast.add({
            severity: "success",
            summary: "Registration complete",
            detail: "Check your email for OTP.",
            life: 4000,
        });
        localStorage.setItem("pending_email", email.value);
        router.push({ name: "verifyEmail" });
    } catch (error) {
        const details = error.errors || {};
        formMessage.value = error.message;
        formErrors.value = details;
        toast.add({
            severity: "error",
            summary: "Registration failed",
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
        class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden"
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
                            Create your account
                        </div>
                        <span class="text-muted-color font-medium"
                            >Join the user dashboard</span
                        >
                    </div>

                    <div>
                        <Message
                            v-if="formMessage"
                            severity="error"
                            class="mb-4"
                            >{{ formMessage }}</Message
                        >
                        <label
                            for="register-name"
                            class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2"
                            >Name</label
                        >
                        <InputText
                            id="register-name"
                            type="text"
                            placeholder="Full name"
                            class="w-full md:w-[30rem] mb-6"
                            v-model="name"
                        />
                        <small
                            v-if="formErrors.name"
                            class="text-red-500 block -mt-4 mb-6"
                            >{{ formErrors.name[0] }}</small
                        >

                        <label
                            for="register-email"
                            class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2"
                            >Email</label
                        >
                        <InputText
                            id="register-email"
                            type="text"
                            placeholder="Email address"
                            class="w-full md:w-[30rem] mb-6"
                            v-model="email"
                        />
                        <small
                            v-if="formErrors.email"
                            class="text-red-500 block -mt-4 mb-6"
                            >{{ formErrors.email[0] }}</small
                        >

                        <label
                            for="register-password"
                            class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2"
                            >Password</label
                        >
                        <Password
                            id="register-password"
                            v-model="password"
                            placeholder="Password"
                            :toggleMask="true"
                            class="w-full mb-4"
                            inputClass="w-full"
                            :feedback="false"
                        />
                        <small
                            v-if="formErrors.password"
                            class="text-red-500 block -mt-2 mb-4"
                            >{{ formErrors.password[0] }}</small
                        >

                        <label
                            for="register-password-confirm"
                            class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2"
                            >Confirm password</label
                        >
                        <Password
                            id="register-password-confirm"
                            v-model="passwordConfirmation"
                            placeholder="Confirm password"
                            :toggleMask="true"
                            class="w-full mb-4"
                            inputClass="w-full"
                            :feedback="false"
                        />
                        <small
                            v-if="formErrors.password_confirmation"
                            class="text-red-500 block -mt-2 mb-4"
                            >{{ formErrors.password_confirmation[0] }}</small
                        >

                        <div class="flex items-center gap-2 mt-2 mb-8">
                            <Checkbox
                                v-model="accepted"
                                inputId="register-terms"
                                binary
                            />
                            <label
                                for="register-terms"
                                class="text-sm text-muted-color"
                                >I accept the terms</label
                            >
                        </div>
                        <small
                            v-if="formErrors.terms"
                            class="text-red-500 block -mt-6 mb-6"
                            >{{ formErrors.terms[0] }}</small
                        >
                        <Button
                            label="Create account"
                            :loading="loading"
                            class="w-full"
                            :disabled="!accepted"
                            @click="submit"
                        />
                        <div class="text-sm text-center mt-6">
                            <router-link
                                to="/auth/login"
                                class="text-primary font-medium"
                                >Already have an account? Sign in</router-link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
