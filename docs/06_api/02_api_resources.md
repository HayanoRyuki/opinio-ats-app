# API リソース設計

## リソース設計の考え方
- 名詞ベースのリソース設計
- 状態遷移はイベントとして扱う
- 副作用のある処理は明示する

## 主なリソース
- candidates
- jobs
- applications
- selection_steps
- evaluations
- messages
- schedules
- dashboards

## イベント的リソース
- application_step_changed
- evaluation_submitted
- interview_scheduled

## NG パターン
- UI 専用 API
- 状態を隠したブラックボックス API
