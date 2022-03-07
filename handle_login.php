<?php
  session_start();
  require_once('./conn.php');
  require_once('./utils.php');
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (empty($username) || empty($password)) {
    header('Location: ./login.php?errorCode=1');
    die();
  }

  // 先看有沒有這個 user
  $sql = "SELECT * FROM users WHERE username=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $username);
  $result = $stmt->execute();

  $result = $stmt->get_result();

  // 沒有的話顯示錯誤訊息
  if ($result->num_rows === 0) {
    header('Location: ./login.php?errorCode=2');
    die();
  }

  // 有的話把密碼的 hash 值拿出來
  $row = $result->fetch_assoc();
  // 驗證（比對明碼跟 hash 值）
  if (password_verify($password, $row['password'])) {
    $_SESSION['username'] = $username;
    // 登入成功
    header('Location: ./index.php');
  } else {
    // 登入失敗
    header('Location: ./login.php?errorCode=2');
  }
?>