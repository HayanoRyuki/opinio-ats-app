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
- PostgreSQL 16
- Redis
- Inertia.js + Vue 3
- Tailwind CSS

## 現在の進捗

### 完了
- [x] プロジェクト初期セットアップ
- [x] DB マイグレーション（SoT + 取り込みテーブル）
- [x] ログイン機能
- [x] ダッシュボード
- [x] 取り込み一覧画面
- [x] 候補者ドラフト確認 UI

### 次のタスク
- [ ] ドラフト確定時の SoT 登録処理（Person, Candidate, Application 作成）
- [ ] 4チャネル API 実装
- [ ] 候補者一覧画面

## 設計ドキュメント参照

MVP スコープ: `/docs/12_roadmap/01_mvp_scope.md`
技術スタック: `/docs/10_infrastructure/01_tech_stack.md`
