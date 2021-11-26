let jumlah_asset
let tbody = $("table.tb_daftar tbody")
const allow_edit = $("#allow_edit").val() === "1"
const allow_delete = $("#allow_delete").val() === "1"
const allow_print = $("#allow_print").val() === "1"

$(document).ready(function() {
  init()

  async function init() {
    fetch_jumlah()
    fetch_list()
    init_delete_link_listener()
    init_link_pagination_listener()
    init_search_field_listener()
    init_search_icon_listener()
    init_export_button_listener()
    init_date_filter()
    init_table_style()
  }

  // event function
  function init_delete_link_listener() {
    $("body").on("click", "a.delete_link", function() {
      const serial_number = $(this).data("dialog").split("#")[1]
      const data = [
        "Masukkan Alasan:",
        serial_number,
        `delete_asset#${$(this).data("dialog")}`
      ]
      $(this).after(create_dialog("input", data)).next().slideDown(400, () => $(`#${serial_number}`).focus())
    })
  }

  function init_link_pagination_listener() {
    $("body").on("click", "a.link_pagination", function() {
      const page = parseInt($(this).data("pagination").split("#")[0])
      const data = $(this).data("pagination").split("#")[1].split(";") 
      const date = { date1: data[0], date2: data[1] }
      const sta = data[2]
      const filter = data[3]
      fetch_list(page, date, sta, filter)
    })
  }

  function init_search_icon_listener() {
    $("span.search_icon").on("click", function() {
      fetch_jumlah()
      fetch_list()
    })
  }

  function init_search_field_listener() {
    $("input#filter").on("keyup", function(e) {
      if (e.keyCode === 13) {
        fetch_jumlah()
        fetch_list()
      }
    })
  }

  function init_export_button_listener() {
    $("#export_data").on("click", function() {
      window.open(`${base_url}daftar/asset/export_to_excel`)
    })
  }
})

// fetch function
async function fetch_jumlah() {
  const date = get_date_filter() 
  const status = $("select#sta").val()
  const filter = $("input#filter").val()
  const response = await fetch_request({url: `daftar/asset/fetch?date1=${date.date1}&date2=${date.date2}&sta=${status}&filter=${filter}`})
  jumlah_asset = response || 0
  $("caption").fadeOut(400, function() {
    $(this).html(`Jumlah Asset: ${convert_number_tocurrency(jumlah_asset)}`).fadeIn()
  })
}

async function fetch_list(page = 0, date = get_date_filter(), sta = $("select#sta").val(), filter = $("input#filter").val()) {
  const response = await fetch_request({url: `daftar/asset/fetch?date1=${date.date1}&date2=${date.date2}&sta=${sta}&filter=${filter}&page=${page}&display_per_page=${display_per_page}`})
  $("div.pagination").empty()
  tbody.empty().css("display", "none")
  for (let i = 0; i < response.length; i++) {
    const no = page * display_per_page + i + 1
    const id_asset = response[i].id_asset
    const nama_barang = response[i].nama_barang
    const qr_code = response[i].qr_code
    const serial_number = if_empty_then({value: response[i].serial_number})
    const tanggal_akuisisi_asset = format_date({date: response[i].tanggal_akuisisi_asset})
    const no_surat_kontrak = if_empty_then({value: response[i].no_surat_kontrak})
    const tanggal_berakhir_kontrak = format_date({date: response[i].tanggal_berakhir_kontrak})
    const nama_kepemilikan = response[i].nama_kepemilikan
    const keterangan = if_empty_then({value: response[i].keterangan})
    const lokasi = response[i].nama_gudang || response[i].nama_pelanggan
    let sta = response[i].sta
    if (sta === "1") sta = "Rusak" 
    if (sta === "2") sta = "Siap Pakai"
    
    let links = []
    if (allow_edit) links.push(`<a href="${base_url}master/asset?id=${id_asset}">Edit</a>`)
    if (allow_delete && response[i].nama_pelanggan === null) links.push(`<a href="#" class="delete_link" data-dialog="${id_asset}#${serial_number}">Hapus</a>`) 
    else if (allow_delete && response[i].nama_pelanggan !== null) links.push(`<a href="#" class="no_edit">Hapus</a>`)
    if (allow_print) links.push(`<a href="${base_url}laporan/asset/print?fb=id&id=${id_asset}" target="_blank">History</a>`)
    links = links.join(" | ")
    if (links !== "") links = `<td>${links}</td>`

    const tr = `
      <tr class="border">
        <td>${no}</td>
        ${links}
        <td>${nama_barang}</td>
        <td>${qr_code}</td>
        <td>${serial_number}</td>
        <td>${tanggal_akuisisi_asset}</td>
        <td>${no_surat_kontrak}</td>
        <td>${tanggal_berakhir_kontrak}</td>
        <td>${nama_kepemilikan}</td>
        <td>${keterangan}</td>
        <td>${lokasi}</td>
        <td>${sta}</td>
      </tr>
    `
    tbody.append(tr)
  }
  tbody.fadeIn(400, function() {
    create_paginations(page, jumlah_asset, display_per_page, `#${date.date1};${date.date2};${sta};${filter}`)
  })
}

// utils function
async function delete_asset(data) {
  let status = ""
  let message = ""

  const id_asset = data[0]
  const alasan = data[1]
  if (alasan === "") {
    status = "error"
    message = "Silakan isi alasan anda."  
  } else {
    const response = await fetch_request({url: "daftar/asset/delete", method: "post", data: {"id_asset": id_asset, "alasan": alasan}})
    if (!response) {
      status = "error"
      message = "Gagal menghapus data asset."
    } else {
      status = response.status
      message = response.message
    }
  }
  $("body").append(create_dialog("simple", [status, message]))
  $("div.simple_dialog").slideDown()
  if (status === "success") {
    await fetch_jumlah()
    await fetch_list()
  }
}

function init_table_style() {
  var data = {
    "template_columns": "1fr 1fr",
    "padding_left": "130",
  }
  get_responsive_style("table", "table.tb_daftar", data)  
}
