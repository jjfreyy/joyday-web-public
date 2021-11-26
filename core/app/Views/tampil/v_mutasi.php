<?= view("templates/header", ["title" => "Tampil Mutasi", "data" => "dialog"]) ?>

<section id="scol1" class="main">
	<input type="text" class="hidden" id="allow_edit" value="<?= $allow_edit ?>" />
	<input type="text" class="hidden" id="allow_delete" value="<?= $allow_delete ?>" />
		
	<div class="tb_container">
		<aside class="search_container3">
			<div class="accordion"><span class="nav_icon1"></span></div>
			<div class="search_box2">
				<div class="radio_date_box">
					<div class="checkbox_reverse">
						<input type="radio" name="date_search_method" id="tanggal_check" checked="checked" />
						<label for="">Tanggal</label>
					</div>
					<div class="input_container_box">
						<input type="date" name="tanggal1" id="tanggal1" value="<?php echo date("Y-m-d"); ?>" />
						<input type="date" name="tanggal2" id="tanggal2" value="<?php echo date("Y-m-d"); ?>" />
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

				<div class="name_box">
					<label for="filter">Pencarian</label>
					<input type="text" name="filter" id="filter" />
				</div>

				<span class="search_icon search_icon2"></span>
			</div>
		</aside>

		<table class="tb_daftar tb_main">
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
				<col span="1" width="100px">
				<col span="1" width="150px">
				<col span="1" width="150px">
				<col span="1" width="200px">
				<col span="1" width="100px">
			</colgroup>

			<thead>
				<tr>
					<th>No.</th>
					<?php if ($width !== 0) echo "<th></th>"; ?>
					<th>No. Mutasi</th>
					<th>User</th>
					<th>Dari Pelanggan</th>
					<th>Keterangan</th>
					<th>Tgl Mutasi</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>

		<div class="pagination"></div>
	</div>
</section>

<div class="dialog_background" style="display:none">
	<div class="dialog" style="display:none">
		<div class="dialog_header">
			<span class="dialog_close_btn" title="Tutup Dialog"></span>
		</div>
		<div class="dialog_body">
			<table class="td_daftar tb_dialog">
				<colgroup>
					<col span="1" width="50px">
					<col span="1" width="150px">
					<col span="1" width="100px">
					<col span="1" width="100px">
					<col span="1" width="250px">
				</colgroup>

				<thead>
					<tr>
						<th>No.</th>
						<th>Kode QR</th>
						<th>Merek</th>
						<th>Tipe</th>
						<th>Ke Pelanggan</th>
					</tr>
				</thead>

				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

<?= view("templates/footer", ["data" => ["dialog", "tampil/mutasi"]]) ?>
