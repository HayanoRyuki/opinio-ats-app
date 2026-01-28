# Opinio ATS｜Service 定義一覧

本ドキュメントは、Opinio ATS における  
**Service 層（業務ロジック・集計・変換・補助処理）** を一覧化したものである。

Service は以下を責務とする。

- Controller から業務ロジックを分離する
- Model を直接またがる処理を集約する
- 判断・評価を行わず、事実整理と補助に徹する
- 再計算・再実行可能な構造を保つ

---

## Service 設計原則（厳守）

- Service は **結論を出さない**
- Service は **状態を勝手に確定しない**
- Service は **説明可能な処理のみを行う**
- Service は **副作用を最小化** する
- 自動化は「人の判断を助ける範囲」に限定する

---

## 2-1 候補者管理

### CandidateMergeService
- 候補者重複検出
- 候補者統合（人の確認前提）
- 統合履歴管理

---

### CandidateHistoryService
- 候補者の行動・履歴集約
- 応募・推薦・面接履歴の横断整理

---

## 2-2 選考パイプライン管理

### PipelineTransitionService
- ステップ移動処理
- 遷移履歴の記録
- 不正遷移の防止（構造的制約）

※ 合否判断は行わない

---

### PipelineAnalyticsService
- ステップ滞留日数計算
- 遷移回数・滞留傾向算出
- 分析・ダッシュボード用データ生成

---

## 2-3 日程調整・カレンダー連携

### InterviewScheduleService
- 面接日程作成・更新
- 候補日管理
- 面接官・会議室紐付け

---

### CalendarIntegrationService
- 外部カレンダー連携
- 予定登録・更新
- 同期状態管理

※ 成否判断は行わない  
※ 登録失敗も事実として返す

---

## 2-4 メッセージ・コミュニケーション

### MessageSendService
- メッセージ送信処理
- テンプレート適用
- 送信結果管理

---

### MessageThreadService
- スレッド生成・紐付け
- 候補者・応募との関連管理

---

## 2-5 求人管理・キャリアページ

### JobPublishService
- 求人公開・非公開切り替え
- 公開状態管理
- 公開履歴保持

---

### JobStructureService
- 求人要件構造整理
- 表示用データ整形

---

## 2-6 評価観点・質問項目

### EvaluationStructureService
- 評価観点・質問構造管理
- 有効／無効制御
- 表示順管理

---

### InterviewMemoService
- 面接メモ保存・更新
- 評価観点との紐付け

※ 評価の正否判断は行わない

---

## 2-7 ダッシュボード・KPI

### DashboardAggregationService
- KPI 定義に基づく集計
- 期間指定集計
- 再計算可能設計

---

### DashboardSnapshotService
- KPI スナップショット生成
- 過去データ保持
- 再生成処理

---

## 2-8 分析・レポート

### ReportAggregationService
- レポート定義に基づく集計
- フィルタ条件処理
- JSON 形式結果生成

---

### ReportSnapshotService
- レポート結果スナップショット生成
- 過去比較用保存
- 再計算可能設計

---

## 2-9 応募取り込み（メール等）

### IntakeParserService
- メール／フォーム構造解析
- 必須項目抽出
- raw_payload 正規化

---

### IntakeConversionService
- Draft → 正式応募変換
- 重複候補者検出
- person_id 紐付け補助

---

## 2-10 エージェント推薦

### RecommendationParserService
- 推薦文構造整理
- マッチ／懸念点抽出補助
- 不完全データ前提処理

---

### RecommendationLinkService
- 推薦と候補者・応募の紐付け
- 重複推薦検出
- 履歴管理

---

## 2-11 設定・チーム管理

### UserInvitationService
- 招待メール送信
- 招待トークン管理
- 初期ロール付与

---

### PermissionCheckService
- 権限判定ロジック集約
- API / View 両対応
- 最小権限原則担保

---

### AuditLogService
- 操作ログ生成
- before / after 状態保存
- 巻き戻し前提の履歴保持

---

## Service 層で「やらないこと」

- 合否・評価・優劣の決定
- スコアリング・ランキング
- 処遇・人事制度への接続
- 自動意思決定

---

## 一文定義（迷ったとき用）

Service 層は、  
**人が判断するために必要な事実と構造を、安全に用意する層である。**
