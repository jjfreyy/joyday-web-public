<?= view("templates/header", ["title" => "Tambah User"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("sistem/tambah_user/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>

    <div class="name_box">
      <label for="nama_user">Nama User*</label>
      <input type="text" name="nama_user" id="nama_user" value="<?= session("nama_user") ?>" required />
    </div>

    <div class="name_box">
      <label for="username">Username*</label>
      <input type="text" name="username" id="username" value="<?= session("username") ?>" required />
    </div>

    <div class="name_box">
      <label for="password">Password*</label>
      <input type="password" name="password" id="password" value="<?= session("password") ?>" required />
    </div>

    <div class="name_box">
      <label for="confirm_password">Konfirmasi Password*</label>
      <input type="password" name="confirm_password" id="confirm_password" value="<?= session("confirm_password") ?>" required />
    </div>

    <div class="name_box">
      <label for="no_hp">No. HP</label>
      <input type="text" name="no_hp" id="no_hp" value="<?= session("no_hp") ?>" />
    </div>

    <div class="name_box">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="<?= session("email") ?>" />
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>

    <div class="select_box">
      <label for="id_level">Level</label>
      <select name="id_level" id="id_level">
        <option value="1" <?= is_empty(session("id_level")) || session("id_level") === "1" ? "selected" : "" ?> >Admin</option>
        <option value="2" <?= session("id_level") === "2" ? "selected" : "" ?>>Kepala Gudang</option>
        <option value="3" <?= session("id_level") === "3" ? "selected" : "" ?>>Sales</option>
      </select>
    </div>

    <div class="button">
        <?= session("report"); ?>
        <button class="i_btn" id="save_btn" name="save_user">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer") ?>
