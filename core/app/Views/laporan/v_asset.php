<?= view("templates/header", ["title" => "Laporan Asset"]) ?>

<section id="scol1" class="main">
  <aside class="search_container3">
    <div class="search_box2">
      <div class="radio_date_box">
        <div class="checkbox_reverse">
          <input type="radio" name="date_search_method" id="tanggal_check" checked="checked" />
          <label for="">Tanggal</label>
        </div>
        <div class="input_container_box">
          <input type="date" name="tanggal1" id="tanggal1" value="<?php echo date("Y-m-d"); ?>" required />
          <input type="date" name="tanggal2" id="tanggal2" value="<?php echo date("Y-m-d"); ?>" required />
        </div>
      </div>

      <div class="radio_select_box">
        <div class="checkbox_reverse">
          <input type="radio" name="date_search_method" id="bulan_check" />
          <label for="">Bulan</label>
        </div>
        <div class="select_box">
          <select name="bulan" id="bulan" disabled>
              <?php
              if (!is_empty_array($period)) {
                foreach ($period as $row) {
                  $tahun = $row->tahun;
                  $bulan = $row->bulan;
                  $nama_bulan = $row->nama_bulan;
                  echo "<option value='$tahun-$bulan'>$nama_bulan $tahun</option>";
                }
              }
              ?>
          </select>
        </div>
      </div>

      <div class="select_box">
        <label for="kondisi">Kondisi</label>
        <select id="kondisi">
          <option value="">Semua</option>
          <option value="1">Rusak</option>
          <option value="2">Siap Pakai</option>
        </select>
      </div>

      <div class="name_box">
        <label for="filter">Pencarian</label>
        <input type="text" name="filter" id="filter" />
      </div>

      <span class="search_icon search_icon2"></span>
    </div>
  </aside>
</section>
  
  <?= view("templates/footer", ["data" => "laporan/asset"]) ?>
