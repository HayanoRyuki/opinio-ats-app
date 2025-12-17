<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>求人作成</title>
</head>
<body>

<h1>求人作成</h1>

<form method="POST" action="{{ route('jobs.store') }}">
    @csrf

    <div style="margin-bottom:12px;">
        <label>求人タイトル</label><br>
        <input type="text" name="title" required>
    </div>

    <div style="margin-bottom:12px;">
        <label>説明</label><br>
        <textarea name="description" rows="4"></textarea>
    </div>

    <button>作成する</button>
</form>

</body>
</html>
