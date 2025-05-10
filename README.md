# BitStep

**BitStep** は、プログラミング初学者のための「4択問題ドリル形式」の学習Webアプリケーションです。  
PHPとMariaDBを用いて構築されており、カテゴリごとに問題を出題・管理できます。

---

## 🧩 主な機能

- ログイン・ログアウト機能（`login.php`）
- ホーム画面（`home.php`）からカテゴリ選択
- ドリル問題の出題と解答（`drill.php`）
- 管理者による問題表示（`admin/display_questions.php`）

---

## 🛠️ 技術スタック

- 言語: PHP 7.x〜8.x
- データベース: MariaDB / MySQL
- 管理ツール: VS Code + GitHub
- レイアウト: HTML / CSS（シンプル構成）

---

## ⚙️ セットアップ手順（開発環境向け）

1. このリポジトリをクローン：
    ```bash
    git clone https://github.com/your-username/bitstep.git
    ```

2. データベースを用意し、`create_tables.sql` をインポート

3. `config/db_org.php` を作成し、DB接続情報を記述：
    ```php
    <?php
    $pdo = new PDO('mysql:host=localhost;dbname=bitstep;charset=utf8mb4', 'ユーザー名', 'パスワード');
    ?>
    ```

4. ブラウザで `login.php` にアクセスしてスタート

---

## 🚫 注意事項

- `config/db_org.php` は機密情報を含むため、Git管理に含めないでください。
- `.gitignore` に以下のように記述済みです：
    ```
    .vscode/
    .temp/
    .config/db_org.php
    ```

---

## 📄 ライセンス

このプロジェクトは学習・教育目的で公開されています。  
商用利用・再配布はご遠慮ください。
