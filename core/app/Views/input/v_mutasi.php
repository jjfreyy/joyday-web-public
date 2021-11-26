<?= view("templates/header", ["title" => "Input Mutasi"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("input/mutasi/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>
    
    <input class="hidden" name="id_mutasi" id="id_mutasi" value="<?= session("id_mutasi") ?>" />
    
    <div class="name_box">
      <label for="no_mutasi">No. Mutasi</label>
      <input type="text" value="<?= session("no_mutasi") ?>" readonly />
    </div>

    <div class="name_box">
      <input type="hidden" name="dari_id_pelanggan" id="dari_id_pelanggan" value="<?= session("dari_id_pelanggan") ?>">
      <label for="dari_pelanggan">Dari Pelanggan*</label>
      <input type="text" name="dari_pelanggan" id="dari_pelanggan" autocomplete="off" list="dari_pelanggan_list" value="<?= session("dari_pelanggan") ?>" autofocus>
      <datalist id="dari_pelanggan_list"></datalist>
    </div>

    <div class="description_box">
      <label for="alamat">Alamat</label>
      <textarea name="alamat" id="alamat" readonly><?= session("alamat") ?></textarea>
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>

    <div class="table_input_container">
      <table class="tb_input">
        <thead>
          <tr>
            <th>No.</th>
            <th style="min-width:150px;width:20vw;">
              <div class="name_box2">
                <label for="qr_code">Kode QR*</label>
                <input type="text" id="qr_code" autocomplete="off" list="asset_list" />
                <datalist id="asset_list"></datalist>
              </div>
            </th>
            <th style="min-width:150px;width:20vw;">
              <div class="name_box2">
                <label for="no_surat_kontrak">No. Surat Kontrak</label>
                <input type="text" id="no_surat_kontrak">
              </div>
            </th>
            <th style="min-width:200px;width:20vw;">
              <div class="name_box2">
                <label for="ke_pelanggan">Ke Pelanggan*</label>
                <input type="text" id="ke_pelanggan" autocomplete="off" list="ke_pelanggan_list">
                <datalist id="ke_pelanggan_list"></datalist>
              </div>
            </th>
            <th style="min-width:250px;width:30vw;">Alamat</th>
            <th><a href="#" style="font-weight: bold" id="add_asset">+</a></th>
          </tr>
        </thead>

        <tbody>
          <?php
          $mutasi1 = session("mutasi1");
          if (!is_empty_array($mutasi1)) {
            for ($i = 0; $i < count($mutasi1); $i++) {
              $no = $i + 1;
              $asset = explode(";", $mutasi1[$i]);
              echo "
                <tr>
                  <td>$no</td>
                  <td>
                    <input class='hidden' type='text' name='mutasi1[]' value='$mutasi1[$i]' />
                    $asset[1]
                  </td>
                  <td>" .if_empty_then($asset[2]). "</td>
                  <td>$asset[4]</td>
                  <td>$asset[5]</td>
                  <td class='centered'><a href='#' class='delete_asset'>Hapus</a></td>
                </tr>";
            }
          }
          ?>
        </tbody>
      </table>
    </div>

    <div class="button">
        <?= session("report"); ?>
        <button class="i_btn" id="save_btn" name="save_mutasi">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "input/mutasi"]) ?>
