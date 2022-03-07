<?php
  require_once('./conn.php');
  require_once('./utils.php');
  session_start();
  $id= $_POST['id'];
  $content = $_POST['content'];
  $username = $_SESSION['username'];
  $role = get_role($username);


  if (empty($content)) {
    header("Location: ./update_comment.php?id=$id&errorCode=1");
    die();
  }

  // 管理員
  if ($role === 2) {
    $sql = "UPDATE comments SET content=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $content, $id);
    $stmt->execute();
    header('Location: ./index.php');
    die();
  }

  // 一般使用者
  $sql = "UPDATE comments SET content=? WHERE id=? AND username=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('sis', $content, $id, $username);
  $stmt->execute();

  header('Location: ./index.php');
?>