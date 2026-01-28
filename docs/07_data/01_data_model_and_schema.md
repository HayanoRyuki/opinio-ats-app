# データモデルとスキーマ設計

## 目的
Opinio ATS のデータは、
「判断の履歴」を安全に、将来に渡って保持するために設計する。

## 基本原則
- マスタDBは作らない
- アプリごとに責任を持つ
- 共通なのは ID のみ

## 主なエンティティ
- companies
- persons
- candidates
- jobs
- applications
- selection_steps
- evaluations
- notes

## ID設計
- UUID を全エンティティで使用
- person_id / company_id は全アプリ共通

## 不変データの考え方
- 採用判断理由は原則編集不可
- 修正は追記として残す
