<?php
namespace App\Controllers\laporan;

use App\Controllers\BaseController;
use App\Libraries\tcpdf\PDF;

class Barang_Masuk extends BaseController {
    
  function index() {
    if (!\check_privileges("BM-R")) return redirect()->route("404");
    $data = [
      "period" => json_decode(\fetch_get_request("laporan/barang_masuk/fetch", ["fetch_barang_masuk" => true, "type" => "period"])->getBody()),
    ];
    echo view("laporan/v_barang_masuk", $data);
  }

  private $data;
  private $pdf;
  private $table_width;

  function print() {
    $date1 = get_get("d1");
    $date2 = get_get("d2");
    $tipe = get_get("t");
    $filter = get_get("f");
    try {
      $response = \fetch_get_request("laporan/barang_masuk/fetch", [
        "fetch_barang_masuk" => true, 
        "type" => "laporan",
        "id_user" => session("joyday")["id_user"], 
        "date1" => $date1,
        "date2" => $date2,
        "tipe" => $tipe,
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
          "table_header_height" => 2.5,
          "size" => "A3",
        ]);
        $this->_build_title($date1, $date2);
        $this->_build_body();  
        $this->pdf->Output("LaporanBarangMasuk_" .date("dmyHis"). ".pdf", "I");
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
      "txt" => "Laporan Barang Masuk",
      "height" =>  $this->pdf->get_cell_height() + 2.5,
      "new_line" => 1,
      "align" => "C",
    ]);
    $this->pdf->set_font(["font_style" => "", "font_size" => 10]);
    $this->pdf->dcell([
      "txt" => \get_period($date1, $date2),
      "align" => "C",
      "new_line" => 1,
    ]);
    $this->pdf->Ln(3);
  }

  private function _build_body() {
    $title_width = .334;
    $this->table_width = [
      [.05, .325, .325, .15, .15],
      [.05, .2, .2, .1, .1, .35],
    ];
    $len = count($this->data);
    for ($i = 0; $i < $len; $i++) {
      $no_masuk = $this->data[$i]->no_masuk;
      $tipe = $this->data[$i]->tipe;
      $penerima = $this->data[$i]->penerima;
      $no_faktur = if_empty_then($this->data[$i]->no_faktur);
      $no_po = $this->data[$i]->no_po;
      $ke_gudang = $this->data[$i]->ke_gudang;
      $ke_agen = $this->data[$i]->ke_agen;
      $ke_agen = strlen($ke_agen) > 35 ? substr($ke_agen, 0, 35). "..." : $ke_agen;
      $keterangan = if_empty_then($this->data[$i]->keterangan);
      $qty_pesan = $this->data[$i]->qty_pesan;
      $qty_masuk = $this->data[$i]->qty_masuk;
      $tanggal_masuk = format_date($this->data[$i]->tanggal_masuk);
      $barang_masuk1 = explode("#", $this->data[$i]->barang_masuk1);
      
      $this->pdf->set_font_style("B");
      $this->pdf->draw_line();
      $this->pdf->dcell([
        "txt" => "No. Masuk: $no_masuk",
        "width" => $title_width,
      ]);
      $this->pdf->dcell([
        "txt" => "Penerima: $penerima",
        "width" => $title_width,
      ]);
      
      if ($tipe !== "1") {
        $this->pdf->dcell([
          "txt" => "No. Faktur: $no_faktur",
          "width" => $title_width,
          "new_line" => 1,
        ]);
        $this->pdf->dcell([
          "txt" => "No. PO: $no_po",
          "width" => $title_width,
        ]);
        if ($tipe === "0") {
          $this->pdf->dcell([
            "txt" => "Ke Gudang: $ke_gudang",
            "width" => $title_width,
          ]);
        } else {
          $this->pdf->dcell([
            "txt" => "Ke Agen: $ke_agen",
            "width" => $title_width,
          ]);
        }
      } else {
        $this->pdf->dcell([
          "txt" => "Ke Gudang: $ke_gudang",
          "width" => $title_width,
          "new_line" => 1,
        ]);
      }

      $this->pdf->dcell([
        "txt" => "Keterangan: $keterangan",
        "width" => $title_width,
        "new_line" => $tipe !== "1" ? 1 : 0,
      ]);

      if ($tipe !== "1") {
        $this->pdf->dcell([
          "txt" => "Qty Pesan: $qty_pesan",
          "width" => $title_width,
        ]);
      }

      $this->pdf->dcell([
        "txt" => "Qty Masuk: $qty_masuk",
        "width" => $title_width,
      ]);
      
      $this->pdf->dcell([
        "txt" => "Tanggal Masuk: $tanggal_masuk",
        "width" => $title_width,
        "new_line" => 1,
      ]);
      
      $this->pdf->draw_line();
      $this->pdf->Ln(3);
      
      $this->pdf->set_font_style("");
      $this->_build_table($tipe, $barang_masuk1, $i < ($len - 1) ? true : false);
    }
  }

  private function _build_table($tipe, $barang_masuk1, $add_new_line) {
    $table_width = $tipe !== "1" ? $this->table_width[0] : $this->table_width[1];
    $this->_build_table_header($table_width, $tipe);
    $this->_build_table_body($table_width, $tipe, $barang_masuk1, $add_new_line);
    if ($this->pdf->GetY() > $this->pdf->GetPageHeight()) $this->pdf->AddPage();
  }

  private function _build_table_header($table_width, $tipe) {
    $this->pdf->dcell([
      "txt" => "No.",
      "width" => $table_width[0],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
      ]);
    $this->pdf->dcell([
      "txt" => "Kode QR",
      "width" => $table_width[1],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
      $this->pdf->dcell([
      "txt" => "No. SN",
      "width" => $table_width[2],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Merek",
      "width" => $table_width[3],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Tipe",
      "width" => $table_width[4],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
      "new_line" => $tipe !== "1" ? 1 : 0,
    ]);
    if ($tipe === "1") {
      $this->pdf->dcell([
        "txt" => "Dari Pelanggan",
        "width" => $table_width[5],
        "height" => $this->pdf->get_table_header_height(),
        "fill" => true,
        "border" => 1,
        "align" => "C",
        "new_line" => 1,
      ]);
    }
  }

  private function _build_table_body($table_width, $tipe, $barang_masuk1, $add_new_line) {
    $len = count($barang_masuk1);
    for ($i = 0; $i < $len; $i++) {
      $no = $i + 1;
      $data = explode(";", $barang_masuk1[$i]);
      $qr_code = $data[0];
      $serial_number = $data[1];
      $nama_barang = $data[2];
      $tipe_barang = $data[3];
      $dari_pelanggan = $data[4];
      $dari_pelanggan = strlen($dari_pelanggan) > 47 ? \substr($dari_pelanggan, 0, 47). "..." : $dari_pelanggan;

      $this->pdf->dcell([
        "txt" => $no,
        "width" => $table_width[0],
        "border" => "RBL",
        "align" => "R",
      ]);
      $this->pdf->dcell([
        "txt" => $qr_code,
        "width" => $table_width[1],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $serial_number,
        "width" => $table_width[2],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $nama_barang,
        "width" => $table_width[3],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $tipe_barang,
        "width" => $table_width[4],
        "border" => "RB",
        "new_line" => $tipe !== "1" ? 1 : 0,
      ]);
      if ($tipe === "1") {
        $this->pdf->dcell([
          "txt" => $dari_pelanggan,
          "width" => $table_width[5],
          "border" => "RB",
          "new_line" => 1,
        ]);
      }
    }
    if ($add_new_line) $this->pdf->Ln(5);
  }

}