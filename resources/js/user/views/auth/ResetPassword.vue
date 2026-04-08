<script setup>
import { computed, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const route = useRoute();
const toast = useToast();
const authStore = useAuthStore();

const token = ref(route.query.token || '');
const email = ref(route.query.email || '');
const password = ref('');
const passwordConfirmation = ref('');
const loading = ref(false);
const canSubmit = computed(() => Boolean(token.value && email.value));

const submit = async () => {
    loading.value = true;
    try {
        await authStore.resetPassword({
            token: token.value,
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value
        });
        toast.add({ severity: 'success', summary: 'Password reset', detail: 'You can sign in now.', life: 4000 });
        router.push({ name: 'login' });
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Reset failed', detail: error.message, life: 4000 });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Set a new password</div>
                        <span class="text-muted-color font-medium">Set a new password for your account</span>
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <Message v-if="!canSubmit" severity="warn" class="mb-4">Missing reset token or email. Please use the link from your email.</Message>
                        <div v-else class="mb-6 text-sm text-muted-color">Resetting password for <span class="font-medium text-surface-900 dark:text-surface-0">{{ email }}</span></div>

                        <label for="reset-password" class="w-full text-start block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">New password</label>
                        <Password id="reset-password" v-model="password" placeholder="New password" :toggleMask="true" class="w-full mb-4" inputClass="w-full" :feedback="false" />

                        <label for="reset-password-confirm" class="w-full text-start block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Confirm password</label>
                        <Password id="reset-password-confirm" v-model="passwordConfirmation" placeholder="Confirm password" :toggleMask="true" class="w-full mb-6" inputClass="w-full" :feedback="false" />

                        <Button label="Update password" class="w-full" :loading="loading" :disabled="!canSubmit" @click="submit" />
                        <div class="text-sm text-center mt-6">
                            <router-link to="/auth/login" class="text-primary font-medium">Back to login</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
