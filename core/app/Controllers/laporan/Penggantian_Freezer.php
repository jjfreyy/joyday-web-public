<?php
namespace App\Controllers\laporan;

use App\Controllers\BaseController;
use App\Libraries\tcpdf\PDF;

class Penggantian_Freezer extends BaseController {
  function index() {
    if (!\check_privileges("ASS-R2")) return redirect()->route("404");
    $data = [
      "period" => \json_decode(\fetch_get_request("laporan/penggantian_freezer/fetch", ["fetch_asset" => true, "type" => "period"])->getBody()),
    ];
    echo view("laporan/v_penggantian_freezer", $data);
  }

  function print() {
    $date1 = get_get("d1");
    $date2 = get_get("d2");
    $filter = get_get("f");
    
    try {
      $response = \fetch_get_request("laporan/penggantian_freezer/fetch", [
        "fetch_asset" => true, 
        "type" => "laporan",
        "id_user" => session("joyday")["id_user"], 
        "date1" => $date1,
        "date2" => $date2,
        "filter" => $filter,
      ]);

      $this->data = json_decode($response->getBody());
      if ($response->getStatusCode() !== 200) {
        $message = $this->data->message;
        echo "<script>alert('$message'); window.close()</script>";
      } else if (empty($this->data)) {
        throw new \Exception("Empty Data!");
      } else {
        $this->pdf = new PDF([
          "font_style" => "B",
          "font_size" => 12,
          "orientation" => "L",
        ]);
        $this->_build_title($date1, $date2);
        $this->_build_body();
        $this->pdf->Output("LaporanPenggantianFreezer_" .date("dmyHis"). ".pdf", "I");
      }
      exit();
    } catch (\Exception $e) {
      echo "<script>alert('Data tidak dapat ditemukan.'); window.close()</script>";
      \send_500_response(\format_exception($e));
    }
  }

  // private function
  private function _build_title($date1, $date2) {
    $this->pdf->dcell([
      "txt" => "Laporan Penggantian Freezer",
      "height" =>  $this->pdf->get_cell_height() + 2.5,
      "new_line" => 1,
      "align" => "C",
    ]);
    $this->pdf->set_font(["font_style" => "", "font_size" => 10]);
    if (!is_empty($date1) && !is_empty($date2)) {
      $this->pdf->dcell([
        "txt" => \get_period($date1, $date2),
        "align" => "C",
        "new_line" => 1,
      ]);
    }
    $this->pdf->Ln(3);
  }

  private function _build_body() {
    $this->table_width = [.05, .35, .2, .2, .2];
    $len = count($this->data);
    for ($i = 0; $i < $len; $i++) {
      $nama_pelanggan = $this->data[$i]->nama_pelanggan;
      $nama_propinsi = if_empty_then($this->data[$i]->nama_propinsi);
      $nama_kabupaten = if_empty_then($this->data[$i]->nama_kabupaten);
      $nama_kecamatan = if_empty_then($this->data[$i]->nama_kecamatan);
      $nama_kelurahan = if_empty_then($this->data[$i]->nama_kelurahan);
      $kode_pos = if_empty_then($this->data[$i]->kode_pos);
      $alamat = if_empty_then($this->data[$i]->alamat);
      $list_asset = explode("#", $this->data[$i]->list_asset);
      
      $this->pdf->set_font_style("B");
      $this->pdf->draw_line();
      $this->pdf->dcell([
        "txt" => "Nama Pelanggan: $nama_pelanggan",
        "new_line" => 1,
      ]);

      $this->pdf->dcell([
        "txt" => "Alamat: $alamat",
        "new_line" => 1,
      ]);
      
      $this->pdf->dcell([
        "txt" => "Propinsi: $nama_propinsi",
        "width" => .334,
      ]);
      $this->pdf->dcell([
        "txt" => "Kabupaten: $nama_kabupaten",
        "width" => .334,
      ]);
      $this->pdf->dcell([
        "txt" => "Kecamatan: $nama_kecamatan",
        "width" => .334,
        "new_line" => 1,
      ]);

      $this->pdf->dcell([
        "txt" => "Kelurahan: $nama_kelurahan",
        "width" => .334,
      ]);
      $this->pdf->dcell([
        "txt" => "Kodepos: $kode_pos",
        "width" => .334,
        "new_line" => 1,
      ]);
      $this->pdf->draw_line();
      $this->pdf->Ln(3);
      
      $this->pdf->set_font_style("");
      $this->_build_table($list_asset, $i < ($len - 1) ? true : false);
    }
  }

  private function _build_table($list_asset, $add_new_line) {
    $this->_build_table_header();
    $this->_build_table_body($list_asset, $add_new_line);
    if ($add_new_line && $this->pdf->GetY() > $this->pdf->GetPageHeight()) $this->pdf->AddPage();
  }

  private function _build_table_header() {
    $this->pdf->dcell([
      "txt" => "No.",
      "width" => $this->table_width[0],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "QR Code",
      "width" => $this->table_width[1],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Merek",
      "width" => $this->table_width[2],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Tipe",
      "width" => $this->table_width[3],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Tanggal",
      "width" => $this->table_width[4],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
      "new_line" => 1,
    ]);
  }

  private function _build_table_body($list_asset, $add_new_line) {
    $len = count($list_asset);
    for ($i = 0; $i < $len; $i++) {
      $no = $i + 1;
      $data = explode(";", $list_asset[$i]);
      $qr_code = $data[0];
      $merek = $data[1];
      $tipe = $data[2];
      $tanggal = format_date($data[3]);

      $this->pdf->dcell([
        "txt" => $no,
        "width" => $this->table_width[0],
        "border" => "RBL",
        "align" => "R",
      ]);
      $this->pdf->dcell([
        "txt" => $qr_code,
        "width" => $this->table_width[1],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $merek,
        "width" => $this->table_width[2],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $tipe,
        "width" => $this->table_width[3],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $tanggal,
        "width" => $this->table_width[4],
        "border" => "RB",
        "new_line" => 1,
      ]);
    }
    if ($add_new_line) $this->pdf->Ln(5);
  }
}
