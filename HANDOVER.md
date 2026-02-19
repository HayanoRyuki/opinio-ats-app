# Opinio ATS 引き継ぎメモ

**最終更新**: 2026/02/14

---

## 全体の進捗サマリー

| フェーズ | 内容 | 状態 |
|---------|------|------|
| Phase 1 | Gmail連携（5媒体メールパーサー） | ✅ 本番稼働中 |
| Phase 2 | Chrome拡張（BizReach候補者取り込み） | 🔧 実装完了・テスト待ち |
| Phase 3 | API個別交渉（BizReach API等） | ⏳ 未着手 |

---

## Phase 1: Gmail連携（完了）

### 概要
Gmail OAuth連携で、5媒体からの通知メールを自動パースしてIntakeCandidateDraftに取り込む。

### 対応済み媒体
| 媒体 | パーサー | 状態 |
|------|---------|------|
| ビズリーチ | `BizreachParser.php` | ✅ |
| Wantedly | `WantedlyParser.php` | ✅ |
| doda | `DodaParser.php` | ✅ |
| リクナビ | `RikunabiParser.php` | ✅ |
| マイナビ | `MynaviParser.php` | ✅ |

### 本番での修正履歴（2/14）
1. **SSO→DB永続化** — `VerifyJwt.php` で `new User()` (メモリのみ) → `User::firstOrCreate` に修正。Gmail接続時のFK制約違反を解消。
2. **Email重複対応** — 検索順序を `email → id → create` に変更。
3. **auth_user_id修正** — JWT `sub` ではなくDB上の `$user->id` を使用するよう修正。

### 関連ファイル
- `app/Http/Middleware/VerifyJwt.php` — SSO認証（修正済み）
- `app/Services/Gmail/` — パーサー群
- `resources/js/Pages/Settings/GmailSync.vue` — Gmail連携設定画面

---

## Phase 2: Chrome拡張（テスト待ち）

### 概要
ビズリーチの候補者詳細ページを開くと、フローティングボタンが表示され、ワンクリックでATSの `POST /api/intake/scout` に候補者データを送信する。

### 実装済みファイル

#### Chrome拡張 (`ats-app/ats-extension/`)
| ファイル | 内容 |
|---------|------|
| `manifest.json` | Manifest V3、BizReachドメインでcontent script自動注入 |
| `content.js` | 候補者ページ検出 → DOM抽出 → フローティングUI → 送信 |
| `content.css` | フローティングボタン・カード・トーストのスタイル |
| `popup.html` + `popup.js` + `popup.css` | 設定画面（ATS URL、会社ID、APIキー） |
| `background.js` | Service Worker — APIリクエスト送信 |
| `icons/` | Opinio ブランドアイコン（16/48/128px） |

#### バックエンド変更（今回追加）
| ファイル | 変更内容 |
|---------|---------|
| `bootstrap/app.php` | `api:` ルート読み込み追加、`intake.apikey` ミドルウェア登録 |
| `routes/api.php` | intakeルートに `intake.apikey` ミドルウェア適用 |
| `database/migrations/2026_02_14_000001_create_api_keys_table.php` | **新規** APIキーテーブル |
| `app/Models/ApiKey.php` | **新規** キー生成（`opn_` prefix）・ SHA-256ハッシュ検証 |
| `app/Http/Middleware/ValidateIntakeApiKey.php` | **新規** `X-API-Key` ヘッダー認証（未設定時は `company_id` フォールバック） |
| `config/cors.php` | **新規** API全パスでCORS許可 |

### DOMセレクタ（⚠️ 要調整）
`content.js` の `SELECTORS` オブジェクトはプレースホルダーです。実際のビズリーチ画面でCSSセレクタを確認・調整する必要があります。

```javascript
const SELECTORS = {
    name: ['.candidate-name', '.profile-name', 'h1.name', ...],
    currentCompany: ['.current-company', '.company-name', ...],
    // ...その他フィールド
};
```

### Chrome拡張の読み込み手順
1. `chrome://extensions` → デベロッパーモードON
2. 「パッケージ化されていない拡張機能を読み込む」→ `ats-app/ats-extension/` 選択
3. 拡張アイコンをクリック → 設定入力:
   - ATS URL: `https://ats.opinio.co.jp`（本番） / `http://localhost`（ローカル）
   - 会社ID: `c67253d7-010e-4492-8c21-663af325ff73`
   - APIキー: （任意、未設定でも `company_id` ボディ指定で動作）

### 次のアクション
1. ✅ Chromeに拡張読み込み済み
2. ⏳ **ビズリーチの法人アカウント作成待ち**
3. ⏳ 実際の候補者ページでDOMセレクタ調整
4. ⏳ 動作テスト（フローティングUI表示 → ATS送信 → ドラフト画面で確認）
5. ⏳ 必要に応じてAPIキー発行（tinker で `ApiKey::generate($companyId, 'Chrome拡張用')`）

---

## 以前完了した作業（2/4）

### ダッシュボード
- `DashboardController.php` — KPI、ファネル、チャネル別分析
- `Dashboard.vue` — Opinioカラーでデザイン

### 求人管理ページ
- `Jobs/Index.vue` — カード型Stats、リスト形式

### 本番シーダー
- `OpinioProductionSeeder.php` — 求人3件、応募3件

---

## 環境情報

### ローカル開発
```bash
cd ~/opinio/apps/ats-app
./vendor/bin/sail up -d    # Docker起動
pnpm dev                   # Vite起動
# アクセス: http://localhost
```

### 本番デプロイ
```bash
# ローカルで
git add . && git commit -m "update: 内容" && git push origin main

# 本番サーバーで
ssh -i ~/.ssh/opinio-2026.pem ubuntu@52.195.88.211
cd /var/www/ats-app
sudo chown -R ubuntu:ubuntu .
git fetch origin && git reset --hard origin/main
npm install && npm run build
php artisan migrate
sudo chown -R www-data:www-data .
sudo chmod -R 755 . && sudo chmod -R 775 storage bootstrap/cache
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear
php artisan config:cache && php artisan route:cache
sudo systemctl reload nginx
```

### テストアカウント
- admin@example.com / password
- recruiter@example.com / password
- 本番SSO: hshiba@opinio.co.jp

### Opinioカラー
```
Primary: #332c54 (紫)
Teal:    #4e878c
Green:   #65b891
Cream:   #f4f4ed (背景)
```

---

## 確認済みページ

- ✅ ダッシュボード https://ats.opinio.co.jp/dashboard
- ✅ 求人管理 https://ats.opinio.co.jp/jobs
- ✅ 応募一覧
- ✅ 候補者一覧
- ✅ 取り込み管理
- ✅ Gmail連携設定 https://ats.opinio.co.jp/settings/gmail

---

## 未対応・今後の検討事項

1. **ローカルと本番のスキーマ差異** — ローカルはPerson/Candidate分離構造、本番は統合構造
2. **ダッシュボードの「今週の面接」** — プレースホルダー（Interviewモデル未実装）
3. **Phase 3: API個別交渉** — BizReach API正式連携
4. **APIキー管理UI** — 現在tinkerでのみ発行可能。設定画面に追加予定
5. **レート制限** — intake APIへのレート制限追加
