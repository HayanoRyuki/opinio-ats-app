# インフラ全体設計

## 目的
Opinio ATS は、
少人数・複数アプリ運営を前提とした「壊れにくい基盤」として設計する。

## 全体構成
- AWS を前提とする
- アプリごとに責任範囲を分離
- 認証は共通基盤（app.opinio.co.jp）

## 主な構成要素
- ALB
- ECS / EC2（段階的）
- RDS（正のデータ）
- DynamoDB（ログ・履歴）
- S3（長期保管）
- CloudWatch（監視）

## 環境区分
- production
- staging
- local

## 原則
- 単一障害点を作らない
- 小さく始めて段階的に分離
