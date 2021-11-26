let jumlah_barang
let tbody = $("table.tb_daftar tbody")
const allow_edit = $("#allow_edit").val() === "1"
const allow_delete = $("#allow_delete").val() === "1"

$(document).ready(function() {
  init()

  async function init() {
    fetch_jumlah()
    fetch_list()
    init_delete_link_listener()
    init_link_pagination_listener()
    init_search_field_listener()
    init_search_icon_listener()
    init_table_style()
  }

  // event function
  function init_delete_link_listener() {
    $("body").on("click", "a.delete_link", function() {
      const kode_barang = $(this).data("dialog").split("#")[1]
      const data = [
        "Masukkan Alasan:",
        kode_barang,
        `delete_barang#${$(this).data("dialog")}`
      ]
      $(this).after(create_dialog("input", data)).next().slideDown(400, () => $(`#${kode_barang}`).focus())
    })
  }

  function init_link_pagination_listener() {
    $("body").on("click", "a.link_pagination", function() {
      const page = parseInt($(this).data("pagination").split("#")[0]) 
      const filter = $(this).data("pagination").split("#")[1]
      fetch_list(page, filter)
    })
  }

  function init_search_icon_listener() {
    $("span.search_icon").on("click", function() {
      fetch_jumlah()
      fetch_list()
    })
  }

  function init_search_field_listener() {
    $("input.search_field").on("keyup", function(e) {
      if (e.keyCode === 13) {
        fetch_jumlah()
        fetch_list()
      }
    })
  }
  
})

// fetch function
async function fetch_jumlah() {
  const filter = $("input.search_field").val()
  const response = await fetch_request({url: `daftar/barang/fetch?filter=${filter}`})
  jumlah_barang = response || 0
  $("caption").fadeOut(400, function() {
    $(this).html(`Jumlah Barang: ${convert_number_tocurrency(jumlah_barang)}`).fadeIn()
  })
}

async function fetch_list(page = 0, filter = $("input.search_field").val()) {
  const response = await fetch_request({url: `daftar/barang/fetch?filter=${filter}&page=${page}&display_per_page=${display_per_page}`})
  $("div.pagination").empty()
  tbody.empty().css("display", "none")
  for (let i = 0; i < response.length; i++) {
    const no = page * display_per_page + i + 1
    const id_barang = response[i].id_barang
    const kode_barang = response[i].kode_barang
    // const nama_barang = response[i].nama_barang
    const nama_brand = if_empty_then({value: response[i].nama_brand})
    const nama_tipe = response[i].nama_tipe
    const ukuran = if_empty_then({value: response[i].ukuran})
    const keterangan = if_empty_then({value: response[i].keterangan})
    
    let links = []
    if (allow_edit) links.push(`<a href="${base_url}master/barang?id=${id_barang}">Edit</a>`)
    if (allow_delete) links.push(`<a href="#" class="delete_link" data-dialog="${id_barang}#${kode_barang}">Hapus</a>`) 
    links = links.join(" | ")
    if (links !== "") links = `<td>${links}</td>`

    const tr = `
      <tr class="border">
        <td>${no}</td>
        ${links}
        <td>${kode_barang}</td>
        <td>${nama_brand}</td>
        <td>${nama_tipe}</td>
        <td>${ukuran}</td>
        <td>${keterangan}</td>
      </tr>
    `
    tbody.append(tr)
  }
  tbody.fadeIn(400, function() {
    create_paginations(page, jumlah_barang, display_per_page, `#${filter}`)
  })
}

// utils function
async function delete_barang(data) {
  let status = ""
  let message = ""
  
  const id_barang = data[0]
  const alasan = data[1]
  if (alasan === "") {
    status = "error"
    message = "Silakan isi alasan anda."  
  } else {
    const response = await fetch_request({url: "daftar/barang/delete", method: "post", data: {"id_barang": id_barang, "alasan": alasan}})
    if (!response) {
      status = "error"
      message = "Gagal menghapus data barang."
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
    "padding_left": "125",
  }
  get_responsive_style("table", "table.tb_daftar", data)
}
