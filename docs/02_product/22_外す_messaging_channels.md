# メッセージングチャネル（Messaging Channels）

本ドキュメントは、Opinio ATS における  
**機能定義：メッセージングチャネル** を扱う。

---

## 機能の位置づけ
メッセージングチャネルは、  
Opinio ATS において **候補者・エージェント・社内メンバーとのやりとりを、チャネル横断で一元管理するための機能**である。

本機能は、
- 連絡手段を増やすこと
- 自動化を最大化すること

を目的とするものではない。

あくまで、
**「誰と・いつ・何をやり取りしたか」を後から説明できる状態を保つ**
ことを主目的とする。

---

## 基本思想（重要）
- チャネルの違いは「伝達手段の違い」にすぎない
- 判断や評価は、メッセージ内容から自動生成しない
- すべてのやりとりは履歴として残す
- 個人の私的連絡への侵入は行わない

---

## 対応チャネル（初期）
- メール
- LINE（公式アカウント連携）
- Slack
- Chatwork

※ SMS / WhatsApp 等は将来拡張  
※ チャネルの ON / OFF は会社単位で設定可能

---

## メッセージの位置づけ
- メッセージは **Application（応募）に紐づく事実ログ** として扱う
- 評価・合否・判断の確定を意味しない
- 自動送信・手動送信を区別して記録する

---

## 主な利用シーン
- 面接日程調整の連絡
- 面接前後のリマインド
- 選考結果の連絡
- 候補者からの質問対応
- エージェントとの調整

---

## チャネル統合の考え方
- UI 上は **タイムラインとして統合表示**する
- 実際の送受信は各外部サービスに委譲する
- 送信失敗・未達も状態として保持する

---

## データモデル（必須）

### Message
- message_id
- application_id
- channel_type  
  （email / line / slack / chatwork）
- direction  
  （outbound / inbound）
- sender_type  
  （candidate / agent / user / system）
- sender_identifier
- body
- sent_at
- received_at
- status  
  （sent / delivered / failed / read）
- created_at

---

### MessageChannelSetting
- setting_id
- company_id
- channel_type
- is_enabled
- config_payload
- created_at
- updated_at

---

### MessageTemplate
- template_id
- company_id
- channel_type
- usage_type  
  （schedule / reminder / result / custom）
- subject
- body
- is_active
- created_at
- updated_at

---

## 送受信フロー（概要）
1. UI または自動トリガーから送信指示
2. チャネル設定を確認
3. 外部 API へ送信
4. 結果を Message として保存
5. 受信時は Webhook 等で取り込み
6. タイムラインに反映

---

## コントローラ設計

### MessagingController
- index  
  タイムライン表示
- send  
  メッセージ送信
- retry  
  送信失敗時の再送

---

### MessageTemplateController
- index  
  テンプレート一覧
- create / update  
  テンプレート管理

---

## Service

### MessagingDispatchService
- チャネル別送信処理
- 送信可否チェック
- 失敗時ハンドリング

---

### IncomingMessageService
- 外部チャネルからの受信処理
- Application への紐付け
- 重複防止

---

## 権限・制御
- メッセージ閲覧権限は Application 権限に準拠
- 送信権限はロール・権限で制御
- 個人チャネル（Slack DM 等）は opt-in 前提

---

## 監査・ログ
- すべての送受信を AuditLog 対象とする
- 内容改ざんは不可
- 削除は論理削除のみ

---

## 他機能との関係
- 日程調整：  
  リマインド・候補日連絡
- 通知機能：  
  未読・重要通知生成
- 応募チャネル：  
  エージェント連絡の一部
- 判断履歴：  
  判断前後の文脈補足

---

## 本機能で「やらないこと」
- 内容解析による自動評価
- 感情分析・スコア化
- 私的連絡の監視
- 外部チャネルへの強制移行

---

## 一文定義（迷ったとき用）
メッセージングチャネルは、  
**採用に関わるやりとりを、説明可能な履歴として束ねるための機能である。**
