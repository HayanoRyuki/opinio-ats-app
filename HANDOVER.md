# OpinioATS 引き継ぎドキュメント

## 現在の状態 (2026-01-28) ✅ 全ページ動作確認済み

### 完了した作業

1. **Blade → Vue/Inertia SPA移行** ✅
   - DashboardController: Inertia対応済み
   - Web\CandidateController: Inertia + JWT対応済み
   - Web\JobController: Inertia + JWT対応済み
   - Web\ApplicationController: Inertia + JWT対応済み
   - Web\IntakeController: Inertia + JWT対応済み
   - Web\MyPageController: Inertia + JWT対応済み ← NEW

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
| マイページ | ✅ ← NEW |

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
