<?php
namespace App\Controllers\laporan;

use App\Controllers\BaseController;
use App\Libraries\tcpdf\PDF;

class Asset extends BaseController {
    
  function index() {
    if (!\check_privileges("ASS-R")) return redirect()->route("404");
    $data = [
      "period" => json_decode(\fetch_get_request("laporan/asset/fetch", ["fetch_asset" => true, "type" => "period"])->getBody()),
    ];
    echo view("laporan/v_asset", $data);
  }

  private $data;
  private $pdf;
  private $table_width;

  function print() {
    $filter_by = get_get("fb");
    $id = get_get("id");
    $date1 = get_get("d1");
    $date2 = get_get("d2");
    $kondisi = get_get("k");
    $filter = get_get("f");

    try {
      $response = \fetch_get_request("laporan/asset/fetch", [
        "fetch_asset" => true, 
        "type" => "laporan",
        "id_user" => session("joyday")["id_user"], 
        "filter_by" => $filter_by,
        "id" => $id,
        "date1" => $date1,
        "date2" => $date2,
        "kondisi" => $kondisi,
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
           "orientation" => "L",
        ]);
        $this->_build_title($filter_by, $date1, $date2);
        $this->_build_body();
        $this->pdf->Output("LaporanAsset_" .date("dmyHis"). ".pdf", "I");
      }
      exit();
    } catch (\Exception $e) {
      echo "<script>alert('Data tidak dapat ditemukan.'); window.close()</script>";
      \send_500_response(\format_exception($e));
    }
  }

  // private function
  private function _build_title($filter_by, $date1, $date2) {
    $this->pdf->dcell([
      "txt" => "Laporan Asset",
      "height" =>  $this->pdf->get_cell_height() + 2.5,
      "new_line" => 1,
      "align" => "C",
    ]);
    $this->pdf->set_font(["font_style" => "", "font_size" => 10]);
    if ($filter_by !== "id" || (!is_empty($date1) && !is_empty($date2))) {
      $this->pdf->dcell([
        "txt" => \get_period($date1, $date2),
        "align" => "C",
        "new_line" => 1,
      ]);
    }
    $this->pdf->Ln(3);
  }

  private function _build_body() {
    $title_width = .334;
    $this->table_width = [.03, .06, .06, .15, .2, .2, .3];
    $len = count($this->data);
    for ($i = 0; $i < $len; $i++) {
      $qr_code = $this->data[$i]->qr_code;
      $serial_number = $this->data[$i]->serial_number;
      $no_surat_kontrak = $this->data[$i]->no_surat_kontrak;
      $nama_brand = $this->data[$i]->nama_brand;
      $nama_tipe = if_empty_then($this->data[$i]->nama_tipe);
      $keterangan = if_empty_then($this->data[$i]->keterangan);
      $detail = explode("#", $this->data[$i]->detail);
      
      $this->pdf->set_font_style("B");
      $this->pdf->draw_line();
      $this->pdf->dcell([
        "txt" => "QR Code: $qr_code",
        "width" => $title_width,
      ]);
      $this->pdf->dcell([
        "txt" => "No. SN: $serial_number",
        "width" => $title_width,
      ]);
      $this->pdf->dcell([
        "txt" => "No. Surat Kontrak: $no_surat_kontrak",
        "width" => $title_width,
        "new_line" => 1,
      ]);
      $this->pdf->dcell([
        "txt" => "Merek: $nama_brand",
        "width" => $title_width,
      ]);
      $this->pdf->dcell([
        "txt" => "Tipe: $nama_tipe",
        "width" => $title_width,
      ]);
      $this->pdf->dcell([
        "txt" => "Keterangan: $keterangan",
        "width" => $title_width,
        "new_line" => 1,
      ]);
      $this->pdf->draw_line();
      $this->pdf->Ln(3);
      
      $this->pdf->set_font_style("");
      $this->_build_table($detail, $i < ($len - 1) ? true : false);
    }
  }

  private function _build_table($detail, $add_new_line) {
    $this->_build_table_header();
    $this->_build_table_body($detail, $add_new_line);
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
      "txt" => "Tanggal",
      "width" => $this->table_width[1],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "No. Dokumen",
      "width" => $this->table_width[2],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Penginput",
      "width" => $this->table_width[3],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Dari",
      "width" => $this->table_width[4],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Ke",
      "width" => $this->table_width[5],
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
    $this->pdf->dcell([
      "txt" => "Keterangan",
      "width" => $this->table_width[6],
      "new_line" => 1,
      "height" => $this->pdf->get_table_header_height(),
      "fill" => true,
      "border" => 1,
      "align" => "C",
    ]);
  }

  private function _build_table_body($detail, $add_new_line) {
    $len = count($detail);
    for ($i = 0; $i < $len; $i++) {
      $no = $i + 1;
      $data = explode(";", $detail[$i]);
      $tanggal = format_date($data[0]);
      $no_dokumen = $data[1];
      $usr = $data[2];
      $dari = $data[3];
      $dari = \strlen($dari) > 38 ? \substr($dari, 0, 38). "..." : $dari;
      $ke = $data[4];
      $ke = \strlen($ke) > 38 ? \substr($ke, 0, 38). "..." : $ke;
      $keterangan = $data[5];

      $this->pdf->dcell([
        "txt" => $no,
        "width" => $this->table_width[0],
        "border" => "RBL",
        "align" => "R",
      ]);
      $this->pdf->dcell([
        "txt" => $tanggal,
        "width" => $this->table_width[1],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $no_dokumen,
        "width" => $this->table_width[2],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $usr,
        "width" => $this->table_width[3],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $dari,
        "width" => $this->table_width[4],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $ke,
        "width" => $this->table_width[5],
        "border" => "RB",
      ]);
      $this->pdf->dcell([
        "txt" => $keterangan,
        "width" => $this->table_width[6],
        "border" => "RB",
        "new_line" => 1,
      ]);
    }
    if ($add_new_line) $this->pdf->Ln(5);
  }

}