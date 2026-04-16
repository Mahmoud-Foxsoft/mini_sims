<script setup>
import { computed, ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import QRCode from 'qrcode';
import { apiRequest } from '@/services/api';
import { usePaymentDialogStore } from '@/stores/paymentDialogStore';

const toast = useToast();
const paymentDialogStore = usePaymentDialogStore();

const dialogVisible = computed({
    get: () => paymentDialogStore.visible,
    set: (value) => {
        paymentDialogStore.visible = value;
    }
});

const createLoading = ref(false);
const estimateLoading = ref(false);
const estimateError = ref('');

const amount = ref(null);
const currency = ref(null);
const paidAmount = ref(null);
const currencies = ref([]);

const receipt = ref(null);
const receiptQr = ref('');

let estimateRequestId = 0;
let lastEstimateKey = '';

const resetForm = () => {
    amount.value = null;
    currency.value = null;
    paidAmount.value = null;
    receipt.value = null;
    receiptQr.value = '';
    // reset estimate state
    estimateError.value = '';
    estimateLoading.value = false;
    lastEstimateKey = '';
};

const fetchCurrencies = async () => {
    if (currencies.value.length) return;
    try {
        const data = await apiRequest('/payments/currencies');
        currencies.value = (data.currencies || []).map((currencyCode) => ({
            label: currencyCode,
            value: currencyCode
        }));
    } catch (error) {
        toast.add({ severity: 'warn', summary: 'Currencies unavailable', detail: error.message, life: 3000 });
    }
};

const extractEstimatedAmount = (payload) => {
    const estimatePayload = payload?.data ?? payload;
    const estimated =
        estimatePayload?.estimated_amount ??
        estimatePayload?.estimatedAmount ??
        estimatePayload?.estimated ??
        null;
    if (estimated === null || estimated === undefined) return { estimatePayload, estimated };
    const numeric = Number(estimated);
    return { estimatePayload, estimated: Number.isFinite(numeric) ? numeric : estimated };
};

const runEstimate = async () => {
    if (!amount.value || !currency.value) {
        paidAmount.value = null;
        estimateError.value = '';
        return;
    }

    const requestId = ++estimateRequestId;
    estimateLoading.value = true;
    estimateError.value = '';

    try {
        const data = await apiRequest('/payments/estimate', {
            method: 'POST',
            body: {
                amount: amount.value,
                currency: currency.value
            }
        });
        if (requestId !== estimateRequestId) return;
        const { estimated } = extractEstimatedAmount(data);
        paidAmount.value = estimated;
    } catch (error) {
        if (requestId !== estimateRequestId) return;
        paidAmount.value = null;
        estimateError.value = error.message;
    } finally {
        if (requestId === estimateRequestId) {
            estimateLoading.value = false;
        }
    }
};

const createPayment = async () => {
    if (!amount.value || !currency.value) {
        toast.add({ severity: 'warn', summary: 'Missing details', detail: 'Enter amount and currency.', life: 3000 });
        return;
    }
    if (!paidAmount.value) {
        toast.add({ severity: 'warn', summary: 'Estimate required', detail: 'Get an estimate before creating the payment.', life: 3000 });
        return;
    }
    createLoading.value = true;
    try {
        const data = await apiRequest('/payments', {
            method: 'POST',
            body: {
                amount: amount.value,
                currency: currency.value,
                paid_amount: paidAmount.value
            }
        });
        receipt.value = data.payment || data;
        receiptQr.value = receipt.value?.pay_address ? await QRCode.toDataURL(receipt.value.pay_address) : '';
        toast.add({ severity: 'success', summary: 'Payment created', detail: 'Use the address to complete payment.', life: 4000 });
        paymentDialogStore.notifyCreated();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Payment failed', detail: error.message, life: 4000 });
    } finally {
        createLoading.value = false;
    }
};

const copyAddress = async () => {
    if (!receipt.value?.pay_address) return;
    await navigator.clipboard.writeText(receipt.value.pay_address);
    toast.add({ severity: 'success', summary: 'Copied', detail: 'Address copied to clipboard.', life: 2000 });
};

watch(
    () => dialogVisible.value,
    async (value) => {
        if (!value) return;
        resetForm();
        await fetchCurrencies();
    }
);

watch([amount, currency], () => {
    if (!amount.value || !currency.value) {
        paidAmount.value = null;
        estimateError.value = '';
        lastEstimateKey = '';
        receipt.value = null;
        receiptQr.value = '';
        return;
    }
    const key = `${amount.value}-${currency.value}`;
    if (key === lastEstimateKey) return;
    lastEstimateKey = key;
    receipt.value = null;
    receiptQr.value = '';
    runEstimate();
});
</script>

<template>
    <Dialog v-model:visible="dialogVisible" modal header="Create payment" :style="{ width: '32rem' }">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <label for="payment-amount" class="font-medium">Amount</label>
                <InputNumber
                    id="payment-amount"
                    v-model="amount"
                    :min="1"
                    mode="decimal"
                    placeholder="Amount"
                    class="w-full"
                    inputClass="w-full"
                />
            </div>
            <div class="flex flex-col gap-2">
                <label for="payment-currency" class="font-medium">Currency</label>
                <Dropdown
                    id="payment-currency"
                    v-model="currency"
                    :options="currencies"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Select currency"
                    class="w-full"
                />
            </div>
            <div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700 dark:border-amber-700 dark:bg-amber-900 dark:text-amber-50">
                It will cost 5% more.
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-medium">Estimated paid amount</label>
                <InputText :modelValue="paidAmount ?? ''" readonly placeholder="Estimate will appear here" class="w-full" />
                <small v-if="estimateLoading" class="text-gray-500">Estimating...</small>
                <small v-else-if="estimateError" class="text-red-500">{{ estimateError }}</small>
            </div>
            <Button label="Create payment" :loading="createLoading" @click="createPayment" />
        </div>

        <div v-if="receipt" class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h4 class="font-semibold mb-2">Payment address</h4>
            <p class="text-sm break-all">{{ receipt.pay_address }}</p>
            <div class="flex items-center gap-2 mt-3">
                <Button label="Copy address" icon="pi pi-copy" size="small" severity="secondary" @click="copyAddress" />
                <span v-if="receipt.pay_amount" class="text-sm text-gray-600">Pay: {{ receipt.pay_amount }}</span>
            </div>
            <div v-if="receiptQr" class="mt-4 flex justify-center">
                <img :src="receiptQr" alt="Payment QR" class="w-40 h-40" />
            </div>
        </div>
    </Dialog>
</template>
