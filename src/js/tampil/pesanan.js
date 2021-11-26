let jumlah_pesanan
let tbody = $("table.tb_main tbody")
const allow_edit = $("#allow_edit").val() === "1"
const allow_delete = $("#allow_delete").val() === "1"

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
        `delete_pesanan#${$(this).data("dialog")}`
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
      const data_qty = $(this).data("qty").split(";")
      const pesanan1 = $(this).data("pesanan1").split("#")
      $("div.dialog_background").remove()
      let dialog = `
        <div class="dialog_background">
          <div class="dialog" style="display:none;">
            <div class="dialog_header">
              <span class='dialog_close_btn' title='Tutup Dialog'></span>
            </div>
            <div class="dialog_body">
              <h2 style="">Qty Pesanan: ${data_qty[0]}</h2>
              <h2 style="">Qty Masuk: ${data_qty[1]}</h2>
              <table class="tb_daftar tb_dialog">
                <colgroup>
                  <col span="1" width="50px">
                  <col span="1" width="100px">
                  <col span="1" width="100px">
                  <col span="1" width="100px">
                  <col span="1" width="100px">
                </colgroup>
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Merek</th>
                    <th>Tipe</th>
                    <th>Qty Pesan</th>
                    <th>Qty Masuk</th>
                  </tr>
                </thead>
                <tbody>
      `

      pesanan1.forEach((data_barang, i) => {
        const arr_barang = data_barang.split(";")
        const no = i + 1
        const merek = arr_barang[0]
        const tipe = arr_barang[1]
        const qty_pesan = arr_barang[2]
        const qty_masuk = arr_barang[3]
        dialog += `
          <tr class="border">
            <td>${no}</td>
            <td>${merek}</td>
            <td>${tipe}</td>
            <td>${qty_pesan}</td>
            <td>${qty_masuk}</td>
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
  const response = await fetch_request({url: `tampil/pesanan/fetch?date1=${date.date1}&date2=${date.date2}&filter=${filter}`})
  jumlah_pesanan = response || 0
  $("caption").fadeOut(400, function() {
    $(this).html(`Jumlah pesanan: ${convert_number_tocurrency(jumlah_pesanan)}`).fadeIn()
  })
}

async function fetch_list(page = 0, date = get_date_filter(), filter = $("input#filter").val()) {
  const response = await fetch_request({url: `tampil/pesanan/fetch?date1=${date.date1}&date2=${date.date2}&filter=${filter}&page=${page}&display_per_page=${display_per_page}`})
  $("div.pagination").empty()
  tbody.empty().css("display", "none")
  for (let i = 0; i < response.length; i++) {
    const no = page * display_per_page + i + 1
    const id_pesanan = response[i].id_pesanan
    const no_po = response[i].no_po
    const distributor = response[i].distributor
    const pemesan = response[i].pemesan
    const keterangan = if_empty_then({value: response[i].keterangan})
    const tanggal_pesan = format_date({date: response[i].tanggal_pesan})
    const qty_pesan = parseInt(response[i].qty_pesan)
    const qty_masuk = parseInt(response[i].qty_masuk)
    let input_sta = "&#215"
    if (qty_pesan - qty_masuk > 0 && qty_masuk > 0) input_sta = "&frac12"
    else if (qty_pesan - qty_masuk === 0) input_sta = "&#10003"
    const pesanan1 = response[i].pesanan1

    let links = []
    if (allow_edit && qty_masuk === 0) links.push(`<a href="${base_url}input/pesanan?id=${id_pesanan}">Edit</a>`)
    else if (allow_edit && qty_masuk > 0) links.push(`<a href="#" class="no_edit">Edit</a>`)
    if (allow_delete && qty_masuk === 0) links.push(`<a href="#" class="delete_link" data-dialog="${id_pesanan}#${no_po}">Hapus</a>`) 
    else if (allow_edit && qty_masuk > 0) links.push(`<a href="#" class="no_edit">Hapus</a>`)
    links = links.join(" | ")
    if (links !== "") links = `<td>${links}</td>`

    const tr = `
      <tr class="border">
        <td>${no}</td>
        ${links}
        <td data-qty="${qty_pesan};${qty_masuk}" data-pesanan1="${pesanan1}" class="link_clickable">${no_po}</td>
        <td>${distributor}</td>
        <td>${pemesan}</td>
        <td>${keterangan}</td>
        <td>${tanggal_pesan}</td>
        <td>${input_sta}</td>
      </tr>
    `
    tbody.append(tr)
  }
  tbody.fadeIn(400, function() {
    create_paginations(page, jumlah_pesanan, display_per_page, `#${date.date1};${date.date2};${filter}`)
  })
}

// utils function
async function delete_pesanan(data) {
  let status = ""
  let message = ""

  const id_pesanan = data[0]
  const alasan = data[1]
  if (alasan === "") {
    status = "error"
    message = "Silakan masukkan alasan anda."
  } else {
    const response = await fetch_request({url: "tampil/pesanan/delete", method: "post", data: {"id_pesanan": id_pesanan, "alasan": alasan}})
    if (!response) {
      status = "error"
      message = "Gagal menghapus data pesanan."
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
