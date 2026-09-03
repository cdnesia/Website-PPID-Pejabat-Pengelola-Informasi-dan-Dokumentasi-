<script setup>
import { reactive, ref } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const form = reactive({ email: '', password: '' });
const error = ref('');
const submitting = ref(false);

async function submit() {
    submitting.value = true;
    error.value = '';

    try {
        await auth.login(form);
        router.push(route.query.redirect?.toString() ?? (auth.isAdmin ? '/admin/dashboard' : '/'));
    } catch (err) {
        error.value = err.response?.data?.message ?? 'Email atau kata sandi salah.';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <AuthLayout>
        <Card>
            <CardHeader>
                <CardTitle>Masuk</CardTitle>
                <CardDescription>Masuk untuk mengajukan permohonan informasi.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" class="mt-1" required autofocus />
                    </div>
                    <div>
                        <Label for="password">Kata Sandi</Label>
                        <Input id="password" v-model="form.password" type="password" class="mt-1" required />
                    </div>
                    <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
                    <Button type="submit" class="w-full" :disabled="submitting">
                        {{ submitting ? 'Memproses...' : 'Masuk' }}
                    </Button>
                </form>
                <p class="mt-4 text-center text-sm text-muted-foreground">
                    Belum punya akun?
                    <RouterLink to="/auth/register" class="font-medium text-primary hover:underline">Daftar</RouterLink>
                </p>
                <p class="mt-2 text-center text-sm">
                    <RouterLink to="/auth/forgot-password" class="text-primary hover:underline">Lupa kata sandi?</RouterLink>
                </p>
            </CardContent>
        </Card>
    </AuthLayout>
</template>
