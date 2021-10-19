<?php
  // Include db config
  // dbconfigを含めます

  require_once 'db.php';

  // Inserting variable
  //変数を挿入します

  $email = $password = '';
  $email_err = $password_err = '';

  // Process form when post submit
  //送信後にフォームを処理します

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Sanitize POST
    // POSTを綺麗にします
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate email
    // メールを検証

    if(empty($email)){
      $email_err = 'メールアドレスを入力してください';
    }

    // Validate password
    //パスワードを検証します
    if(empty($password)){
      $password_err = 'パスワードを入力してください';
    }

    // Make sure errors are empty
    //エラーが空であることを確認します

    if(empty($email_err) && empty($password_err)){

      // Prepare query
      //クエリを準備します

      $sql = 'SELECT username, email, password FROM users WHERE email = :email';

      // Prepare statement
      //ステートメントを準備します

      if($stmt = $pdo->prepare($sql)){
        // Bind params
        //パラメータをバインドします
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        // Attempt execute
        //実行を試みます

        if($stmt->execute()){

          // Check if email exists
          //メールが存在するかどうかを確認します

          if($stmt->rowCount() === 1){
            if($row = $stmt->fetch()){
              $hashed_password = $row['password'];
              if(password_verify($password, $hashed_password)){
                // SUCCESSFUL LOGIN
                //成功したログイン
                session_start();
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $row['username'];
                header('location: index.php');
              } else {
                // Display wrong password message
                // 間違ったパスワードメッセージを表示する
                $password_err = 'あなたが入力したパスワードは有効ではありません';
              }
            }
          } else {
            $email_err = 'そのメールのアカウントが見つかりません';
          }
        } else {
          die('何かがうまくいかなかった');
        }
      }
      // Close statement
      //ステートメントを閉じる
      unset($stmt);
    }

    // Close connection
    //接続を閉じます
    unset($pdo);
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="_resources/bootstrap.css">
  <title>アカウントにログイン</title>
</head>
<body class="bg-primary">
  <div class="container">
    <div class="row">
      <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
          <h2>ログイン</h2>
          <p>クレデンシャルを入力してください</p>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">   
            <div class="form-group">
              <label for="email">メール</label>
              <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
              <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
              <label for="password">パスワード</label>
              <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
              <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-row">
              <div class="col">
                <input type="submit" value="ログイン" class="btn btn-success btn-block">
              </div>
              <div class="col">
                <a href="register.php" class="btn btn-light btn-block">アカウントがありませんか？ 登録</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>