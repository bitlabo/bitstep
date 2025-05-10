<?php
session_start();
require '../config/db.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// セッションに問題の情報がなければ戻る
if (!isset($_SESSION['question_no'], $_SESSION['shuffled_correct'], $_SESSION['choices'])) {
    header("Location: drill.php");
    exit();
}

// セッションデータ取得
$question_no = $_SESSION['question_no'];
$correct_index = $_SESSION['shuffled_correct'];
$choices = $_SESSION['choices'];
$selected_index = $_POST['answer'] ?? null;

if ($selected_index === null || !isset($choices[$selected_index - 1])) {
    header("Location: drill.php");
    exit();
}

// 正誤判定
$is_correct = intval($selected_index) === intval($correct_index);

// 問題文取得
$stmt = $pdo->prepare("SELECT question_text FROM questions WHERE id = :id");
$stmt->execute([':id' => $question_no]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);
$question_text = $question['question_text'] ?? '不明な問題';

// 履歴登録/更新
$stmt = $pdo->prepare("SELECT * FROM user_history WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$history = $stmt->fetch(PDO::FETCH_ASSOC);

if ($history) {
    $stmt = $pdo->prepare("
        UPDATE user_history 
        SET attempted_questions = attempted_questions + 1,
            correct_answers = correct_answers + :correct 
        WHERE user_id = :user_id
    ");
    $stmt->execute([
        'correct' => $is_correct ? 1 : 0,
        'user_id' => $user_id
    ]);
} else {
    $stmt = $pdo->prepare("
        INSERT INTO user_history (user_id, attempted_questions, correct_answers) 
        VALUES (:user_id, 1, :correct)
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'correct' => $is_correct ? 1 : 0
    ]);
}

// セッション情報クリア
unset($_SESSION['question_no'], $_SESSION['shuffled_correct'], $_SESSION['choices']);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitStep - 回答結果</title>
    <link rel="icon" href="../assets/icons8-b-100.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .result-container {
            max-width: 720px;
            margin: 2em auto;
            padding: 2em;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 1.8em;
            margin-bottom: 1em;
            text-align: center;
        }

        .result-container p {
            font-size: 1.1em;
            margin: 0.5em 0;
        }

        .next-buttons {
            margin-top: 2em;
            display: flex;
            justify-content: center;
            gap: 1.5em;
        }
    </style>
</head>

<body>

    <div class="main-container">
        <h2><?= $is_correct ? '✅ 正解です！' : '❌ 不正解です' ?></h2>

        <div class="result-detail">
            <p><strong>問題:</strong> <?= htmlspecialchars($question_text) ?></p>
            <p><strong>あなたの回答:</strong> <?= htmlspecialchars($choices[$selected_index - 1]) ?></p>
            <?php if (!$is_correct): ?>
                <p><strong>正しい答え:</strong> <?= htmlspecialchars($choices[$correct_index - 1]) ?></p>
            <?php endif; ?>
        </div>

        <!--
        <div class="next-buttons">
            <a href="drill.php" class="btn primary"></a>
        </div>
        -->

        <form method="post" action="drill.php">
            <button type="submit" class="btn primary">次の問題へ</button>
        </form>

        <div class="end-drill" style="text-align: center; margin-top: 1.5em;">
            <a href="../index.php" class="btn back">🔙 トップに戻る</a>
        </div>
    </div>

</body>

</html>