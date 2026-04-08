<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import QRCode from 'qrcode';
import { apiRequest } from '@/services/api';

const toast = useToast();
const loading = ref(false);
const payments = ref([]);
const totalRecords = ref(0);
const first = ref(0);
const rows = ref(20);
const createDialog = ref(false);
const createLoading = ref(false);

const amount = ref(null);
const currency = ref(null);
const currencies = ref([]);

const receipt = ref(null);
const receiptQr = ref('');

const fetchPayments = async (page = 1) => {
    loading.value = true;
    try {
        const data = await apiRequest(`/v1/payments?page=${page}`);
        payments.value = data.data || [];
        totalRecords.value = data.total || 0;
        rows.value = data.per_page || 20;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Failed to load payments', detail: error.message, life: 4000 });
    } finally {
        loading.value = false;
    }
};

const fetchCurrencies = async () => {
    try {
        const data = await apiRequest('/v1/payments/currencies');
        currencies.value = (data.currencies || []).map((currencyCode) => ({ label: currencyCode, value: currencyCode }));
    } catch (error) {
        toast.add({ severity: 'warn', summary: 'Currencies unavailable', detail: error.message, life: 3000 });
    }
};

const openCreate = () => {
    createDialog.value = true;
    receipt.value = null;
    receiptQr.value = '';
    amount.value = null;
    currency.value = null;
    fetchCurrencies();
};

const createPayment = async () => {
    if (!amount.value || !currency.value) {
        toast.add({ severity: 'warn', summary: 'Missing details', detail: 'Enter amount and currency.', life: 3000 });
        return;
    }
    createLoading.value = true;
    try {
        const data = await apiRequest('/v1/payments', {
            method: 'POST',
            body: {
                amount: amount.value,
                currency: currency.value
            }
        });
        receipt.value = data.payment || data;
        receiptQr.value = receipt.value?.pay_address ? await QRCode.toDataURL(receipt.value.pay_address) : '';
        toast.add({ severity: 'success', summary: 'Payment created', detail: 'Use the address to complete payment.', life: 4000 });
        fetchPayments();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Payment failed', detail: error.message, life: 4000 });
    } finally {
        createLoading.value = false;
    }
};

const onPage = (event) => {
    first.value = event.first;
    rows.value = event.rows;
    fetchPayments(event.page + 1);
};

const statusSeverity = (status) => {
    switch (status) {
        case 'finished':
            return 'success';
        case 'waiting':
            return 'warning';
        case 'confirming':
            return 'info';
        case 'sending':
            return 'info';
        case 'refunded':
            return 'danger';
        default:
            return 'secondary';
    }
};

const copyAddress = async () => {
    if (!receipt.value?.pay_address) return;
    await navigator.clipboard.writeText(receipt.value.pay_address);
    toast.add({ severity: 'success', summary: 'Copied', detail: 'Address copied to clipboard.', life: 2000 });
};

onMounted(() => fetchPayments());
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Payments</h2>
                <p class="text-gray-600">Create and track payments.</p>
            </div>
            <Button label="New payment" icon="pi pi-plus" @click="openCreate" />
        </div>

        <Card class="shadow-sm">
            <template #content>
                <DataTable
                    :value="payments"
                    :loading="loading"
                    :paginator="true"
                    :rows="rows"
                    :totalRecords="totalRecords"
                    :first="first"
                    @page="onPage"
                >
                    <Column field="transaction_id" header="Transaction" style="min-width: 14rem" />
                    <Column field="amount" header="Amount" style="min-width: 8rem" />
                    <Column field="currency" header="Currency" style="min-width: 8rem" />
                    <Column field="status" header="Status" style="min-width: 10rem">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status" :severity="statusSeverity(slotProps.data.status)" />
                        </template>
                    </Column>
                    <Column field="paid_amount" header="Paid" style="min-width: 10rem" />
                    <Column field="created_at" header="Created" style="min-width: 12rem" />
                </DataTable>
            </template>
        </Card>

        <Dialog v-model:visible="createDialog" modal header="Create payment" :style="{ width: '32rem' }">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="payment-amount" class="font-medium">Amount</label>
                    <InputNumber id="payment-amount" v-model="amount" :min="1" mode="decimal" placeholder="Amount" class="w-full" inputClass="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="payment-currency" class="font-medium">Currency</label>
                    <Dropdown id="payment-currency" v-model="currency" :options="currencies" optionLabel="label" optionValue="value" placeholder="Select currency" class="w-full" />
                </div>
                <Button label="Create payment" :loading="createLoading" @click="createPayment" />
            </div>

            <div v-if="receipt" class="mt-6 p-4 border border-gray-200 rounded-lg">
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
    </div>
</template>
