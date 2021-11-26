$(document).ready(() => {
  
  let list_gudang = {}
  let list_kepala_gudang = {}
  const gudang_list = $("#gudang_list")
  const kepala_gudang_list = $("#kepala_gudang_list")
  let delay_gudang = delay_kepala_gudang = new Date().getTime()
  let last_value_gudang = last_value_kepala_gudang = ""

  init()

  async function init() {
    fetch_gudang($("#kode_gudang").val().toLowerCase())
    init_kode_gudang_listener()
    init_kepala_gudang_listener()
  }

  // fetch function
  async function fetch_gudang(filter = "") {
    const response = await fetch_request({url: `master/gudang/fetch?type=gudang&filter=${filter}`})
    list_gudang[filter] = response || undefined
  }

  async function fetch_kepala_gudang(filter = "") {
    const response = await fetch_request({url: `master/gudang/fetch?type=kepala_gudang&filter${filter}`})
    list_kepala_gudang[filter] = response || undefined
  }

  // event function
  function init_kode_gudang_listener() {
    const fill_gudang_list = (value) => {
      gudang_list.empty()
      last_value_gudang = value
      if (list_gudang[value] === undefined) return
      for (const gudang of list_gudang[value]) {
        const kode_gudang = gudang.kode_gudang
        const nama_gudang = gudang.nama_gudang
        if (kode_gudang.toLowerCase().includes(value)) {
          gudang_list.append(`<option value="${kode_gudang}"></option>`)
        } else if (nama_gudang.toLowerCase().includes(value)) {
          gudang_list.append(`<option value="${nama_gudang}"></option>`)
        }
      }
    }

    $("#kode_gudang").on("focus keyup", function() {
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

    $("#kode_gudang").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_gudang").val("")
      setTimeout(() => {
        if (list_gudang[last_value_gudang] === undefined) return
        for (const gudang of list_gudang[last_value_gudang]) {
          const kode_gudang = gudang.kode_gudang
          const nama_gudang = gudang.nama_gudang
          if (kode_gudang.toLowerCase() === value || nama_gudang.toLowerCase() === value) {
            const id_gudang = gudang.id_gudang
            const id_kepala_gudang = gudang.id_kepala_gudang
            const kepala_gudang = id_kepala_gudang === null ? "" : `${gudang.kode_kepala_gudang} / ${gudang.nama_kepala_gudang}`
            const keterangan = gudang.keterangan

            $("#id_gudang").val(id_gudang)
            $("#id_kepala_gudang").val(id_kepala_gudang)
            $("#kepala_gudang").val(kepala_gudang)
            $("#kode_gudang").val(kode_gudang.split("-")[1])
            $("#nama_gudang").val(nama_gudang)
            $("#keterangan").val(keterangan)
            return
          }
        }
      }, list_gudang[last_value_gudang] === undefined ? on_change_delay : 0)
    })
  }

  function init_kepala_gudang_listener() {
    const fill_kepala_gudang_list = (value) => {
      kepala_gudang_list.empty()
      last_value_kepala_gudang = value
      if (list_kepala_gudang[value] === undefined) return
      for (const data_kepala_gudang of list_kepala_gudang[value]) {
        const kepala_gudang = `${data_kepala_gudang.kode_kepala_gudang} / ${data_kepala_gudang.nama_kepala_gudang}`
        kepala_gudang_list.append(`<option value="${kepala_gudang}"></option>`)
      }
    }

    $("#kepala_gudang").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_kepala_gudang[value] === undefined) {
        delay_kepala_gudang = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_kepala_gudang < fetch_request_delay) return
          await fetch_kepala_gudang(value)
          fill_kepala_gudang_list(value)
        }, fetch_request_delay)
      } else {
        fill_kepala_gudang_list(value)
      }
    })

    $("#kepala_gudang").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_kepala_gudang").val("")
      setTimeout(() => {
        if (list_kepala_gudang[last_value_kepala_gudang] === undefined) return
        for (const data_kepala_gudang of list_kepala_gudang[last_value_kepala_gudang]) {
          const kepala_gudang = `${data_kepala_gudang.kode_kepala_gudang} / ${data_kepala_gudang.nama_kepala_gudang}`
          if (kepala_gudang.toLowerCase() === value) {
            const id_kepala_gudang = data_kepala_gudang.id_kepala_gudang

            $("#id_kepala_gudang").val(id_kepala_gudang)
            $("#kepala_gudang").val(kepala_gudang)
            return
          }
        }
      }, list_kepala_gudang[last_value_kepala_gudang] === undefined ? on_change_delay : 0)
    })
  }

})
