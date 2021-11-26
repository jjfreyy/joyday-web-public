$(document).ready(() => {
  
  let list_pesanan = {}
  let list_distributor = {}
  let list_barang = {}
  const pesanan_list = $("#pesanan_list")
  const distributor_list = $("#distributor_list")
  const barang_list = $("#barang_list")
  let delay_pesanan = delay_distributor = delay_barang = new Date().getTime()
  let last_value_pesanan = last_value_distributor = last_value_barang = ""
  const default_value_pesanan1 = $(".tb_input tbody").children()

  init()

  async function init() {
    fetch_pesanan($("#no_po").val().toLowerCase())
    init_no_po_listener()
    init_distributor_listener()
    init_barang_listener()
    init_reset_btn_listener()
  }

  // fetch function
  async function fetch_pesanan(filter = "") {
    const response = await fetch_request({url: `input/pesanan/fetch?type=pesanan&filter=${filter}`})
    list_pesanan[filter] = response || undefined
  }

  async function fetch_distributor(filter = "") {
    const response = await fetch_request({url: `input/pesanan/fetch?type=distributor&filter=${filter}`})
    list_distributor[filter] = response || undefined
  }

  async function fetch_barang(filter = "") {
    const response = await fetch_request({url: `input/pesanan/fetch?type=barang&filter=${filter}`})
    list_barang[filter] = response || undefined
  }

  // event function
  function init_no_po_listener() {
    const fill_pesanan_list = (value) => {
      pesanan_list.empty()
      last_value_pesanan = value
      if (list_pesanan[value] === undefined) return
      for (const pesanan of list_pesanan[value]) {
        const no_po = pesanan.no_po
        pesanan_list.append(`<option value="${no_po}"></option>`)
      }
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
      $("#id_pesanan").val("")
      setTimeout(() => {
        if (list_pesanan[last_value_pesanan] === undefined) return
        for (const pesanan of list_pesanan[last_value_pesanan]) {
          const no_po = pesanan.no_po
          if (no_po.toLowerCase() === value) {
            const id_pesanan = pesanan.id_pesanan
            const id_distributor = pesanan.id_distributor
            const distributor = pesanan.distributor
            const keterangan = pesanan.keterangan
            const pesanan1 = pesanan.pesanan1.split("#")

            $("#id_pesanan").val(id_pesanan)
            $("#no_po").val(no_po.split("-")[1])
            $("#id_distributor").val(id_distributor)
            $("#distributor").val(distributor)
            $("#keterangan").val(keterangan)
            $(".tb_input tbody tr").remove()
            pesanan1.forEach((data_barang, i) => {
              const no = i + 1
              const barang = data_barang.split(";")[1]
              const qty = data_barang.split(";")[2]
              $(".tb_input tbody").append(`
                <tr>
                  <td>${no}</td>
                  <td>
                    <input class="hidden" type="text" name="pesanan1[]" value="${data_barang}" />
                    ${barang}
                  </td>
                  <td>${qty}</td>
                  <td class="centered"><a href="#" class="delete_barang">Hapus</a></td>
                </tr>
              `)
            });
            return
          }
        }
      }, list_pesanan[last_value_pesanan] === undefined ? on_change_delay : 0)
    })
  }

  function init_distributor_listener() {
    const fill_distributor_list = (value) => {
      distributor_list.empty()
      last_value_distributor = value
      if (list_distributor[value] === undefined) return
      for (const data_distributor of list_distributor[value]) {
        const distributor = `${data_distributor.kode_distributor} / ${data_distributor.nama_distributor}`
        distributor_list.append(`<option value="${distributor}"></option>`)
      }
    }

    $("#distributor").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_distributor[value] === undefined) {
        delay_distributor = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_distributor < fetch_request_delay) return
          await fetch_distributor(value)
          fill_distributor_list(value)
        }, fetch_request_delay)
      } else {
        fill_distributor_list(value)
      }
    })

    $("#distributor").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_distributor").val("")
      setTimeout(() => {
        if (list_distributor[last_value_distributor] === undefined) return
        for (const data_distributor of list_distributor[last_value_distributor]) {
          const distributor = `${data_distributor.kode_distributor} / ${data_distributor.nama_distributor}`
          if (distributor.toLowerCase() === value) {
            const id_distributor = data_distributor.id_distributor

            $("#id_distributor").val(id_distributor)
            $("#distributor").val(distributor)
            return
          }
        }
      }, list_distributor[last_value_distributor] === undefined ? on_change_delay : 0)
    })
  }

  function init_barang_listener() {
    const fill_barang_list = (value) => {
      barang_list.empty()
      last_value_barang = value
      if (list_barang[value] === undefined) return
      for (const data_barang of list_barang[value]) {
        barang_list.append(`<option value="${data_barang.barang_detail}"></option>`)
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
      setTimeout(() => {
        if (list_barang[last_value_barang] === undefined) return
        for (const data_barang of list_barang[last_value_barang]) {
          const barang = `${data_barang.barang_detail}`
          if (barang.toLowerCase() === value) {
            $("#barang").val(barang)
            return
          }
        }
      }, list_barang[last_value_barang] === undefined ? on_change_delay : 0)
    })

    $("#barang, #qty, #add_barang").on("keydown", function(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        add_barang()  
      }
    })

    $("#add_barang").on("click", function() {
      add_barang()      
    })

    $("body").on("click", "a.delete_barang", function() {
      $(this).parent().parent().remove()
      $(".tb_input tbody tr").each(function(i) {
        $(this).find("td:first-child").html(`${i+1}`)
      })
    })
  }

  function init_reset_btn_listener() {
    $("#reset_btn").on("click", function() {
      $(".tb_input tbody").empty()
      $(".tb_input tbody").append(default_value_pesanan1)
      $(".tb_input tbody").children().each(function(i) {
        $(this).find("td:first-child").html(i+1)
      })
    })
  }

  // utils function
  function add_barang() {
    const barang = $("#barang").val().toLowerCase()
    const qty = parseInt($("#qty").val())
    if (list_barang[last_value_barang] === undefined) return
    for (const data_barang of list_barang[last_value_barang]) {
      const barang1 = `${data_barang.barang_detail}`
      if (barang1.toLowerCase() === barang) {
        if (isNaN(qty) || qty <= 0) {
          $("#qty").focus()
          return
        }
        const no = $(".tb_input tbody").children().length + 1
        const id_barang = data_barang.id_barang
        const pesanan1 = `${id_barang};${barang};${qty}`
        $(".tb_input tbody").append(`
          <tr>
            <td>${no}</td>
            <td>
              <input class="hidden" type="text" name="pesanan1[]" value="${pesanan1}" />
              ${barang1}
            </td>
            <td>${qty}</td>
            <td class="centered"><a href="#" class="delete_barang">Hapus</a></td>
          </tr>
        `)
        $("#barang, #qty").val("")
        $("#barang").focus()
        return
      }
    }
  }
})
