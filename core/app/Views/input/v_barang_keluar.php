<?= view("templates/header", ["title" => "Input Barang Keluar"]); ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("input/barang_keluar/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>

    <input class="hidden" name="id_gudang" id="id_gudang" value="<?= $id_gudang ?>" />
    <input class="hidden" name="id_barang_keluar" id="id_barang_keluar" value="<?= session("id_barang_keluar"); ?>" />

    <div class="name_box">
      <label for="no_keluar">No. Keluar</label>
      <input type="text" readonly name="no_keluar" value="<?= session("no_keluar") ?>">
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan" autofocus><?= session('keterangan'); ?></textarea>
    </div>

    <div class="table_input_container">
      <table class="tb_input">
        <thead>
          <tr>
            <th style="width:5vw">No.</th>
            <th style="width:30vw">
              <div class="name_box2">
                <label for="qr_code">Kode QR*</label>
                <input type="text" id="qr_code" autocomplete="off" list="asset_list" />
                <datalist id="asset_list"></datalist>
              </div>
            </th>
            <th style="width:60vw;min-width: 250px;">Barang</th>
            <th style="width:5vw"><a href="#" style="font-weight: bold" id="add_asset">+</a></th>
          </tr>
        </thead>

        <tbody>
          <?php
          $barang_keluar1 = session("barang_keluar1");
          if (!is_empty_array($barang_keluar1)) {
            for ($i = 0; $i < count($barang_keluar1); $i++) {
                $no = $i + 1;
                $asset = explode(";", $barang_keluar1[$i]);
                echo "
                <tr>
                  <td>$no</td>
                  <td>
                    <input class='hidden' type='text' name='barang_keluar1[]' value='$barang_keluar1[$i]' />
                    $asset[1]
                  </td>
                  <td>$asset[2]</td>
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
        <button class="i_btn" id="save_btn" name="save_barang_keluar">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "input/barang_keluar"]);
