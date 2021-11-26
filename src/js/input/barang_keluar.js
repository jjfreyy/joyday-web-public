$(document).ready(() => {
  
  let list_asset = {}
  let list_ke_pelanggan = {}
  const asset_list = $("#asset_list")
  const ke_pelanggan_list = $("#ke_pelanggan_list")
  let delay_ke_pelanggan = new Date().getTime()
  let last_value_ke_pelanggan = ""
  const default_value_barang_keluar1 = $("tbody").children()

  init()

  async function init() {
    fetch_asset_gudang()
    init_qr_code_listener()
    // init_ke_pelanggan_listener()
    init_add_asset_listener()
    init_delete_asset_listener()
    init_reset_btn_listener()
  }

  // fetch function
  async function fetch_asset_gudang() {
    const response = await fetch_request({url: `input/barang_keluar/fetch?type=asset_gudang&filter=${$("#id_gudang").val()};${$("#id_barang_keluar").val()}`})
    list_asset = {}
    if (response.length === 1) {
      const asset_arr = response[0].asset.split("#")
      for (let asset of asset_arr) {
        asset = asset.split(";")
        const id_asset = asset[0]
        const qr_code = asset[1]
        const barang = asset[2]
        list_asset[qr_code.toLowerCase()] = [id_asset, qr_code, barang, false]
      }
    }
    check_table_input()
    fill_asset_list()
  }

  async function fetch_ke_pelanggan(filter = "") {
    const response = await fetch_request({url: `input/barang_keluar/fetch?type=ke_pelanggan&filter=${filter}`})
    list_ke_pelanggan[filter] = response || undefined
  }

  // event function
  function init_qr_code_listener() {
    $("#qr_code").on("keydown", function(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        if ($(this).val() === "") return
        add_asset()
      }
    })
  }

  function init_ke_pelanggan_listener() {
    const fill_ke_pelanggan_list = (value) => {
      ke_pelanggan_list.empty()
      last_value_ke_pelanggan = value
      if (list_ke_pelanggan[value] === undefined) return
      for (const data_pelanggan of list_ke_pelanggan[value]) {
        const pelanggan = data_pelanggan.pelanggan
        ke_pelanggan_list.append(`<option value="${pelanggan}"></option>`)
      }
    }

    $("#ke_pelanggan").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_ke_pelanggan[value] === undefined) {
        delay_ke_pelanggan = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_ke_pelanggan < fetch_request_delay) return
          await fetch_ke_pelanggan(value)
          fill_ke_pelanggan_list(value)
        }, fetch_request_delay)
      } else {
        fill_ke_pelanggan_list(value)
      }
    })

    $("#pelanggan").on("change", function() {
      const value = $(this).val().toLowerCase()
      setTimeout(() => {
        if (list_ke_pelanggan[last_value_pelanggan] === undefined) return
        for (const data_pelanggan of list_ke_pelanggan[last_value_pelanggan]) {
          const pelanggan = data_pelanggan.pelanggan
          if (pelanggan.toLowerCase() === value) {
            $(this).val(pelanggan)
            return
          }
        }
      }, list_ke_pelanggan[last_value_pelanggan] === undefined ? on_change_delay : 0)
    })

    $("#ke_pelanggan").on("keydown", function(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        add_asset()
      }
    })
  }

  function init_add_asset_listener() {
    $("a#add_asset").on("click", () => {
      add_asset()
    })
  }

  function init_delete_asset_listener() {
    $("body").on("click", "a.delete_asset", function() {
      delete_asset($(this).parent().parent().index())
    })

    $("#body").on("keydown", "a.delete_asset", function(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        delete_asset($(this).parent().parent().index())
      }
    })
  }

  function init_reset_btn_listener() {
    $("#reset_btn").on("click", function(e) {
      $("tbody").empty()
      $("tbody").append(default_value_barang_keluar1)
      $("tbody tr").each(function(i) {
        $(this).children().eq(0).html(i+1)
      })
      check_table_input()
      fill_asset_list()
    })
  }

  // utils function
  function add_asset() {
    let qr_code = $("#qr_code").val()
    if (qr_code === "") return

    let asset = list_asset[qr_code.toLowerCase()]
    if (asset === undefined || asset[3]) return
    const id_asset = asset[0]
    qr_code = asset[1]
    const barang = asset[2]

    // let id_pelanggan
    // let ke_pelanggan
    // const ke_pelanggan_val = $("#ke_pelanggan").val().toLowerCase()
    // const ke_pelanggan_arr = list_ke_pelanggan[last_value_ke_pelanggan]
    // $("#ke_pelanggan").focus()
    // if (ke_pelanggan_arr === undefined) return
    // for (pelanggan of ke_pelanggan_arr) {
    //   if (pelanggan.pelanggan.toLowerCase() === ke_pelanggan_val) {
    //     id_pelanggan = pelanggan.id_pelanggan
    //     ke_pelanggan = pelanggan.pelanggan
    //     break
    //   }
    // }
    // if (id_pelanggan === undefined) return

    list_asset[qr_code.toLowerCase()][3] = true
    fill_asset_list()
    const barang_keluar1 = `${id_asset};${qr_code};${barang}`
    append_tr(barang_keluar1)
  }

  function append_tr(barang_keluar1) {
    const no = $("tbody").children().length + 1
    const barang_keluar1_arr = barang_keluar1.split(";")
    $("tbody").append(`
    <tr>
      <td>${no}</td>
      <td>
        <input type="text" class="hidden" name="barang_keluar1[]" value="${barang_keluar1}" />
        ${barang_keluar1_arr[1]}
      </td>
      <td>${barang_keluar1_arr[2]}</td>
      <td class="centered"><a href="#" class="delete_asset">Hapus</a></td>
    </tr>
    `)

    $("#qr_code").val("").focus()
    // $("#ke_pelanggan").val("")
  }

  function check_table_input() {
    for (const asset of Object.entries(list_asset)) {
      asset[1][3] = false
    }
    $(`input[name="barang_keluar1[]"]`).each(function() {
      const barang_masuk1_arr = $(this).val().split(";")
      const qr_code = barang_masuk1_arr[1]
      list_asset[qr_code.toLowerCase()][3] = true
    })
  }

  function delete_asset(index) {
    const tr = $(".tb_input tbody tr").eq(index)
    const barang_masuk1_arr = tr.children().eq(1).children().eq(0).val().split(";")
    
    list_asset[barang_masuk1_arr[1].toLowerCase()][3] = false
    fill_asset_list()
    
    tr.remove()
    $("tbody").children().each(function(i) {
      $(this).find("td:first-child").html(i+1)
    })
    $("#qr_code").focus()
  }

  function fill_asset_list() {
    asset_list.empty()
    for (asset of Object.entries(list_asset)) {
      if (asset[1][3]) continue
      asset_list.append(`<option value="${asset[1][1]}"></option>`)
    }
  }
})
