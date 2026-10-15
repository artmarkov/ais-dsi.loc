<?php
use artsoft\helpers\Html;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Технические работы</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
        }
        .retry-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<h1><?= Html::encode($message) ?></h1>
<p>Нажмите «F5» для обновления</p>
<div class="retry-btn">
    <button onclick="setTimeout(function() { window.location.reload(); }, <?= $retry_after ?>)">
        Повторить
    </button>
</div>
</body>
</html>