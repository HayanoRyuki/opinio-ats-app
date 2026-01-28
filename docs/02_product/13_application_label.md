# 応募ラベル（Application Label）

本ドキュメントは、Opinio ATS における  
**機能定義：応募ラベル（Application Label）** を扱う。

## 機能の位置づけ
応募ラベルは、  
Opinio ATS において **応募（Application）に対して補助的な意味づけを与え、一覧・判断・整理をしやすくするための機能**である。

本機能は、
- 選考結果を決める
- 応募の評価を確定する

ためのものではなく、  
**人が状況を把握し、考えやすくするための補助情報**として設計する。

---

## 応募ラベルの役割
応募ラベルは、以下のような用途を想定する。

- 「要注意」「急ぎ」「保留」などの状態メモ
- 特定条件（ハイレイヤー、即戦力、リファラル等）の視認
- チーム内での暗黙知の可視化
- 一覧画面での絞り込み・並び替え補助

※ ラベルは **評価・合否・進行を代替しない**  
※ あくまで「補助的な視点」を与えるものとする

---

## 設計上の前提（重要）
- ラベルは **事実でも判断でもない**
- 同一応募に複数ラベルを付与可能
- ラベルの付与・解除は履歴として残す
- ラベルが付いていること自体に業務的な強制力は持たせない

---

## ラベルの性質
- 会社単位で定義する
- 色・名称はカスタマイズ可能
- 意味は組織ごとに異なってよい
- システム側で意味解釈は行わない

---

## 中核概念

### ApplicationLabel
応募ラベルの定義。

- label_id
- company_id
- name
- color
- description
- is_active
- created_at
- updated_at

---

### ApplicationLabelAssignment
応募とラベルの紐付け。

- assignment_id
- application_id
- label_id
- assigned_by
- assigned_at

---

### ApplicationLabelHistory（任意）
ラベル操作の履歴保持。

- history_id
- application_id
- label_id
- action_type（assign / remove）
- actor_user_id
- occurred_at

---

## 主な操作
- 応募へのラベル付与
- 応募からのラベル解除
- ラベルによる一覧フィルタリング
- ラベル定義の追加・編集・無効化

---

## この機能でやること
- 応募に補助的なタグ情報を付与する
- 一覧画面・ダッシュボードでの整理性を向上させる
- チーム内の視点共有を助ける

---

## この機能でやらないこと
- ラベルによる自動評価
- ラベルによる合否・処遇判断
- ラベルを条件にした自動遷移
- ラベルの意味をシステムが解釈すること

---

## コントローラ設計

### ApplicationLabelController
- index  
  ラベル一覧
- store  
  ラベル作成
- update  
  ラベル編集
- deactivate  
  無効化

---

### ApplicationLabelAssignmentController
- assign  
  応募へのラベル付与
- remove  
  応募からのラベル解除

---

## ビュー設計

### 表示系
- settings/application_labels  
  ラベル管理画面
- applications/partials/labels  
  応募一覧・詳細でのラベル表示

---

## Controller / View 以外に必要な要素

### Service

#### ApplicationLabelService
- ラベル定義管理
- 付与・解除ロジックの集約

---

### Policy
- ApplicationLabelPolicy  
  ラベル定義の管理権限
- ApplicationLabelAssignmentPolicy  
  付与・解除権限

---

## ログ・監査の考え方
- ラベル付与・解除は履歴として保持
- ラベル自体は判断記録とみなさない
- 誰が・いつ操作したかは追跡可能にする

---

## 他機能との関係
- 応募一覧・詳細  
  → 視認性・整理性の向上
- ダッシュボード  
  → ラベル付き応募の把握
- 通知  
  → 特定ラベル付き応募の補助通知（将来）
- 設定・チーム管理  
  → ラベル定義の管理

---

## 一文定義（迷ったとき用）
応募ラベルは、  
**判断を代替しない形で、応募を整理しやすくするための補助情報である。**
