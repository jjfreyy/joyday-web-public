let jumlah_pelanggan
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
      const kode_pelanggan = $(this).data("dialog").split("#")[1]
      const data = [
        "Masukkan Alasan:",
        kode_pelanggan,
        `delete_pelanggan#${$(this).data("dialog")}`
      ]
      $(this).after(create_dialog("input", data)).next().slideDown(400, () => $(`#${kode_pelanggan}`).focus())
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
  const response = await fetch_request({url: `daftar/pelanggan/fetch?filter=${filter}`})
  jumlah_pelanggan = response || 0
  $("caption").fadeOut(400, function() {
    $(this).html(`Jumlah Pelanggan: ${convert_number_tocurrency(jumlah_pelanggan)}`).fadeIn()
  })
}

async function fetch_list(page = 0, filter = $("input.search_field").val()) {
  const response = await fetch_request({url: `daftar/pelanggan/fetch?filter=${filter}&page=${page}&display_per_page=${display_per_page}`})
  $("div.pagination").empty()
  tbody.empty().css("display", "none")
  for (let i = 0; i < response.length; i++) {
    const no = page * display_per_page + i + 1
    
    const id_pelanggan = response[i].id_pelanggan
    const nama_level = response[i].nama_level
    const nama_agen = if_empty_then({value: response[i].nama_agen})
    const kode_pelanggan = response[i].kode_pelanggan
    const nama_pelanggan = response[i].nama_pelanggan
    
    const no_identitas = if_empty_then({value: response[i].no_identitas})
    const no_hp1 = if_empty_then({value: response[i].no_hp1})
    const no_hp2 = if_empty_then({value: response[i].no_hp2})
    const email = if_empty_then({value: response[i].email})
    const nama_propinsi = if_empty_then({value: response[i].nama_propinsi})
    
    const nama_kabupaten = if_empty_then({value: response[i].nama_kabupaten})
    const nama_kecamatan = if_empty_then({value: response[i].nama_kecamatan})
    const nama_kelurahan = if_empty_then({value: response[i].nama_kelurahan})
    const alamat = if_empty_then({value: response[i].alamat})
    const kode_pos = if_empty_then({value: response[i].kode_pos})
    
    const keterangan = if_empty_then({value: response[i].keterangan})
    const daya_listrik = if_empty_then({value: response[i].daya_listrik})
    const latitude = if_empty_then({value: response[i].latitude})
    const longitude = if_empty_then({value: response[i].longitude})
    const nama_kerabat = if_empty_then({value: response[i].nama_kerabat})
    
    const no_identitas_kerabat = if_empty_then({value: response[i].no_identitas_kerabat})
    const no_hp_kerabat = if_empty_then({value: response[i].no_hp_kerabat})
    const alamat_kerabat = if_empty_then({value: response[i].alamat_kerabat})
    const hubungan = if_empty_then({value: response[i].hubungan})

    let links = []
    if (allow_edit) links.push(`<a href="${base_url}master/pelanggan?id=${id_pelanggan}">Edit</a>`)
    if (allow_delete) links.push(`<a href="#" class="delete_link" data-dialog="${id_pelanggan}#${kode_pelanggan}">Hapus</a>`) 
    links = links.join(" | ")
    if (links !== "") links = `<td>${links}</td>`

    const tr = `
      <tr class="border">
        <td>${no}</td>
        ${links}

        <td>${nama_level}</td>
        <td>${nama_agen}</td>
        <td>${kode_pelanggan}</td>
        <td>${nama_pelanggan}</td>
        <td>${no_identitas}</td>
        
        <td>${no_hp1}</td>
        <td>${no_hp2}</td>
        <td>${email}</td>
        <td>${nama_propinsi}</td>
        
        <td>${nama_kabupaten}</td>
        <td>${nama_kecamatan}</td>
        <td>${nama_kelurahan}</td>
        <td>${alamat}</td>
        <td>${kode_pos}</td>

        <td>${keterangan}</td>
        <td>${daya_listrik}</td>
        <td>${latitude}</td>
        <td>${longitude}</td>
        <td>${nama_kerabat}</td>

        <td>${no_identitas_kerabat}</td>
        <td>${no_hp_kerabat}</td>
        <td>${alamat_kerabat}</td>
        <td>${hubungan}</td>
      </tr>
    `
    tbody.append(tr)
  }
  tbody.fadeIn(400, function() {
    create_paginations(page, jumlah_pelanggan, display_per_page, `#${filter}`)
  })
}

// utils function
async function delete_pelanggan(data) {
  let status = ""
  let message = ""

  const id_pelanggan = data[0]
  const alasan = data[1]
  if (alasan === "") {
    status = "error"
    message = "Silakan isi alasan anda."
  } else {
    const response = await fetch_request({url: "daftar/pelanggan/delete", method: "post", data: {"id_pelanggan": id_pelanggan, "alasan": alasan}})
    if (!response) {
      status = "error"
      message = "Gagal menghapus data pelanggan."
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
