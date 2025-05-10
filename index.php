<?php
session_start();
require 'config/db.php';

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// ユーザー情報を取得
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

// ユーザー履歴の取得
$stmt = $pdo->prepare("SELECT * FROM user_history WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$history = $stmt->fetch(PDO::FETCH_ASSOC);

// スコア計算
$attempted = $history['attempted_questions'] ?? 0;
$correct = $history['correct_answers'] ?? 0;
$score = $attempted * 10 + $correct * 2;

// カテゴリをDBから取得
$stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// カテゴリが選択されて送信された場合
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categories'])) {
    $selected = $_POST['categories'];
    $json = json_encode($selected);

    // INSERT or UPDATE
    $stmt = $pdo->prepare("
        INSERT INTO user_preferences (user_id, selected_categories)
        VALUES (:user_id, :categories)
        ON DUPLICATE KEY UPDATE selected_categories = :categories
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'categories' => $json,
    ]);

    // drill.php へリダイレクト
    $_SESSION['selected_categories'] = $selected;
    header("Location: drill/drill.php");
    exit();
    /*
    $_SESSION['selected_categories'] = $_POST['categories']; // セッションに保存
    header("Location: drill/drill.php"); // drill.phpにリダイレクト
    exit();
    */
}

// DBから保存されたカテゴリ選択を読み込む
$stmt = $pdo->prepare("SELECT selected_categories FROM user_preferences WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$pref = $stmt->fetch(PDO::FETCH_ASSOC);

$selected_categories = [];
if ($pref && !empty($pref['selected_categories'])) {
    $selected_categories = json_decode($pref['selected_categories'], true);
}

// 全ユーザーのスコアを取得（user_historyとusersをJOIN）
$stmt = $pdo->query("
    SELECT u.username, uh.user_id,
           (uh.attempted_questions * 10 + uh.correct_answers * 2) AS score
    FROM user_history uh
    JOIN users u ON uh.user_id = u.id
    ORDER BY score DESC
");
$rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rank = 1;
foreach ($rankings as $r) {
    if ($r['user_id'] == $user_id) {
        break;
    }
    $rank++;
}

// 全ユーザーのスコアを取得（スコアは: attempted * 10 + correct * 2）
$sql = "
    SELECT user_id, (attempted_questions * 10 + correct_answers * 2) AS total_score
    FROM user_history
    ORDER BY total_score DESC
";
$stmt = $pdo->query($sql);
$all_scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
$rank = 0;
$total_users = count($all_scores);

foreach ($all_scores as $index => $row) {
    if ($row['user_id'] == $user_id) {
        $rank = $index + 1; // 1位スタート
        break;
    }
}
*/

$rank = 0;
$total_users = count($all_scores);
$my_score = 0;
$diff_with_upper = null;

foreach ($all_scores as $index => $row) {
    if ($row['user_id'] == $user_id) {
        $rank = $index + 1;
        $my_score = $row['total_score'];

        // 上位者とのスコア差を計算（1位の場合はnull）
        if ($index > 0) {
            $diff_with_upper = $all_scores[$index - 1]['total_score'] - $my_score;
        }
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitStep - ホーム</title>
    <link rel="icon" href="assets/icons8-b-100.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        button:disabled {
            background-color: #ccc !important;
            color: #666 !important;
            cursor: not-allowed !important;
            border-color: #aaa !important;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="container">
            <a href="index.php" class="logo">BitStep</a>
            <div class="navbar-links">
                <a href="auth/logout.php" class="btn logout">ログアウト</a>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="welcome-message" style="margin-bottom: 2em;">
            <h2 style="font-size: 1.8em; color: #333;">ようこそ、<?= htmlspecialchars($user['username']) ?>さん！</h2>
            <p style="color: #555;">あなたの学習状況を確認し、次の問題に挑戦しましょう！</p>
        </div>

        <div class="dashboard" style="display: flex; flex-wrap: wrap; gap: 2em;">
            <div style="flex: 1; min-width: 280px; background: #f9f9f9; border-radius: 10px; padding: 1.5em; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 1em; font-size: 1.4em;">📊 学習スコア</h3>
                <!--
                <ul style="list-style: none; padding: 0; margin: 0; line-height: 1.8;">
                    <li><strong>解答済み:</strong> <?= number_format($attempted) ?> 問</li>
                    <li><strong>正解数:</strong> <?= number_format($correct) ?> 問</li>
                    <li><strong>スコア:</strong> <?= number_format($score) ?> 点</li>
                </ul>
                -->
                <ul style="list-style: none; padding: 0; margin: 0; line-height: 1.8;">
                    <li style="display: flex; justify-content: space-between;">
                        <span><strong>解答済み:</strong></span>
                        <span><?= number_format($attempted) ?> 問</span>
                    </li>
                    <li style="display: flex; justify-content: space-between;">
                        <span><strong>正解数:</strong></span>
                        <span><?= number_format($correct) ?> 問</span>
                    </li>
                    <li style="display: flex; justify-content: space-between;">
                        <span><strong>スコア:</strong></span>
                        <span><?= number_format($score) ?> 点</span>
                    </li>
                    <?php if ($rank > 0): ?>
                        <li style="display: flex; justify-content: space-between;">
                            <span><strong>順位:</strong></span>
                            <span><?= $total_users ?>人中 <?= $rank ?> 位</span>
                        </li>
                    <?php endif; ?>
                    <?php if (!is_null($diff_with_upper)): ?>
                        <li style="display: flex; justify-content: space-between;">
                            <span><strong>上位との差:</strong></span>
                            <span><?= $diff_with_upper ?> 点</span>
                        </li>
                    <?php endif; ?>
                </ul>
                <p style="color: #555;">
                    <b>解けば解くほど、自信になる！</b><br>
                    ちょっとした時間にできる4択問題に、どんどん挑戦！<br>
                    間違えても大丈夫。たくさん問題を解くことで、理解が深まり、確かなスキルが身につきます。さあ、どんどんステップアップしよう！
                </p>
            </div>

            <div style="flex: 1; min-width: 280px; background: #eef6ff; border-radius: 10px; padding: 1.5em; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 1em; font-size: 1.4em;">📝 カテゴリを選択して下さい</h3>
                <p style="color: #444;">学習するカテゴリを選択してください。</p>
                <?php /*
                <form method="POST" action="index.php">
                    <div style="margin-bottom: 1em;">
                        <?php foreach ($categories as $category): ?>
                            <label style="display: block; margin-bottom: 10px;">
                                <input type="checkbox" name="categories[]" value="<?= $category['id'] ?>"
                                    <?= in_array($category['id'], $selected_categories) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" claindexmary" style="margin-top: 1em;">挑戦する</button>
                </form>
                */ ?>
                <form method="POST" action="index.php" id="category-form">
                    <div style="margin-bottom: 1em;">
                        <?php foreach ($categories as $category): ?>
                            <label style="display: block; margin-bottom: 10px;">
                                <input type="checkbox" class="category-checkbox" name="categories[]" value="<?= $category['id'] ?>"
                                    <?= in_array($category['id'], $selected_categories) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn primary" id="submit-btn" style="margin-top: 1em;" disabled>挑戦する</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // ボタン制御ロジック
        function updateButtonState() {
            const checkboxes = document.querySelectorAll('.category-checkbox');
            const submitButton = document.getElementById('submit-btn');
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            submitButton.disabled = !anyChecked;
        }

        // 初期状態チェック
        document.addEventListener('DOMContentLoaded', updateButtonState);

        // チェック状態が変わったら更新
        document.querySelectorAll('.category-checkbox').forEach(cb => {
            cb.addEventListener('change', updateButtonState);
        });
    </script>
</body>

</html>