# 日程調整・カレンダー連携

本ドキュメントは、Opinio ATS における **機能定義 2-3：日程調整・カレンダー連携** を扱う。

## 機能の位置づけ
日程調整・カレンダー連携は、面接・面談に関わる日程調整を効率化し、  
**選考進行を止めないための補助機能**である。

本機能は、選考判断そのものを支援するものではなく、  
あくまで **「判断の場を確実に設定するためのインフラ」**として位置づける。

---

## 主な機能
- 面接候補日の提示
- 面接日程の確定・変更
- 外部カレンダーとの連携
- 面接予定の一覧表示
- Web会議URLの管理（必要に応じて）

---

## 設計方針
以下は、本機能を判断補助ではなく「進行支援」として扱うための前提である。

- 日程の最終確定は **必ず人が行う**
- 自動確定・自動承認は行わない
- 外部カレンダー（Google / Outlook 等）を正とする
- ATS 側は「予定の写し」を保持するにとどめる

---

## 想定連携先
- Google Calendar
- Outlook Calendar
- その他 CalDAV 互換サービス

---

## データモデル（必須）

### InterviewSchedule
面接・面談の日程情報を保持するモデル。

- interview_schedule_id
- application_id
- interviewer_id
- scheduled_at
- duration_minutes
- meeting_url
- calendar_type（google / outlook 等）
- status（予定 / 変更 / キャンセル）
- created_at

---

## コントローラ設計

### InterviewScheduleController
- index  
  面接予定一覧表示
- create  
  日程候補作成
- store  
  日程確定
- update  
  日程変更・キャンセル

---

## ビュー設計
- schedules/index  
  面接・面談予定一覧
- schedules/form  
  日程設定・変更画面
- schedules/detail  
  個別予定詳細

---

## Controller / View 以外に必要な要素

### Service
#### CalendarIntegrationService
- 外部カレンダーの空き時間取得
- 予定登録・更新・削除
- 連携方式ごとの差分吸収  

外部 API 呼び出しは Controller に持たせない。

---

### Job（非同期処理）
- SyncCalendarJob  

外部カレンダー連携は失敗前提とし、  
リトライ可能な非同期処理とする。

---

### Policy
- InterviewSchedulePolicy  

面接官・採用担当者ごとの操作権限制御。

---

## 他機能との関係
- 選考パイプライン：  
  面接ステップに紐づく補助情報として利用
- メッセージ機能：  
  日程確定・変更時の通知送信
- ダッシュボード：  
  「今週の面接」「未設定日程」を可視化

---

## 本機能で「やらないこと」
以下は、Opinio ATS の思想に基づく設計判断である。

- 日程の自動確定
- 面接実施可否の自動判断
- 出欠による評価・判断の確定
- カレンダー情報を正として上書きすること

日程調整・カレンダー連携は、  
**判断を加速させるための補助機能**であり、  
判断そのものを代替する仕組みではない。
