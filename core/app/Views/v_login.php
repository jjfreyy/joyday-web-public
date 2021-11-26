<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Halaman Login</title>
  <?= load_css("login") ?>
</head>
<body>

  <section class="login_page">
    <div class="b_container">
      <div class="container">
        <div class="col header">
          <h2>Welcome</h2>
        </div>

        <div class="col">
          <?= session("report"); ?>
        </div>

        <div class="col">
          <form method="post" accept-charset="utf-8" action="<?= base_url("login/process") ?>" class="form_login" >
            <div class="col form_container">
              <div class="row input_row">
                <input type="text" class="i_login" name="username" id="username" autocomplete="off" autofocus required>
                <span class="placeholder">Username/Email/Telepon</span>
              </div>

              <div class="row input_row">
                <input type="password" class="i_login" name="password" id="password" required>
                <span class="placeholder">Password</span>
              </div>

              <div class="row btn_row">
                <button type="submit" name="login_btn">
                  <img src="<?= base_url("src/img/login.png") ?>" alt="">
                  <span>Login</span>
                </button>
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

<?= view("templates/footer") ?>
