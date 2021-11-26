<?= view("templates/header", ["title" => "Daftar Pelanggan", "data" => "dialog"]) ?>

<section id="scol1" class="main">
  <input type="text" class="hidden" id="allow_edit" value="<?= $allow_edit ?>" />
  <input type="text" class="hidden" id="allow_delete" value="<?= $allow_delete ?>" />
  <div class="tb_container">
    <div class="search_container">
        <div class="search_box">
        <input type="text" class="search_field" placeholder="Pencarian" onfocus="this.placeholder=''" 
        onblur="this.placeholder='Pencarian'" />
        <span class="search_icon"></span>
        </div>
    </div>

    <table class="tb_daftar">
      <caption></caption>
      <colgroup>
        <col span="1" width="50px">
        <?php
          $width = 100;
          $px = "px";
          if (!$allow_edit) $width -= 50;
          if (!$allow_delete) $width -= 50;
          if ($width !== 0) echo "<col span='1' width='$width$px'>"; 
        ?>
        
        <col span="1" width="100px">  <!-- status -->
        <col span="1" width="150px">  <!-- agen -->
        <col span="1" width="100px">  <!-- kode_pelanggan --> 
        <col span="1" width="150px">  <!-- nama_pelanggan -->
        <col span="1" width="100px">  <!-- no_identitas -->
        
        <col span="1" width="150px">  <!-- no_hp1 -->
        <col span="1" width="150px">  <!-- no_hp2 -->
        <col span="1" width="150px">  <!-- email -->
        <col span="1" width="150px">  <!-- nama_propinsi -->
        <col span="1" width="150px">  <!-- nama_kabupaten -->

        <col span="1" width="150px">  <!-- nama_kecamatan -->
        <col span="1" width="150px">  <!-- nama_kelurahan -->
        <col span="1" width="250px">  <!-- alamat -->
        <col span="1" width="100px">  <!-- kode_pos -->
        <col span="1" width="200px">  <!-- keterangan -->

        <col span="1" width="100px">  <!-- daya_listrik -->
        <col span="1" width="100px">  <!-- latitude -->
        <col span="1" width="100px">  <!-- longitude -->
        <col span="1" width="150px">  <!-- nama_kerabat -->
        <col span="1" width="100px">  <!-- no_identitas_kerabat -->

        <col span="1" width="100px">  <!-- no_hp_kerabat -->
        <col span="1" width="250px">  <!-- alamat_kerabat -->
        <col span="1" width="100px">  <!-- hubungan -->
      </colgroup>

      <thead>
        <tr>
          <th>No.</th>
          <?php if ($width !== 0) echo "<th></th>"; ?>
          
          <th>Status</th>
          <th>Agen</th>
          <th>Kode</th>
          <th>Nama</th>
          <th>NIK/SIM</th>
          
          <th>No. HP1</th>
          <th>No. HP2</th>
          <th>Email</th>
          <th>Propinsi</th>
          <th>Kabupaten</th>
          
          <th>Kecamatan</th>
          <th>Kelurahan</th>
          <th>Alamat</th>
          <th>Kode Pos</th>
          <th>Keterangan</th>
          
          <th>Daya Listrik</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th>Nama Kerabat</th>
          <th>NIK/SIM Kerabat</th>
          <th>No. HP Kerabat</th>
          <th>Alamat Kerabat</th>
          <th>Hubungan</th>
        </tr>
      </thead>

      <tbody>
      </tbody>
    </table>

    <div class="pagination"></div>
  </div>
</section>

<?= view("templates/footer", ["data" => ["dialog", "daftar/pelanggan"]]) ?>
