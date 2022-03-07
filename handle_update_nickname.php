<?php
  require_once('./conn.php');
  session_start();
  $username = $_SESSION['username'];
  $new_nick_name = $_POST['nickname'];

  if (empty($new_nick_name)) {
    header('Location: ./index.php?errorCode=1');
    die();
  }

  $sql = "UPDATE users SET nickname=? WHERE username=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ss', $new_nick_name, $username);
  $result = $stmt->execute();
  
  header('Location: ./index.php');

?>