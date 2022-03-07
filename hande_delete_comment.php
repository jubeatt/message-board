<?php
  require_once('./conn.php');
  require_once('./utils.php');
  session_start();
  $id= $_GET['id'];
  $username = $_SESSION['username'];
  $role = get_role($username);

  // 管理員
  if ($role === 2) {
    $sql = "UPDATE comments SET is_deleted=1 WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: ./index.php');
    die();
  }

  // 一般使用者
  $sql = "UPDATE comments SET is_deleted=1 WHERE id=? AND username=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('is', $id, $username);
  $stmt->execute();

  header('Location: ./index.php');
?>