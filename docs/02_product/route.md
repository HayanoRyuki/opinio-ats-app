# Opinio ATS｜ルート定義骨子（画面 / API）

本ドキュメントは、02_product 配下で定義された機能について  
**画面ルート（Web）および API ルートの骨子を一覧化した正本**である。

実装時は、本定義を起点として
- middleware
- prefix
- versioning
- 権限制御

を追加する。

---

## 共通前提

- 画面系： `/app/*`
- API系： `/api/v1/*`
- 認証必須（共通）
- company_id はセッション or JWT から解決

---

## 2-1 候補者管理

### Web
- GET    /app/candidates
- GET    /app/candidates/{candidate_id}
- GET    /app/candidates/create
- POST   /app/candidates
- PUT    /app/candidates/{candidate_id}

### API
- GET    /api/v1/candidates
- GET    /api/v1/candidates/{candidate_id}
- POST   /api/v1/candidates
- PUT    /api/v1/candidates/{candidate_id}

---

## 2-2 選考パイプライン管理

### Web
- GET    /app/applications/{application_id}
- GET    /app/pipeline
- POST   /app/pipeline/move
- GET    /app/pipeline/history

### API
- GET    /api/v1/applications/{application_id}
- GET    /api/v1/pipeline
- POST   /api/v1/pipeline/move
- GET    /api/v1/pipeline/history

---

## 2-3 日程調整・カレンダー連携

### Web
- GET    /app/interviews
- GET    /app/interviews/create
- POST   /app/interviews
- PUT    /app/interviews/{schedule_id}

### API
- GET    /api/v1/interviews
- POST   /api/v1/interviews
- PUT    /api/v1/interviews/{schedule_id}

---

## 2-4 メッセージ・コミュニケーション

### Web
- GET    /app/messages
- GET    /app/messages/{thread_id}
- POST   /app/messages

- GET    /app/message-templates
- POST   /app/message-templates

### API
- GET    /api/v1/messages
- GET    /api/v1/messages/{thread_id}
- POST   /api/v1/messages

- GET    /api/v1/message-templates
- POST   /api/v1/message-templates

---

## 2-5 求人管理・キャリアページ

### Web（管理）
- GET    /app/jobs
- GET    /app/jobs/create
- POST   /app/jobs
- GET    /app/jobs/{job_id}/edit
- PUT    /app/jobs/{job_id}
- POST   /app/jobs/{job_id}/close

### Web（公開）
- GET    /careers
- GET    /careers/{job_id}

### API
- GET    /api/v1/jobs
- POST   /api/v1/jobs
- PUT    /api/v1/jobs/{job_id}
- POST   /api/v1/jobs/{job_id}/close

---

## 2-6 評価観点・質問項目

### Web
- GET    /app/evaluation-perspectives
- GET    /app/evaluation-perspectives/create
- POST   /app/evaluation-perspectives
- PUT    /app/evaluation-perspectives/{perspective_id}
- POST   /app/evaluation-perspectives/{perspective_id}/deactivate

- POST   /app/evaluation-questions
- PUT    /app/evaluation-questions/{question_id}
- POST   /app/evaluation-questions/{question_id}/deactivate

- POST   /app/interview-memos
- PUT    /app/interview-memos/{memo_id}

### API
- GET    /api/v1/evaluation-perspectives
- POST   /api/v1/evaluation-perspectives
- PUT    /api/v1/evaluation-perspectives/{perspective_id}
- POST   /api/v1/evaluation-perspectives/{perspective_id}/deactivate

---

## 2-7 ダッシュボード・KPI

### Web
- GET    /app/dashboard
- GET    /app/dashboard/summary
- GET    /app/dashboard/pipeline
- GET    /app/dashboard/channel

### API
- GET    /api/v1/dashboard
- GET    /api/v1/dashboard/summary
- GET    /api/v1/dashboard/pipeline
- GET    /api/v1/dashboard/channel

---

## 2-8 分析・レポート

### Web
- GET    /app/reports
- GET    /app/reports/{report_key}
- GET    /app/reports/{report_key}/export

### API
- GET    /api/v1/reports
- GET    /api/v1/reports/{report_key}
- GET    /api/v1/reports/{report_key}/export

---

## 2-9 応募取り込み（メール等）

### Web
- GET    /app/intakes
- GET    /app/intakes/{intake_id}
- POST   /app/intakes/{intake_id}/classify
- POST   /app/intakes/{intake_id}/convert

### API
- GET    /api/v1/intakes
- GET    /api/v1/intakes/{intake_id}
- POST   /api/v1/intakes/{intake_id}/classify
- POST   /api/v1/intakes/{intake_id}/convert

---

## 2-10 エージェント推薦

### Web
- GET    /app/recommendations
- GET    /app/recommendations/{recommendation_id}
- POST   /app/recommendations/{recommendation_id}/link
- POST   /app/recommendations/{recommendation_id}/unlink
- POST   /app/recommendations/{recommendation_id}/archive

### API
- GET    /api/v1/recommendations
- GET    /api/v1/recommendations/{recommendation_id}
- POST   /api/v1/recommendations/{recommendation_id}/link
- POST   /api/v1/recommendations/{recommendation_id}/unlink
- POST   /api/v1/recommendations/{recommendation_id}/archive

---

## 2-11 設定・チーム管理

### Web
- GET    /app/settings
- PUT    /app/settings

- GET    /app/settings/users
- POST   /app/settings/users/invite
- PUT    /app/settings/users/{user_id}/role
- POST   /app/settings/users/{user_id}/deactivate

- GET    /app/settings/roles
- GET    /app/settings/roles/{role_id}/edit
- PUT    /app/settings/roles/{role_id}

### API
- GET    /api/v1/settings
- PUT    /api/v1/settings

- GET    /api/v1/users
- POST   /api/v1/users/invite
- PUT    /api/v1/users/{user_id}/role
- POST   /api/v1/users/{user_id}/deactivate

- GET    /api/v1/roles
- PUT    /api/v1/roles/{role_id}
