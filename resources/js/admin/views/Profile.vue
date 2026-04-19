<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { useAuthStore } from "@/admin/stores/authStore";

const toast = useToast();
const authStore = useAuthStore();

const loading = ref(false);
const saving = ref(false);

const form = ref({
    name: "",
    password: "",
    password_confirmation: "",
});

const loadProfile = async () => {
    loading.value = true;
    try {
        const profile = await authStore.getProfile();
        form.value.name = profile.name || "";
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
        const payload = {
            name: form.value.name,
        };
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
        </div>
    </div>
</template>
