# 評価観点・質問項目

本ドキュメントは、Opinio ATS における **機能定義 2-6：評価観点・質問項目** を扱う。

## 機能の位置づけ
評価観点・質問項目は、  
Opinio ATS において **面接時の思考整理と抜け漏れ防止を支援するための補助機能**である。

本機能は、
- 採用判断を自動化・定量化するためのものではなく
- 面接官が「何をどう確認したか」を言語として残すための支援機構

として設計される。

---

## 扱う内容
- 面接で確認したい観点  
  （例：経験、スタンス、期待役割との関係）
- 質問の例（問いのヒント）
- 観点ごとの自由記述メモ欄

---

## 設計上の前提
以下は、本機能を「評価確定」ではなく「思考補助」として扱うための前提である。

- 点数化・ランク化は一切行わない
- 合否や評価を確定する仕組みを持たない
- 観点は「正解」ではなく思考の補助線として扱う
- 面接官ごとの視点の違いを許容する

---

## 設計思想
- 観点は「評価軸」ではなく **確認の視点**
- 質問は「聞くべきこと」ではなく **考えるための問い**
- メモは「結論」ではなく **その場の気づき**

評価を固めないことで、
- 後から振り返れる
- 他者と視点を共有できる
- 法的・制度的な判断領域に踏み込まない

構造を保つ。

---

## データモデル（必須）

### EvaluationPerspective
- perspective_id
- company_id
- name  
  （例：業務経験／スタンス／価値観）
- description
- order
- is_active
- created_at
- updated_at

### EvaluationQuestion
- question_id
- perspective_id
- question_text
- hint_text
- order
- is_active
- created_at
- updated_at

### InterviewPerspectiveMemo
- memo_id
- interview_id
- perspective_id
- memo_text
- created_by
- created_at

※ 点数・選択肢・ランク系のカラムは持たない  
※ すべて自由記述を前提とする

---

## コントローラ設計

### EvaluationPerspectiveController
- index  
  観点一覧
- create  
  観点作成
- store  
  観点保存
- edit  
  観点編集
- update  
  観点更新
- deactivate  
  観点無効化

### EvaluationQuestionController
- store  
  質問追加
- update  
  質問更新
- deactivate  
  質問無効化

### InterviewPerspectiveMemoController
- store  
  面接メモ保存
- update  
  面接メモ更新

---

## ビュー設計
- evaluation/perspectives/index  
  観点管理一覧
- evaluation/perspectives/form  
  観点・質問編集画面
- interview/perspective_memo  
  面接中の観点メモ入力UI

---

## Controller / View 以外に必要な要素

### Service
#### PerspectiveTemplateService
- 企業ごとの観点テンプレ管理
- 初期観点セットの複製
- 将来の共有テンプレ対応余地

---

### Policy
- EvaluationPerspectivePolicy  
  観点・質問の編集権限管理
- InterviewPerspectiveMemoPolicy  
  面接メモの閲覧・編集権限管理

---

### Hook / 拡張余地
- 求人ごとの観点セット切り替え
- 面接種別（一次／最終）ごとの観点表示
- AI要約  
  ※ 確定判断には使用しない

---

## 他機能との関係
- 面接管理：  
  面接ごとに観点メモが紐づく
- 候補者管理：  
  メモは候補者履歴の一部として参照可能
- 分析・レポート：  
  観点の使用頻度・傾向分析  
  ※ 定量評価は行わない

---

## 本機能で「やらないこと」
以下は、Opinio ATS の思想に基づく設計判断である。

- 評価点・ランクの付与
- 合否判断の確定
- 自動評価・スコアリング
- 処遇・制度判断への接続

---

## 一文定義（迷ったとき用）
評価観点・質問項目は、  
**採用判断を下すための物差しではなく、考えるための補助線である。**
