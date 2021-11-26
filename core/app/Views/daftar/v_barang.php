<?= view("templates/header", ["title" => "Daftar Barang", "data" => "dialog"]) ?>

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
				<col span="1" width="100px">
				<!-- <col span="1" width="150px"> -->
				<col span="1" width="150px">
				<col span="1" width="150px">
				<col span="1" width="150px">
				<col span="1" width="200px">
			</colgroup>

			<thead>
				<tr>
					<th>No.</th>
					<?php if ($width !== 0) echo "<th></th>"; ?>
					<th>Kode</th>
					<!-- <th>Nama</th> -->
					<th>Brand</th>
					<th>Tipe</th>
					<th>Ukuran</th>
					<th>Keterangan</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>

		<div class="pagination"></div>
	</div>
</section>

<?= view("templates/footer", ["data" => ["dialog", "daftar/barang"]]) ?>
