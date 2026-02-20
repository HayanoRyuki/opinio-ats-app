<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    agent: Object,
    company: Object,
    token: String,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};
</script>

<template>
    <Head :title="`推薦完了 - ${company.name}`" />

    <div :style="{ minHeight: '100vh', backgroundColor: colors.cream }">
        <!-- ヘッダー -->
        <header :style="{
            backgroundColor: colors.primary,
            padding: '24px 0',
            textAlign: 'center',
        }">
            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: bold;">
                Opinio ATS
            </h1>
            <p :style="{ margin: '4px 0 0', color: colors.green, fontSize: '13px' }">
                候補者推薦フォーム
            </p>
        </header>

        <!-- メインコンテンツ -->
        <main style="max-width: 640px; margin: 0 auto; padding: 48px 16px;">
            <div style="background: #ffffff; border-radius: 12px; padding: 48px 32px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <!-- チェックアイコン -->
                <div :style="{
                    width: '64px',
                    height: '64px',
                    borderRadius: '50%',
                    backgroundColor: '#f0f7f4',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    margin: '0 auto 24px',
                }">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#65b891" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <h2 :style="{ margin: '0 0 16px', fontSize: '22px', color: colors.primary }">
                    推薦を受け付けました
                </h2>

                <p style="margin: 0 0 8px; font-size: 15px; color: #333; line-height: 1.8;">
                    {{ agent.organization }} {{ agent.name }} 様
                </p>

                <p style="margin: 0 0 32px; font-size: 14px; color: #666; line-height: 1.8;">
                    候補者の推薦情報を <strong>{{ company.name }}</strong> の採用チームにお送りしました。<br>
                    確認後、担当者よりご連絡いたします。
                </p>

                <!-- 別の候補者を推薦ボタン -->
                <Link
                    :href="`/recommend/${token}`"
                    :style="{
                        display: 'inline-block',
                        padding: '12px 32px',
                        backgroundColor: colors.teal,
                        color: '#ffffff',
                        borderRadius: '8px',
                        fontSize: '15px',
                        fontWeight: 'bold',
                        textDecoration: 'none',
                    }"
                >
                    別の候補者を推薦する
                </Link>
            </div>
        </main>

        <!-- フッター -->
        <footer style="text-align: center; padding: 24px; font-size: 12px; color: #999;">
            Opinio ATS - 採用管理システム
        </footer>
    </div>
</template>
