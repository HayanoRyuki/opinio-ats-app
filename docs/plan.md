# 2月16日週 実装プラン

> 期間: 2026/02/16（月）〜 02/22（日）
> 目標: MVP 全機能を一気通貫で動作可能な状態にする

---

## 週の全体像

```
月     火     水     木     金     土日
─────────────────────────────────────────
Day1   Day2   Day3   Day4   Day5   予備
パイプ パイプ ダッシュ 面接   評価   設定
ライン ライン ボード  スケ   フレーム ユーザ
+候補者 +バグ  +求人   ジュール ワーク  管理
詳細   修正   クローズ       +キャリア
```

---

## Day 1（月）: パイプライン Kanban + 候補者詳細

### AM — パイプライン基盤

**1-1. StepTransition テーブル追加**
```
ファイル: database/migrations/xxxx_create_step_transitions_table.php
カラム: id, application_id, from_step_id, to_step_id, moved_by, reason, moved_at
```

**1-2. PipelineTransitionService**
```
ファイル: app/Services/PipelineTransitionService.php
メソッド:
  - moveStep(application, toStep, user, reason)  → StepTransition 作成 + ApplicationStep 更新
  - getHistory(application)  → 遷移履歴取得
  - validateTransition(application, toStep)  → 移動可否チェック
```

**1-3. PipelineController に move / history 追加**
```
ファイル: app/Http/Controllers/Web/PipelineController.php (既存)
追加:
  - POST /pipeline/move  → moveStep アクション
  - GET  /pipeline/{application}/history  → 遷移履歴 JSON
```

### PM — 候補者詳細の充実

**1-4. 候補者詳細画面の強化**
```
ファイル: resources/js/Pages/Candidates/Show.vue (既存)
追加:
  - 応募一覧セクション（Application 履歴）
  - タイムライン表示（StepTransition + CandidateMessage 統合）
  - 候補者情報の編集フォーム（モーダル）
```

**1-5. CandidateController に update 追加**
```
ファイル: app/Http/Controllers/Web/CandidateController.php (既存)
追加: PUT /candidates/{candidate} → update アクション
```

### 成果物
- [ ] step_transitions テーブルが作成される
- [ ] 候補者詳細で応募履歴・タイムラインが見える
- [ ] 候補者情報を編集できる

---

## Day 2（火）: パイプライン Kanban UI + バグ修正

### AM — Kanban UI

**2-1. パイプラインボード Kanban 化**
```
ファイル: resources/js/Pages/Pipeline/Index.vue (既存を大幅改修)
実装:
  - 求人ごとにカラム＝選考ステップ、カード＝応募者
  - ドラッグ&ドロップで ApplicationStep を移動
  - 移動時に PipelineTransitionService.moveStep() を呼出
  - 各カードに候補者名、現在日数、チャネルバッジ
ライブラリ: vuedraggable or @dnd-kit 相当
```

**2-2. パイプラインのフィルタ機能**
```
追加:
  - 求人で絞り込み
  - ステータスで絞り込み（進行中 / 不合格 / 内定）
  - 検索（候補者名）
```

### PM — 全画面バグ修正

**2-3. 各ページの動作確認 & エラー修正**
```
対象（優先順）:
  1. /intake — ドラフト確認 → SoT 登録のフロー通し確認
  2. /candidates — 一覧表示、検索、フィルタ
  3. /jobs — CRUD 全操作
  4. /agents — CRUD 全操作
  5. /recommendations — リンク / アンリンク
  6. /applications — 一覧・詳細表示
  7. /pipeline — Kanban 動作
```

### 成果物
- [ ] Kanban ボードでドラッグ&ドロップでステップ移動できる
- [ ] 主要画面のエラーが解消されている

---

## Day 3（水）: ダッシュボード KPI + 求人クローズ

### AM — ダッシュボード

**3-1. DashboardController にKPI集計ロジック追加**
```
ファイル: app/Http/Controllers/DashboardController.php (既存)
追加データ:
  - 今月の応募数（チャネル別）
  - 進行中の応募数
  - 今月の面接数
  - 内定数 / 不合格数
  - 歩留まり率（応募→面接→内定）
  - 直近のアクションアイテム（長期停滞、未対応ドラフトなど）
```

**3-2. ダッシュボード UI 刷新**
```
ファイル: resources/js/Pages/Dashboard.vue (既存を大幅改修)
レイアウト:
  - KPI カード 4枚（応募数、面接数、内定数、歩留まり率）
  - チャネル別応募グラフ（棒グラフ）
  - 直近アクションアイテム一覧
  - パイプライン概要（ステップ別件数の横棒グラフ）
```

### PM — 求人クローズ

**3-3. 求人クローズ機能**
```
ファイル: app/Http/Controllers/Web/JobController.php (既存)
追加:
  - POST /jobs/{job}/close → ステータスを closed に変更
  - close 時に進行中の Application を警告表示
```

**3-4. 求人ステータスバッジの追加**
```
ファイル: resources/js/Pages/Jobs/Index.vue (既存)
追加: draft / open / closed のバッジ表示 + フィルタ
```

### 成果物
- [ ] ダッシュボードに KPI カードとグラフが表示される
- [ ] 求人をクローズできる（進行中応募の警告付き）

---

## Day 4（木）: 面接スケジュール

### AM — DB & バックエンド

**4-1. interview_schedules テーブル作成**
```
ファイル: database/migrations/xxxx_create_interview_schedules_table.php
カラム:
  id, application_id, interviewer_id (user_id), scheduled_at,
  duration_minutes, location, meeting_url, memo, status (scheduled/completed/cancelled),
  created_at, updated_at
```

**4-2. InterviewSchedule モデル & リレーション**
```
ファイル: app/Models/InterviewSchedule.php
リレーション:
  - application() BelongsTo
  - interviewer() BelongsTo (User)
```

**4-3. InterviewScheduleService**
```
ファイル: app/Services/InterviewScheduleService.php
メソッド:
  - create(application, interviewer, datetime, options)
  - update(schedule, data)
  - cancel(schedule, reason)
  - getUpcoming(user)  → 面接官の今後の面接一覧
```

### PM — UI

**4-4. 面接 CRUD UI**
```
ファイル:
  - resources/js/Pages/Interviews/Index.vue (既存を改修)
  - resources/js/Pages/Interviews/Create.vue (新規)
  - resources/js/Pages/Interviews/Show.vue (新規)
内容:
  - 面接一覧（カレンダービュー or リスト）
  - 面接登録フォーム（応募選択、面接官選択、日時、場所/URL）
  - 面接詳細（情報表示 + キャンセル操作）
```

**4-5. InterviewController の CRUD 実装**
```
ファイル: app/Http/Controllers/InterviewController.php (既存)
追加: index, create, store, show, update, cancel
ルート:
  GET    /interviews
  GET    /interviews/create
  POST   /interviews
  GET    /interviews/{id}
  PUT    /interviews/{id}
  POST   /interviews/{id}/cancel
```

### 成果物
- [ ] 面接を登録・一覧表示・キャンセルできる
- [ ] 応募詳細から面接を紐づけて登録できる

---

## Day 5（金）: 評価フレームワーク + キャリアページ

### AM — 評価フレームワーク

**5-1. 評価テーブル作成**
```
マイグレーション:
  - evaluation_perspectives: id, company_id, name, description, sort_order, is_active
  - evaluation_questions: id, perspective_id, question_text, hint_text, sort_order, is_active
  - interview_perspective_memos: id, interview_schedule_id, perspective_id, memo_text, rating, created_by
```

**5-2. 評価モデル & コントローラー**
```
モデル:
  - EvaluationPerspective (HasMany questions)
  - EvaluationQuestion (BelongsTo perspective)
  - InterviewPerspectiveMemo (BelongsTo interview, perspective)

コントローラー:
  - EvaluationPerspectiveController: index, store, update, deactivate
  - InterviewPerspectiveMemoController: store, update
```

**5-3. 評価管理 UI**
```
ファイル:
  - resources/js/Pages/Evaluations/Index.vue (新規)
  - resources/js/Pages/Evaluations/PerspectiveForm.vue (新規)
内容:
  - 評価観点の一覧 & 並べ替え
  - 観点ごとの質問編集
  - 面接メモ入力（面接詳細画面に統合）
```

### PM — キャリアページ

**5-4. キャリアページ（公開用）**
```
コントローラー: app/Http/Controllers/CareerPageController.php (新規)
ルート:
  GET /careers           → 公開求人一覧
  GET /careers/{job_id}  → 求人詳細

Vue ページ:
  - resources/js/Pages/Careers/Index.vue (新規)
  - resources/js/Pages/Careers/Show.vue (新規)

内容:
  - 会社情報ヘッダー
  - 公開中求人のカード一覧
  - 求人詳細（説明、要件、応募ボタン）
  - 認証なしでアクセス可能（public ルート）
```

### 成果物
- [ ] 評価観点・質問を設定できる
- [ ] 面接時にメモを観点別に入力できる
- [ ] 公開キャリアページで求人を閲覧できる

---

## 土日（予備）: 設定・ユーザー管理 + 仕上げ

### ユーザー管理

**6-1. ユーザー管理画面**
```
コントローラー: app/Http/Controllers/Web/UserManagementController.php (新規)
ルート:
  GET  /settings/users           → ユーザー一覧
  POST /settings/users/invite    → ユーザー招待
  PUT  /settings/users/{id}/role → ロール変更
  POST /settings/users/{id}/deactivate → 無効化

Vue:
  - resources/js/Pages/Settings/Users/Index.vue (新規)
```

**6-2. ロール・権限管理**
```
Vue:
  - resources/js/Pages/Settings/Roles/Index.vue (新規)
内容:
  - Admin / Recruiter / Interviewer のロール一覧
  - ロールごとの権限チェックボックス
```

### 統合テスト & 仕上げ

**6-3. 全画面通し確認**
```
シナリオ:
  1. 取り込み → ドラフト確認 → SoT 登録
  2. 候補者詳細 → 応募確認 → パイプライン移動
  3. 面接登録 → 評価メモ入力
  4. ダッシュボード KPI 確認
  5. 求人作成 → キャリアページ確認 → クローズ
  6. ユーザー招待 → ロール設定
```

---

## ファイル作成・変更一覧（概算）

### 新規ファイル（約20ファイル）

| カテゴリ | ファイル |
|----------|---------|
| Migration | step_transitions, interview_schedules, evaluation_perspectives, evaluation_questions, interview_perspective_memos |
| Model | StepTransition, InterviewSchedule, EvaluationPerspective, EvaluationQuestion, InterviewPerspectiveMemo |
| Service | PipelineTransitionService, InterviewScheduleService |
| Controller | CareerPageController, UserManagementController, EvaluationPerspectiveController, InterviewPerspectiveMemoController |
| Vue Page | Interviews/Create, Interviews/Show, Evaluations/Index, Evaluations/PerspectiveForm, Careers/Index, Careers/Show, Settings/Users/Index, Settings/Roles/Index |

### 改修ファイル（約15ファイル）

| ファイル | 変更内容 |
|----------|---------|
| PipelineController | move, history アクション追加 |
| Pipeline/Index.vue | Kanban 化 |
| CandidateController | update アクション追加 |
| Candidates/Show.vue | タイムライン、編集モーダル |
| DashboardController | KPI 集計追加 |
| Dashboard.vue | KPI カード、グラフ |
| JobController | close アクション追加 |
| Jobs/Index.vue | ステータスバッジ |
| InterviewController | CRUD 実装 |
| Interviews/Index.vue | 一覧 UI |
| routes/web.php | 新規ルート追加 |
| AppLayout.vue | ナビ項目の調整（必要なら） |

---

## リスク & 判断ポイント

| リスク | 対策 |
|--------|------|
| Kanban の D&D ライブラリ選定 | vuedraggable（Vue3 対応版）を第一候補、ダメなら native HTML5 D&D |
| ダッシュボード KPI のクエリ負荷 | MVP はリアルタイム集計、P2 でスナップショット方式に移行 |
| 面接 × 評価の連携が複雑 | 面接詳細画面に評価メモを埋め込む形でシンプルに |
| キャリアページのデザイン | Tailwind で最低限のレイアウト、デザイン磨きは後日 |
| 土日に設定管理が終わらない | 最悪 P1 に回してOK、MVP 起動には Admin 直接操作で代替可 |

---

## 完了の定義

この週が終わった時点で、以下の操作が一気通貫で動くこと:

1. Gmail/API から候補者が取り込まれる
2. ドラフトを確認し、SoT（Person/Candidate/Application）に登録できる
3. パイプラインボード（Kanban）で選考ステップを移動できる
4. 面接を登録し、評価メモを記録できる
5. ダッシュボードで KPI を確認できる
6. 求人を作成・公開・クローズできる
7. キャリアページで公開求人を閲覧できる
8. ユーザーを招待し、ロールを設定できる
