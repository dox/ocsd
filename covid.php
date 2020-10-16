<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/config.php');

require_once($root . '/includes/globalFunctions.php');
require_once($root . '/includes/db.php');
require_once($root . '/includes/classLogs.php');

$db = new db(db_host, db_username, db_password, db_name);

$logsClass = new Logs();

$bodcard = $_POST['inputBodcard'];
if (isset($bodcard)) {
  $logInsert = (new Logs)->insert("bodcard-tap","success",null,"bodcard tap for " . $bodcard);
  $message = "Bodcard Tap Recorded";
} else {
  $message = "";
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Andrew Breakspear">
    <title>Bodcard Tap-In</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <style>
      html,body {
        height: 100%;
      }
      body {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        width: 100%;
        max-width: 330px;
        padding: 15px;
        margin: auto;
      }

      .form-signin .form-control {
        position: relative;
        box-sizing: border-box;
        height: auto;
        padding: 10px;
        font-size: 16px;
      }

      .form-signin .form-control:focus {
        z-index: 2;
      }
    </style>
  </head>
  <body class="text-center">
    <form class="form-signin" id="bodcardTapin" method="post" role="main">
      <h1 class="h3 mb-3 font-weight-normal">Please Tap Your Bodcard</h1>
      <label for="inputBodcard" class="sr-only">Bodcard</label>
      <input type="text" id="inputBodcard" name="inputBodcard" class="form-control" required autofocus>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
      <p class="mt-5 mb-3 text-muted">&copy; 2020</p>
    </form>
  </body>
</html>
