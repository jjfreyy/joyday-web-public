$(document).ready(() => {
    
  let list_asset = {}
  let list_barang = {}
  let list_gudang = {}
  let list_pelanggan = {}
  const asset_list = $("#asset_list")
  const barang_list = $("#barang_list")
  const gudang_list = $("#gudang_list")
  const pelanggan_list = $("#pelanggan_list")
  let delay_asset = delay_barang = delay_gudang = delay_pelanggan = new Date().getTime()
  let last_value_asset = last_value_barang = last_value_gudang = last_value_pelanggan = ""
  const default_tujuan_mutasi_value = $("#tujuan_mutasi").val()
  const default_sta_value = $("#sta").val()

  init()

  async function init() {
    fetch_asset($("#asset").val().toLowerCase())
    init_barang_listener()
    init_asset_listener()
    init_tujuan_mutasi_listener()
    init_gudang_listener()
    init_pelanggan_listener()
    init_sta_listener()
    init_reset_btn_listener()
  }
  
  // fetch function
  async function fetch_asset(filter = "") {
      const response = await fetch_request({url: `master/asset/fetch?type=asset&filter=${filter}`})
      list_asset[filter] = response || undefined
  }

  async function fetch_barang(filter = "") {
      const response = await fetch_request({url: `master/asset/fetch?type=barang&filter=${filter}`})
      list_barang[filter] = response || undefined
  }
  
  async function fetch_gudang(filter = "") {
    const response = await fetch_request({url: `master/asset/fetch?type=gudang&filter=${filter}`})
    list_gudang[filter] = response || undefined
  }

  async function fetch_pelanggan(filter = "") {
    const response = await fetch_request({url: `master/asset/fetch?type=pelanggan&filter=${filter}`})
    list_pelanggan[filter] = response || undefined
  }

  // event function
  function init_barang_listener() {
    const fill_barang_list = (value) => {
      barang_list.empty()
      last_value_barang = value
      if (list_barang[value] === undefined) return
      for (const barang of list_barang[value]) {
        const barang_detail = barang.barang_detail
        barang_list.append(`<option value="${barang_detail}"></option>`)
      }
    }

    $("#barang").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_barang[value] === undefined) {
        delay_barang = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_barang < fetch_request_delay) return
          await fetch_barang(value)
          fill_barang_list(value)
        }, fetch_request_delay)
      } else {
        fill_barang_list(value)
      }
    })

    $("#barang").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_barang").val("")
      setTimeout(() => {
        list_barang
        if (list_barang[last_value_barang] === undefined) return
        for (const data_barang of list_barang[last_value_barang]) {
          const barang_detail = data_barang.barang_detail 
          if (barang_detail.toLowerCase() === value) {
            const id_barang = data_barang.id_barang
            $("#id_barang").val(id_barang)
            $("#barang").val(barang_detail)
            return
          }
        }
      }, list_barang[last_value_barang] === undefined ? on_change_delay : 0)
    })
  }

  function init_asset_listener() {
    const fill_asset_list = (value) => {
      asset_list.empty()
      last_value_asset = value
      if (list_asset[value] === undefined) return
      for (const asset of list_asset[value]) {
        const qr_code = asset.qr_code
        const serial_number = asset.serial_number
        if (qr_code.toLowerCase().includes(value)) {
          asset_list.append(`<option value="${qr_code}"></option>`)
        } else if (serial_number.toLowerCase().includes(value)) {
          asset_list.append(`<option value="${serial_number}"></option>`)
        }
      }
    }

    $("#asset").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_asset[value] === undefined) {
        delay_asset = new Date().getTime()
        setTimeout(async() => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_asset < fetch_request_delay) return
          await fetch_asset(value)
          fill_asset_list(value)
        }, fetch_request_delay);
      } else {
        fill_asset_list(value)
      }
    })

    $("#asset").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_asset").val("")
      toggle("insert")
      setTimeout(() => {
        if (list_asset[last_value_asset] === undefined) return
        for (const asset of list_asset[last_value_asset]) {
          const qr_code = asset.qr_code
          if (qr_code.toLowerCase() === value) {
            const id_asset = asset.id_asset
            const serial_number = asset.serial_number
            const tanggal_akuisisi_asset = asset.tanggal_akuisisi_asset
            const no_surat_kontrak = asset.no_surat_kontrak
            const tanggal_berakhir_kontrak = asset.tanggal_berakhir_kontrak
            const id_kepemilikan = asset.id_kepemilikan
            const keterangan = asset.keterangan
            const sta = asset.sta
            const alasan = asset.alasan

            $("#asset").val(qr_code.toLowerCase() === value ? qr_code : serial_number)
            $("#id_asset").val(id_asset)
            $("#qr_code").val(qr_code)
            $("#serial_number").val(serial_number)
            $("#tanggal_akuisisi_asset").val(tanggal_akuisisi_asset)
            $("#no_surat_kontrak").val(no_surat_kontrak)
            $("#tanggal_berakhir_kontrak").val(tanggal_berakhir_kontrak)
            $("#id_kepemilikan").children().filter(`option[value="${id_kepemilikan}"]`).prop("selected", true)
            $("#keterangan").val(keterangan)
            $("#sta").val(sta)
            toggle("update")
            toggle_alasan(alasan)
            return
          }
        }
      }, list_asset[last_value_asset] === undefined ? on_change_delay : 0)
    })
  }

  function init_tujuan_mutasi_listener() {
    $("#tujuan_mutasi").on("change", function() {
      const tujuan_mutasi = $(this).val()
      if (tujuan_mutasi === "0") {
        $("#id_pelanggan").val("")
        $("#pelanggan").val("").addClass("hidden")
        $("#gudang").removeClass("hidden")
      }

      if (tujuan_mutasi === "1") {
        $("#id_gudang").val("")
        $("#gudang").val("").addClass("hidden")
        $("#pelanggan").removeClass("hidden")
      }
    })
  }

  function init_gudang_listener() {
      const fill_gudang_list = (value) => {
          gudang_list.empty()
          last_value_gudang = value
          if (list_gudang[value] === undefined) return
          for (const gudang of list_gudang[value]) {
              const kode_gudang = gudang.kode_gudang
              const nama_gudang = gudang.nama_gudang
              gudang_list.append(`<option value="${kode_gudang} / ${nama_gudang}"></option>`)
          }
      }

      $("#gudang").on("focus keyup", function() {
          const value = $(this).val().toLowerCase()
          if (list_gudang[value] === undefined) {
              delay_gudang = new Date().getTime()
              setTimeout(async () => {
                  const current_timestamp = new Date().getTime()
                  if (current_timestamp - delay_gudang < fetch_request_delay) return
                  await fetch_gudang(value)
                  fill_gudang_list(value)
              }, fetch_request_delay)
          } else {
              fill_gudang_list(value)
          }
      })

      $("#gudang").on("change", function() {
          const value = $(this).val().toLowerCase()
          $("#id_gudang").val("")
          setTimeout(() => {
              if (list_gudang[last_value_gudang] === undefined) return
              for (const data_gudang of list_gudang[last_value_gudang]) {
                  const gudang = `${data_gudang.kode_gudang} / ${data_gudang.nama_gudang}` 
                  if (gudang.toLowerCase() === value) {
                      const id_gudang = data_gudang.id_gudang
                      
                      $("#id_gudang").val(id_gudang)
                      $("#gudang").val(gudang)
                      return
                  }
              }
          }, list_gudang[last_value_gudang] === undefined ? on_change_delay : 0)
      })
  }
  
  function init_pelanggan_listener() {
    const fill_pelanggan_list = (value) => {
      pelanggan_list.empty()
      last_value_pelanggan = value
      if (list_pelanggan[value] === undefined) return
      for (const pelanggan of list_pelanggan[value]) {
        const kode_pelanggan = pelanggan.kode_pelanggan
        const nama_pelanggan = pelanggan.nama_pelanggan
        pelanggan_list.append(`<option value="${kode_pelanggan} / ${nama_pelanggan}"></option>`)
      }
    }

    $("#pelanggan").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_pelanggan[value] === undefined) {
        delay_pelanggan = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_pelanggan < fetch_request_delay) return
          await fetch_pelanggan(value)
          fill_pelanggan_list(value)
        }, fetch_request_delay)
      } else {
        fill_pelanggan_list(value)
      }
    })

    $("#pelanggan").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_pelanggan").val("")
      setTimeout(() => {
        if (list_pelanggan[last_value_pelanggan] === undefined) return
        for (const data_pelanggan of list_pelanggan[last_value_pelanggan]) {
          const pelanggan = `${data_pelanggan.kode_pelanggan} / ${data_pelanggan.nama_pelanggan}`
          if (pelanggan.toLowerCase() === value) {
            const id_pelanggan = data_pelanggan.id_pelanggan

            $("#id_pelanggan").val(id_pelanggan)
            $("#pelanggan").val(pelanggan)
            return
          }
        }      
      }, list_pelanggan[last_value_pelanggan] === undefined ? on_change_delay : 0)
    })
  }

  function init_sta_listener() {
    $("#sta").on("change", function() {
      toggle_alasan()
    })
  }

  function init_reset_btn_listener() {
    $("#reset_btn").on("click", function(e) {
      toggle($("#id_asset").prop("defaultValue") === "" ? "insert" : "update")

      if (default_tujuan_mutasi_value === "0") {
        $("#gudang").removeClass("hidden")
        $("#pelanggan").addClass("hidden")
      }

      if (default_tujuan_mutasi_value === "1") {
        $("#gudang").addClass("hidden")
        $("#pelanggan").removeClass("hidden")
      }

      if (default_sta_value === "1") {
        $("#alasan_box").removeClass("hidden")
        $("#alasan").prop("required", true)
      }

      if (default_sta_value === "2") {
        $("#alasan_box").addClass("hidden")
        $("#alasan").prop("required", false)
      }
    })
  }
})

// utils function
function toggle(mode) {
  $("#id_barang").val("")
  $("#barang").val("")
  $("#id_gudang").val("")
  $("#gudang").val("")
  $("#id_pelanggan").val("")
  $("#pelanggan").val("")
  
  if (mode === "insert") {
    $("#barang_box").removeClass("hidden")
    $("#barang").prop("required", true)
    $("#tujuan_mutasi_box").removeClass("hidden")
    $("#sta").val("2")
  }
  
  if (mode === "update") {
    $("#barang_box").addClass("hidden")
    $("#barang").prop("required", false)
    $("#tujuan_mutasi_box").addClass("hidden")
  }
}

function toggle_alasan(alasan = "") {
  const sta = $("#sta").val()
  $("#alasan").val(alasan)
  if (sta === "1") {
    $("#alasan_box").removeClass("hidden")
    $("#alasan").prop("required", true)
  }
  else if (sta === "2") {
    $("#alasan_box").addClass("hidden")
    $("#alasan").prop("required", false)
  }
}
