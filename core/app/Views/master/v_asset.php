<?= view("templates/header", ["title" => "Master Asset"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("master/asset/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>
    
    <div class="name_box">
      <label for="asset">Asset</label>
      <input type="text" class="hidden" id="id_asset" name="id_asset" value="<?= session("id_asset") ?>" />
      <input type="text" name="asset" id="asset" value="<?= session("asset") ?>" autocomplete="off" list="asset_list" autofocus >
      <datalist id="asset_list"></datalist>
    </div>
    
    <div id="barang_box" class="name_box <?= !is_empty(session("id_asset")) ? "hidden" : "" ?>">
      <label for="barang">Barang*</label>
      <input type="text" class="hidden" name="id_barang" id="id_barang" value="<?= session("id_barang") ?>">
      <input type="text" name="barang" id="barang" autocomplete="off" list="barang_list" value="<?= session("barang") ?>" <?= is_empty(session("id_asset")) ? "required" : "" ?> />
      <datalist id="barang_list"></datalist> 
    </div>
    
    <div class="name_box">
      <label for="qr_code">Kode QR*</label>
      <input type="text" name="qr_code" id="qr_code" value="<?= session("qr_code") ?>" required required />
    </div>

    <div class="name_box">
      <label for="serial_number">No. SN</label>
      <input type="text" name="serial_number" id="serial_number" value="<?= session("serial_number"); ?>" />
    </div>

    <div class="name_box">
      <label for="tanggal_akuisisi_asset">Tanggal Akuisisi Asset</label>
      <input type="date" name="tanggal_akuisisi_asset" id="tanggal_akuisisi_asset" value="<?=  session("tanggal_akuisisi_asset") ?>">
    </div>

    <div class="name_box">
      <label for="no_surat_kontrak">No. Surat Kontrak</label>
      <input type="text" name="no_surat_kontrak" id="no_surat_kontrak" value="<?= session("no_surat_kontrak"); ?>" />
    </div>

    <div class="name_box">
      <label for="tanggal_berakhir_kontrak">Tgl Berakhir Kontrak</label>
      <input type="date" name="tanggal_berakhir_kontrak" id="tanggal_berakhir_kontrak"
      value="<?= session("tanggal_berakhir_kontrak") ?>" />
    </div>

    <div class="select_box">
      <label for="id_kepemilikan">Status Kepemilikan</label>
      <select name="id_kepemilikan" id="id_kepemilikan">
        <?php $id_kepemilikan = session("id_kepemilikan"); foreach ($kepemilikan_list as $kepemilikan): ?>
          <option value="<?= $kepemilikan->id ?>" <?= $id_kepemilikan === $kepemilikan->id ? "selected" : "" ?>><?= $kepemilikan->nama_kepemilikan ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>

    <div id="tujuan_mutasi_box" class="name_box <?= !is_empty(session("id_asset")) ? "hidden" : "" ?>">
      <label>Mutasi Ke*</label>
      <div class="input_container_box">
        <div class="select_box">
          <select name="tujuan_mutasi" id="tujuan_mutasi">
            <option value="0" <?=
            (is_empty(session("id_gudang")) && is_empty(session("id_pelanggan"))) || 
            (!is_empty(session("id_gudang")) && !is_empty(session("id_pelanggan"))) || 
            !is_empty(session("id_gudang")) ? "selected" : "" 
            ?>>Gudang</option>
            <option value="1" <?= is_empty(session("id_pelanggan")) || (!is_empty(session("id_gudang")) && !is_empty("id_pelanggan")) ?  "" : "selected" ?>>Pelanggan</option>
          </select>
        </div>  
        <input type="text" class="hidden" name="id_gudang" id="id_gudang" value="<?= session("id_gudang") ?>" />
        <input type="text" class="<?= 
        (is_empty(session("id_gudang")) && is_empty(session("id_pelanggan"))) || 
        (!is_empty(session("id_gudang")) && !is_empty(session("id_pelanggan"))) || 
        !is_empty(session("id_gudang")) ? "" : "hidden" ?>" 
        name="gudang" id="gudang" value="<?= session("gudang") ?>" list="gudang_list" autocomplete="off" />
        <datalist id="gudang_list"></datalist>
        
        <input type="text" class="hidden" name="id_pelanggan" id="id_pelanggan" value="<?= session("id_pelanggan") ?>" />
        <input type="text" class="<?= is_empty(session("id_pelanggan")) || (!is_empty(session("id_gudang")) && !is_empty("id_pelanggan")) ? "hidden" : "" ?> " name="pelanggan" id="pelanggan" value="<?= session("pelanggan") ?>" list="pelanggan_list" autocomplete="off" />
        <datalist id="pelanggan_list"></datalist>
      </div>
    </div>

    <div class="select_box">
      <label for="sta">Status</label>
      <select name="sta" id="sta">
        <option value="1" <?= session("sta") === "1" ? "selected" : "" ?>>Rusak</option>
        <option value="2" <?= is_empty(session("sta")) || session("sta") === "2" ? "selected" : "" ?>>Siap Pakai</option>
      </select>
    </div>

    <div id="alasan_box" class="description_box <?= session("sta") !== "1" ? "hidden" : "" ?>">
      <label for="alasan">Alasan*</label>
      <textarea <?= session("sta") !== "1" ? "" : "required" ?> name="alasan" id="alasan"><?= session("alasan") ?></textarea>
    </div>

    <div class="button">
        <?= session("report"); ?>
        <button class="i_btn" id="save_btn" name="save_asset">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "master/asset"]) ?>
