<?= view("templates/header", ["title" => "Master Barang"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("master/barang/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>

    <div class="code_box">
      <label for="kode_barang">Kode Barang</label>
      <div>
        <input type="text" class="hidden" name="id_barang" id="id_barang" value="<?= session("id_barang") ?>">
        <input type="text" value="B" readonly />
        <input type="text" name="kode_barang" id="kode_barang" autocomplete="off" list="barang_list" autofocus 
        value="<?= session("kode_barang") ?>" />
        <datalist id="barang_list"></datalist>
      </div>
    </div>
    
    <div class="name_box hidden">
      <label for="nama_barang">Nama Barang</label>
      <input type="text" name="nama_barang" id="nama_barang" value="<?= session("nama_barang") ?>" />
    </div>

    <div class="name_box">
      <label for="nama_brand">Merek*</label>
      <input type="text" class="hidden" name="id_brand" id="id_brand" value="<?= session("id_brand") ?>">
      <input type="text" name="nama_brand" id="nama_brand" autocomplete="off" list="brand_list" value="<?= session("nama_brand"); ?>" required />
      <datalist id="brand_list"></datalist>
    </div>

    <div class="name_box">
      <label for="nama_tipe">Tipe*</label>
      <input type="text" class="hidden" name="id_tipe" id="id_tipe" value="<?= session("id_tipe") ?>">
      <input type="text" name="nama_tipe" id="nama_tipe" autocomplete="off" list="tipe_list" value="<?= session("nama_tipe"); ?>" required />
      <datalist id="tipe_list"></datalist>
    </div>

    <div class="name_box">
      <label for="ukuran">Ukuran</label>
      <input type="number" name="ukuran" id="ukuran" min="1" step="1" value="<?= session('ukuran'); ?>" />
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>

    <div class="button">
      <?= session("report"); ?>
      <button class="i_btn" id="save_btn" name="save_barang">Simpan</button>
      <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "master/barang"]);
