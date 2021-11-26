$(document).ready(() => {
  
  let list_distributor = {}
  const distributor_list = $("#distributor_list")
  let delay_distributor = new Date().getTime()
  let last_value_distributor = ""

  init()

  async function init() {
    fetch_distributor($("#kode_distributor").val().toLowerCase())
    init_kode_distributor_listener()
  }

  // fetch function
  async function fetch_distributor(filter = "") {
    const response = await fetch_request({url: `master/distributor/fetch?filter=${filter}`})
    list_distributor[filter] = response || undefined
  }

  // event function
  function init_kode_distributor_listener() {
    const fill_distributor_list = (value) => {
      distributor_list.empty()
      last_value_distributor = value
      if (list_distributor[value] === undefined) return
      for (const distributor of list_distributor[value]) {
        const kode_distributor = distributor.kode_distributor
        const nama_distributor = distributor.nama_distributor
        if (kode_distributor.toLowerCase().includes(value)) {
          distributor_list.append(`<option value="${kode_distributor}"></option>`)
        } else if (nama_distributor.toLowerCase().includes(value)) {
          distributor_list.append(`<option value="${nama_distributor}"></option>`)
        }
      }
    }

    $("#kode_distributor").on("focus keyup", function() {
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

    $("#kode_distributor").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_distributor").val("")
      setTimeout(() => {
        if (list_distributor[last_value_distributor] === undefined) return
        for (const distributor of list_distributor[last_value_distributor]) {
          const kode_distributor = distributor.kode_distributor
          const nama_distributor = distributor.nama_distributor
          if (kode_distributor.toLowerCase() === value || nama_distributor.toLowerCase() === value) {
            const id_distributor = distributor.id_distributor
            const alamat = distributor.alamat
            const no_hp = distributor.no_hp
            const email = distributor.email
            const keterangan = distributor.keterangan

            $("#id_distributor").val(id_distributor)
            $("#kode_distributor").val(kode_distributor.split("-")[1])
            $("#nama_distributor").val(nama_distributor)
            $("#alamat").val(alamat)
            $("#no_hp").val(no_hp)
            $("#email").val(email)
            $("#keterangan").val(keterangan)
            return
          }
        }
      }, list_distributor[last_value_distributor] === undefined ? on_change_delay : 0)
    })
  }

})
