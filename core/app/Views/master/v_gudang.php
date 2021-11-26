<?= view("templates/header", ["title" => "Master Gudang"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("master/gudang/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>


    <div class="name_box">
    <input type="text" class="hidden" name="id_kepala_gudang" id="id_kepala_gudang", value="<?= session("id_kepala_gudang") ?>">
      <label for="kepala_gudang">Kepala Gudang</label>
      <input type="text" name="kepala_gudang" id="kepala_gudang" value="<?= session("kepala_gudang") ?>" autocomplete="off" list="kepala_gudang_list" />
      <datalist id="kepala_gudang_list"></datalist>
    </div>
    
    <div class="code_box">
      <label for="kode_gudang">Kode Gudang</label>
      <div>
        <input type="text" class="hidden" name="id_gudang" id="id_gudang" value="<?= session("id_gudang") ?>">
        <input type="text" value="GUD" readonly />
        <input type="text" name="kode_gudang" id="kode_gudang" autocomplete="off" list="gudang_list" autofocus 
        value="<?= session("kode_gudang") ?>" />
        <datalist id="gudang_list"></datalist>
      </div>
    </div>
    
    <div class="name_box">
      <label for="nama_gudang">Nama Gudang*</label>
      <input type="text" name="nama_gudang" id="nama_gudang" value="<?= session("nama_gudang") ?>" required />
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>

    <div class="button">
        <?= session("report"); ?>
        <button class="i_btn" id="save_btn" name="save_gudang">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "master/gudang"]) ?>
