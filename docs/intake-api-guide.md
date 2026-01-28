# 候補者取り込みAPI ガイド

Opinio ATS では、4つのチャンネルから候補者データを取り込むAPIを提供しています。

## 概要

| チャンネル | エンドポイント | 用途 |
|-----------|---------------|------|
| Web応募 | `POST /api/intake/web` | 自社採用サイトからの直接応募 |
| エージェント | `POST /api/intake/agent` | 人材紹介会社からの推薦 |
| スカウト | `POST /api/intake/scout` | スカウトサービス（ビズリーチ等）からの応募 |
| リファラル | `POST /api/intake/referral` | 社員紹介プログラム |

## 共通仕様

### ベースURL
```
本番環境: https://ats.example.com/api
開発環境: http://localhost/api
```

### リクエストヘッダー
```
Content-Type: application/json
Accept: application/json
```

### レスポンス形式

**成功時 (201 Created)**
```json
{
  "success": true,
  "message": "応募を受け付けました。",
  "data": {
    "intake_id": 123,
    "draft_id": 456
  }
}
```

**バリデーションエラー (422 Unprocessable Entity)**
```json
{
  "success": false,
  "errors": {
    "email": ["メールアドレスの形式が正しくありません。"],
    "name": ["名前は必須です。"]
  }
}
```

**サーバーエラー (500 Internal Server Error)**
```json
{
  "success": false,
  "error": "応募の処理中にエラーが発生しました。"
}
```

---

## 1. Web応募API

自社採用サイトのエントリーフォームからの応募を受け付けます。

### エンドポイント
```
POST /api/intake/web
```

### リクエストパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| company_id | integer | ✅ | 企業ID |
| job_id | integer | ✅ | 求人ID |
| name | string | ✅ | 候補者名 |
| email | string | ✅ | メールアドレス |
| phone | string | - | 電話番号 |
| resume_url | string | - | 履歴書URL |
| cover_letter | string | - | 志望動機 |
| profile | object | - | その他プロフィール情報 |

### リクエスト例
```bash
curl -X POST https://ats.example.com/api/intake/web \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 1,
    "job_id": 5,
    "name": "山田 太郎",
    "email": "yamada@example.com",
    "phone": "090-1234-5678",
    "resume_url": "https://storage.example.com/resumes/yamada.pdf",
    "cover_letter": "貴社の〇〇に魅力を感じ...",
    "profile": {
      "current_company": "株式会社ABC",
      "current_position": "エンジニア",
      "experience_years": 5
    }
  }'
```

### 採用サイトへの実装依頼

**依頼先:** 採用サイト制作会社 / 社内Web担当

**依頼内容:**
```
採用サイトのエントリーフォームから、以下のAPIにデータを送信する実装をお願いします。

エンドポイント: POST https://ats.example.com/api/intake/web
Content-Type: application/json

送信データ:
- company_id: 1（固定値）
- job_id: 求人詳細ページの求人ID
- name: フォームの「お名前」
- email: フォームの「メールアドレス」
- phone: フォームの「電話番号」
- resume_url: アップロードされた履歴書のURL
- cover_letter: フォームの「志望動機」

※ 応募完了後、レスポンスの success が true であれば完了画面を表示してください。
```

---

## 2. エージェントAPI

人材紹介会社からの候補者推薦を受け付けます。

### エンドポイント
```
POST /api/intake/agent
```

### リクエストパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| company_id | integer | ✅ | 企業ID |
| job_id | integer | ✅ | 求人ID |
| agent_company | string | ✅ | 紹介会社名 |
| agent_name | string | - | 担当者名 |
| agent_email | string | - | 担当者メール |
| candidate | object | ✅ | 候補者情報 |
| candidate.name | string | ✅ | 候補者名 |
| candidate.email | string | - | 候補者メール |
| candidate.phone | string | - | 候補者電話番号 |
| candidate.resume_url | string | - | 履歴書URL |
| candidate.recommendation | string | - | 推薦コメント |
| candidate.profile | object | - | その他プロフィール |

### リクエスト例
```bash
curl -X POST https://ats.example.com/api/intake/agent \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 1,
    "job_id": 5,
    "agent_company": "株式会社リクルートエージェント",
    "agent_name": "佐藤 花子",
    "agent_email": "sato@agent.example.com",
    "candidate": {
      "name": "鈴木 一郎",
      "email": "suzuki@example.com",
      "phone": "080-9876-5432",
      "resume_url": "https://agent.example.com/resumes/suzuki.pdf",
      "recommendation": "即戦力として活躍が期待できます。コミュニケーション能力が高く...",
      "profile": {
        "current_salary": 6000000,
        "expected_salary": 7000000,
        "available_date": "2024-04-01"
      }
    }
  }'
```

### 人材紹介会社への連携依頼

**依頼先:** 契約中の人材紹介会社

**依頼内容（メール例）:**
```
件名: 候補者推薦のAPI連携について

〇〇株式会社
ご担当者様

お世話になっております。
弊社では採用管理システムを導入いたしました。

つきましては、候補者様のご推薦時に以下のAPIをご利用いただけますと、
選考状況の共有がスムーズになりますので、ご検討いただけますと幸いです。

【API仕様】
エンドポイント: POST https://ats.example.com/api/intake/agent
詳細仕様書: （本ドキュメントのURLを共有）

【必要な情報】
- 候補者名、連絡先
- 履歴書・職務経歴書のURL
- ご推薦コメント

ご不明点がございましたらお気軽にお問い合わせください。
```

---

## 3. スカウトAPI

ビズリーチ、Wantedly等のスカウトサービスからの応募を受け付けます。

### エンドポイント
```
POST /api/intake/scout
```

### リクエストパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| company_id | integer | ✅ | 企業ID |
| job_id | integer | - | 求人ID（オープンポジションの場合は省略可） |
| scout_service | string | ✅ | サービス名（ビズリーチ、Wantedly等） |
| scout_id | string | - | サービス側の候補者ID |
| candidate | object | ✅ | 候補者情報 |
| candidate.name | string | ✅ | 候補者名 |
| candidate.email | string | - | 候補者メール |
| candidate.phone | string | - | 候補者電話番号 |
| candidate.profile_url | string | - | サービス上のプロフィールURL |
| candidate.profile | object | - | その他プロフィール |

### リクエスト例
```bash
curl -X POST https://ats.example.com/api/intake/scout \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 1,
    "job_id": 5,
    "scout_service": "ビズリーチ",
    "scout_id": "BR-12345678",
    "candidate": {
      "name": "田中 次郎",
      "email": "tanaka@example.com",
      "profile_url": "https://bizreach.jp/profile/12345678",
      "profile": {
        "current_company": "株式会社XYZ",
        "current_position": "マネージャー",
        "skills": ["Python", "AWS", "チームマネジメント"]
      }
    }
  }'
```

### スカウトサービス連携方法

各サービスの連携方法は異なります。以下を参照してください。

#### ビズリーチ
1. ビズリーチ管理画面 → 設定 → 外部連携
2. Webhook URLに `https://ats.example.com/api/intake/scout` を設定
3. または、定期的なCSVエクスポート → 手動インポート

#### Wantedly
1. Wantedly Admin → 設定 → API連携
2. 応募通知のWebhook設定

#### Green
1. Green管理画面 → システム連携
2. 応募データ連携を有効化

**社内担当者向けメモ:**
```
スカウトサービスでは、サービス側の管理画面から直接API連携できない場合があります。
その場合は、以下の代替手段を検討してください：

1. Zapier / Make(Integromat) 経由で連携
2. メール通知 → メールパーサー → API送信
3. 定期的な手動データ入力（取り込み管理画面から）
```

---

## 4. リファラルAPI

社員紹介プログラムからの応募を受け付けます。

### エンドポイント
```
POST /api/intake/referral
```

### リクエストパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| company_id | integer | ✅ | 企業ID |
| job_id | integer | - | 求人ID（オープンポジションの場合は省略可） |
| referrer | object | ✅ | 紹介者（社員）情報 |
| referrer.employee_id | string | - | 社員番号 |
| referrer.name | string | ✅ | 紹介者名 |
| referrer.email | string | ✅ | 紹介者メール |
| referrer.department | string | - | 所属部署 |
| referrer.relationship | string | - | 候補者との関係（友人、元同僚等） |
| candidate | object | ✅ | 候補者情報 |
| candidate.name | string | ✅ | 候補者名 |
| candidate.email | string | - | 候補者メール |
| candidate.phone | string | - | 候補者電話番号 |
| candidate.recommendation | string | - | 推薦コメント |
| candidate.profile | object | - | その他プロフィール |

### リクエスト例
```bash
curl -X POST https://ats.example.com/api/intake/referral \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 1,
    "job_id": 5,
    "referrer": {
      "employee_id": "EMP-001",
      "name": "高橋 三郎",
      "email": "takahashi@company.com",
      "department": "開発部",
      "relationship": "前職の同僚"
    },
    "candidate": {
      "name": "伊藤 四郎",
      "email": "ito@example.com",
      "phone": "070-1111-2222",
      "recommendation": "前職で3年間一緒に働いていました。技術力が高く、チームワークも抜群です。"
    }
  }'
```

### リファラル制度の運用

**社内周知用資料:**
```
【社員紹介制度のご案内】

知り合いで転職を考えている方がいらっしゃいましたら、
ぜひご紹介ください！

▼ 紹介方法
1. 社内リファラルフォーム（https://internal.example.com/referral）にアクセス
2. 紹介者（ご自身）の情報を入力
3. 候補者の情報を入力
4. 送信

▼ 紹介報酬
- 候補者が入社された場合：◯万円

▼ 対象ポジション
- 現在募集中の全求人

ご不明点は人事部までお問い合わせください。
```

**リファラルフォーム実装依頼（社内システム担当向け）:**
```
社内ポータルにリファラル紹介フォームの実装をお願いします。

【送信先API】
POST https://ats.example.com/api/intake/referral

【フォーム項目】
紹介者情報:
- 社員番号（ログイン情報から自動取得）
- 氏名（ログイン情報から自動取得）
- メール（ログイン情報から自動取得）
- 所属部署
- 候補者との関係

候補者情報:
- 氏名（必須）
- メールアドレス
- 電話番号
- 希望ポジション（求人一覧から選択）
- 推薦コメント
```

---

## データフロー

```
[外部ソース]
    │
    ▼
[Intake API] ─── POST /api/intake/{channel}
    │
    ▼
[ApplicationIntake] ─── 取り込み履歴
    │
    ▼
[IntakeCandidateDraft] ─── 確認待ちドラフト
    │
    ▼
[取り込み管理画面] ─── 人事担当者が確認
    │
    ├── 承認 ───▶ [Person] + [Candidate] + [Application] 作成
    │
    └── 却下 ───▶ ドラフト削除
```

---

## トラブルシューティング

### よくあるエラー

**「指定された求人が見つからないか、募集が終了しています」**
- job_id が正しいか確認
- 求人のステータスが「募集中(open)」か確認

**「company_id が存在しません」**
- 正しい company_id を使用しているか確認
- 本番環境と開発環境で ID が異なる場合があります

**タイムアウトエラー**
- 履歴書等の大きなファイルは、先にストレージにアップロードしてURLを送信してください

---

## お問い合わせ

API連携に関するご質問は、以下までお問い合わせください。

- 社内: 人事システム担当
- 外部: api-support@example.com
