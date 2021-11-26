<?= view("templates/header", ["title" => "Input Barang Masuk", "data" => "dialog"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("input/barang_masuk/save") ?>" class="edit_form" id="form">
    <h1>(*) Wajib Diisi</h1>
    <?php 
    $tipe = is_empty(session("tipe")) ? "0" : session("tipe");
    // if ($id_level === "1") $tipe = "2";
    $edit_mode = is_empty(session("id_barang_masuk")) ? false : true;
    ?>
    
    <input class="hidden" name="id_barang_masuk" id="id_barang_masuk" value="<?= session("id_barang_masuk"); ?>" />

    <div class="name_box">
      <label for="no_masuk">No. Masuk</label>
      <input type="text" readonly name="no_masuk" value="<?= session("no_masuk") ?>">
    </div>

    <?php if (!$edit_mode) : ?> 
      <div class="select_box">
        <label for="tipe">Tipe</label>
        <select name="tipe" id="tipe">
          <?php if ($id_level === "2"): ?><option value="0" <?= $tipe === "0" ? "selected" : "" ?>>Dari Distributor</option><?php endif; ?>
          <?php if ($id_level === "2"): ?><option value="1" <?= $tipe === "1" ? "selected" : "" ?>>Dari Pelanggan</option><?php endif; ?>
          <?php if ($id_level === "1"): ?><option value="2" <?= $tipe === "2" ? "selected" : "" ?>>Ke Agen</option><?php endif; ?>
        </select>
      </div>
    <?php else: ?>
      <input type="text" class="hidden" name="tipe" id="tipe" value="<?= $tipe ?>">
    <?php endif; ?>

    <div id="no_faktur_box" class="name_box <?= in_array($tipe, ["0", "2"]) ? "" : "hidden" ?>">
      <label for="no_faktur">No. Faktur</label>
      <input type="text" name="no_faktur" id="no_faktur" autofocus value="<?= session("no_faktur"); ?>">
    </div>
    
    <div id="no_po_box" class="name_box <?= in_array($tipe, ["0", "2"]) ? "" : "hidden" ?>">
      <input class="hidden" name="dari_id_pesanan" id="dari_id_pesanan" value="<?= session("dari_id_pesanan"); ?>" />
      <label for="no_po">No. PO*</label>
      <input type="text" name="no_po" id="no_po" autocomplete="off" list="pesanan_list" value="<?= session('no_po') ?>" <?= $tipe === "0" ? "required" : "" ?> />
      <datalist id="pesanan_list"></datalist>
    </div>  

    <div id="ke_agen_box" class="name_box <?= $tipe === "2" || ($id_level === "1" && (!$edit_mode || $edit_mode && $tipe === "2")) ? "" : "hidden" ?>">
        <input type="hidden" name="ke_id_agen" id="ke_id_agen" value="<?= session("ke_id_agen") ?>">
        <label for="ke_agen">Ke Agen*</label>
        <input type="text" name="ke_agen" id="ke_agen" autocomplete="off" list="ke_agen_list" value="<?= session("ke_agen") ?>" <?= $tipe === "2" || ($id_level === "1" && (!$edit_mode || $edit_mode && $tipe === "2")) ? "required" : "" ?>>
        <datalist id="ke_agen_list"></datalist>
    </div>

    <div id="alamat_box" class="description_box <?= $tipe === "2" || ($id_level === "1" && (!$edit_mode || $edit_mode && $tipe === "2")) ? "" : "hidden" ?>">
      <label for="alamat">Alamat</label>
      <textarea name="alamat" id="alamat" readonly><?= session("alamat") ?></textarea>
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>
    
    <div class="table_input_container <?= $tipe === "1" && $edit_mode ? "hidden" : "" ?>">
      <table class="tb_input">
        <thead>
          <tr>
            <th style="width:5vw">No.</th>
            <th style="width:20vw;">
              <div class="name_box2">
                <label for="qr_code">Kode QR*</label>
                <input type="text" id="qr_code" autocomplete="off" list="asset_list">
                <datalist id="asset_list"></datalist>
              </div>
            </th>
            <th style="width:55vw">
              <div class="name_box2" id="th_barang_box" style="display:<?= $tipe === "1" ? "none" : "grid" ?>">
                <label for="barang">Barang</label>
                <input type="text" id="barang" autocomplete="off" list="barang_list">
                <datalist id="barang_list"></datalist>
              </div>
              <label id="th_dari_pelanggan" style="display:<?= $tipe === "1" ? "block" : "none" ?>;min-width: 250px;text-align:center" for="">Dari Pelanggan</label>
            </th>
            <th id="th_status" style="width:15vw;display:<?= $tipe === "1" ? "table-cell" : "none" ?>;">
              <div class="name_box2">
                <label for="th_lb_status">Kondisi</label>
                <select id="th_select_status" style="width: 150px;justify-self:center;">
                  <option value="2">Bagus</option>
                  <option value="1">Rusak</option>
                </select>
              </div>
            </th>
            <th style="width:5vw;"><a href="#" style="font-weight:bold;" id="add_asset">+</a></th>
          </tr>
        </thead>

        <tbody>
          <?php
          $barang_masuk1 = session("barang_masuk1");
          if (!is_empty_array($barang_masuk1)) {
            for ($i = 0; $i < count($barang_masuk1); $i++) {
              $no = $i + 1;
              $asset = explode(";", $barang_masuk1[$i]);
              $tr = "
                <tr>
                  <td>$no</td>
                  <td>
                    <input class='hidden' type='text' name='barang_masuk1[]' value='$barang_masuk1[$i]' />
                    $asset[1]
                  </td>
              ";
              if ($tipe === "1") {
                $tr .= "
                  <td>$asset[5]</td>
                  <td>$asset[7]</td>
                ";
              } else {
                $tr .= "<td>$asset[2]</td>";
              }

              $tr .= "
                  <td class='centered'><a href='#' class='delete_asset'>Hapus</a></td>
                </tr>
              ";
              echo $tr;
            }
          }
          ?>
        </tbody>
      </table>
    </div>

    <div class="button">
        <?= session("report"); ?>
        <input type="hidden" name="save_barang_masuk">
        <button class="i_btn" id="save_btn" name="save_barang_masuk">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "input/barang_masuk", "data" => ["input/barang_masuk", "dialog"]]) ?>
