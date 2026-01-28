# エージェント推薦

本ドキュメントは、Opinio ATS における **機能定義：エージェント推薦** を扱う。

## 機能の位置づけ
エージェント推薦は、  
人材エージェント（人）および AI エージェントから提示される  
**候補者に関する「推薦情報」を整理・参照するための補助機能**である。

本機能は、
- 推薦内容を判断材料の一部として可視化する
- 推薦の背景・文脈を失わずに残す
- 採用判断を人が再解釈する余地を確保する

ことを目的とし、  
**推薦内容を正・判断根拠・結論として扱わない**。

---

## 扱う情報
- 推薦理由（エージェント視点の説明）
- マッチポイント（求人・期待役割との対応）
- 注意点・懸念点
- 推薦日時・推薦者（エージェント）

※ いずれも **主観情報** として扱う  
※ 点数・ランク・確定評価は保持しない

---

## 基本思想
- 推薦 ＝ 判断ではない
- 推薦は「視点の一つ」に過ぎない
- 鵜呑みにしない前提で設計する

エージェント推薦は、  
**採用担当者の思考を補助するための材料**であり、  
意思決定を代替するものではない。

---

## 表示・UI設計の原則
- 「推薦情報」であることを明示する
- 断定的表現を強調しない
- 懸念点・前提条件も同列で表示する
- 候補者本人の事実情報と混在させない

---

## データモデル（必須）

### Agent
- agent_id
- company_id
- agent_type（human / ai）
- name
- organization
- contact_info
- is_active
- created_at
- updated_at

---

### AgentRecommendation
推薦情報の本体。

- recommendation_id
- agent_id
- candidate_id（未確定可）
- job_id（任意）
- recommendation_text
- match_points
- concern_points
- received_at
- source_type（mail / form / api）
- raw_payload（JSON / 原文）
- created_at

※ raw_payload は必ず保持する  
※ recommendation_text 等は整理後の参照用データとする

---

### RecommendationLink
候補者・応募との関係性を表す。

- link_id
- recommendation_id
- candidate_id
- application_id（任意）
- linked_at

---

## 推薦取り込みフロー

1. エージェントから推薦が届く
2. 原文・payload を保存
3. 推薦情報として構造化
4. 候補者・応募と紐付け（任意）
5. 参照・検討に利用

※ 推薦単体で選考は開始しない  
※ 必ず人の確認を挟む

---

## コントローラ設計

### AgentRecommendationController
- index  
  推薦一覧
- show  
  推薦詳細（原文含む）
- link  
  候補者・応募との紐付け
- unlink  
  紐付け解除
- archive  
  非表示・保管

---

## ビュー設計

### 表示系
- recommendations/index  
  推薦一覧
- recommendations/show  
  推薦詳細
- recommendations/partials/recommendation_body  
  推薦理由・マッチ・懸念表示
- recommendations/partials/raw_payload  
  原文・通知内容表示

---

## Controller / View 以外に必要な要素

### Service

#### RecommendationParserService
- 推薦文の構造整理
- マッチポイント・懸念点の抽出補助
- 不完全データ前提の安全処理

---

#### RecommendationLinkService
- 候補者・応募との紐付け管理
- 重複推薦の検出
- 履歴管理

---

### Job / Batch

#### RecommendationReceiveJob
- メール・Webhook 受信
- 非同期保存
- raw_payload の正規化

---

### Policy
- RecommendationPolicy  
  閲覧・紐付け権限
- RecommendationEditPolicy  
  編集・整理権限

---

## ログ・監査の考え方
- 推薦受信日時
- 紐付け・解除履歴
- 編集履歴（整理・補足）

※ 推薦内容は「判断記録」にはしない  
※ 処遇・採否判断と切り離す

---

## 他機能との関係
- 応募取り込み：  
  推薦通知の入口
- 候補者管理：  
  候補者への補足情報
- 求人管理：  
  マッチ文脈の参照
- メッセージ機能：  
  エージェントとのやりとり履歴

---

## 本機能で「やらないこと」
- 推薦スコアリング
- 推薦内容による自動選考
- 推薦＝内定・合格の示唆
- 処遇判断への接続

---

## 一文定義（迷ったとき用）
エージェント推薦は、  
**判断の材料であって、判断そのものではない。**
