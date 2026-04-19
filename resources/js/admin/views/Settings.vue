<script setup>
import { computed, onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { apiRequest } from "@/admin/services/api";

const toast = useToast();

const loading = ref(false);
const savingIds = ref({});
const settings = ref([]);
const values = ref({});
const imageFiles = ref({});

const socialIconOptions = [
    { label: "Facebook", value: "facebook" },
    { label: "Twitter X", value: "twitter-x" },
    { label: "Instagram", value: "instagram" },
    { label: "Telegram", value: "telegram" },
    { label: "YouTube", value: "youtube" },
    { label: "LinkedIn", value: "linkedin" },
    { label: "TikTok", value: "tiktok" },
    { label: "WhatsApp", value: "whatsapp" },
    { label: "GitHub", value: "github" },
];

const sortedSettings = computed(() => {
    return [...settings.value].sort((a, b) => {
        const aTitle = (a.title || a.key || "").toLowerCase();
        const bTitle = (b.title || b.key || "").toLowerCase();
        return aTitle.localeCompare(bTitle);
    });
});

const parseArraySetting = (value) => {
    if (Array.isArray(value)) {
        return value;
    }

    if (typeof value !== "string" || !value.trim()) {
        return [];
    }

    try {
        const parsed = JSON.parse(value);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
};

const parseSettingValue = (setting) => {
    if (setting.type === "faq_array" || setting.type === "social_array") {
        return parseArraySetting(setting.value);
    }

    return setting.value ?? "";
};

const buildValues = (list) => {
    const map = {};
    list.forEach((setting) => {
        map[setting.id] = parseSettingValue(setting);
    });
    values.value = map;
    imageFiles.value = {};
};

const getArrayValue = (settingId) => {
    const current = values.value[settingId];
    return Array.isArray(current) ? current : [];
};

const addFaq = (settingId) => {
    const next = [...getArrayValue(settingId), { q: "", a: "" }];
    values.value[settingId] = next;
};

const addSocial = (settingId) => {
    const next = [
        ...getArrayValue(settingId),
        { icon: socialIconOptions[0].value, url: "" },
    ];
    values.value[settingId] = next;
};

const getSocialLabel = (value) => {
    const option = socialIconOptions.find((item) => item.value === value);
    return option ? option.label : value;
};

const removeValueFromArray = (settingId, index) => {
    const next = [...getArrayValue(settingId)];
    next.splice(index, 1);
    values.value[settingId] = next;
};

const fetchSettings = async () => {
    loading.value = true;
    try {
        const data = await apiRequest("/settings");
        settings.value = data || [];
        buildValues(settings.value);
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Failed to load settings",
            detail: error.message,
            life: 4000,
        });
    } finally {
        loading.value = false;
    }
};

const onImagePick = (settingId, event) => {
    const file = event.target?.files?.[0] || null;
    if (!file) {
        imageFiles.value[settingId] = null;
        return;
    }
    imageFiles.value[settingId] = file;
};

const imagePreview = (setting) => {
    const selectedFile = imageFiles.value[setting.id];
    if (selectedFile) {
        return URL.createObjectURL(selectedFile);
    }
    return values.value[setting.id] || "";
};

const saveSetting = async (setting) => {
    const payloadValue = values.value[setting.id];
    const file = imageFiles.value[setting.id] || null;

    if (setting.type === "image" && !file && !payloadValue) {
        toast.add({
            severity: "warn",
            summary: "Missing image",
            detail: "Please select an image before saving.",
            life: 3000,
        });
        return;
    }

    savingIds.value[setting.id] = true;
    try {
        if (setting.type === "image") {
            const formData = new FormData();
            if (file) {
                formData.append("value", file);
            }
            formData.append("_method", "PUT");

            await apiRequest(`/settings/${setting.id}`, {
                method: "POST",
                body: formData,
                isFormData: true,
            });
        } else {
            let valueToSend = payloadValue;
            if (setting.type === "faq_array" || setting.type === "social_array") {
                valueToSend = JSON.stringify(
                    Array.isArray(payloadValue) ? payloadValue : [],
                );
            }

            await apiRequest(`/settings/${setting.id}`, {
                method: "PUT",
                body: {
                    value: valueToSend,
                },
            });
        }

        toast.add({
            severity: "success",
            summary: "Setting updated",
            detail: `${setting.title || setting.key} saved successfully.`,
            life: 3000,
        });

        await fetchSettings();
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Update failed",
            detail: error.message,
            life: 4000,
        });
    } finally {
        savingIds.value[setting.id] = false;
    }
};

onMounted(() => {
    fetchSettings();
});
</script>

<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold">Settings</h2>
            <p class="text-gray-600">
                Update system settings with type-aware inputs.
            </p>
        </div>

        <Card class="shadow-sm">
            <template #content>
                <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <Card v-for="n in 6" :key="`settings-loading-${n}`" class="shadow-sm">
                        <template #content>
                            <Skeleton width="45%" height="1rem" class="mb-3" />
                            <Skeleton width="100%" height="2.5rem" class="mb-3" />
                            <Skeleton width="30%" height="2.2rem" />
                        </template>
                    </Card>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <Card
                        v-for="setting in sortedSettings"
                        :key="setting.id"
                        class="shadow-sm border border-surface-200"
                    >
                        <template #content>
                            <div class="flex flex-col gap-3">
                                <div>
                                    <h3 class="font-semibold text-base">
                                        {{ setting.title || setting.key }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <span class="font-medium">Key:</span> {{ setting.key }}
                                        <span class="mx-2">-</span>
                                        <span class="font-medium">Type:</span> {{ setting.type }}
                                    </p>
                                </div>

                                <div v-if="setting.type === 'image'" class="flex flex-col gap-3">
                                    <img
                                        v-if="imagePreview(setting)"
                                        :src="imagePreview(setting)"
                                        alt="Setting preview"
                                        class="w-full max-w-xs h-28 object-cover rounded border border-surface-200"
                                    />
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="onImagePick(setting.id, $event)"
                                        class="block text-sm"
                                    />
                                </div>

                                <div v-else-if="setting.type === 'html'" class="flex flex-col gap-2">
                                    <Editor
                                        v-model="values[setting.id]"
                                        editorStyle="height: 200px"
                                    />
                                </div>

                                <div v-else-if="setting.type === 'faq_array'" class="flex flex-col gap-3">
                                    <div
                                        v-for="(faq, index) in getArrayValue(setting.id)"
                                        :key="`faq-${setting.id}-${index}`"
                                        class="rounded border border-surface-200 p-3"
                                    >
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="font-medium">Question</label>
                                            <Button
                                                icon="pi pi-trash"
                                                severity="danger"
                                                text
                                                @click="removeValueFromArray(setting.id, index)"
                                            />
                                        </div>
                                        <InputText
                                            v-model="faq.q"
                                            placeholder="Question"
                                            class="w-full mb-2"
                                        />
                                        <label class="font-medium mb-2">Answer</label>
                                        <Textarea
                                            v-model="faq.a"
                                            rows="3"
                                            class="w-full"
                                        />
                                    </div>

                                    <Button
                                        label="Add FAQ"
                                        icon="pi pi-plus"
                                        severity="secondary"
                                        outlined
                                        @click="addFaq(setting.id)"
                                    />
                                </div>

                                <div v-else-if="setting.type === 'social_array'" class="flex flex-col gap-3">
                                    <div
                                        v-for="(social, index) in getArrayValue(setting.id)"
                                        :key="`social-${setting.id}-${index}`"
                                        class="rounded border border-surface-200 p-3"
                                    >
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="font-medium">Social</label>
                                            <Button
                                                icon="pi pi-trash"
                                                severity="danger"
                                                text
                                                @click="removeValueFromArray(setting.id, index)"
                                            />
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="flex flex-col gap-2">
                                                <label class="font-medium">Icon</label>
                                                <Dropdown
                                                    v-model="social.icon"
                                                    :options="socialIconOptions"
                                                    optionLabel="label"
                                                    optionValue="value"
                                                    placeholder="Select icon"
                                                    class="w-full"
                                                >
                                                    <template #value="slotProps">
                                                        <div
                                                            v-if="slotProps.value"
                                                            class="flex items-center gap-2"
                                                        >
                                                            <i :class="`bi bi-${slotProps.value}`"></i>
                                                            <span>{{ getSocialLabel(slotProps.value) }}</span>
                                                        </div>
                                                        <span v-else>{{ slotProps.placeholder }}</span>
                                                    </template>
                                                    <template #option="slotProps">
                                                        <div class="flex items-center gap-2">
                                                            <i :class="`bi bi-${slotProps.option.value}`"></i>
                                                            <span>{{ slotProps.option.label }}</span>
                                                        </div>
                                                    </template>
                                                </Dropdown>
                                            </div>

                                            <div class="flex flex-col gap-2">
                                                <label class="font-medium">Link</label>
                                                <InputText
                                                    v-model="social.url"
                                                    placeholder="https://..."
                                                    class="w-full"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <Button
                                        label="Add Social Link"
                                        icon="pi pi-plus"
                                        severity="secondary"
                                        outlined
                                        @click="addSocial(setting.id)"
                                    />
                                </div>

                                <div v-else class="flex flex-col gap-2">
                                    <Textarea
                                        v-model="values[setting.id]"
                                        rows="4"
                                        class="w-full"
                                    />
                                </div>

                                <div class="flex justify-end">
                                    <Button
                                        label="Save"
                                        icon="pi pi-save"
                                        :loading="Boolean(savingIds[setting.id])"
                                        @click="saveSetting(setting)"
                                    />
                                </div>
                            </div>
                        </template>
                    </Card>

                    <div
                        v-if="!sortedSettings.length"
                        class="flex flex-col items-center justify-center p-8 text-gray-500"
                    >
                        <i class="pi pi-inbox text-4xl mb-4 text-gray-400"></i>
                        <p class="text-lg font-medium">No settings found.</p>
                    </div>
                </div>
            </template>
        </Card>
    </div>
</template>
