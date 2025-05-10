<?php
require 'config/db.php';

$stmt = $pdo->query("SELECT id, question_text FROM questions");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$threshold = 96; // 類似度の閾値（%）

for ($i = 0; $i < count($questions); $i++) {
    for ($j = $i + 1; $j < count($questions); $j++) {
        $text1 = $questions[$i]['question_text'];
        $text2 = $questions[$j]['question_text'];

        similar_text($text1, $text2, $percent);
        if ($percent >= $threshold) {
            /*
            echo "【類似】<br>";
            echo "ID {$questions[$i]['id']}: " . mb_substr($text1, 0, 50) . "<br>";
            echo "ID {$questions[$j]['id']}: " . mb_substr($text2, 0, 50) . "<br>";
            echo "→ 類似度: {$percent}%<br><br>";
            */
            echo "-- {$questions[$i]['id']}: " . mb_substr($text1, 0, 50) . "<br>";
            echo "-- {$questions[$j]['id']}: " . mb_substr($text2, 0, 50) . "<br>";
            echo "-- {$percent} / {$questions[$i]['id']},{$questions[$j]['id']}<br>";
            echo "DELETE FROM `questions` WHERE `id`={$questions[$j]['id']};<br>";
        }
    }
}
