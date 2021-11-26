<?php
namespace App\Controllers\Tampil;

use App\Controllers\BaseController;
use App\Libraries\tcpdf\PDF;

class Barang_Keluar extends BaseController {
    
    function index() {
      if (!\check_privileges("BK-V")) return redirect()->route("404");
      $data = [
        "allow_edit" => \check_privileges("BK-E"), 
        "allow_delete" => \check_privileges("BK-D"), 
        "allow_print" => \check_privileges("BK-SJ"),
        "period" => json_decode(\fetch_get_request("tampil/barang_keluar/fetch", ["fetch_barang_keluar" => true, "type" => "period"])->getBody()),
      ];
      echo view("tampil/v_barang_keluar", $data);
    }

    function delete() {
      if ($this->request->isAJAX()) {
        try {
          $id_barang_keluar = sanitize($this->request->getJSON()->id_barang_keluar);
          $alasan = sanitize($this->request->getJSON()->alasan);
          $response = \fetch_post_request("tampil/barang_keluar/delete", ["delete_barang_keluar" => true, "id_user" => session("joyday")["id_user"], "id_barang_keluar" => $id_barang_keluar, "alasan" => $alasan]);
          if ($response->getStatusCode() === 500) throw new \Exception();
          \send_response($response->getStatusCode(), $response->getBody());
        } catch (\Exception $e) {
          \send_500_response(\format_exception($e));
        }
      }
      return redirect()->route("404");
    }

    function fetch() {
      if ($this->request->isAJAX()) {
        try {
          $response = \fetch_get_request("tampil/barang_keluar/fetch", [
            "fetch_barang_keluar" => true,
            "type" => "barang_keluar",
            "id_user" => session("joyday")["id_user"],
            "date1" => get_get("date1"),
            "date2" => get_get("date2"),
            "filter" => get_get("filter"),
            "page" => get_get("page"),
            "display_per_page" => get_get("display_per_page"),
          ]);
          if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
          \send_response($response->getBody());
        } catch (\Exception $e) {
          \send_500_response(\format_exception($e));
        }
      }
      return redirect()->route("404");
    }

    private $data;
    private $pdf;
    private $table_width;

    function print() {
      if (!isset($_GET["id"])) return redirect()->route("404");
      try {
        $response = \fetch_get_request("tampil/barang_keluar/fetch", [
          "fetch_barang_keluar" => true, 
          "type" => "cetak_surat_jalan",
          "id_user" => session("joyday")["id_user"], 
          "id_barang_keluar" => get_get("id"),
        ]);
        $this->data = json_decode($response->getBody());
        if ($response->getStatusCode() !== 200) {
          $message = $this->data->message;
          echo "<script>alert('$message'); window.close()</script>";
        } else {
          $this->pdf = new PDF([
            "show_header" => false,
            "margin" => [1, 5],
            "font_style" => "B",
            "font_size" => 15,
          ]);
          $this->_build_header();
          $this->_build_table_header();  
          $this->_build_table_body();
          $this->_build_table_footer();
          $this->pdf->Output("SuratJalan_" .date("dmyHis"). ".pdf", "I");
        }
        exit();
      } catch (\Exception $e) {
        echo "<script>alert('Data tidak dapat ditemukan.'); window.close()</script>";
        \send_500_response(\format_exception($e));
      }
    }

    private function _build_header() {
      $company_info = \get_company_info();
      $x = $this->pdf->get_margin()[0];

      if (\check_file("logo.png")) {
        $this->pdf->Image("logo.png", 0, 0, 20, 20);
        $x = 20;
      }

      $this->pdf->setX($x);
      $this->pdf->dcell([
        "txt" => $company_info["company"],
        "width" => [$x, .6],
      ]);
      $this->pdf->dcell([
        "txt" => "SURAT JALAN FREEZER:",
        "width" => [$x, .4],
        "new_line" => 1,
        "border" => "L",
      ]);

      $this->pdf->setX($x);
      $this->pdf->set_font(["font_style" => "", "font_size" => 9]);
      $this->pdf->dcell([
        "txt" => $company_info["address"],
        "width" => [$x, .6],
      ]);
      $this->pdf->dcell([
        "txt" => "No. Sj",
        "width" => [$x, .1],
        "border" => "L",
      ]);
      $this->pdf->dcell([
        "txt" => ":",
        "width" => [$x, .01],
        "align" => "R",
      ]);
      $this->pdf->dcell([
        "txt" => $this->data[0]->no_keluar,
        "width" => [$x, .29],
        "new_line" => 1,
      ]);

      $this->pdf->setX($x);
      $this->pdf->dcell([
        "txt" => "Telp. $company_info[phone]",
        "width" => [$x, .6],
      ]);
      $this->pdf->dcell([
        "txt" => "Kepada",
        "width" => [$x, .1],
        "border" => "L",
      ]);
      $this->pdf->dcell([
        "txt" => ":",
        "width" => [$x, .01],
        "align" => "R",
      ]);
      $this->pdf->dcell([
        "width" => [$x, .29],
        "new_line" => 1,
      ]);

      $this->pdf->setX($x);
      $this->pdf->dcell([
        "txt" => "",
        "width" => [$x, .6],
      ]);
      $this->pdf->dcell([
        "txt" => "Tanggal",
        "width" => [$x, .1],
        "border" => "L",
      ]);
      $this->pdf->dcell([
        "txt" => ":",
        "width" => [$x, .01],
        "align" => "R",
      ]);
      $this->pdf->dcell([
        "width" => [$x, .29],
        "new_line" => 1,
      ]);

      $this->pdf->setX($x);
      $this->pdf->dcell([
        "txt" => "",
        "width" => [$x, .6],
      ]);
      $this->pdf->dcell([
        "txt" => "No. Polisi",
        "width" => [$x, .1],
        "border" => "L",
      ]);
      $this->pdf->dcell([
        "txt" => ":",
        "width" => [$x, .01],
        "align" => "R",
      ]);
      $this->pdf->dcell([
        "width" => [$x, .29],
        "new_line" => 1,
      ]);
      $this->pdf->Ln(1);
      $this->pdf->draw_line(.8);
      $this->pdf->Ln(1.5);
    }

    private function _build_table_header() {
      $this->table_width = [.05, .25, .25, .15, .15, .15];
      $this->pdf->set_font_style("b");
      $this->pdf->dcell([
        "txt" => "No.",
        "width" => $this->table_width[0],
        "height" => $this->pdf->get_cell_height() * 2,
        "align" => "C",
        "border" => "TRB",
        "fill" => true,
      ]);
      $this->pdf->dcell([
        "txt" => "QR FREEZER",
        "width" => $this->table_width[1],
        "height" => $this->pdf->get_cell_height() * 2,
        "align" => "C",
        "border" => "TRBL",
        "fill" => "true",
      ]);
      $this->pdf->dcell([
        "txt" => "SERIAL NUMBER",
        "width" => $this->table_width[2],
        "height" => $this->pdf->get_cell_height() * 2,
        "align" => "C",
        "border" => "TRBL",
        "fill" => "true",
      ]);
      $this->pdf->dcell([
        "txt" => "MERK",
        "width" => $this->table_width[3],
        "height" => $this->pdf->get_cell_height() * 2,
        "align" => "C",
        "border" => "TRBL",
        "fill" => "true",
      ]);
      $this->pdf->dcell([
        "txt" => "TYPE",
        "width" => $this->table_width[4],
        "height" => $this->pdf->get_cell_height() * 2,
        "align" => "C",
        "border" => "TRBL",
        "fill" => "true",
      ]);
      $this->pdf->dcell([
        "txt" => "KET",
        "width" => $this->table_width[5],
        "height" => $this->pdf->get_cell_height() * 2,
        "align" => "C",
        "border" => "TBL",
        "new_line" => 1,
        "fill" => "true",
      ]);
      $this->pdf->set_font_style("");
    }

    private function _build_table_body() {
      $data = array_slice($this->data, 1);
      $first_page = true;
      $j = 0;
      $len = count($data);
      for ($i = 0; $i < $len; $i++) {
        $no = $i + 1;
        $qr_code = $data[$i]->qr_code;
        $serial_number = if_empty_then($data[$i]->serial_number);
        $nama_barang = $data[$i]->nama_barang;
        $tipe = $data[$i]->tipe;
        $ukuran = $data[$i]->ukuran;

        $this->pdf->dcell([
          "txt" => $no,
          "width" => $this->table_width[0],
          "align" => "R",
          "border" => "RB",
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
          "txt" => $nama_barang,
          "width" => $this->table_width[3],
          "border" => "RB",
        ]);
        $this->pdf->dcell([
          "txt" => $tipe,
          "width" => $this->table_width[4],
          "border" => "RB",
        ]);
        $this->pdf->dcell([
          "width" => $this->table_width[5],
          "border" => "B",
          "new_line" => 1,
        ]);

        $j++;
        if ($i < ($len - 1) && $j === ($first_page ? 40 : 45)) {
          $j = 0;
          $first_page = false;
          $this->pdf->AddPage();
          $this->_build_table_header();
        }
      }

      if ($j < ($first_page ? 40 : 45)) {
        for (; $j < ($first_page ? 40 : 45); $j++) {
          $this->pdf->dcell([
            "width" => $this->table_width[0],
            "align" => "R",
            "border" => "RB",
          ]);
          $this->pdf->dcell([
            "width" => $this->table_width[1],
            "border" => "RB",
          ]);
          $this->pdf->dcell([
            "width" => $this->table_width[2],
            "border" => "RB",
          ]);
          $this->pdf->dcell([
            "width" => $this->table_width[3],
            "border" => "RB",
          ]);
          $this->pdf->dcell([
            "width" => $this->table_width[4],
            "border" => "RB",
          ]);
          $this->pdf->dcell([
            "width" => $this->table_width[5],
            "border" => "B",
            "new_line" => 1,
          ]);
        }
      }
    }

    private function _build_table_footer() {
      $footer_width = [.2, .2, .2, .2, .2];
      $this->pdf->setX(0);
      $this->pdf->setY($this->pdf->GetPageHeight() * .87);
      $this->pdf->Ln(5);
      $this->pdf->dcell([
        "txt" => "Admin",
        "align" => "C",
        "width" => $footer_width[0],
      ]);
      $this->pdf->dcell([
        "txt" => "Staff Gudang",
        "align" => "C",
        "width" => $footer_width[1],
      ]);
      $this->pdf->dcell([
        "txt" => "Kepala Gudang",
        "align" => "C",
        "width" => $footer_width[2],
      ]);
      $this->pdf->dcell([
        "txt" => "Driver",
        "align" => "C",
        "width" => $footer_width[3],
      ]);
      $this->pdf->dcell([
        "txt" => "Customer",
        "align" => "C",
        "width" => $footer_width[4],
        "new_line" => 1,
      ]);
      $this->pdf->Ln(10);
      $this->pdf->set_font_family("times");
      $this->pdf->dcell([
        "txt" => "(______________________)",
        "width" => $footer_width[0],
        "align" => "C",
      ]);
      $this->pdf->dcell([
        "txt" => "(______________________)",
        "width" => $footer_width[1],
        "align" => "C",
      ]);
      $this->pdf->dcell([
        "txt" => "(______________________)",
        "width" => $footer_width[2],
        "align" => "C",
      ]);
      $this->pdf->dcell([
        "txt" => "(______________________)",
        "width" => $footer_width[3],
        "align" => "C",
      ]);
      $this->pdf->dcell([
        "txt" => "(______________________)",
        "width" => $footer_width[4],
        "align" => "C",
        "new_line" => 1,
      ]);
    }

}