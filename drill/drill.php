<?php
session_start();
require '../config/db.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$selected_categories = $_SESSION['selected_categories'] ?? [];

if (empty($selected_categories)) {
    echo "<p>カテゴリが選択されていません。</p>";
    exit;
}

// ランダムに1問取得
$category_ids = implode(",", array_map('intval', $selected_categories));
$stmt = $pdo->prepare("SELECT * FROM questions WHERE category_id IN ($category_ids) ORDER BY RAND() LIMIT 1");
$stmt->execute();
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    echo "<p>選択したカテゴリには問題がありません。</p>";
    exit;
}

// カテゴリ名を取得
$stmt2 = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
$stmt2->execute([$question['category_id']]);
$category = $stmt2->fetch(PDO::FETCH_ASSOC);
$category_name = $category ? $category['name'] : '未分類';

$choices = [
    $question['choice1'],
    $question['choice2'],
    $question['choice3'],
    $question['choice4']
];
$original_choices = $choices;
shuffle($choices);

$correct = $question['correct_choice'];
$shuffled_correct = array_search($original_choices[$correct - 1], $choices) + 1;

$_SESSION['question_no'] = $question['id'];
$_SESSION['shuffled_correct'] = $shuffled_correct;
$_SESSION['choices'] = $choices;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitStep 問題</title>
    <link rel="icon" href="../assets/icons8-b-100.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /*
        body {
            font-family: sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        */
        .drill-container {
            max-width: 720px;
            margin: 2em auto;
            padding: 2em;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 1.8em;
            margin-bottom: 1em;
            text-align: center;
            color: #333;
        }

        .question-text {
            font-size: 1.3em;
            margin-bottom: 1.5em;
            font-weight: bold;
        }

        .choices {
            display: flex;
            flex-direction: column;
            gap: 0.2em;
            margin-bottom: 2em;
        }

        .choice {
            background: #eef6ff;
            border: 2px solid transparent;
            padding: 0.8em 1em;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1em;
        }

        .choice:hover {
            border-color: #3399ff;
            background: #dceeff;
        }

        .choice input[type="radio"] {
            margin-right: 0.8em;
            transform: scale(1.2);
        }
    </style>
</head>

<body>

    <div class="main-container">
        <h1>📘 問題</h1>

        <div><?= htmlspecialchars($category_name) ?></div>
        <div class="question-text"><?= htmlspecialchars($question['question_text']) ?></div>

        <form method="post" action="check_answer.php">
            <div class="choices">
                <?php foreach ($choices as $index => $choice): ?>
                    <label class="choice">
                        <input type="radio" name="answer" value="<?= $index + 1 ?>" required>
                        <?= $index + 1 ?>. <?= htmlspecialchars($choice) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn primary">✅ 回答する</button>
        </form>

        <div class="end-drill" style="text-align: center; margin-top: 1.5em;">
            <a href="../index.php" class="btn back">🔙 トップに戻る</a>
        </div>

    </div>

</body>

</html>