<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    agent: Object,
    company: Object,
    jobs: Array,
    token: String,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const form = useForm({
    candidate_name: '',
    candidate_email: '',
    candidate_phone: '',
    job_id: '',
    recommendation_comment: '',
    resume: null,
});

const resumeFileName = ref('');

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.resume = file;
        resumeFileName.value = file.name;
    }
};

const submit = () => {
    form.post(`/recommend/${props.token}`, {
        forceFormData: true,
    });
};

const employmentTypes = {
    full_time: '正社員',
    part_time: 'パート・アルバイト',
    contract: '契約社員',
    intern: 'インターン',
};
</script>

<template>
    <Head :title="`候補者推薦 - ${company.name}`" />

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
        <main style="max-width: 640px; margin: 0 auto; padding: 32px 16px;">
            <!-- 挨拶 -->
            <div style="background: #ffffff; border-radius: 12px; padding: 32px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <p style="margin: 0 0 8px; font-size: 15px; color: #333; line-height: 1.8;">
                    {{ agent.organization }}<br>
                    {{ agent.name }} 様
                </p>
                <p style="margin: 0; font-size: 14px; color: #666; line-height: 1.8;">
                    以下のフォームから <strong>{{ company.name }}</strong> への候補者推薦を行えます。
                </p>
            </div>

            <!-- エラー表示 -->
            <div v-if="form.errors.general"
                style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin-bottom: 24px; color: #991b1b; font-size: 14px;">
                {{ form.errors.general }}
            </div>

            <!-- フォーム -->
            <form @submit.prevent="submit"
                style="background: #ffffff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

                <h2 :style="{ margin: '0 0 24px', fontSize: '18px', color: colors.primary, borderBottom: `2px solid ${colors.green}`, paddingBottom: '8px' }">
                    候補者情報
                </h2>

                <!-- 候補者名 -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 6px;">
                        候補者名 <span style="color: #dc2626;">*</span>
                    </label>
                    <input
                        v-model="form.candidate_name"
                        type="text"
                        placeholder="例: 山田 太郎"
                        :style="{
                            width: '100%',
                            padding: '10px 14px',
                            border: form.errors.candidate_name ? '1px solid #dc2626' : '1px solid #d1d5db',
                            borderRadius: '8px',
                            fontSize: '14px',
                            outline: 'none',
                            boxSizing: 'border-box',
                        }"
                    />
                    <p v-if="form.errors.candidate_name" style="margin: 4px 0 0; font-size: 12px; color: #dc2626;">
                        {{ form.errors.candidate_name }}
                    </p>
                </div>

                <!-- メールアドレス -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 6px;">
                        メールアドレス
                    </label>
                    <input
                        v-model="form.candidate_email"
                        type="email"
                        placeholder="例: yamada@example.com"
                        :style="{
                            width: '100%',
                            padding: '10px 14px',
                            border: form.errors.candidate_email ? '1px solid #dc2626' : '1px solid #d1d5db',
                            borderRadius: '8px',
                            fontSize: '14px',
                            outline: 'none',
                            boxSizing: 'border-box',
                        }"
                    />
                    <p v-if="form.errors.candidate_email" style="margin: 4px 0 0; font-size: 12px; color: #dc2626;">
                        {{ form.errors.candidate_email }}
                    </p>
                </div>

                <!-- 電話番号 -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 6px;">
                        電話番号
                    </label>
                    <input
                        v-model="form.candidate_phone"
                        type="tel"
                        placeholder="例: 090-1234-5678"
                        :style="{
                            width: '100%',
                            padding: '10px 14px',
                            border: '1px solid #d1d5db',
                            borderRadius: '8px',
                            fontSize: '14px',
                            outline: 'none',
                            boxSizing: 'border-box',
                        }"
                    />
                </div>

                <!-- ポジション（求人選択） -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 6px;">
                        推薦ポジション
                    </label>
                    <select
                        v-model="form.job_id"
                        :style="{
                            width: '100%',
                            padding: '10px 14px',
                            border: '1px solid #d1d5db',
                            borderRadius: '8px',
                            fontSize: '14px',
                            outline: 'none',
                            backgroundColor: '#fff',
                            boxSizing: 'border-box',
                        }"
                    >
                        <option value="">選択してください（任意）</option>
                        <option v-for="job in jobs" :key="job.id" :value="job.id">
                            {{ job.title }}
                            <template v-if="job.location"> - {{ job.location }}</template>
                        </option>
                    </select>
                </div>

                <!-- 推薦理由 -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 6px;">
                        推薦理由・候補者の強み
                    </label>
                    <textarea
                        v-model="form.recommendation_comment"
                        rows="5"
                        placeholder="候補者の経験、スキル、推薦理由などをご記入ください"
                        :style="{
                            width: '100%',
                            padding: '10px 14px',
                            border: '1px solid #d1d5db',
                            borderRadius: '8px',
                            fontSize: '14px',
                            outline: 'none',
                            resize: 'vertical',
                            boxSizing: 'border-box',
                        }"
                    ></textarea>
                </div>

                <!-- 履歴書アップロード -->
                <div style="margin-bottom: 28px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 6px;">
                        履歴書・職務経歴書
                    </label>
                    <div :style="{
                        border: '2px dashed #d1d5db',
                        borderRadius: '8px',
                        padding: '20px',
                        textAlign: 'center',
                        cursor: 'pointer',
                        backgroundColor: resumeFileName ? '#f0f7f4' : '#fafafa',
                    }"
                        @click="$refs.fileInput.click()"
                    >
                        <input
                            ref="fileInput"
                            type="file"
                            accept=".pdf,.doc,.docx"
                            style="display: none;"
                            @change="handleFileChange"
                        />
                        <p v-if="resumeFileName" :style="{ margin: 0, fontSize: '14px', color: colors.teal, fontWeight: 600 }">
                            {{ resumeFileName }}
                        </p>
                        <p v-else style="margin: 0; font-size: 13px; color: #999;">
                            クリックしてファイルを選択（PDF, Word / 最大10MB）
                        </p>
                    </div>
                    <p v-if="form.errors.resume" style="margin: 4px 0 0; font-size: 12px; color: #dc2626;">
                        {{ form.errors.resume }}
                    </p>
                </div>

                <!-- 送信ボタン -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    :style="{
                        width: '100%',
                        padding: '14px',
                        backgroundColor: form.processing ? '#9ca3af' : colors.teal,
                        color: '#ffffff',
                        border: 'none',
                        borderRadius: '8px',
                        fontSize: '16px',
                        fontWeight: 'bold',
                        cursor: form.processing ? 'not-allowed' : 'pointer',
                    }"
                >
                    {{ form.processing ? '送信中...' : '候補者を推薦する' }}
                </button>
            </form>
        </main>

        <!-- フッター -->
        <footer style="text-align: center; padding: 24px; font-size: 12px; color: #999;">
            Opinio ATS - 採用管理システム
        </footer>
    </div>
</template>
