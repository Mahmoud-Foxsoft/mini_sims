import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import AppLayout from '@/layout/AppLayout.vue';

const router = createRouter({
    history: createWebHistory('/dashboard/'),
    routes: [
        {
            path: '/',
            component: AppLayout,
            children: [
                {
                    path: '',
                    name: 'dashboard',
                    meta: { requiresAuth: true },
                    component: () => import('@/views/Dashboard.vue')
                },
                {
                    path: 'orders',
                    name: 'orders',
                    meta: { requiresAuth: true },
                    component: () => import('@/views/Orders.vue')
                },
                {
                    path: 'phone-numbers',
                    name: 'phoneNumbers',
                    meta: { requiresAuth: true },
                    component: () => import('@/views/PhoneNumbers.vue')
                },
                {
                    path: 'transactions',
                    name: 'transactions',
                    meta: { requiresAuth: true },
                    component: () => import('@/views/Transactions.vue')
                },
                {
                    path: 'payments',
                    name: 'payments',
                    meta: { requiresAuth: true },
                    component: () => import('@/views/Payments.vue')
                },
                {
                    path: 'profile',
                    name: 'profile',
                    meta: { requiresAuth: true },
                    component: () => import('@/views/Profile.vue')
                }
            ]
        },
        {
            path: '/auth/login',
            name: 'login',
            meta: { guestOnly: true },
            component: () => import('@/views/auth/Login.vue')
        },
        {
            path: '/auth/register',
            name: 'register',
            meta: { guestOnly: true },
            component: () => import('@/views/auth/Register.vue')
        },
        {
            path: '/auth/forgot-password',
            name: 'forgotPassword',
            meta: { guestOnly: true },
            component: () => import('@/views/auth/ForgotPassword.vue')
        },
        {
            path: '/auth/reset-password',
            name: 'resetPassword',
            meta: { guestOnly: true },
            component: () => import('@/views/auth/ResetPassword.vue')
        },
        {
            path: '/auth/verify-email',
            name: 'verifyEmail',
            meta: { guestOnly: true },
            component: () => import('@/views/auth/VerifyEmail.vue')
        }
    ]
});

router.beforeEach((to) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    if (to.meta.requiresAuth && !isAuthenticated) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.guestOnly && isAuthenticated) {
        return { name: 'dashboard' };
    }

    return true;
});

export default router;
