# OpinioATS 引き継ぎドキュメント

## 現在の状態 (2026-01-28) ✅ デモ用データ投入済み

### 完了した作業

1. **Blade → Vue/Inertia SPA移行** ✅
   - DashboardController: Inertia対応済み
   - Web\CandidateController: Inertia + JWT対応済み
   - Web\JobController: Inertia + JWT対応済み
   - Web\ApplicationController: Inertia + JWT対応済み
   - Web\IntakeController: Inertia + JWT対応済み
   - Web\MyPageController: Inertia + JWT対応済み

2. **routes/web.php 更新** ✅
   - Web名前空間のコントローラーを使用
   - admin.php は無効化（コメントアウト）
   - パイプライン・面接・レポートは仮Vueページで直接レンダリング

3. **JWT認証対応** ✅
   - 全コントローラーで `$request->user()` → `$request->attributes->get('company_id')` に変更
   - VerifyJwtミドルウェアがcompany_id, auth_user_id, roleをrequestにセット

4. **パッケージインストール** ✅
   - spatie/laravel-activitylog: 本番インストール済み
   - firebase/php-jwt: インストール済み
   - inertiajs/inertia-laravel: インストール済み
   - tightenco/ziggy: インストール済み

5. **サンプルデータ投入** ✅ (2026-01-28)
   - 求人: 3件
   - 候補者: 4名
   - 応募: 5件
   - Membershipにuser_id=2（柴社長）をOpinio社に紐付け済み

6. **モデル・コントローラー修正** ✅ (2026-01-28)
   - Candidateモデル: name/email/phoneは直接持つ（personリレーション不要）
   - Candidateモデル: applicationsリレーション追加
   - Applicationモデル: stepsリレーション追加（ただしテーブル未作成）
   - 全コントローラー・Vue: `candidate.person.xxx` → `candidate.xxx` に修正
   - 全コントローラー・Vue: `applied_at` → `created_at` に修正
   - ApplicationController: application_stepsテーブル未作成のためsteps参照を一時削除

### 動作確認済みページ

| ページ | 状態 |
|--------|------|
| ダッシュボード | ✅ |
| 取り込み管理 | ✅ |
| 候補者 | ✅ |
| 求人 | ✅ |
| 応募 | ✅ |
| パイプライン | ✅（仮ページ） |
| 面接 | ✅（仮ページ） |
| レポート | ✅（仮ページ） |
| マイページ | ✅ |

### 同期状態

| 環境 | 状態 |
|------|------|
| ローカル | ✅ 最新 |
| GitHub | ✅ 最新 |
| 本番 | ✅ 最新 |

### 技術スタック

- Laravel 12 + Inertia.js + Vue 3
- JWT認証（RS256）
- Tailwind CSS v4
- MySQL RDS（UUID主キー）

### サーバー情報

- **本番ATS**: ats.opinio.co.jp (EC2: 10.0.4.1)
- **Auth**: auth.opinio.co.jp (EC2: 3.114.99.99)
- **ユーザー**: ubuntu
- **パス**: /var/www/ats-app

### 重要なファイル

- `bootstrap/app.php`: ミドルウェア設定（HandleInertiaRequests追加済み）
- `routes/web.php`: Inertiaルート定義
- `app/Http/Controllers/Web/`: Inertia対応コントローラー
- `resources/js/Pages/`: Vueページコンポーネント
- `resources/js/Layouts/AppLayout.vue`: レイアウト（ユーザーメニューにマイページリンクあり）

### マイページ機能

- `/mypage` でアクセス可能
- ユーザーID、権限、所属会社を表示
- プロフィール編集（名前・メール・パスワード）はAuth側（auth.opinio.co.jp）で行う
- ヘッダーのユーザー名クリックでドロップダウンメニューが表示される

### 残タスク

1. **application_stepsテーブル作成** - 選考進捗機能を有効化するため
2. **Auth側のマイページ作成** - プロフィール編集機能（名前・メール・パスワード）

---

## 次のタスク: Auth側をBladeからVue/Inertiaに移行

auth.opinio.co.jp のUIをATSと同じデザインに揃える
- 現在: Blade
- 目標: Vue/Inertia（ATSと同じ）
- マイページ編集機能を実装
