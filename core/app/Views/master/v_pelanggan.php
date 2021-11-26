<?= view("templates/header", ["title" => "Master Pelanggan"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("master/pelanggan/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>

    <div class="select_box">
        <label for="id_level">Daftarkan Sebagai</label>
        <select name="id_level" id="id_level">
            <option value="1" <?= session("id_level") === "1" ? "selected" : "" ?>>Retail</option>
            <option value="2" <?= session("id_level") === "2" ? "selected" : "" ?> >Agen</option>
        </select>
    </div>
    
    <div class="name_box <?= session("id_level") === "2" ? "hidden" : "" ?>" id="name_box_agen">
      <input type="text" class="hidden" name="id_agen" id="id_agen" value="<?= session("id_agen") ?>">
      <label for="agen">Agen</label>
      <input type="text" name="agen" id="agen" value="<?= session("agen") ?>" autocomplete="off" list="agen_list" />
      <datalist id="agen_list"></datalist>
    </div>

    <div class="code_box">
      <label for="kode_pelanggan">Kode Pelanggan</label>
      <div>
        <input type="text" class="hidden" name="id_pelanggan" id="id_pelanggan" value="<?= session("id_pelanggan") ?>">
        <input type="text" value="<?= session("id_level") === "2" ? "AGE" : "RET" ?>" readonly />
        <input type="text" name="kode_pelanggan" id="kode_pelanggan" autocomplete="off" list="pelanggan_list" autofocus 
        value="<?= session("kode_pelanggan") ?>" />
        <datalist id="pelanggan_list"></datalist>
      </div>
    </div>
    
    <div class="name_box">
      <label for="nama_pelanggan">Nama Pelanggan*</label>
      <input type="text" name="nama_pelanggan" id="nama_pelanggan" value="<?= session("nama_pelanggan") ?>" required />
    </div>

    <div class="name_box">
      <label for="no_identitas">NIK/SIM</label>
      <input type="number" name="no_identitas" id="no_identitas" value="<?= session("no_identitas") ?>" />
    </div>

    <div class="name_box">
      <label for="no_hp1">No. HP1</label>
      <input type="text" name="no_hp1" id="no_hp1" value="<?= session("no_hp1") ?>" />
    </div>

    <div class="name_box">
      <label for="no_hp2">No. HP2</label>
      <input type="text" name="no_hp2" id="no_hp2" value="<?= session("no_hp2") ?>" />
    </div>

    <div class="name_box">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="<?= session("email") ?>" />
    </div>

    <div class="name_box">
      <label for="nama_propinsi">Alamat Propinsi</label>
      <input type="text" class="hidden" name="id_propinsi" id="id_propinsi" value="<?= session("id_propinsi") ?>">
      <input type="text" name="nama_propinsi" id="nama_propinsi" autocomplete="off" list="propinsi_list" value="<?= session("nama_propinsi"); ?>" />
      <datalist id="propinsi_list"></datalist>
    </div>

    <div class="name_box">
      <label for="nama_kabupaten">Alamat Kabupaten</label>
      <input type="text" class="hidden" name="id_kabupaten" id="id_kabupaten" value="<?= session("id_kabupaten") ?>" >
      <input type="text" name="nama_kabupaten" id="nama_kabupaten" autocomplete="off" list="kabupaten_list" value="<?= session('nama_kabupaten'); ?>" />
      <datalist id="kabupaten_list"></datalist>
    </div>

    <div class="name_box">
      <label for="nama_kecamatan">Alamat Kecamatan</label>
      <input type="text" class="hidden" name="id_kecamatan" id="id_kecamatan" value="<?= session("id_kecamatan") ?>" >
      <input type="text" name="nama_kecamatan" id="nama_kecamatan" value="<?= session('nama_kecamatan'); ?>" autocomplete="off" list="kecamatan_list" />
      <datalist id="kecamatan_list"></datalist>
    </div>

    <div class="name_box">
      <label for="nama_kelurahan">Alamat Kelurahan*</label>
      <input type="text" class="hidden" name="id_kelurahan" id="id_kelurahan" value="<?= session("id_kelurahan") ?>" >
      <input type="text" name="nama_kelurahan" id="nama_kelurahan" value="<?= session('nama_kelurahan'); ?>" autocomplete="off" list="kelurahan_list" />
      <datalist id="kelurahan_list"></datalist>
    </div>

    <div class="description_box">
      <label for="alamat">Alamat</label>
      <textarea name="alamat" id="alamat"><?= session('alamat'); ?></textarea>
    </div>

    <div class="name_box">
      <label for="kode_pos">Kodepos</label>
      <input type="number" name="kode_pos" id="kode_pos" min="0" value="<?= session('kode_pos'); ?>" />
    </div>

    <div class="name_box">
      <label for="daya_listrik">Daya Listrik</label>
      <input type="number" name="daya_listrik" id="daya_listrik" min="1" step="1" value="<?= session('daya_listrik'); ?>" />
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>

    <div class="name_box">
      <label for="latitude">Latitude</label>
      <input type="text" name="latitude" id="latitude" value="<?= session('latitude'); ?>" />
    </div>

    <div class="name_box">
      <label for="longitude">Longitude</label>
      <input type="text" name="longitude" id="longitude" value="<?= session('longitude'); ?>" />
    </div>

    <div class="name_box">
      <label for="nama_kerabat">Nama Kerabat</label>
      <input type="text" name="nama_kerabat" id="nama_kerabat" value="<?= session('nama_kerabat'); ?>" />
    </div>

    <div class="name_box">
      <label for="no_identitas_kerabat">NIK/SIM Kerabat</label>
      <input type="number" name="no_identitas_kerabat" id="no_identitas_kerabat" value="<?= session('no_identitas_kerabat'); ?>" />
    </div>

    <div class="name_box">
      <label for="no_hp_kerabat">No. HP Kerabat</label>
      <input type="text" name="no_hp_kerabat" id="no_hp_kerabat" value="<?= session('no_hp_kerabat'); ?>" />
    </div>

    <div class="name_box">
      <label for="alamat_kerabat">Alamat Kerabat</label>
      <input type="text" name="alamat_kerabat" id="alamat_kerabat" value="<?= session('alamat_kerabat'); ?>" />
    </div>

    <div class="name_box">
      <label for="hubungan">Hubungan</label>
      <input type="text" name="hubungan" id="hubungan" value="<?= session('hubungan'); ?>" />
    </div>

    <div class="button">
        <?= session("report"); ?>
        <button class="i_btn" id="save_btn" name="save_pelanggan">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "master/pelanggan"]) ?>
