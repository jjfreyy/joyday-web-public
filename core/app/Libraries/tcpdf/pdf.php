<?php
namespace App\Libraries\tcpdf;
require_once("tcpdf.php");
use TCPDF;

class PDF extends TCPDF {
    private $cell_height;
    private $table_header_height;
    private $font_family;
    private $font_style;
    private $font_size;
    private $line_color;
    private $margin;

    function __construct($show_header = true, $show_footer = true, $orientation = "P", $size = "A4", $margin = [1,20], $auto_page_break_margin = 7, 
    $cell_height = 5, $table_header_height = 2.5, $font_family = "cid0cs", $font_style = "", $font_size = 12, $line_color = [11, 11, 11]) {
        if (is_array($show_header)) {
            $data = $show_header;
            $show_header = $data["show_header"] ?? true;
            $show_footer = $data["show_footer"] ?? $show_footer;
            $orientation = $data["orientation"] ?? $orientation;
            $size = $data["size"] ?? $size;
            $margin = $data["margin"] ?? $margin;
            $auto_page_break_margin = $data["auto_page_break_margin"] ?? $auto_page_break_margin;
            $cell_height = $data["cell_height"] ?? $cell_height;
            $table_header_height = $data["table_header_height"] ?? $table_header_height;
            $font_family = $data["font_family"] ?? $font_family;
            $font_style = $data["font_style"] ?? $font_style; 
            $font_size = $data["font_size"] ?? $font_size; 
            $line_color = $data["line_color"] ?? $line_color;
        }

        $this->cell_height = $cell_height;
        $this->table_header_height = $table_header_height;
        $this->font_family = $font_family;
        $this->font_style = $font_style;
        $this->font_size = $font_size;
        $this->line_color = $line_color;
        $this->margin = $margin;

        parent::__construct($orientation, "mm", $size);
        parent::setPrintHeader($show_header);
        parent::setPrintFooter($show_footer);
        parent::SetAutoPageBreak(true, $auto_page_break_margin);
        parent::setMargins($this->margin[0], $this->margin[1]);
        parent::SetLineStyle(["color" => $this->line_color]);
        parent::SetFillColor(191, 191, 191);
        parent::SetFont($this->font_family, $this->font_style, $this->font_size);
        parent::AddPage();
    }

    function get_computed_width($width) {
        if (is_array($width)) {
            return ($this->GetPageWidth() - $width[0]) * $width[1];
        }
        return $this->GetPageWidth() * $width;
    }

    function draw_line($line_width = .2) {
        parent::SetLineWidth($line_width);
        parent::Line($this->margin[0], parent::GetY(), $this->GetPageWidth() + $this->margin[0], parent::GetY());
        parent::SetLineWidth(.2);
    }

    /** Override */
    function dcell($data = []) {
        $w = !isset($data["width"]) ? $this->GetPageWidth() : $this->get_computed_width($data["width"]);
        $h = $data["height"] ?? $this->get_cell_height();
        $txt = $data["txt"] ?? "";
        $border = $data["border"] ?? 0;
        $new_line = $data["new_line"] ?? 0;
        $align = $data["align"] ?? "";
        $fill = $data["fill"] ?? false;
        $link = $data["link"] ?? "";
        $stretch = $data["stretch"] ?? 0;
        $ignore_min_height = $data["ignore_min_height"] ?? false;
        $calign = $data["calign"] ?? "T";
        $valign = $data["valign"] ?? "M";
        parent::cell($w, $h, $txt, $border, $new_line, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign);
    }

    function Header() {
        $header_data = get_company_info();
        $x = $this->margin[0];
        if (check_file("logo.png")) {
            parent::Image("logo.png", 0, 0, 20, 15);
            $x = 20;
        }
        parent::SetFont($this->font_family, "B", 12);
        parent::setX($x);
        parent::cell(0, 5, $header_data["company"], 0, 1);
        parent::SetFont($this->font_family, "", 11);
        parent::setX($x);
        parent::cell(0, 5, "Alamat: " .$header_data["address"], 0, 1);
        parent::setX($x);
        parent::cell(0, 5, "Telp / Wa: " .$header_data["phone"], 0, 1);
        parent::SetLineStyle(["color" => $this->line_color]);
        parent::Ln(1);
        $this->draw_line();
    }

    function Footer() {
        parent::setY(-6);
        parent::SetLineStyle(["color" => $this->line_color]);
        $this->draw_line();
        parent::SetFont($this->font_family, "", 9);
        parent::cell($this->get_computed_width(.5), $this->cell_height, session("username"). " (" .date("d-m-Y H:i:s"). ")");
        parent::cell($this->get_computed_width(.5), $this->cell_height, "Page " .parent::GetAliasNumPage(). " / " .parent::GetAliasNbPages(), 0, 0, "R");
    }

    function GetPageWidth($pagenum = "") {
        return parent::GetPageWidth($pagenum) - $this->margin[0] * 2;
    }

    function GetPageHeight($pagenum = "") {
        return parent::GetPageHeight($pagenum) - $this->margin[1] * 2;
    }

    /** accessors and mutators */
    
    function get_cell_height() {
        return $this->cell_height;
    }
    
    function set_cell_height($cell_height) {
        $this->cell_height = $cell_height;
    }

    function set_font($font = []) {
        $this->font_family = $font["font_family"] ?? $this->font_family;
        $this->font_style = $font["font_style"] ?? $this->font_style;
        $this->font_size = $font["font_size"] ?? $this->font_size;
        parent::SetFont($this->font_family, $this->font_style, $this->font_size);
    }

    function get_font_family() {
        return $this->font_family;
    }

    function set_font_family($font_family) {
        $this->font_family = $font_family;
        $this->set_font();
    }

    function get_font_style() {
        return $this->font_style;
    }

    function set_font_style($font_style) {
        $this->font_style = $font_style;
        $this->set_font();
    }

    function get_font_size() {
        return $this->font_size();
    }

    function set_font_size($font_size) {
        $this->font_size = $font_size;
        $this->set_font();
    }

    function get_line_color() {
        return $this->line_color;
    }

    function set_line_color($line_color) {
        $this->line_color = $line_color;
        parent::SetLineStyle(["color" => $this->line_color]);
    }
    
    function get_margin() {
        return $this->margin;
    }
    
    function set_margin($margin) {
        $this->margin = $margin;
    }

    function get_table_header_height() {
        return $this->cell_height + $this->table_header_height;
    }

    function set_table_header_height($table_header_height) {
        $this->table_header_height = $table_header_height;
    }
}