<?= view("templates/header", ["title" => "Hak Akses"]) ?>

<section id="scol1" class="main">
  <form method="post" accept-charset="utf-8" action="<?= base_url("sistem/edit_akun/save") ?>" class="edit_form">
    <div class="name_box">
      <input type="text" class="hidden" id="id_user">
      <label for="user">User</label>
      l
    </div>

    <table class="tb_daftar">
      <colgroup>
        <col span="1" width="50px">
        <col span="1" width="150px">
        <col span="1" width="50px">
      </colgroup>

      <thead>
        <tr>
          <th>Kode Akses</th>
          <th>Nama Akses</th>
          <th>Status</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($hak_akses_arr as $row) {
          $kode_akses = $row->kode_akses;
          $nama_akses = $row->nama_akses;
          $sta = $row->sta;
        ?>
          <tr>
            <td><?= $kode_akses ?></td>
            <td><?= $nama_akses ?></td>
            <td><input type="checkbox" name="hak_akses_arr" id="hak_akses_arr" <?= $sta === "0" ? "" : "checked" ?> /></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

  </form>
</section>

<?= view("templates/footer") ?>
