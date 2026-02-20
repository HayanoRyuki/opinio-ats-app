# SES セットアップ ハンドオーバー

## 完了済み
- [x] AWS SDK (`aws/aws-sdk-php`) インストール
- [x] `AgentWelcomeMail` Mailable クラス作成
- [x] メールテンプレート (Blade) 作成（Opinio配色）
- [x] `AgentController` store() にメール送信追加 & email 必須化
- [x] フロントエンド (Create.vue / Edit.vue) email 必須化
- [x] ローカル `.env` Mailpit 設定済み

## 次のステップ: AWS SES セットアップ

### 1. SES ドメイン認証（AWS Console）
- AWS Console → SES → Verified identities → Create identity
- Identity type: `Domain`
- Domain: `opinio.co.jp`
- DKIM: `Enable`（推奨）
- → 「Create identity」

### 2. DNS レコード追加
- SES が表示する CNAME レコード 3つをドメインの DNS に追加
- opinio.co.jp のドメイン管理先を確認（お名前.com、Route53 等）

### 3. 本番 `.env` に追加
```
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@opinio.co.jp
MAIL_FROM_NAME="Opinio ATS"
```

### 4. IAM ポリシー
- 既存の IAM ユーザーに `AmazonSESFullAccess` を追加
- AWS_ACCESS_KEY_ID / SECRET が `.env` に既にあれば同じものを使用可能

### 5. サンドボックス解除申請
- SES はデフォルトでサンドボックス（認証済みアドレスにしか送れない）
- Account dashboard → Request production access
- ユースケース説明を記入して申請（通常1-2営業日で承認）

### 6. 本番デプロイ
```bash
# ローカルでコミット＆プッシュ
git add . && git commit -m "feat: エージェント登録時ウェルカムメール送信機能" && git push origin main

# 本番サーバーで
cd /var/www/ats-app
sudo chown -R ubuntu:ubuntu .
git fetch origin && git reset --hard origin/main
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache && php artisan route:cache
sudo chown -R www-data:www-data .
sudo chmod -R 755 . && sudo chmod -R 775 storage bootstrap/cache
sudo systemctl reload nginx
```

## 変更ファイル一覧
| ファイル | 変更 |
|---|---|
| `composer.json` / `composer.lock` | aws/aws-sdk-php 追加 |
| `app/Mail/AgentWelcomeMail.php` | 新規 |
| `resources/views/emails/agent-welcome.blade.php` | 新規 |
| `app/Http/Controllers/Web/AgentController.php` | store/update email必須化、store にメール送信 |
| `resources/js/Pages/Agents/Create.vue` | email必須化 + 注記 |
| `resources/js/Pages/Agents/Edit.vue` | email必須化 |
