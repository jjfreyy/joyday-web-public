let jumlah_barang_keluar
let tbody = $("table.tb_main tbody")
const allow_edit = $("#allow_edit").val() === "1"
const allow_delete = $("#allow_delete").val() === "1"
const allow_print = $("#allow_print").val() === "1"

$(document).ready(function() {
  init()

  async function init() {
    fetch_jumlah()
    fetch_list()
    init_delete_link_listener()
    init_link_clickable_listener()
    init_link_pagination_listener()
    init_filter_listener()
    init_search_icon_listener()
    init_date_filter()
    init_table_style()
  }

  // event function
  function init_delete_link_listener() {
    $("body").on("click", "a.delete_link", function() {
      const no_po = $(this).data("dialog").split("#")[1]
      const data = [
        "Masukkan Alasan:",
        no_po,
        `delete_barang_keluar#${$(this).data("dialog")}`
      ]
      $(this).after(create_dialog("input", data)).next().slideDown(400, () => $(`#${no_po}`).focus())
    })
  }

  function init_link_pagination_listener() {
    $("body").on("click", "a.link_pagination", function() {
      const page = parseInt($(this).data("pagination").split("#")[0]) 
      const data = $(this).data("pagination").split("#")[1].split(";")
      const date = { date1: data[0], date2: data[1] }
      const filter = data[2]
      fetch_list(page, date, filter)
    })
  }

  function init_link_clickable_listener() {
    prepare_close_btn_dialog()
    $("body").on("click", "td.link_clickable", function() {
      const barang_keluar1 = $(this).data("barang_keluar1").split("#")
      $("div.dialog_background").remove()
      let dialog = `
        <div class="dialog_background">
          <div class="dialog" style="display:none;">
            <div class="dialog_header">
              <span class='dialog_close_btn' title='Tutup Dialog'></span>
            </div>
            <div class="dialog_body">
              <table class="tb_daftar tb_dialog">
                <colgroup>
                  <col span="1" width="50px">
                  <col span="1" width="150px">
                  <col span="1" width="100px">
                  <col span="1" width="100px">
                </colgroup>
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Kode QR</th>
                    <th>Merek</th>
                    <th>Tipe</th>
                  </tr>
                </thead>
                <tbody>
      `

      barang_keluar1.forEach((data_barang, i) => {
        const arr_barang = data_barang.split(";")
        const no = i + 1
        const qr_code = arr_barang[0]
        const merek = arr_barang[1]
        const tipe = arr_barang[2]
        dialog += `
          <tr class="border">
            <td>${no}</td>
            <td>${qr_code}</td>
            <td>${merek}</td>
            <td>${tipe}</td>
          </tr>
        `
      })

      dialog += `</tbody></table></div></div></div>`
      $("body").append(dialog)
      $("div.dialog").slideDown()
    })
  }

  function init_search_icon_listener() {
    $("span.search_icon").on("click", function() {
      fetch_jumlah()
      fetch_list()
    })
  }

  function init_filter_listener() {
    $("input#filter").on("keyup", function(e) {
      if (e.keyCode === 13) {
        fetch_jumlah()
        fetch_list()
      }
    })
  }
  
})

// fetch function
async function fetch_jumlah() {
  const date = get_date_filter()
  const filter = $("input#filter").val()
  const dari = $("#dari").val()
  const response = await fetch_request({url: `tampil/barang_keluar/fetch?date1=${date.date1}&date2=${date.date2}&dari=${dari}&filter=${filter}`})
  jumlah_barang_keluar = response || 0
  $("caption").fadeOut(400, function() {
    $(this).html(`Jumlah Barang Keluar: ${convert_number_tocurrency(jumlah_barang_keluar)}`).fadeIn()
  })
}

async function fetch_list(page = 0, date = get_date_filter(), dari = $("#dari").val(), filter = $("input#filter").val()) {
  const response = await fetch_request({url: `tampil/barang_keluar/fetch?date1=${date.date1}&date2=${date.date2}&dari=${dari}&filter=${filter}&page=${page}&display_per_page=${display_per_page}`})
  $("div.pagination").empty()
  tbody.empty().css("display", "none")
  for (let i = 0; i < response.length; i++) {
    const no = page * display_per_page + i + 1
    const id_barang_keluar = response[i].id_barang_keluar
    const no_keluar = response[i].no_keluar
    const pengurus = response[i].pengurus
    const gudang = response[i].gudang
    const keterangan = if_empty_then({value: response[i].keterangan})
    const tanggal_keluar = format_date({date: response[i].tanggal_keluar})
    const can_edit = response[i].can_edit
    const barang_keluar1 = response[i].barang_keluar1
    
    let links = []
    if (allow_edit && can_edit === "1") links.push(`<a href="${base_url}input/barang_keluar?id=${id_barang_keluar}">Edit</a>`)
    else if (allow_edit && can_edit === "0") links.push(`<a href="#" class="no_edit">Edit</a>`)
    if (allow_delete && can_edit === "1") links.push(`<a href="#" class="delete_link" data-dialog="${id_barang_keluar}#${no_keluar}">Hapus</a>`)
    else if (allow_delete && can_edit === "0") links.push(`<a href="#" class="no_edit">Hapus</a>`)
    if (allow_print) links.push(`<a target="_blank" href="${base_url}tampil/barang_keluar/print?id=${id_barang_keluar}">Print</a>`)
    links = links.join(" | ")
    if (links !== "") links = `<td>${links}</td>`

    const tr = `
      <tr class="border">
        <td>${no}</td>
        ${links}
        <td data-barang_keluar1="${barang_keluar1}" class="link_clickable">${no_keluar}</td>
        <td>${pengurus}</td>
        <td>${gudang}</td>
        <td>${keterangan}</td>
        <td>${tanggal_keluar}</td>
      </tr>
    `
    tbody.append(tr)
  }
  tbody.fadeIn(400, function() {
    create_paginations(page, jumlah_barang_keluar, display_per_page, `#${date.date1};${date.date2};${filter}`)
  })
}

// utils function
async function delete_barang_keluar(data) {
  let status = ""
  let message = ""

  const id_barang_keluar = data[0]
  const alasan = data[1]
  if (alasan === "") {
    status = "error"
    message = "Silakan masukkan alasan anda."
  } else {
    const response = await fetch_request({url: "tampil/barang_keluar/delete", method: "post", data: {"id_barang_keluar": id_barang_keluar, "alasan": alasan}})
    if (!response) {
      status = "error"
      message = "Gagal menghapus data barang keluar."
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
  get_responsive_style("table", "table.tb_main", {"template_columns": "1fr 1fr", "padding_left": "125"})
  get_responsive_style("table", "table.tb_dialog", {"padding_left": "115"})
}
