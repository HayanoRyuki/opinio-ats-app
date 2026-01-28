# API 全体概要と認証

## 目的
Opinio ATS の API は、
フロントエンド・外部連携・将来のアプリ分離を前提として設計する。

## 基本方針
- API は常にアプリケーション境界を意識する
- UI 依存の API を作らない
- 将来の公開・分離に耐える設計とする

## 認証方式
- 共通認証アプリ（app.opinio.co.jp）で認証
- JWT を用いたトークン連携
- API はトークンのみを信頼する

## 権限情報
- company_id
- person_id
- role（admin / recruiter / interviewer など）

## セキュリティ原則
- API 単位で権限チェック
- UI で隠しても API では必ず制御する
