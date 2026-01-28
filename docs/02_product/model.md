# Opinio ATS｜プロダクトモデル定義

本ドキュメントは、02_product 配下で定義された  
**すべての機能に登場するデータモデルを一覧化した正本**である。

本ドキュメントに記載のモデル定義は、
- 実装
- API設計
- データ設計
- ドメイン整理

の起点とする。

---

## 2-1 候補者管理

### Candidate
- candidate_id
- person_id
- company_id
- display_name
- email
- phone
- status
- created_at

### CandidateNote
- candidate_note_id
- candidate_id
- note_type
- body
- created_by
- created_at

---

## 2-2 選考パイプライン管理

### Application
- application_id
- candidate_id
- job_id
- channel_type
- current_step_id
- applied_at
- status

### SelectionStep
- selection_step_id
- job_id
- step_order
- step_type
- is_active

### StepTransition
- step_transition_id
- application_id
- from_step_id
- to_step_id
- moved_by
- moved_at

---

## 2-3 日程調整・カレンダー連携

### InterviewSchedule
- interview_schedule_id
- application_id
- interviewer_id
- scheduled_at
- duration_minutes
- meeting_url
- calendar_type
- status
- created_at

---

## 2-4 メッセージ・コミュニケーション

### Message
- message_id
- application_id
- channel_type
- sender_type
- body
- sent_at
- created_at

### MessageTemplate
- template_id
- company_id
- usage_type
- body
- created_at

---

## 2-5 求人管理・キャリアページ

### Job
- job_id
- company_id
- title
- description_public
- description_internal
- expected_role
- status
- published_at
- created_at
- updated_at

### JobRevision
- revision_id
- job_id
- changed_fields
- changed_by
- created_at

---

## 2-6 評価観点・質問項目

### EvaluationPerspective
- perspective_id
- company_id
- name
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

---

## 2-7 ダッシュボード・KPI

### DashboardMetricDefinition
ダッシュボードで表示するKPIの定義。
「4つのKPI」に限定し、評価・ランキング用途では使用しない。

- metric_key  
  （例：applications_count / in_progress_count / offer_accept_rate 等）
- label
- description
- aggregation_rule  
  （count / ratio / days_diff など）
- comparison_rule  
  （前週比・前月比など。null可）
- is_active
- created_at
- updated_at

---

### DashboardMetricSnapshot
KPIの期間スナップショット。
過去比較・説明責任のために保存する。

- snapshot_id
- metric_key
- company_id
- value
- comparison_value  
  （前期間との差分。null可）
- period_start
- period_end
- calculated_at

※ スコア・順位・評価点は保持しない

---

### DashboardActionItem
「今すぐ対応が必要なこと」を表す行動単位のモデル。

- action_item_id
- company_id
- action_type  
  （schedule_pending / evaluation_missing / reply_pending / long_stagnation 等）
- related_type  
  （application / interview / message 等）
- related_id
- priority_level  
  （low / medium / high）
- detected_at
- resolved_at（null可）

※ 自動判断は「対応が必要そう」という検知まで  
※ 対応の是非・判断は人が行う

---

### DashboardActionRule
ActionItem を生成するための検知ルール定義。

- rule_id
- action_type
- description
- condition_definition  
  （滞留日数・未入力時間など）
- is_active
- created_at
- updated_at

---

## 2-8 分析・レポート

### ReportDefinition
- report_key
- title
- description
- available_filters
- is_active
- created_at
- updated_at

### ReportResultSnapshot
- snapshot_id
- report_key
- company_id
- result_data
- period_start
- period_end
- calculated_at

---

## 2-9 応募取り込み（メール等）

### ApplicationIntake
- intake_id
- company_id
- channel_type
- source_type
- raw_payload
- received_at
- processed_at
- status

### IntakeCandidateDraft
- draft_id
- intake_id
- person_id
- name
- email
- resume_text
- memo
- created_at

---

## 2-10 エージェント推薦

### Agent
- agent_id
- company_id
- agent_type
- name
- organization
- contact_info
- is_active
- created_at
- updated_at

### AgentRecommendation
- recommendation_id
- agent_id
- candidate_id
- job_id
- recommendation_text
- match_points
- concern_points
- received_at
- source_type
- raw_payload
- created_at

### RecommendationLink
- link_id
- recommendation_id
- candidate_id
- application_id
- linked_at

---

## 2-11 設定・チーム管理

### User
- user_id
- company_id
- email
- name
- status
- created_at
- updated_at

### Team
- team_id
- company_id
- name
- description
- created_at

### Role
- role_id
- company_id
- name
- description
- is_system_role
- created_at

### Permission
- permission_key
- description
- scope

### UserRole
- user_id
- role_id
- assigned_at

### RolePermission
- role_id
- permission_key

### AuditLog
- log_id
- actor_user_id
- action_type
- target_type
- target_id
- before_state
- after_state
- occurred_at
