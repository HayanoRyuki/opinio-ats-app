# 設定・チーム管理

本ドキュメントは、Opinio ATS における  
**機能定義：設定・チーム管理** を扱う。

## 機能の位置づけ
設定・チーム管理は、  
Opinio ATS を **誰が・どの範囲で・どの責任を持って使うか** を定義するための基盤機能である。

本機能は、
- 採用判断そのものには関与しない
- 組織としての利用ルールを明確にする
- 操作責任と閲覧範囲を切り分ける

ことを目的とし、  
**権限管理・設定操作自体が判断や評価にならないよう設計する。**

---

## 管理対象の範囲

### 人に関する管理
- ユーザー招待・削除
- ロール（役割）の付与
- 所属チームの管理

### 権限に関する管理
- 機能単位の閲覧・編集権限
- データ操作権限
- 管理画面へのアクセス制御

### システム設定
- 基本設定（会社情報、表示設定）
- 採用フロー設定
- 通知・外部連携設定

---

## 設計上の前提（重要）
- 権限は **最小単位で制御** する
- 「とりあえず管理者」は作らない
- 管理操作もすべてログ対象とする
- 権限付与・変更は常に説明可能であること

---

## 権限設計の基本方針

### ロールベース + 権限制御
- ロールは便宜的な集合体とする
- 実際の制御は **権限単位** で行う
- ロール構成は後から編集可能とする

---

### 想定ロール（初期）
- 管理者  
  → 全体設定・権限管理
- 採用担当  
  → 候補者・求人・選考進行管理
- 面接官  
  → 参照・メモ入力のみ

※ ロール名・粒度は固定しない  
※ 組織に応じたカスタマイズを前提とする

---

## データモデル（必須）

### User
- user_id
- company_id
- email
- name
- status（active / invited / disabled）
- created_at
- updated_at

---

### Team
- team_id
- company_id
- name
- description
- created_at

---

### Role
- role_id
- company_id
- name
- description
- is_system_role
- created_at

---

### Permission
- permission_key
- description
- scope（read / write / admin）

---

### UserRole
- user_id
- role_id
- assigned_at

---

### RolePermission
- role_id
- permission_key

---

## 主な設定項目

### ユーザー管理
- ユーザー招待（メール）
- 有効／無効切り替え
- ロール付与・変更
- 所属チーム設定

---

### 権限管理
- ロール作成・編集
- 権限の付与・剥奪
- 管理画面アクセス制御

---

### 採用フロー設定
- 選考ステップ定義
- 表示順制御
- 無効化（削除は行わない）

---

### 通知・連携設定
- 通知 ON / OFF
- カレンダー連携
- メッセージ連携

---

## コントローラ設計

### SettingsController
- index  
  設定トップ表示
- update  
  基本設定更新

---

### UserManagementController
- index  
  ユーザー一覧
- invite  
  ユーザー招待
- updateRole  
  ロール・権限変更
- deactivate  
  無効化

---

### RolePermissionController
- index  
  ロール一覧
- edit  
  権限編集
- update  
  保存

---

## ビュー設計

### 表示系
- settings/index  
  設定トップ
- settings/users  
  ユーザー管理
- settings/roles  
  ロール管理
- settings/permissions  
  権限制御

---

## Controller / View 以外に必要な要素

### Service

#### UserInvitationService
- 招待メール送信
- 招待トークン管理
- 初期ロール付与

---

#### PermissionCheckService
- 権限判定ロジックの共通化
- API / View 両対応
- 最小権限原則の担保

---

### Policy
- UserPolicy  
  ユーザー操作権限
- RolePolicy  
  ロール編集権限
- SettingsPolicy  
  システム設定権限

---

### Audit Log（必須）

#### AuditLog
- log_id
- actor_user_id
- action_type
- target_type
- target_id
- before_state（JSON）
- after_state（JSON）
- occurred_at

※ 権限変更・設定変更は必ず記録する  
※ 巻き戻し可能な粒度で保持する

---

## 他機能との関係
- 共通認証：  
  user_id / company_id の起点
- すべての機能：  
  権限制御の前提
- 監査・ガバナンス：  
  操作履歴の基盤

---

## 本機能で「やらないこと」
- 人事評価・査定管理
- 組織制度の定義
- 処遇判断フロー
- 承認ワークフローの強制

---

## 一文定義（迷ったとき用）
設定・チーム管理は、  
**誰が何に責任を持って操作するかを明確にするための機能である。**
