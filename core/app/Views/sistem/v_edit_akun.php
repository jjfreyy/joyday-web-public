<?= view("templates/header", ["title" => "Edit Akun"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("sistem/edit_akun/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>

    <div class="name_box">
      <label for="nama_user">Nama User*</label>
      <input type="text" name="nama_user" id="nama_user" value="<?= $nama_user ?>" required />
    </div>

    <div class="name_box">
      <label for="username">Username*</label>
      <input type="text" name="username" id="username" value="<?= $username ?>" required />
    </div>

    <div class="name_box">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" />
    </div>

    <div class="name_box">
      <label for="confirm_password">Konfirmasi Password</label>
      <input type="password" name="confirm_password" id="confirm_password" />
    </div>

    <div class="name_box">
      <label for="no_hp">No. HP</label>
      <input type="text" name="no_hp" id="no_hp" value="<?= $no_hp ?>" />
    </div>

    <div class="name_box">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="<?= $email ?>" />
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= $keterangan ?></textarea>
    </div>

    <div class="button">
        <?= session("report"); ?>
        <button class="i_btn" id="save_btn" name="save_user">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer") ?>
