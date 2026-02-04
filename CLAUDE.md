# Opinio ATS 開発ガイド

## プロジェクト概要

採用管理システム（ATS）のMVP開発。4チャネルからの候補者取り込みを優先実装中。

## フォルダ構成

```
/Users/hayanoryuki12/opinio/apps/
├── ats-app/          # Laravel アプリケーション（本プロジェクト）
│   ├── app/          # PHP コード
│   ├── resources/js/ # Vue コンポーネント
│   ├── database/     # マイグレーション・シーダー
│   └── ...
└── (その他)

/Users/hayanoryuki12/opinio/docs/  # 設計ドキュメント（別フォルダ）
```

## 開発環境の起動方法

### ターミナル 1: Laravel (Docker)

```bash
cd /Users/hayanoryuki12/opinio/apps/ats-app
./vendor/bin/sail up -d
```

### ターミナル 2: Vite (フロントエンド)

```bash
cd /Users/hayanoryuki12/opinio/apps/ats-app
pnpm dev
```

### アクセス URL

- アプリ: http://localhost
- Mailpit: http://localhost:8025

### テストアカウント

- admin@example.com / password
- recruiter@example.com / password

## よく使うコマンド

```bash
# マイグレーション
./vendor/bin/sail artisan migrate

# マイグレーションリセット
./vendor/bin/sail artisan migrate:fresh --seed

# シーダー実行
./vendor/bin/sail artisan db:seed

# キャッシュクリア
./vendor/bin/sail artisan cache:clear

# 停止
./vendor/bin/sail down
```

## 技術スタック

- Laravel 11.x / PHP 8.3
- PostgreSQL 16（ローカル） / MySQL 8（本番 RDS）
- Redis
- Inertia.js + Vue 3
- Tailwind CSS

## 重要な技術メモ

### テーブル名の注意
- `Job` モデルは `recruitment_jobs` テーブルを使用（`$table = 'recruitment_jobs'`）
- Laravel のキューテーブル `jobs` と区別するため

### ローカル開発の認証バイパス
本番は SSO 認証だが、ローカルでは以下の設定でバイパス可能：

`.env` に追加：
```
DEV_BYPASS_AUTH=true
```

関連ファイル：
- `config/app.php` - `dev_bypass_auth` 設定
- `app/Http/Middleware/VerifyJwt.php` - バイパス処理
- `app/Http/Middleware/RequireSso.php` - バイパス処理

バイパス時は自動で開発用ユーザー・会社が作成される。

### UI構成
- ナビゲーション: トップナビゲーション（Salesforce風）
- レイアウト: `resources/js/Layouts/AppLayout.vue`
- テーマカラー: Opinio色（#332c54, #4e878c, #65b891, #f4f4ed）

### 本番デプロイ
詳細は運用ドキュメント参照。簡易手順：
```bash
# 1. ローカルでコミット＆プッシュ
git add . && git commit -m "update: 内容" && git push origin main

# 2. 本番サーバーで反映
ssh -i ~/.ssh/opinio-2026.pem ubuntu@52.195.88.211
cd /var/www/ats-app
sudo chown -R ubuntu:ubuntu .
git fetch origin && git reset --hard origin/main
npm install && npm run build
php artisan migrate
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear
php artisan config:cache && php artisan route:cache
sudo chown -R www-data:www-data .
sudo chmod -R 755 . && sudo chmod -R 775 storage bootstrap/cache
sudo systemctl reload nginx
```

## 現在の進捗

### 完了
- [x] プロジェクト初期セットアップ
- [x] DB マイグレーション（SoT + 取り込みテーブル）
- [x] ログイン機能
- [x] ダッシュボード
- [x] 取り込み一覧画面
- [x] 候補者ドラフト確認 UI
- [x] トップナビゲーション実装（Salesforce風）
- [x] ローカル開発用の認証バイパス機能

### 次のタスク
- [ ] ドラフト確定時の SoT 登録処理（Person, Candidate, Application 作成）
- [ ] 4チャネル API 実装
- [ ] 各ページのエラー修正・動作確認

## 設計ドキュメント参照

MVP スコープ: `/docs/12_roadmap/01_mvp_scope.md`
技術スタック: `/docs/10_infrastructure/01_tech_stack.md`
