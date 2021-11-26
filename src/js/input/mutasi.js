$(document).ready(() => {
  
  let list_dari_pelanggan = {}
  let list_ke_pelanggan = {}
  let list_asset = {}
  const dari_pelanggan_list = $("#dari_pelanggan_list")
  const ke_pelanggan_list = $("#ke_pelanggan_list")
  const asset_list = $("#asset_list")
  let delay_dari_pelanggan = delay_ke_pelanggan = new Date().getTime()
  let last_value_dari_pelanggan = last_value_ke_pelanggan = ""
  const default_id_mutasi = $("#id_mutasi").val()
  const default_dari_pelanggan = $("#dari_pelanggan").val().toLowerCase()
  const default_value_mutasi1 = $("tbody").children()

  init()

  async function init() {
    fetch_dari_pelanggan($("#dari_pelanggan").val().toLowerCase())
    init_dari_pelanggan_listener()
    init_qr_code_listener()
    init_ke_pelanggan_listener()
    init_add_asset_listener()
    init_delete_asset_listener()
    init_reset_btn_listener()
  }

  // fetch function
  async function fetch_dari_pelanggan(filter = "") {
    const response = await fetch_request({url: `input/mutasi/fetch?type=dari_pelanggan&filter=${filter};${default_id_mutasi}`})
    list_dari_pelanggan[filter] = response || undefined
  }

  async function fetch_ke_pelanggan(filter = "") {
    const response = await fetch_request({url: `input/mutasi/fetch?type=ke_pelanggan&filter=${filter}`})
    list_ke_pelanggan[filter] = response || undefined
  }

  // event function
  async function init_dari_pelanggan_listener() {
    const fill_dari_pelanggan_list = value => {
      dari_pelanggan_list.empty()
      last_value_dari_pelanggan = value
      if (list_dari_pelanggan[value] === undefined) return
      for (const dari_pelanggan of list_dari_pelanggan[value]) {
        const pelanggan = dari_pelanggan.pelanggan
        const alamat = dari_pelanggan.alamat
        dari_pelanggan_list.append(`<option value="${pelanggan}"></option>`)
      }
    }

    if (default_id_mutasi !== "" || $("#dari_pelanggan").val() !== "") {
      last_value_dari_pelanggan = $("#dari_pelanggan").val().toLowerCase()
      await fetch_dari_pelanggan(last_value_dari_pelanggan)
      reinit_asset_list()
    }

    $("#dari_pelanggan").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_dari_pelanggan[value] === undefined) {
        delay_dari_pelanggan = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_dari_pelanggan < fetch_request_delay) return
          await fetch_dari_pelanggan(value)
          fill_dari_pelanggan_list(value)
        }, fetch_request_delay);
      } else {
        fill_dari_pelanggan_list(value)
      }
    })    

    $("#dari_pelanggan").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#dari_id_pelanggan").val("")
      $("#alamat").val("")
      $("tbody").empty()
      asset_list.empty()
      list_asset = {}
      setTimeout(() => {
        if (list_dari_pelanggan[last_value_dari_pelanggan] === undefined) return
        for (const dari_pelanggan of list_dari_pelanggan[last_value_dari_pelanggan]) {
          const pelanggan = dari_pelanggan.pelanggan
          const alamat = if_empty_then({value: dari_pelanggan.alamat})
          if (pelanggan.toLowerCase() === value) {
            const id_pelanggan = dari_pelanggan.id_pelanggan
            const asset_arr = dari_pelanggan.asset.split("#")
            $("#dari_id_pelanggan").val(id_pelanggan)
            $("#dari_pelanggan").val(pelanggan)
            $("#alamat").val(alamat)
            for (let asset of asset_arr) {
              asset = asset.split(";")
              list_asset[asset[1].toLowerCase()] = {id_asset: asset[0], qr_code: asset[1], no_surat_kontrak: asset[2], in_list: false}
            }
            ke_pelanggan_list.empty()
            fill_asset_list()
            return
          }
        }
      }, list_dari_pelanggan[last_value_dari_pelanggan] === undefined ? on_change_delay : 0)
    })
  }

  function init_qr_code_listener() {
    $("#qr_code").on("change", function() {
      $("#no_surat_kontrak").val("")
      const val = $(this).val().toLowerCase()
      const data_asset = list_asset[val]
      if (is_empty(data_asset)) return
      $("#no_surat_kontrak").val(data_asset.no_surat_kontrak)
    })
  }

  function init_ke_pelanggan_listener() {
    const fill_ke_pelanggan_list = (value) => {
      const dari_id_pelanggan = $("#dari_id_pelanggan").val()
      ke_pelanggan_list.empty()
      last_value_ke_pelanggan = value
      if (list_ke_pelanggan[value] === undefined) return
      for (const ke_pelanggan of list_ke_pelanggan[value]) {
        const id_pelanggan = ke_pelanggan.id_pelanggan
        const pelanggan = ke_pelanggan.pelanggan
        const alamat = ke_pelanggan.alamat
        if (id_pelanggan === dari_id_pelanggan) continue
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
  }

  function init_add_asset_listener() {
    $("a#add_asset").on("click", () => {
      add_asset()
    })

    $("a#add_asset, #qr_code, #no_surat_kontrak, #ke_pelanggan").on("keydown", function(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        add_asset()
      }
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
    $("#reset_btn").on("click", function() {
      $("tbody").empty()
      $("tbody").append(default_value_mutasi1)
      $("tbody").children().each(function(i) {
        $(this).find("td:first-child").html(i+1)
      })
      reinit_asset_list()
    })
  }

  // utils function
  function add_asset() {
    const qr_code_val = $("#qr_code").val().toLowerCase()
    const ke_pelanggan_val = $("#ke_pelanggan").val().toLowerCase()
    if (list_asset[qr_code_val] === undefined) return
    
    const id_asset =  list_asset[qr_code_val].id_asset
    const qr_code = list_asset[qr_code_val].qr_code
    const no_surat_kontrak = $("#no_surat_kontrak").val()
    if (ke_pelanggan_val === "" || list_ke_pelanggan[last_value_ke_pelanggan] === undefined) {
      $("#ke_pelanggan").focus()
      return
    }

    const dari_id_pelanggan = $("#dari_id_pelanggan").val()
    let id_pelanggan = ""
    let pelanggan = ""
    let alamat = ""
    for (const data_pelanggan of list_ke_pelanggan[last_value_ke_pelanggan]) {
      const pelanggan1 = data_pelanggan.pelanggan
      if (pelanggan1.toLowerCase() === ke_pelanggan_val) {
        id_pelanggan = data_pelanggan.id_pelanggan
        if (id_pelanggan === dari_id_pelanggan) {
          $("#ke_pelanggan").focus()
          return
        }
        pelanggan = pelanggan1
        alamat = if_empty_then({value: data_pelanggan.alamat})
        break
      }
    }
    if (id_pelanggan === "") return
    
    list_asset[qr_code_val].in_list = true
    fill_asset_list()
    
    const no = $("tbody").children().length + 1
    const mutasi1 = `${id_asset};${qr_code};${no_surat_kontrak};${id_pelanggan};${pelanggan};${alamat}`
    $(".tb_input tbody").append(`
    <tr>
      <td>${no}</td>
      <td>
        <input type="text" class="hidden" name="mutasi1[]" value="${mutasi1}" />
        ${qr_code}
      </td>
      <td>${if_empty_then({value: no_surat_kontrak})}</td>
      <td>${pelanggan}</td>
      <td>${alamat}</td>
      <td class="centered"><a href="#" class="delete_asset">Hapus</a></td>
    </tr>
    `)
    $("#qr_code").val("")
    $("#ke_pelanggan").val("")
    $("#qr_code").focus()
  }

  function check_table_input() {
    for (const [key, val] of Object.entries(list_asset)) {
      val.in_list = false
    }

    $(`input[name="mutasi1[]"]`).each(function() {
      const mutasi1_arr = $(this).val().split(";")
      const qr_code = mutasi1_arr[1]
      list_asset[qr_code.toLowerCase()].in_list = true
    })
  }

  function delete_asset(index) {
    const tr = $("tbody tr").eq(index)
    const mutasi1_arr = tr.children().eq(1).children().eq(0).val().split(";")
    
    list_asset[mutasi1_arr[1].toLowerCase()].in_list = false
    fill_asset_list()
    
    tr.remove()
    $("tbody").children().each(function(i) {
      $(this).find("td:first-child").html(i+1)
    })
  }

  function fill_asset_list() {
    asset_list.empty()
    for (const [key, val] of Object.entries(list_asset)) {
      if (val.in_list) continue
      asset_list.append(`<option value="${val.qr_code}"></option>`)
    }
  }

  function reinit_asset_list() {
    const dari_pelanggan_val = $("#dari_pelanggan").val().toLowerCase()
    for (const dari_pelanggan of list_dari_pelanggan[default_dari_pelanggan]) {
      const pelanggan = dari_pelanggan.pelanggan
      if (pelanggan.toLowerCase() === dari_pelanggan_val) {
        const asset_arr = dari_pelanggan.asset.split("#")
        for (let asset of asset_arr) {
          asset = asset.split(";")
          list_asset[asset[1].toLowerCase()] = {id_asset: asset[0], qr_code: asset[1], no_surat_kontrak: asset[2], in_list: false}
        }
        check_table_input()
        fill_asset_list()
        return
      }
    }
  }

})
