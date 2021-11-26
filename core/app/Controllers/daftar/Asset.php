<?php
namespace App\Controllers\Daftar;

use App\Controllers\BaseController;
require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class Asset extends BaseController {
    function index() {
      if (!\check_privileges("ASS-V")) return redirect()->route("404");
      $data = [
        "allow_edit" => \check_privileges("ASS-E"), 
        "allow_delete" => \check_privileges("ASS-D"),
        "allow_print" => \check_privileges("ASS-R"),
        "period" => json_decode(\fetch_get_request("daftar/asset/fetch", ["fetch_asset" => true, "type" => "period"])->getBody()), 
      ];
      echo view("daftar/v_asset", $data);
    }

    function delete() {
      if ($this->request->isAJAX()) {
        try {
          $id_asset = sanitize($this->request->getJSON()->id_asset);
          $alasan = sanitize($this->request->getJSON()->alasan);
          $response = \fetch_post_request("daftar/asset/delete", ["delete_asset" => true, "id_user" => session("joyday")["id_user"], "id_asset" => $id_asset, "alasan" => $alasan]);
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
          $response = \fetch_get_request("daftar/asset/fetch", [
            "fetch_asset" => true,
            "type" => "asset",
            "id_user" => session("joyday")["id_user"],
            "date1" => get_get("date1"),
            "date2" => get_get("date2"),
            "sta" => get_get("sta"),
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

    function export_to_excel() {
      try {
        $response = \fetch_get_request("daftar/asset/fetch", ["fetch_asset" => true, "id_user" => session("joyday")["id_user"], "type" => "export_excel"]);
        if ($response->getStatusCode() !== 200) {
          // \js_alert(json_decode($response->getBody())->message);
          goto end;
        } 

        $data = json_decode($response->getBody());
        if (empty($data)) {
          // \js_alert("Data tidak dapat ditemukan.");
          goto end;
        }
        
        $curdatetime = date("ymdHis");
        $company_info = \get_company_info();
        Cell::setValueBinder(new AdvancedValueBinder());
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Data Asset");
        $header = [
          "No", "Tanggal Akuisisi Asset", "QR Code", "Serial Number", "Tipe", 
          "Merek", "Keterangan", "Ownership", "No. Surat Kontrak", "Penanggung Jawab", 
          "Nama Toko", "Alamat", "Bujur", "Lintang",
        ];
        $sheet->fromArray(
          $header,
          null,
          "A1"
        );
  
        for ($i = 0; $i < count($data); $i++) {
          $sheet_no = $i+2;
          $no = $i+1;
          $tanggal_akuisisi_asset = $data[$i]->tanggal_akuisisi_asset;
          $qr_code = $data[$i]->qr_code;
          $serial_number = $data[$i]->serial_number;
          $nama_tipe = $data[$i]->nama_tipe;
          $nama_brand = $data[$i]->nama_brand;
          $keterangan = $data[$i]->keterangan;
          $nama_kepemilikan = $data[$i]->nama_kepemilikan;
          $no_surat_kontrak = $data[$i]->no_surat_kontrak;
          $penanggung_jawab = is_empty($data[$i]->nama_agen) ? $company_info["company"] : $data[$i]->nama_agen;
          $nama_toko = is_empty($data[$i]->nama_pelanggan) ? $data[$i]->nama_gudang : $data[$i]->nama_pelanggan;
          $alamat = is_empty($data[$i]->alamat) ? $company_info["address"] : $data[$i]->alamat;
          $longitude = is_empty($data[$i]->longitude) ? $company_info["longitude"] : $data[$i]->longitude;
          $latitude = is_empty($data[$i]->latitude) ? $company_info["latitude"] : $data[$i]->latitude;
  
          $sheet->setCellValue("A$sheet_no", $no);
          $sheet->setCellValue("B$sheet_no", $tanggal_akuisisi_asset);
          $sheet->setCellValueExplicit("C$sheet_no", $qr_code, DataType::TYPE_STRING);
          $sheet->setCellValueExplicit("D$sheet_no", $serial_number, DataType::TYPE_STRING);
          $sheet->setCellValue("E$sheet_no", $nama_tipe);
          $sheet->setCellValue("F$sheet_no", $nama_brand);
          $sheet->setCellValue("G$sheet_no", $keterangan);
          $sheet->setCellValue("H$sheet_no", $nama_kepemilikan);
          $sheet->setCellValue("I$sheet_no", $no_surat_kontrak);
          $sheet->setCellValue("J$sheet_no", $penanggung_jawab);
          $sheet->setCellValue("K$sheet_no", $nama_toko);
          $sheet->setCellValue("L$sheet_no", $alamat);
          $sheet->setCellValueExplicit("M$sheet_no", $longitude, DataType::TYPE_STRING);
          $sheet->setCellValueExplicit("N$sheet_no", $latitude, DataType::TYPE_STRING);
        }
  
        $sheet->setAutoFilter(
          $sheet->calculateWorksheetDimension()
        );
        $sheet->setSelectedCell("A1");
        
        $writes = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=data_asset$curdatetime.xlsx");
        $writes->save("php://output");
      } catch (\Exception $e) {
        // js_alert("Terjadi kesalahan internal server");
      }

      end:
      return;
      // \js_close();
    }

}
