<?= view("templates/header", ["title" => "Master Distributor"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("master/distributor/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>

    
    <div class="code_box">
      <label for="kode_distributor">Kode Distributor</label>
      <div>
        <input type="text" class="hidden" name="id_distributor" id="id_distributor" value="<?= session("id_distributor") ?>">
        <input type="text" value="DIS" readonly />
        <input type="text" name="kode_distributor" id="kode_distributor" autocomplete="off" list="distributor_list" autofocus 
        value="<?= session("kode_distributor") ?>" />
        <datalist id="distributor_list"></datalist>
      </div>
    </div>
    
    <div class="name_box">
      <label for="nama_distributor">Nama Distributor*</label>
      <input type="text" name="nama_distributor" id="nama_distributor" value="<?= session("nama_distributor") ?>" required />
    </div>

    <div class="description_box">
      <label for="alamat">Alamat*</label>
      <textarea name="alamat" id="alamat" required><?= session('alamat'); ?></textarea>
    </div>

    <div class="name_box">
      <label for="no_hp">No. HP*</label>
      <input type="text" name="no_hp" id="no_hp" value="<?= session("no_hp") ?>" required />
    </div>

    <div class="name_box">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="<?= session("email") ?>" />
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>

    <div class="button">
        <?= session("report"); ?>
        <button class="i_btn" id="save_btn" name="save_distributor">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "master/distributor"]) ?>
