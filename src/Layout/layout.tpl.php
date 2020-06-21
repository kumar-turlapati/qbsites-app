<?php 
  extract($view_vars);
?>
<!doctype html>
<html lang="en">
<head>
  <?php include_once "partials/head.tpl.php" ?>
</head>
<body>
  <div class="pageContainer">
    <header>
      <?php include_once "partials/header.tpl.php" ?>
    </header>
    <?php echo $content ?>
    <footer>
      <?php include_once "partials/footer.tpl.php" ?>
    </footer>
  </div>
  <?php include_once "partials/footer-scripts.tpl.php" ?>
</body>
</html>