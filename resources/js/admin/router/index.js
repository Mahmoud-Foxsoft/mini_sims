import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/admin/stores/authStore";
import AppLayout from "@/admin/layout/AppLayout.vue";

const router = createRouter({
    history: createWebHistory("/admin/"),
    routes: [
        {
            path: "/",
            component: AppLayout,
            children: [
                {
                    path: "",
                    name: "dashboard",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Dashboard.vue"),
                },
                {
                    path: "orders",
                    name: "orders",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Orders.vue"),
                },
                {
                    path: "phone-numbers",
                    name: "phoneNumbers",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/PhoneNumbers.vue"),
                },
                {
                    path: "transactions",
                    name: "transactions",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Transactions.vue"),
                },
                {
                    path: "payments",
                    name: "payments",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Payments.vue"),
                },
                {
                    path: "services",
                    name: "services",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Services.vue"),
                },
                {
                    path: "profile",
                    name: "profile",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Profile.vue"),
                },
                {
                    path: "users",
                    name: "users",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Users.vue"),
                },
                {
                    path: "admins",
                    name: "admins",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Admins.vue"),
                },
                {
                    path: "settings",
                    name: "settings",
                    meta: { requiresAuth: true },
                    component: () => import("@/admin/views/Settings.vue"),
                },
            ],
        },
        {
            path: "/auth/login",
            name: "login",
            meta: { guestOnly: true },
            component: () => import("@/admin/views/auth/Login.vue"),
        },
    ],
});

router.beforeEach((to) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    if (to.meta.requiresAuth && !isAuthenticated) {
        return { name: "login", query: { redirect: to.fullPath } };
    }

    if (to.meta.guestOnly && isAuthenticated) {
        return { name: "dashboard" };
    }

    return true;
});

export default router;
