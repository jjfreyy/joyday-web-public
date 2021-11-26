<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>
  <?php if (check_file("logo.png")): ?>
    <link rel="shortcut icon" href="<?= base_url("logo.png") ?>" type="image/x-icon">
  <?php endif; ?>
  <?= load_css(isset($data) ? $data : null) ?>
</head>
<body>
  <div class="background"></div>
  <?= view("templates/nav_header") ?>
  <?= view("templates/nav_aside") ?>