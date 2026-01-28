<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="ログイン" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Opinio ATS</h1>
                <p class="mt-2 text-gray-600">アカウントにログイン</p>
            </div>

            <div class="card p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="email" class="label">メールアドレス</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="input"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label for="password" class="label">パスワード</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="input"
                            required
                            autocomplete="current-password"
                        />
                    </div>

                    <div class="flex items-center">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        />
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            ログイン状態を保持する
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary w-full"
                    >
                        <span v-if="form.processing">ログイン中...</span>
                        <span v-else>ログイン</span>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500 text-center">
                        テスト用アカウント
                    </p>
                    <div class="mt-2 text-xs text-gray-400 text-center space-y-1">
                        <p>admin@example.com / password</p>
                        <p>recruiter@example.com / password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
