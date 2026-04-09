<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { useAuthStore } from "@/stores/authStore";

const toast = useToast();
const authStore = useAuthStore();

const loading = ref(false);
const saving = ref(false);
const apiKey = ref("");
const apiKeyDialog = ref(false);

const form = ref({
    name: "",
    password: "",
    password_confirmation: "",
    webhook_url: "",
});

const loadProfile = async () => {
    loading.value = true;
    try {
        const profile = await authStore.getProfile();
        form.value.name = profile.name || "";
        form.value.webhook_url = profile.webhook_url || "";
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load profile",
            detail: error.message,
            life: 4000,
        });
    } finally {
        loading.value = false;
    }
};

const updateProfile = async () => {
    saving.value = true;
    try {
        const payload = { name: form.value.name, webhook_url: form.value.webhook_url };
        if (form.value.password) {
            payload.password = form.value.password;
            payload.password_confirmation = form.value.password_confirmation;
        }
        await authStore.updateProfile(payload);
        toast.add({
            severity: "success",
            summary: "Profile updated",
            detail: "Changes saved.",
            life: 3000,
        });
        form.value.password = "";
        form.value.password_confirmation = "";
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Update failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        saving.value = false;
    }
};

const rotateApiKey = async () => {
    try {
        const data = await authStore.rotateApiKey();
        apiKey.value = data.api_key || "";
        apiKeyDialog.value = true;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Rotation failed",
            detail: error.message,
            life: 4000,
        });
    }
};

const copyApiKey = async () => {
    if (!apiKey.value) return;
    await navigator.clipboard.writeText(apiKey.value);
    toast.add({
        severity: "success",
        summary: "Copied",
        detail: "API key copied.",
        life: 2000,
    });
};

onMounted(loadProfile);
</script>

<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold">Profile</h2>
            <p class="text-gray-600">Manage your account details.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <Card class="lg:col-span-2 shadow-sm">
                <template #content>
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-4 md:gap-2 md:flex-row">
                            <div class="flex flex-col gap-2 flex-1">
                                <label class="font-medium" for="profile-name"
                                    >Name</label
                                >
                                <InputText
                                    id="profile-name"
                                    v-model="form.name"
                                    :disabled="loading"
                                    class="w-full"
                                />
                            </div>
                            <div class="flex flex-col gap-2 flex-1">
                                <label class="font-medium" for="profile-webhook-url"
                                    >Webhook URL</label
                                >
                                <InputText
                                    id="profile-webhook-url"
                                    v-model="form.webhook_url"
                                    placeholder="https://example.com/webhook"
                                    :disabled="loading"
                                    class="w-full"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col gap-4 md:gap-2 md:flex-row">
                            <div class="flex flex-col gap-2 flex-1">
                                <label
                                    class="font-medium"
                                    for="profile-password"
                                    >New password</label
                                >
                                <Password
                                    id="profile-password"
                                    v-model="form.password"
                                    toggleMask
                                    :feedback="false"
                                    placeholder="Leave blank to keep"
                                    class="w-full"
                                    inputClass="w-full"
                                />
                            </div>
                            <div class="flex flex-col gap-2 flex-1">
                                <label
                                    class="font-medium"
                                    for="profile-password-confirm"
                                    >Confirm password</label
                                >
                                <Password
                                    id="profile-password-confirm"
                                    v-model="form.password_confirmation"
                                    toggleMask
                                    :feedback="false"
                                    placeholder="Confirm password"
                                    class="w-full"
                                    inputClass="w-full"
                                />
                            </div>
                        </div>
                        <Button
                            label="Save changes"
                            :loading="saving"
                            @click="updateProfile"
                        />
                    </div>
                </template>
            </Card>

            <Card class="shadow-sm">
                <template #title>API Key</template>
                <template #content>
                    <p class="text-sm text-gray-600">
                        Rotate your API key when needed for external
                        integrations.
                    </p>
                    <div class="flex flex-col gap-2">
                        <Button
                            label="Rotate API key"
                            class="mt-4"
                            severity="secondary"
                            @click="rotateApiKey"
                        />
                        <a target="_blank" href="/v1/docs" class="mt-4 w-full">
                            <Button
                                label="View docs"
                                class="w-full"
                                severity="secondary"
                            />
                        </a>
                    </div>
                </template>
            </Card>
        </div>

        <Dialog
            v-model:visible="apiKeyDialog"
            header="New API key"
            modal
            :style="{ width: '30rem' }"
        >
            <p class="text-sm text-gray-600">
                Copy and store this key. It will not be shown again.
            </p>
            <div
                class="mt-4 p-3 border border-gray-200 dark:border-gray-700 rounded-lg break-all"
            >
                {{ apiKey }}
            </div>
            <div class="mt-4 flex justify-end">
                <Button
                    label="Copy key"
                    icon="pi pi-copy"
                    @click="copyApiKey"
                />
            </div>
        </Dialog>
    </div>
</template>
