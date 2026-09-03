<script setup>
import { ref } from 'vue';
import api, { ensureCsrfCookie } from '@/lib/axios';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

const email = ref('');
const message = ref('');
const error = ref('');
const submitting = ref(false);

async function submit() {
    submitting.value = true;
    message.value = '';
    error.value = '';

    try {
        await ensureCsrfCookie();
        const { data } = await api.post('/auth/forgot-password', { email: email.value });
        message.value = data.message;
    } catch (err) {
        error.value = err.response?.data?.errors?.email?.[0] ?? 'Gagal mengirim tautan reset.';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <AuthLayout>
        <Card>
            <CardHeader>
                <CardTitle>Lupa Kata Sandi</CardTitle>
                <CardDescription>Masukkan email untuk menerima tautan reset kata sandi.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="email" type="email" class="mt-1" required autofocus />
                    </div>
                    <p v-if="message" class="text-sm text-accent">{{ message }}</p>
                    <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
                    <Button type="submit" class="w-full" :disabled="submitting">
                        {{ submitting ? 'Mengirim...' : 'Kirim Tautan Reset' }}
                    </Button>
                </form>
            </CardContent>
        </Card>
    </AuthLayout>
</template>
