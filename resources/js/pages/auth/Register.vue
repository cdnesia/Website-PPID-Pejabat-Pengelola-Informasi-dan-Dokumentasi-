<script setup>
import { reactive, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

const auth = useAuthStore();
const router = useRouter();

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
const errors = ref({});
const submitting = ref(false);

async function submit() {
    submitting.value = true;
    errors.value = {};

    try {
        await auth.register(form);
        router.push('/');
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors;
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <AuthLayout>
        <Card>
            <CardHeader>
                <CardTitle>Daftar Akun</CardTitle>
                <CardDescription>Registrasi diperlukan untuk mengajukan permohonan informasi.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <Label for="name">Nama Lengkap</Label>
                        <Input id="name" v-model="form.name" class="mt-1" required autofocus />
                        <p v-if="errors.name" class="mt-1 text-sm text-destructive">{{ errors.name[0] }}</p>
                    </div>
                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" class="mt-1" required />
                        <p v-if="errors.email" class="mt-1 text-sm text-destructive">{{ errors.email[0] }}</p>
                    </div>
                    <div>
                        <Label for="password">Kata Sandi</Label>
                        <Input id="password" v-model="form.password" type="password" class="mt-1" required />
                        <p v-if="errors.password" class="mt-1 text-sm text-destructive">{{ errors.password[0] }}</p>
                    </div>
                    <div>
                        <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                        <Input id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1" required />
                    </div>
                    <Button type="submit" class="w-full" :disabled="submitting">
                        {{ submitting ? 'Memproses...' : 'Daftar' }}
                    </Button>
                </form>
                <p class="mt-4 text-center text-sm text-muted-foreground">
                    Sudah punya akun?
                    <RouterLink to="/auth/login" class="font-medium text-primary hover:underline">Masuk</RouterLink>
                </p>
            </CardContent>
        </Card>
    </AuthLayout>
</template>
