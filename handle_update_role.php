<?php
  require_once('./conn.php');
  require_once('./utils.php');
  session_start();


  if (empty($_SESSION['username'])) {
    header('Location: ./index.php');
    die();
  }

  $role = get_role($_SESSION['username']);

  if ($role !== 2) {
    header('Location: ./index.php');
    die();
  }




  $new_role = $_POST['role'];
  $username = $_POST['username'];
  echo $new_role . '<br>';
  echo $username;
  $sql = "UPDATE users SET role=? WHERE username=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ss', $new_role, $username);
  $stmt->execute();
  header('Location: ./admin.php');


?>