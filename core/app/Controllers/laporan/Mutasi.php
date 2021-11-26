<?php
namespace App\Controllers\laporan;

use App\Controllers\BaseController;
use App\Libraries\tcpdf\PDF;

class Mutasi extends BaseController {
    
  function index() {
    if (!\check_privileges("BK-R")) return redirect()->route("404");
    $data = [
      "period" => json_decode(\fetch_get_request("laporan/mutasi/fetch", ["fetch_mutasi" => true, "type" => "period"])->getBody()),
    ];
    echo view("laporan/v_mutasi", $data);
  }

  private $data;
  private $pdf;
  private $table_width;

  function print() {
    $date1 = get_get("d1");
    $date2 = get_get("d2");
    $dari_pelanggan = get_get("dp");
    $filter = get_get("f");

    try {
      $response = \fetch_get_request("laporan/mutasi/fetch", [
        "fetch_mutasi" => true, 
        "type" => "laporan",
        "id_user" => session("joyday")["id_user"], 
        "date1" => $date1,
        "date2" => $date2,
        "dari_pelanggan" => $dari_pelanggan,
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
          "size" => "A3",
        ]);
        $this->_build_title($date1, $date2);
        $this->_build_body();
        $this->pdf->Output("LaporanMutasi_" .date("dmyHis"). ".pdf", "I");
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
      "txt" => "Laporan Mutasi",
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
    $this->table_width = [.05, .20, .20, .10, .10, .35];
    $len = count($this->data);
    for ($i = 0; $i < $len; $i++) {
      $no_mutasi = $this->data[$i]->no_mutasi;
      $usr = $this->data[$i]->usr;
      $dari_pelanggan = $this->data[$i]->dari_pelanggan;
      $dari_pelanggan = $this->data[$i]->dari_pelanggan;
      $keterangan = if_empty_then($this->data[$i]->keterangan);
      $keterangan = \strlen($keterangan) > 35 ? \substr($keterangan, 0, 35). "..." : $keterangan;
      $tanggal_mutasi = format_date($this->data[$i]->tanggal_mutasi);
      $mutasi1 = explode("#", $this->data[$i]->mutasi1);
      
      $this->pdf->set_font_style("B");
      $this->pdf->draw_line();
      $this->pdf->dcell([
        "txt" => "No. Mutasi: $no_mutasi",
        "width" => $title_width,
      ]);
      $this->pdf->dcell([
        "txt" => "Pengurus: $usr",
        "width" => $title_width,
      ]);
      $this->pdf->dcell([
        "txt" => "Dari Pelanggan: $dari_pelanggan",
        "width" => $title_width,
        "new_line" => 1,
      ]);
      $this->pdf->dcell([
        "txt" => "Keterangan: $keterangan",
        "width" => $title_width,
      ]);
      $this->pdf->dcell([
        "txt" => "Tanggal Mutasi: $tanggal_mutasi",
        "width" => $title_width,
        "new_line" => 1,
      ]);
      $this->pdf->draw_line();
      $this->pdf->Ln(3);
      
      $this->pdf->set_font_style("");
      $this->_build_table($mutasi1, $i < ($len - 1) ? true : false);
    }
  }

  private function _build_table($mutasi1, $add_new_line) {
    $this->_build_table_header();
    $this->_build_table_body($mutasi1, $add_new_line);
    if ($this->pdf->GetY() > $this->pdf->GetPageHeight()) $this->pdf->AddPage();
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
      "txt" => "Kode QR",
      "width" => $this->table_width[1],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
      $this->pdf->dcell([
      "txt" => "No. SN",
      "width" => $this->table_width[2],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Merek",
      "width" => $this->table_width[3],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Tipe",
      "width" => $this->table_width[4],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Ke Pelanggan",
      "width" => $this->table_width[5],
      "new_line" => 1,
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
  }

  private function _build_table_body($mutasi1, $add_new_line) {
    $len = count($mutasi1);
    for ($i = 0; $i < $len; $i++) {
      $no = $i + 1;
      $data = explode(";", $mutasi1[$i]);
      $qr_code = $data[0];
      $serial_number = $data[1];
      $merek = $data[2];
      $tipe = $data[3];
      $ke_pelanggan = $data[4];

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
        "txt" => $serial_number,
        "width" => $this->table_width[2],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $merek,
        "width" => $this->table_width[3],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $tipe,
        "width" => $this->table_width[4],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $ke_pelanggan,
        "width" => $this->table_width[5],
        "border" => "RB",
        "new_line" => 1,
      ]);
    }
    if ($add_new_line) $this->pdf->Ln(5);
  }

}