<?= view("templates/header", ["title" => "Daftar Asset", "data" => "dialog"]) ?>
<?php if (!is_empty(session("alert"))): ?>
	<script>window.alert('<?= session("alert") ?>')</script>
<?php endif; ?>
<section id="scol1" class="main">
	<input type="text" class="hidden" id="allow_edit" value="<?= $allow_edit ?>" />
	<input type="text" class="hidden" id="allow_delete" value="<?= $allow_delete ?>" />
	<input type="text" class="hidden" id="allow_print" value="<?= $allow_print ?>" />
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
						<input type="date" name="tanggal1" id="tanggal1" />
						<input type="date" name="tanggal2" id="tanggal2" />
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
					<label for="sta">Status</label>
					<select id="sta">
						<option value="">Semua</option>
						<option value="1">Rusak</option>
						<option value="2" selected>Siap Pakai</option>
					</select>
				</div>

				<div class="name_box">
					<label for="filter">Pencarian</label>
					<input type="text" id="filter" name="filter" />
				</div>

				<span class="search_icon search_icon2"></span>

				<button class="i_btn" id="export_data" style="width: 0">Export ke Excel</button>
			</div>
	</aside>

		<table class="tb_daftar">
			<caption></caption>
			<colgroup>
				<col span="1" width="50px">
				<?php
					$width = 0;
					$px = "px";
					if ($allow_edit) $width += 50;
					if ($allow_delete) $width += 50;
					if ($allow_print) $width += 50;
					if ($width !== 0) echo "<col span='1' width='$width$px'>"; 
				?>

				<col span="1" width="150px">
				<col span="1" width="150px">
				<col span="1" width="150px">
				<col span="1" width="250px">
				<col span="1" width="150px">
				
				<col span="1" width="100px">
				<col span="1" width="150px">
				<col span="1" width="250px">
				<col span="1" width="150px">
				<col span="1" width="100px">
			</colgroup>

			<thead>
				<tr>
					<th>No.</th>
					<?php if ($width !== 0) echo "<th></th>"; ?>

					<th>Nama Barang</th>
					<th>Kode QR</th>
					<th>No. SN</th>
					<th>Tgl Akuisisi</th>
					<th>No. Kontrak</th>
					
					<th>Tgl Berakhir</th>
					<th>Kepemilikan</th>
					<th>Keterangan</th>
					<th>Lokasi</th>
					<th>Status</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>

		<div class="pagination"></div>
	</div>
</section>

<?= view("templates/footer", ["data" => ["dialog", "daftar/asset"]]) ?>
