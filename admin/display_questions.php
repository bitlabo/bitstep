<?php
require '../config/db.php';

// -------------------- データの取得 --------------------

$questions = []; // 初期化
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$where_clause = '';
$params = [];

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT q.id, c.name AS category_name, q.question_text, q.choice1, q.choice2, q.choice3, q.choice4, q.correct_choice, q.created_at, q.modified_at
            FROM questions q
            INNER JOIN categories c ON q.category_id = c.id";

if ($category_id !== null) {
    $where_clause = " WHERE q.category_id = :category_id";
    $params = [':category_id' => $category_id];
}

$stmt = $pdo->prepare($sql . $where_clause);
$stmt->execute($params);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// PDO接続を閉じる
$pdo = null;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>質問データ</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>質問データ</h1>

    <?php if (isset($_GET['category_id'])): ?>
        <h2>カテゴリ : <?= htmlspecialchars($questions[0]['category_name']) ?></h2>
        質問数 : <?= number_format(count($questions)) ?> 件
    <?php else: ?>
        <h2>全ての質問</h2>
        質問数 : <?= number_format(count($questions)) ?> 件
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <?php if (!($category_id !== null)) { ?>
                    <th>カテゴリ</th>
                <?php } ?>
                <th>質問内容</th>
                <th>選択肢1</th>
                <th>選択肢2</th>
                <th>選択肢3</th>
                <th>選択肢4</th>
                <th>正解</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($error_message)): ?>
                <tr>
                    <td colspan="9"><?= htmlspecialchars($error_message) ?></td>
                </tr>
            <?php elseif (!empty($questions)): ?>
                <?php foreach ($questions as $question): ?>
                    <tr>
                        <td><?= htmlspecialchars($question['id']) ?></td>
                        <?php if (!($category_id !== null)) { ?>
                            <td><?= htmlspecialchars($question['category_name']) ?></td>
                        <?php } ?>
                        <td><?= htmlspecialchars($question['question_text']) ?></td>
                        <td><?= htmlspecialchars($question['choice1']) ?></td>
                        <td><?= htmlspecialchars($question['choice2']) ?></td>
                        <td><?= htmlspecialchars($question['choice3']) ?></td>
                        <td><?= htmlspecialchars($question['choice4']) ?></td>
                        <td><?= htmlspecialchars($question['correct_choice']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">データが見つかりませんでした。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>