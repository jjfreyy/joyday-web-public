<?= view("templates/header", ["title" => "Input Pesanan"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("input/pesanan/save") ?>" class="edit_form">
    <h1>(*) Wajib Diisi</h1>
    <div class="code_box">
      <input class="hidden" name="id_pesanan" id="id_pesanan" value="<?= session("id_pesanan"); ?>" />
      <label for="no_po">No. PO</label>
      <div>
        <input type="text" value="PO" readonly />
        <input type="text" name="no_po" id="no_po" autocomplete="off" list="pesanan_list" autofocus
        value="<?= session('no_po') ?>" />
        <datalist id="pesanan_list"></datalist>
      </div>
    </div>
    
    <div class="name_box">
      <input class="hidden" name="id_distributor" id="id_distributor" value="<?= session("id_distributor"); ?>" />
      <label for="distributor">Distributor*</label>
      <input type="text" name="distributor" id="distributor" autocomplete="off" list="distributor_list" required value="<?= session('distributor'); ?>" />
      <datalist id="distributor_list"></datalist>
    </div>

    <div class="description_box">
      <label for="keterangan">Keterangan</label>
      <textarea name="keterangan" id="keterangan"><?= session('keterangan'); ?></textarea>
    </div>

    <div class="table_input_container">
      <table class="tb_input">
        <thead>
          <tr>
            <th style="width:5vw">No.</th>
            <th style="width:80vw">
              <div class="name_box2">
                <label for="barang">Barang*</label>
                <input type="text" id="barang" name="barang" autocomplete="off" list="barang_list" />
                <datalist id="barang_list"></datalist>
              </div>
            </th>
            <th style="width:10vw">
              <div class="name_box2">
                <label for="qty">Qty*</label>
                <input type="number" id="qty" name="qty" min="1" />
              </div>
            </th>
            <th style="width:5vw"><a href="#" style="font-weight: bold" id="add_barang">+</a></th>
          </tr>
        </thead>

        <tbody>
          <?php
          $pesanan1 = session("pesanan1");
          if (!is_empty_array($pesanan1)) {
            for ($i = 0; $i < count($pesanan1); $i++) {
                $no = $i + 1;
                $barang = explode(";", $pesanan1[$i]);
                echo "
                  <tr>
                  <td>$no</td>
                  <td>
                    <input class='hidden' type='text' name='pesanan1[]' value='$pesanan1[$i]' />
                    $barang[1]
                  </td>
                  <td>$barang[2]</td>
                  <td class='centered'><a href='#' class='delete_barang'>Hapus</a></td>
                </tr>";
            }
          }
          ?>
        </tbody>
      </table>
    </div>

    <div class="button">
        <?= session("report"); ?>
        <button class="i_btn" id="save_btn" name="save_pesanan">Simpan</button>
        <button class="i_btn" id="reset_btn" type="reset">Reset</button>
    </div>
  </form>
</section>

<?= view("templates/footer", ["data" => "input/pesanan"]) ?>
