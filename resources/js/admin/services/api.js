import { useAuthStore } from "@/user/stores/authStore";

const normalizeBase = (base) => {
    if (!base) return "";
    return base.endsWith("/") ? base.slice(0, -1) : base;
};

const buildUrl = (path) => {
    const base = normalizeBase(import.meta.env.VITE_API_BASE_URL || "/api");
    if (path.startsWith("http")) return path;
    if (!path.startsWith("/")) return `${base}/${path}`;
    return `${base}${path}`;
};

export async function apiRequest(path, options = {}) {
    const authStore = useAuthStore();
    const url = buildUrl(path);
    const headers = {
        Accept: "application/json",
        ...(options.headers || {}),
    };

    if (!options.isFormData) {
        headers["Content-Type"] = "application/json";
    }

    if (authStore.token) {
        headers.Authorization = `Bearer ${authStore.token}`;
    }

    const response = await fetch(url, {
        method: options.method || "GET",
        headers,
        body: options.body
            ? options.isFormData
                ? options.body
                : JSON.stringify(options.body)
            : undefined,
    });

    const payload = await response.json().catch(() => null);

    if (!response.ok || !payload?.success) {
        const message =
            payload?.data?.error || payload?.message || "Request failed.";
        const error = new Error(message);
        error.details = payload?.data || null;
        error.errors = payload?.errors || null;
        throw error;
    }

    return payload.data;
}
