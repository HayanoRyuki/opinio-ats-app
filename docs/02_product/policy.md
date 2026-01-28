# Opinio ATS｜Policy 定義一覧

本ドキュメントは、Opinio ATS における  
**Policy（権限制御・操作可否判定）** を一覧化した正本である。

Policy は以下を責務とする。

- 誰が「何を見られるか」「何を操作できるか」を明確にする
- 判断・評価・結論には関与しない
- UI / API の双方から共通で利用可能である
- すべての操作可否が説明可能であること

---

## Policy 設計原則（厳守）

- Policy は **可否のみを返す**
- Policy は **理由・評価・判断を返さない**
- Policy は **Controller / Service から独立**する
- 「とりあえず許可」「暗黙の管理者権限」は作らない
- データの存在有無と操作権限を混同しない

---

## 2-1 候補者管理

### CandidatePolicy
- view  
  候補者閲覧可否
- create  
  候補者作成可否
- update  
  候補者情報更新可否
- hide  
  非表示・無効化可否

---

## 2-2 選考パイプライン管理

### ApplicationPolicy
- view  
  応募閲覧可否
- move  
  ステップ移動可否
- history  
  遷移履歴閲覧可否

---

### PipelinePolicy
- view  
  パイプライン全体閲覧可否
- operate  
  パイプライン操作可否

---

## 2-3 日程調整・カレンダー連携

### InterviewSchedulePolicy
- view  
  面接予定閲覧可否
- create  
  日程作成可否
- update  
  日程変更・キャンセル可否

---

## 2-4 メッセージ・コミュニケーション

### MessagePolicy
- view  
  メッセージ閲覧可否
- send  
  メッセージ送信可否

---

### MessageTemplatePolicy
- view  
  テンプレート閲覧可否
- manage  
  テンプレート作成・編集可否

---

## 2-5 求人管理・キャリアページ

### JobPolicy
- view  
  求人閲覧可否
- create  
  求人作成可否
- update  
  求人更新可否
- publish  
  公開・非公開切り替え可否
- close  
  募集終了可否

---

### CareerPagePolicy
- view  
  公開求人閲覧可否

※ 原則すべて公開  
※ 非公開求人は制御対象

---

## 2-6 評価観点・質問項目

### EvaluationPerspectivePolicy
- view  
  観点閲覧可否
- create  
  観点作成可否
- update  
  観点編集可否
- deactivate  
  観点無効化可否

---

### EvaluationQuestionPolicy
- manage  
  質問追加・編集・無効化可否

---

### InterviewPerspectiveMemoPolicy
- view  
  面接メモ閲覧可否
- write  
  面接メモ記入・更新可否

---

## 2-7 ダッシュボード・KPI

### DashboardPolicy
- view  
  ダッシュボード閲覧可否

---

### DashboardMetricPolicy
- manage  
  指標表示設定変更可否

---

## 2-8 分析・レポート

### ReportPolicy
- view  
  レポート閲覧可否

---

### ReportExportPolicy
- export  
  CSV / PDF 出力可否

---

## 2-9 応募取り込み（メール等）

### IntakePolicy
- view  
  応募取り込み閲覧可否
- classify  
  チャネル・扱い変更可否
- convert  
  正式応募への昇格可否

---

### IntakeDeletePolicy
- ignore  
  無効化・除外可否

※ 削除は行わない  
※ 非表示・除外のみ許可

---

## 2-10 エージェント推薦

### RecommendationPolicy
- view  
  推薦閲覧可否
- link  
  候補者・応募紐付け可否
- unlink  
  紐付け解除可否

---

### RecommendationEditPolicy
- edit  
  推薦整理・補足編集可否
- archive  
  非表示・保管可否

---

## 2-11 設定・チーム管理

### UserPolicy
- view  
  ユーザー閲覧可否
- invite  
  ユーザー招待可否
- update  
  ユーザー情報更新可否
- deactivate  
  無効化可否

---

### RolePolicy
- view  
  ロール閲覧可否
- manage  
  ロール作成・編集可否

---

### SettingsPolicy
- view  
  設定閲覧可否
- update  
  システム設定変更可否

---

## Policy 層で「やらないこと」

- 合否判断
- 評価・優劣の決定
- スコア・ランク付け
- 承認フローの代替
- 人事制度・処遇判断

---

## 一文定義（迷ったとき用）

Policy は、  
**誰がどこまで操作できるかを決めるだけの層であり、  
何が正しいかを決める層ではない。**
