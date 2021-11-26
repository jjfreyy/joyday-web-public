$(document).ready(() => {
  
  let asset_arr = []

  let list_pesanan = {}
  let list_dari_pelanggan = {}
  // let list_asset = {}
  let list_ke_agen = {}
  let list_pesanan1 = {}

  const pesanan_list = $("#pesanan_list")
  const dari_pelanggan_list = $("#dari_pelanggan_list")
  const asset_list = $("#asset_list")
  const ke_agen_list = $("#ke_agen_list")
  const barang_list = $("#barang_list")

  let delay_pesanan = delay_dari_pelanggan = delay_ke_gudang = delay_pelanggan = delay_ke_agen = new Date().getTime()
  let last_value_pesanan = last_value_dari_pelanggan = last_value_ke_gudang = last_value_ke_pelanggan = last_value_ke_agen = ""
  const edit_mode = $("#id_barang_masuk").prop("defaultValue") !== ""
  const default_value_barang_masuk1 = $(".tb_input tbody tr")
  const default_tipe = $("#tipe").val()
  
  init()

  async function init() {
    init_tipe_listener()
    init_no_po_listener()
    init_ke_agen_listener()
    init_qr_code_listener()
    init_barang_listener()
    init_add_asset_listener()
    init_delete_asset_listener()
    init_reset_btn_listener()
    init_submit_listener()
  }

  // fetch function
  async function fetch_pesanan(filter = "") {
    const response = await fetch_request({url: `input/barang_masuk/fetch?type=pesanan&filter=${filter};${$("#id_barang_masuk").val()}`})
    list_pesanan[filter] = response || undefined
  }
  
  async function fetch_dari_pelanggan(filter = "") {
    const response = await fetch_request({url: `input/barang_masuk/fetch?type=dari_pelanggan&filter=${filter}`})
    list_dari_pelanggan[filter] = response || undefined
  }
  
  async function fetch_ke_agen(filter = "") {
    const response = await fetch_request({url: `input/barang_masuk/fetch?type=ke_agen&filter=${filter}`})
    list_ke_agen[filter] = response || undefined
  }

  async function fetch_asset(filter) {
    const response = await fetch_request({url: `input/barang_masuk/fetch?type=asset&filter=${filter}`})
    return response
  }

  // event function
  async function init_tipe_listener() {
    if ($("#tipe").val() === "1") {
      await fetch_dari_pelanggan()
      $(".tb_input tbody tr").each(function(i) {
        const data = $(this).children().eq(1).children().first().val().split(";")
        const id_asset = data[0]
        asset_arr.push(id_asset)
      })
    }

    $("#tipe").on("change", function() {
      const tipe = $("#tipe").val()
      
      $("#dari_id_pesanan").val("")
      $("#no_po").val("")
      $("#dari_id_pelanggan").val("")
      $("#dari_pelanggan").val("")
      $("tbody").empty()
      list_pesanan1 = {}
      barang_list.empty()
      list_asset = {}
      asset_list.empty()
      list_dari_pelanggan = {}
      dari_pelanggan_list.empty()
      asset_arr = []
  
      if (tipe === "0") {
        $("#no_faktur_box").removeClass("hidden")
  
        $("#no_po_box").removeClass("hidden")
        $("#no_po").prop("required", true)
        
        $("#th_barang_box").css("display", "grid")
        $("#th_dari_pelanggan").css("display", "none")

        $("#th_status").css("display", "none")

        return
      }
  
      if (tipe === "1") {
        $("#no_faktur_box").addClass("hidden")
  
        $("#no_po_box").addClass("hidden")
        $("#no_po").prop("required", false)
        
        $("#th_barang_box").css("display", "none")
        $("#th_dari_pelanggan").css("display", "block")

        $("#th_status").css("display", "table-cell")
  
        return
      }
    })
  }

  async function init_no_po_listener() {
    if (["0", "2"].includes($("#tipe").val())) {
      reinit_pesanan_list()
    }

    $("#no_po").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_pesanan[value] === undefined) {
        delay_pesanan = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_pesanan < fetch_request_delay) return
          await fetch_pesanan(value)
          fill_pesanan_list(value)
        }, fetch_request_delay)
      } else {
        fill_pesanan_list(value)
      }
    })

    $("#no_po").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#dari_id_pesanan").val("")
      list_pesanan1 = {}
      $("tbody").empty()
      barang_list.empty()
      setTimeout(() => {
        if (list_pesanan[last_value_pesanan] === undefined) return
        for (const pesanan of list_pesanan[last_value_pesanan]) {
          const no_po = pesanan.no_po
          if (no_po.toLowerCase() === value) {
            const dari_id_pesanan = pesanan.id_pesanan
            const pesanan1_arr = pesanan.pesanan1.split("#")
            $("#dari_id_pesanan").val(dari_id_pesanan)
            $("#no_po").val(no_po)
            for (let pesanan1 of pesanan1_arr) {
              pesanan1 = pesanan1.split(";")
              if (list_pesanan1[pesanan1[1].toLowerCase()] === undefined) {
                barang_list.append(`<option value="${pesanan1[1]}"></option>`)
                list_pesanan1[pesanan1[1].toLowerCase()] = [pesanan1[0], pesanan1[1], parseInt(pesanan1[2])]
              } else {
                list_pesanan1[pesanan1[1].toLowerCase()][2] += parseInt(pesanan1[2])
              }
            }
            return
          }
        }
      }, list_pesanan[last_value_pesanan] === undefined ? on_change_delay : 0)
    })
  }

  function init_ke_agen_listener() {
    const fill_ke_agen_list = value => {
      ke_agen_list.empty()
      last_value_ke_agen = value
      if (list_ke_agen[value] === undefined) return
      for (const data_agen of list_ke_agen[value]) {
        const agen = data_agen.agen
        const alamat = if_empty_then({value: data_agen.alamat})
        ke_agen_list.append(`<option value="${agen} ~ ${alamat}"></option>`)
      }
    }

    $("#ke_agen").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_ke_agen[value] === undefined) {
        delay_ke_agen = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_ke_agen < fetch_request_delay) return
          await fetch_ke_agen(value)
          fill_ke_agen_list(value)
        }, fetch_request_delay)
      } else {
        fill_ke_agen_list(value)
      }
    })

    $("#ke_agen").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#ke_id_agen").val("")
      ke_agen_list.empty()

      setTimeout(() => {
        if (list_ke_agen[last_value_ke_agen] === undefined) return
        for (const data_agen of list_ke_agen[last_value_ke_agen]) {
          const agen = data_agen.agen
          const alamat = if_empty_then({value: data_agen.alamat})
          if (`${agen} ~ ${alamat}`.toLowerCase() === value) {
            const id_agen = data_agen.id_agen
            $("#ke_id_agen").val(id_agen)
            $("#ke_agen").val(agen)
            $("#alamat").val(alamat)
            return
          }
        }
      }, list_ke_agen[last_value_ke_agen] === undefined ? on_change_delay : 0)
    })
  }

  function init_qr_code_listener() {
    $("#qr_code").on("focus keyup", function() {
      if ($("#tipe").val() === "1") {
        const value = $(this).val().toLowerCase()
        if (list_dari_pelanggan[value] === undefined) {
          delay_dari_pelanggan = new Date().getTime()
          setTimeout(async () => {
            const current_timestamp = new Date().getTime()
            if (current_timestamp - delay_dari_pelanggan < fetch_request_delay) return
            await fetch_dari_pelanggan(value)
            fill_dari_pelanggan_list(value)
          }, fetch_request_delay)
        } else {
          fill_dari_pelanggan_list(value)
        }
      }
    })

    $("#qr_code").on("keydown", function(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        if ($(this).val() === "") return

        const tipe =  $("#tipe").val()
        if (["0", "2"].includes(tipe)) {
          let pesanan_counter = 0
          for (pesanan1 of Object.entries(list_pesanan1)) {
            if (pesanan1[1][2] > 0) pesanan_counter++
          }
  
          if (pesanan_counter === 1) {
            add_pesanan(pesanan_counter)
          } else {
            $("#barang").focus()
          }
          return
        }

        if (tipe === "1") {
          add_asset()
        }
      }
    })
  }

  function init_barang_listener() {
    $("#barang").on("keydown", function(e) {
      if (e.keyCode === 13) {
        e.preventDefault()

        const tipe = $("#tipe").val()
        if (["0", "2"].includes(tipe)) {
          add_pesanan()
          return
        }

        if (tipe === "1") {
          add_asset()
        }
      }
    })
  }

  function init_add_asset_listener() {
    $("a#add_asset").on("click", () => {
      const tipe = $("#tipe").val()
      if (["0", "2"].includes(tipe)){
        add_pesanan()
        return
      }

      if (tipe === "1") {
        add_asset()
      }
    })

    $("a#add_asset").on("keydown", function(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        const tipe = $("#tipe").val()
        if (["0", "2"].includes(tipe)) {
          add_pesanan()
          return
        }

        if (tipe === "1") {
          add_asset()
        }
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
    $("#reset_btn").on("click", function(e) {
      $("tbody").empty()
      $("tbody").append(default_value_barang_masuk1)
      $("tbody tr").each(function(i) {
        $(this).children().eq(0).html(i+1)
      })
      asset_arr = []
      if (["0", "2"].includes(default_tipe)) {
        reinit_pesanan_list()
      } 
      // else if (default_tipe === "1") {
      //   reinit_asset_list()
      // }

      if (default_tipe === "0") {
        $("#no_faktur_box").removeClass("hidden")
  
        $("#no_po_box").removeClass("hidden")
        $("#no_po").prop("required", true)
  
        $("#th_barang_box").css("display", "grid")
        $("#th_dari_pelanggan").css("display", "none")

        $("#th_status").css("display", "none")
  
        return
      }
  
      if (default_tipe === "1") {
        $("#no_faktur_box").addClass("hidden")
  
        $("#no_po_box").addClass("hidden")
        $("#no_po").prop("required", false)
        
        $("#th_barang_box").css("display", "none")
        $("#th_dari_pelanggan").css("display", "block")

        $("#th_status").css("display", "table-cell")
  
        return
      }
    })
  }

  function init_submit_listener() {
    $("#form").on("submit", function(e) {
      e.preventDefault()
      const tipe = $("#tipe").val()
      if (["0", "2"].includes(tipe)) {
        if ($("tbody tr").length === 0) {
          $("body").append(create_dialog("simple", ["error", "Silakan isi data asset minimal 1."]))
          $("div.simple_dialog").slideDown()
          return
        }
        // if (Object.entries(list_pesanan1).length === 0) return
        // for (pesanan1 of Object.entries(list_pesanan1)) {
        //   if (pesanan1[1][2] > 0) {
        //     $("body").append(create_dialog("simple", ["error", "Data pesanan tidak lengkap."]))
        //     $("div.simple_dialog").slideDown()
        //     return
        //   }
        // }
      } else if (tipe === "1" && !edit_mode) {
        if ($("tbody tr").length === 0) {
          $("body").append(create_dialog("simple", ["error", "Silakan isi data asset minimal 1."]))
          $("div.simple_dialog").slideDown()
          return
        }
      }
      $(this)[0].submit()
    })
  }

  // utils function
  function add_asset() {
    let value = $("#qr_code").val().toLowerCase()
    if (value === "") return

    setTimeout(async () => {
      if (list_dari_pelanggan[last_value_dari_pelanggan] === undefined) return
      let is_found = false
      for (const asset of list_dari_pelanggan[last_value_dari_pelanggan]) {
        const id_asset = asset.id_asset
        const qr_code = asset.qr_code
        if (asset_arr.includes(id_asset)) continue

        if (qr_code.toLowerCase() === value) {
          const id_barang = asset.id_barang
          const barang = asset.barang
          const id_pelanggan = asset.id_pelanggan
          const pelanggan = asset.pelanggan
          const alamat = `Alamat: ${if_empty_then({value: asset.alamat})}`
          const sta = $("#th_select_status").val()
          const dsta = sta === "2" ? "Bagus" : "Rusak"
          const barang_masuk1 = `${id_asset};${qr_code};${id_barang};${barang};${id_pelanggan};${pelanggan}<br>${alamat};${sta};${dsta}`
          append_tr(barang_masuk1)
          
          asset_arr.push(id_asset)
          fill_dari_pelanggan_list1()
          is_found = true
        }
      }

      if(!is_found) {
        response = await fetch_asset(value)
        console.log(response);
        if (response.length === 0) $("body").append(create_dialog("simple", ["error", "Data asset tidak dapat ditemukan."]))
        else $("body").append(create_dialog("simple", ["error", `Asset telah terdaftar di gudang '${response[0].nama_gudang}'.`]))
        $("div.simple_dialog").slideDown()
      }
    }, list_dari_pelanggan[value] === undefined ? on_change_delay : 0)
  }

  function add_pesanan(pesanan_counter) {
    const qr_code = $("#qr_code").val()
    if (qr_code === "") return
    
    let id_barang = ""
    let barang = ""
    let qty 
    
    if (pesanan_counter === 1) {
      for (pesanan1 of Object.entries(list_pesanan1)) {
        if (pesanan1[1][2] > 0) {
          id_barang = pesanan1[1][0]
          barang = pesanan1[1][1]
          qty = pesanan1[1][2]
        }
      }
    } else if (Object.entries(list_pesanan1).length === 1) {
      barang = Object.entries(list_pesanan1)[0][0]
      id_barang = list_pesanan1[barang][0]
      qty = list_pesanan1[barang][2]
      barang = list_pesanan1[barang][1]
    } else {
      barang = $("#barang").val().toLowerCase()
      if (list_pesanan1[barang] === undefined) return
      id_barang = list_pesanan1[barang][0]
      qty = list_pesanan1[barang][2]
      barang = list_pesanan1[barang][1]
    }

    if (qty <= 0) return
    list_pesanan1[barang.toLowerCase()] = [id_barang, barang, qty-1]
    if (qty-1 <= 0) {
     fill_barang_list()
    }
    const barang_masuk1 = `${id_barang};${qr_code};${barang}`
    append_tr(barang_masuk1)
  }

  function append_tr(barang_masuk1) {
    const no = $("tbody").children().length + 1
    const barang_masuk1_arr = barang_masuk1.split(";")
    if ($("#tipe").val() === "1") {
      $("tbody").append(`
      <tr>
        <td>${no}</td>
        <td>
          <input type="text" class="hidden" name="barang_masuk1[]" value="${barang_masuk1}" />
          ${barang_masuk1_arr[1]}
        </td>
        <td>${barang_masuk1_arr[5]}</td>
        <td>${barang_masuk1_arr[7]}</td>
        <td class="centered"><a href="#" class="delete_asset">Hapus</a></td>
      </tr>
      `)
    } else {
      $("tbody").append(`
      <tr>
        <td>${no}</td>
        <td>
          <input type="text" class="hidden" name="barang_masuk1[]" value="${barang_masuk1}" />
          ${barang_masuk1_arr[1]}
        </td>
        <td>${barang_masuk1_arr[2]}</td>
        <td class="centered"><a href="#" class="delete_asset">Hapus</a></td>
      </tr>
      `)
    }

    $("#qr_code").val("").focus()
    $("#barang").val("")
  }

  function delete_asset(index) {
    const tr = $("tbody tr").eq(index)
    const barang_masuk1 = tr.children().eq(1).children().eq(0).val().split(";")
    const tipe = $("#tipe").val()
    if (["0", "2"].includes(tipe)){
      list_pesanan1[barang_masuk1[2].toLowerCase()][2]++
      fill_barang_list()
    } else if (tipe === "1") {
      asset_arr = asset_arr.filter((value) => {
        return value !== barang_masuk1[0]
      })
      fill_dari_pelanggan_list1()
    }
    tr.remove()
    $("tbody").children().each(function(i) {
      $(this).find("td:first-child").html(i+1)
    })
    $("#qr_code").focus()
  }

  // function fill_asset_list(value) {
  //   asset_list.empty()
  //   last_value_dari_pelanggan = value
  //   for (asset of list_dari_pelanggan[value]) {
  //     const id_asset = asset.id_asset
  //     const qr_code = asset.qr_code
  //     if (asset_arr.includes(id_asset)) continue
  //     asset_list.append(`<option value="${qr_code}"></option>`)
  //   }
  // }

  function fill_barang_list() {
    barang_list.empty()
    for (pesanan1 of Object.entries(list_pesanan1)) {
      if (pesanan1[1][2] <= 0) continue
      barang_list.append(`<option value="${pesanan1[1][1]}"></option>`)
    }
  }

  function fill_dari_pelanggan_list(value) {
    asset_list.empty()
    last_value_dari_pelanggan = value
    if (list_dari_pelanggan[value] === undefined) return
    for (const asset of list_dari_pelanggan[value]) {
      const id_asset = asset.id_asset
      const qr_code = asset.qr_code
      if (asset_arr.includes(id_asset)) continue
      asset_list.append(`<option value="${qr_code}"></option>`)
    }
  }

  function fill_dari_pelanggan_list1() {
    asset_list.empty()
    if (list_dari_pelanggan[list_dari_pelanggan] === undefined) return
    for (const asset of list_dari_pelanggan[last_value_dari_pelanggan]) {
      const id_asset = asset.id_asset
      const qr_code = asset.qr_code
      if (asset_arr.includes(id_asset)) continue
      asset_list.append(`<option value="${qr_code}"></option>`)
    }
  }

  function fill_pesanan_list(value) {
    pesanan_list.empty()
    last_value_pesanan = value
    if (list_pesanan[value] === undefined) return
    for (const pesanan of list_pesanan[value]) {
      const no_po = pesanan.no_po
      pesanan_list.append(`<option value="${no_po}"></option>`)
    }
  }

  // async function reinit_asset_list() {
  //   const value = $("#qr_code").val().toLowerCase()
  //   await fetch_dari_pelanggan(value)
  //   fill_dari_pelanggan_list(value)
  //   list_asset = {}
  //   for (const dari_pelanggan of list_dari_pelanggan[last_value_dari_pelanggan]) {
  //     const qr_code = dari_pelanggan.qr_code
  //     if (qr_code.toLowerCase() === value) {
  //       asset_list.append(`<option value="${qr_code}"></option>`)
  //     }
  //   }

  //   $(`input[name="barang_masuk1[]"]`).each(function() {
  //     const barang_masuk1_arr = $(this).val().split(";")
  //     list_asset[barang_masuk1_arr[1]][3] = true
  //   })
  //   fill_asset_list()
  // }

  async function reinit_pesanan_list() {
    const value = $("#no_po").prop("defaultValue").toLowerCase()
    await fetch_pesanan(value)
    fill_pesanan_list(value)
    list_pesanan1 = {}
    for (const pesanan of list_pesanan[last_value_pesanan]) {
      const no_po = pesanan.no_po
      if (no_po.toLowerCase() === value) {
        const pesanan1_arr = pesanan.pesanan1.split("#")
        for (let pesanan1 of pesanan1_arr) {
          pesanan1 = pesanan1.split(";")
          if (list_pesanan1[pesanan1[1].toLowerCase()] === undefined) {
            list_pesanan1[pesanan1[1].toLowerCase()] = [pesanan1[0], pesanan1[1], parseInt(pesanan1[2])]
          } else {
            list_pesanan1[pesanan1[1].toLowerCase()][2] += parseInt(pesanan1[2])
          }
        }
        break
      }
    }

    $(`input[name="barang_masuk1[]"]`).each(function() {
      const barang_masuk1_arr = $(this).val().split(";")
      const id_barang = barang_masuk1_arr[0]
      const barang = barang_masuk1_arr[2]
      const pesanan1 = list_pesanan1[barang.toLowerCase()]
      list_pesanan1[barang.toLowerCase()] = [id_barang, barang, pesanan1[2] - 1]
    })
    fill_barang_list()
  }

})
