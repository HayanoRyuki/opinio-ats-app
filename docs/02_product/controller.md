# Opinio ATS｜コントローラ定義一覧

本ドキュメントは、02_product 配下で定義された  
**すべての機能に登場する Controller を一覧化した正本**である。

本ドキュメントに記載の Controller 定義は、
- ルーティング設計
- 権限設計（Policy）
- API設計
- 画面設計

の起点とする。

---

## 2-1 候補者管理

### CandidateController
- index
- show
- create
- store
- update

---

## 2-2 選考パイプライン管理

### ApplicationController
- show

### PipelineController
- board
- move
- history

---

## 2-3 日程調整・カレンダー連携

### InterviewScheduleController
- index
- create
- store
- update

---

## 2-4 メッセージ・コミュニケーション

### MessageController
- index
- show
- store

### MessageTemplateController
- index
- store

---

## 2-5 求人管理・キャリアページ

### JobController
- index
- create
- store
- edit
- update
- close

### CareerPageController
- index
- show

---

## 2-6 評価観点・質問項目

### EvaluationPerspectiveController
- index
- create
- store
- edit
- update
- deactivate

### EvaluationQuestionController
- store
- update
- deactivate

### InterviewPerspectiveMemoController
- store
- update

---

## 2-7 ダッシュボード・KPI

### DashboardController
- index
- summary
- pipeline
- channel

### DashboardMetricController
- index
- update

---

## 2-8 分析・レポート

### ReportController
- index
- show
- export

---

## 2-9 応募取り込み（メール等）

### ApplicationIntakeController
- index
- show
- classify
- convert

---

## 2-10 エージェント推薦

### AgentRecommendationController
- index
- show
- link
- unlink
- archive

---

## 2-11 設定・チーム管理

### SettingsController
- index
- update

### UserManagementController
- index
- invite
- updateRole
- deactivate

### RolePermissionController
- index
- edit
- update
