# Opinio ATS 引き継ぎメモ

**日時**: 2026/02/04 17:50

---

## 今回完了した作業

### 1. ダッシュボード作成
- `DashboardController.php` - 今すぐのアクション、KPI、ファネル、チャネル別分析
- `Dashboard.vue` - Opinioカラーでデザイン
- **注意**: 本番DBは`Application`に`company_id`を直接持つ構造。`whereHas('candidate')`でフィルタリング

### 2. 求人管理ページ リデザイン
- `Jobs/Index.vue` - Opinioカラー、カード型Stats、リスト形式に変更

### 3. 本番シーダー実行
- `OpinioProductionSeeder.php` 作成
- 求人3件作成済み: 営業、エンジニア、CS
- 応募3件作成済み: 田中太郎(直接応募)、鈴木花子(スカウト)、佐藤健一(エージェント)

---

## 本番DB スキーマ修正履歴（今回実施）

```sql
-- recruitment_jobs.company_id を UUID対応に
ALTER TABLE recruitment_jobs MODIFY company_id CHAR(36) NOT NULL;

-- applications の job_id, candidate_id を bigint に
ALTER TABLE applications MODIFY job_id BIGINT UNSIGNED NOT NULL, MODIFY candidate_id BIGINT UNSIGNED NOT NULL;
```

---

## 本番DB 構造メモ

### candidates
- `id`: bigint (auto_increment)
- `company_id`: char(36)
- `name`, `email`, `phone`: 直接持つ（person_idなし）
- `source_channel`: direct/scout/agent/referral

### applications
- `id`: bigint
- `company_id`: char(36) ← 直接持つ
- `job_id`: bigint
- `candidate_id`: bigint
- `status`: active/offered/hired/rejected/withdrawn
- `applied_at`: **存在しない**

### recruitment_jobs
- `id`: bigint
- `company_id`: char(36)

---

## 未対応・次回検討事項

1. **ローカルと本番のスキーマ差異** - ローカルはPerson/Candidate分離構造、本番は統合構造
2. **ダッシュボードの「今週の面接」** - 現在プレースホルダー（Interviewモデル未実装）
3. **選考ステップ** - `selection_step_id`の活用

---

## Opinioカラー

```
Primary: #332c54 (紫)
Teal: #4e878c
Green: #65b891
Cream: #f4f4ed (背景)
```

---

## 確認済みページ

- ✅ ダッシュボード https://ats.opinio.co.jp/dashboard
- ✅ 求人管理 https://ats.opinio.co.jp/jobs
- ✅ 応募一覧
- ✅ 候補者一覧
- ✅ 取り込み管理
