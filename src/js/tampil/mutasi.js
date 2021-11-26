let jumlah_mutasi
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
        `delete_mutasi#${$(this).data("dialog")}`
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
      const mutasi1 = $(this).data("mutasi1").split("#")
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
                  <col span="1" width="250px">
                </colgroup>
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Kode QR</th>
                    <th>Merek</th>
                    <th>Tipe</th>
                    <th>Ke Pelanggan</th>
                  </tr>
                </thead>
                <tbody>
      `

      mutasi1.forEach((data_barang, i) => {
        const arr_barang = data_barang.split(";")
        const no = i + 1
        const qr_code = arr_barang[0]
        const merek = arr_barang[1]
        const tipe = arr_barang[2]
        const ke_pelanggan = arr_barang[3]
        dialog += `
          <tr class="border">
            <td>${no}</td>
            <td>${qr_code}</td>
            <td>${merek}</td>
            <td>${tipe}</td>
            <td>${ke_pelanggan}</td>
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
  const response = await fetch_request({url: `tampil/mutasi/fetch?date1=${date.date1}&date2=${date.date2}&dari=${dari}&filter=${filter}`})
  jumlah_mutasi = response || 0
  $("caption").fadeOut(400, function() {
    $(this).html(`Jumlah Mutasi: ${convert_number_tocurrency(jumlah_mutasi)}`).fadeIn()
  })
}

async function fetch_list(page = 0, date = get_date_filter(), dari = $("#dari").val(), filter = $("input#filter").val()) {
  const response = await fetch_request({url: `tampil/mutasi/fetch?date1=${date.date1}&date2=${date.date2}&dari=${dari}&filter=${filter}&page=${page}&display_per_page=${display_per_page}`})
  $("div.pagination").empty()
  tbody.empty().css("display", "none")
  for (let i = 0; i < response.length; i++) {
    const no = page * display_per_page + i + 1
    const id_mutasi = response[i].id_mutasi
    const no_mutasi = response[i].no_mutasi
    const usr = response[i].usr
    const dari_pelanggan = response[i].dari_pelanggan
    const keterangan = if_empty_then({value: response[i].keterangan})
    const tanggal_mutasi = format_date({date: response[i].tanggal_mutasi})
    const can_edit = response[i].can_edit === "1"
    const mutasi1 = response[i].mutasi1
    
    let links = []
    if (allow_edit && can_edit) links.push(`<a href="${base_url}input/mutasi?id=${id_mutasi}">Edit</a>`)
    else if (allow_edit && !can_edit) links.push(`<a href="#" class="no_edit">Edit</a>`)
    if (allow_delete && can_edit) links.push(`<a href="#" class="delete_link" data-dialog="${id_mutasi}#${no_mutasi}">Hapus</a>`)
    else if (allow_delete && !can_edit) links.push(`<a href="#" class="no_edit">Hapus</a>`) 
    links = links.join(" | ")
    if (links !== "") links = `<td>${links}</td>`

    const tr = `
      <tr class="border">
        <td>${no}</td>
        ${links}
        <td data-mutasi1="${mutasi1}" class="link_clickable">${no_mutasi}</td>
        <td>${usr}</td>
        <td>${dari_pelanggan}</td>
        <td>${keterangan}</td>
        <td>${tanggal_mutasi}</td>
      </tr>
    `
    tbody.append(tr)
  }
  tbody.fadeIn(400, function() {
    create_paginations(page, jumlah_mutasi, display_per_page, `#${date.date1};${date.date2};${filter}`)
  })
}

// utils function
async function delete_mutasi(data) {
  let status = ""
  let message = ""

  const id_mutasi = data[0]
  const alasan = data[1]
  if (alasan === "") {
    status = "error"
    message = "Silakan masukkan alasan anda."
  } else {
    const response = await fetch_request({url: "tampil/mutasi/delete", method: "post", data: {"id_mutasi": id_mutasi, "alasan": alasan}})
    if (!response) {
      status = "error"
      message = "Gagal menghapus data mutasi."
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
