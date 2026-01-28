@extends('layouts.ats')

@section('content')

{{-- ページタイトル --}}
<h1 style="font-size:20px; font-weight:600; margin-bottom:8px;">
    ダッシュボード
</h1>

<p style="opacity:.7; margin-bottom:24px;">
    採用活動全体の状態と、今日やるべきことを確認できます。
</p>

{{-- 今すぐのアクション --}}
<section style="margin-bottom:40px;">
    <h2 style="font-size:16px; font-weight:600; margin-bottom:12px;">
        今すぐのアクション
    </h2>

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        @foreach ([
            '日程調整待ち',
            '評価未入力',
            '返信待ち',
            '長期滞留候補者'
        ] as $label)
            <div style="
                min-width:200px;
                padding:16px;
                border:1px solid #ddd;
                border-radius:8px;
                background:#fff;
            ">
                <div style="font-size:13px; opacity:.6; margin-bottom:4px;">
                    {{ $label }}
                </div>
                <div style="font-size:24px; font-weight:600;">
                    —
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- KPIサマリー --}}
<section style="margin-bottom:40px;">
    <h2 style="font-size:16px; font-weight:600; margin-bottom:12px;">
        KPIサマリー
    </h2>

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        @foreach ([
            '内定承諾率',
            '面接通過率',
            '面接官別通過率',
            '選考リードタイム'
        ] as $kpi)
            <div style="
                min-width:200px;
                padding:16px;
                border:1px solid #ddd;
                border-radius:8px;
                background:#fff;
            ">
                <div style="font-size:13px; opacity:.6; margin-bottom:4px;">
                    {{ $kpi }}
                </div>
                <div style="font-size:24px; font-weight:600;">
                    —
                </div>
                <div style="font-size:12px; opacity:.5;">
                    前週比 —
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- 選考ファネル --}}
<section style="margin-bottom:40px;">
    <h2 style="font-size:16px; font-weight:600; margin-bottom:12px;">
        選考ファネル
    </h2>

    <div style="opacity:.5;">
        応募 → 書類 → 1次 → 2次 → 内定 → 承諾
    </div>
</section>

{{-- 面接官別パフォーマンス --}}
<section style="margin-bottom:40px;">
    <h2 style="font-size:16px; font-weight:600; margin-bottom:12px;">
        面接官別パフォーマンス
    </h2>

    <div style="opacity:.5;">
        面接官ごとの通過率のばらつきを可視化
    </div>
</section>

{{-- チャネル別分析 --}}
<section style="margin-bottom:40px;">
    <h2 style="font-size:16px; font-weight:600; margin-bottom:12px;">
        チャネル別分析
    </h2>

    <div style="opacity:.5;">
        媒体 / エージェント別の歩留まり
    </div>
</section>

@endsection
