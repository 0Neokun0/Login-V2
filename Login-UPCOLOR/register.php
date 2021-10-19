<?php
  // Include db config
  // dbconfigを含めます
  require_once 'db.php';

  // Inserting variable
  //変数を挿入します
  $username = $email = $password = $confirm_password = '';
  $username_err = $email_err = $password_err = $confirm_password_err = '';

  // Process form when post submit
  //送信後にフォームを処理します

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Sanitize POST
    // POSTを綺麗にします
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    
    $name =  trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate email
    // メールを検証

    if(empty($email)){
      $email_err = 'メールアドレスを入力してください';
    } else {
      // Prepare a select statement
      // selectステートメントを準備します

      $sql = 'SELECT id FROM users WHERE email = :email';

      if($stmt = $pdo->prepare($sql)){
        // Bind variables
        //変数をバインドします

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        // Attempt to execute
        //実行を試みます

        if($stmt->execute()){
          // Check if email exists
          //メールが存在するかどうかを確認します
          if($stmt->rowCount() === 1){
            $email_err = 'メールはすでに取られています';
          }
        } else {
          die('何かがうまくいかなかった');
        }
      }

      unset($stmt);
    }

    //ユーザ名前を検証します
    if(empty($name)){
      $name_err = 'ユーザー名を入力してください';
    }

    // Validate password
    //パスワードを検証します
    if(empty($password)){
      $password_err = 'パスワードを入力してください';
    } elseif(strlen($password) < 6){
      $password_err = 'パスワードは最低でも6文字必要です ';
    }

    // Validate Confirm password
    //パスワードの確認を検証します
    if(empty($confirm_password)){
      $confirm_password_err = 'パスワードを確認してください';
    } else {
      if($password !== $confirm_password){
        $confirm_password_err = 'パスワードが一致していません';
      }
    }

    // Make sure errors are empty
    //エラーが空であることを確認します
    if(empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
      // Hash password
      //ハッシュパスワード
      $password = password_hash($password, PASSWORD_DEFAULT);

      // Prepare insert query
      //挿入クエリを準備します
      $sql = 'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)';

      if($stmt = $pdo->prepare($sql)){
        // Bind parameters
        //パラメータをバインドします
        $stmt->bindParam(':username', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);

        // Attempt to execute
        //実行を試みます
        if($stmt->execute()){
          // Redirect to login
          //ログインにリダイレクト
          header('location: login.php');
        } else {
          die('何かがうまくいかなかった');
        }
      }
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
  <title>アカウントを登録</title>
</head>
<body class="bg-primary">
  <div class="container">
    <div class="row">
      <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
          <h2>アカウントを作成</h2>
          <p>このフォームに記入して登録してください</p>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="form-group">
              <label for="name">ユーザー名</label>
              <input type="text" name="username" class="form-control form-control-lg <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
              <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
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
            <div class="form-group">
              <label for="confirm_password">パスワードを認証</label>
              <input type="password" name="confirm_password" class="form-control form-control-lg <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
              <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>

            <div class="form-row">
              <div class="col">
                <input type="submit" value="登録" class="btn btn-success btn-block">
              </div>
              <div class="col">
                <a href="login.php" class="btn btn-light btn-block">アカウントを持っています？ ログインする</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>