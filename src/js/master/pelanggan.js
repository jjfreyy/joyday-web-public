$(document).ready(() => {
  let list_pelanggan = {}
  let list_agen = {}
  let list_propinsi = {}
  let list_kabupaten = {}
  let list_kecamatan = {}
  let list_kelurahan = {}
  const pelanggan_list = $("#pelanggan_list")
  const agen_list = $("#agen_list")
  const propinsi_list = $("#propinsi_list")
  const kabupaten_list = $("#kabupaten_list")
  const kecamatan_list = $("#kecamatan_list")
  const kelurahan_list = $("#kelurahan_list")
  let delay_pelanggan = delay_agen = delay_propinsi = delay_kabupaten = delay_kecamatan = delay_kelurahan = new Date().getTime()
  let last_value_pelanggan = last_value_agen = last_value_propinsi = last_value_kabupaten = last_value_kecamatan = last_value_kelurahan = ""
  const default_id_level = $("#id_level").val()

  init()

  async function init() {
    fetch_pelanggan($("#kode_pelanggan").val().toLowerCase())
    init_reset_listener()
    init_kode_pelanggan_listener()
    init_agen_listener()
    init_id_level_listener()
    init_nama_propinsi_listener()
    init_nama_kabupaten_listener()
    init_nama_kecamatan_listener()
    init_nama_kelurahan_listener()
  }

  // fetch function
  async function fetch_pelanggan(filter = "") {
    const response = await fetch_request({url: `master/pelanggan/fetch?type=pelanggan&filter=${filter}`})
    list_pelanggan[filter] = response || undefined
  }

  async function fetch_agen(filter =  "") {
    const response = await fetch_request({url: `master/pelanggan/fetch?type=agen&filter=${filter}`})
    list_agen[filter] = response || undefined
  }

  async function fetch_propinsi(filter = "") {
    const response = await fetch_request({url: `master/pelanggan/fetch?type=propinsi&filter=${filter}`})
    list_propinsi[filter] = response || undefined
  }
  
  async function fetch_kabupaten(filter = "") {
    const response = await fetch_request({url: `master/pelanggan/fetch?type=kabupaten&filter=${filter.replace("#", "%23")}`})
    list_kabupaten[filter] = response || undefined
  }

  async function fetch_kecamatan(filter = "") {
    const response = await fetch_request({url: `master/pelanggan/fetch?type=kecamatan&filter=${filter.replace("#", "%23")}`})
    list_kecamatan[filter] = response || undefined
  }

  async function fetch_kelurahan(filter = "") {
    const response = await fetch_request({url: `master/pelanggan/fetch?type=kelurahan&filter=${filter.replace("#", "%23")}`})
    list_kelurahan[filter] = response || undefined
  }

  // event function
  function init_reset_listener() {
    $("#reset_btn").on("click", function() {
      if (default_id_level === "1") {
        $("#name_box_agen").removeClass("hidden")
      } else {
        $("#name_box_agen").addClass("hidden")
      }
    })
  }

  function init_kode_pelanggan_listener() {
    const fill_pelanggan_list = (value) => {
      pelanggan_list.empty()
      last_value_pelanggan = value
      if (list_pelanggan[value] === undefined) return
      for (const pelanggan of list_pelanggan[value]) {
        const kode_pelanggan = pelanggan.kode_pelanggan
        const nama_pelanggan = pelanggan.nama_pelanggan
        if (kode_pelanggan.toLowerCase().includes(value)) {
          pelanggan_list.append(`<option value="${kode_pelanggan}"></option>`)
        } else if (nama_pelanggan.toLowerCase().includes(value)) {
          pelanggan_list.append(`<option value="${nama_pelanggan}"></option>`)
        }
      }
    }

    $("#kode_pelanggan").on("focus keyup", function() {
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

    $("#kode_pelanggan").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_pelanggan").val("")
      setTimeout(() => {
        if (list_pelanggan[last_value_pelanggan] === undefined) return
        for (const pelanggan of list_pelanggan[last_value_pelanggan]) {
          const id_pelanggan = pelanggan.id_pelanggan
          const kode_pelanggan = pelanggan.kode_pelanggan
          const nama_pelanggan = pelanggan.nama_pelanggan
          if (kode_pelanggan.toLowerCase() === value || nama_pelanggan.toLowerCase() === value) {
            const id_level = pelanggan.id_level
            const id_agen = pelanggan.id_agen
            const kode_agen = pelanggan.kode_agen
            const nama_agen = pelanggan.nama_agen
            const no_identitas = pelanggan.no_identitas
            
            const no_hp1 = pelanggan.no_hp1
            const no_hp2 = pelanggan.no_hp2
            const email = pelanggan.email
            const id_propinsi = pelanggan.id_propinsi
            const nama_propinsi = pelanggan.nama_propinsi
            
            const id_kabupaten = pelanggan.id_kabupaten
            const nama_kabupaten = pelanggan.nama_kabupaten
            const id_kecamatan = pelanggan.id_kecamatan
            const nama_kecamatan = pelanggan.nama_kecamatan
            const id_kelurahan = pelanggan.id_kelurahan
            
            const nama_kelurahan = pelanggan.nama_kelurahan
            const alamat = pelanggan.alamat
            const kode_pos = pelanggan.kode_pos
            const keterangan = pelanggan.keterangan
            const daya_listrik = pelanggan.daya_listrik
            
            const latitude = pelanggan.latitude
            const longitude = pelanggan.longitude
            const nama_kerabat = pelanggan.nama_kerabat
            const no_identitas_kerabat = pelanggan.no_identitas_kerabat
            const no_hp_kerabat = pelanggan.no_hp_kerabat
            
            const alamat_kerabat = pelanggan.alamat_kerabat
            const hubungan = pelanggan.hubungan

            $("#id_level").val(id_level)
            $("#id_agen").val(id_agen)
            $("#agen").val(`${kode_agen} / ${nama_agen}`)
            $("#id_pelanggan").val(id_pelanggan)
            $("#kode_pelanggan").val(kode_pelanggan.split("-")[1])
            
            $("#nama_pelanggan").val(nama_pelanggan)
            $("#no_identitas").val(no_identitas)
            $("#no_hp1").val(no_hp1)
            $("#no_hp2").val(no_hp2)
            $("#email").val(email)
            
            $("#id_propinsi").val(id_propinsi)
            $("#nama_propinsi").val(nama_propinsi)
            $("#id_kabupaten").val(id_kabupaten)
            $("#nama_kabupaten").val(nama_kabupaten)
            $("#id_kecamatan").val(id_kecamatan)
            
            $("#nama_kecamatan").val(nama_kecamatan)
            $("#id_kelurahan").val(id_kelurahan)
            $("#nama_kelurahan").val(nama_kelurahan)
            $("#alamat").val(alamat)
            $("#kode_pos").val(kode_pos)
            
            $("#keterangan").val(keterangan)
            $("#daya_listrik").val(daya_listrik)
            $("#latitude").val(latitude)
            $("#longitude").val(longitude)
            $("#nama_kerabat").val(nama_kerabat)
            
            $("#no_identitas_kerabat").val(no_identitas_kerabat)
            $("#no_hp_kerabat").val(no_hp_kerabat)
            $("#alamat_kerabat").val(alamat_kerabat)
            $("#hubungan").val(hubungan)

            toggle_level()
            return
          }
        }
      }, list_pelanggan[last_value_pelanggan] === undefined ? on_change_delay : 0)
    })
  }

  function init_agen_listener() {
    const fill_agen_list = (value) => {
      agen_list.empty()
      const id_pelanggan = $("#id_pelanggan").val()
      last_value_agen = value
      if (list_agen[value] === undefined) return
      for (const agen of list_agen[value]) {
        const id_agen = agen.id_agen
        const kode_agen = agen.kode_agen
        const nama_agen = agen.nama_agen
        if (id_pelanggan === id_agen) continue
        agen_list.append(`<option value="${kode_agen} / ${nama_agen}"></option>`)
      }
    }

    $("#agen").on("focus keyup", function() {
      const value = $(this).val().toLowerCase()
      if (list_agen[value] === undefined) {
        delay_agen = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_agen < fetch_request_delay) return
          await fetch_agen(value)
          fill_agen_list(value)
        }, fetch_request_delay)
      } else {
        fill_agen_list(value)
      }
    })

    const filter_agen_list = (value) => {
      if (list_agen[last_value_agen] === undefined) return
      const id_pelanggan = $("#id_pelanggan").val()
      for (let data_agen of list_agen[last_value_agen]) {
        agen = `${data_agen.kode_agen} / ${data_agen.nama_agen}`
        if (agen.toLowerCase() === value) {
          const id_agen = data_agen.id_agen
          if (id_pelanggan === id_agen) {
            $("#agen").val("")
            return
          }
          $("#id_agen").val(id_agen)
          $("#agen").val(agen)
          return
        }
      }
    }

    $("#agen").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_agen").val("")
      setTimeout(() => {
        filter_agen_list(value)
      }, list_agen[last_value_agen] === undefined ? on_change_delay : 0)
    })
  }

  function init_id_level_listener() {
    $("#id_level").on("change", function() {
      $("#id_agen, #agen").val("")
      toggle_level()
    })
  }

  function init_nama_propinsi_listener() {
    const fill_propinsi_list = (value) => {
      propinsi_list.empty()
      last_value_propinsi = value
      if (list_propinsi[value] === undefined) return
      for (const propinsi of list_propinsi[value]) {
        const nama_propinsi = propinsi.nama_propinsi
        propinsi_list.append(`<option value="${nama_propinsi}"></option>`)
      }
    }

    $("#nama_propinsi").on("focus keyup", function() {
        const value = $(this).val().toLowerCase()
        if (list_propinsi[value] === undefined) {
            delay_propinsi = new Date().getTime()
            setTimeout(async () => {
              const current_timestamp = new Date().getTime()
              if (current_timestamp - delay_propinsi < fetch_request_delay) return
              await fetch_propinsi(value)
              fill_propinsi_list(value)
            }, fetch_request_delay)
        } else {
            fill_propinsi_list(value)
        }
    })

    const filter_propinsi_list = (value) => {
      if (list_propinsi[last_value_propinsi] === undefined) return
      for (const propinsi of list_propinsi[last_value_propinsi]) {
        const nama_propinsi = propinsi.nama_propinsi
        if (nama_propinsi.toLowerCase() === value) {
          const id_propinsi = propinsi.id_propinsi
              
          $("#id_propinsi").val(id_propinsi)
          $("#nama_propinsi").val(nama_propinsi)
          return
        }
      }
    }

    $("#nama_propinsi").on("change", function() {
        const value = $(this).val().toLowerCase()
        $("#id_propinsi").val("")
        $("#id_kabupaten").val("")
        $("#nama_kabupaten").val("")
        kabupaten_list.empty()
        $("#id_kecamatan").val("")
        $("#nama_kecamatan").val("")
        kecamatan_list.empty()
        $("#id_kelurahan").val("")
        $("#nama_kelurahan").val("")
        kelurahan_list.empty()
        setTimeout(() => {
          filter_propinsi_list(value)            
        }, list_propinsi[last_value_propinsi] === undefined ? on_change_delay : 0)
    })
  }

  function init_nama_kabupaten_listener() {
    const fill_kabupaten_list = (value) => {
      kabupaten_list.empty()
      last_value_kabupaten = value
      if (list_kabupaten[value] === undefined) return
      for (const kabupaten of list_kabupaten[value]) {
        const nama_kabupaten = kabupaten.nama_kabupaten
        kabupaten_list.append(`<option value="${nama_kabupaten}"></option>`)
      }
    }

    $("#nama_kabupaten").on("focus keyup", function() {
      const id_propinsi = $("#id_propinsi").val()
      if (id_propinsi === "") return
      const value = `${id_propinsi}#${$(this).val().toLowerCase()}`
      if (list_kabupaten[value] === undefined) {
        delay_kabupaten = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_kabupaten < fetch_request_delay) return
          await fetch_kabupaten(value)
          fill_kabupaten_list(value)
        }, fetch_request_delay)
      } else {
        fill_kabupaten_list(value)
      }
    })

    const filter_kabupaten_list = (value) => {
      if (list_kabupaten[last_value_kabupaten] === undefined) return
      for (const kabupaten of list_kabupaten[last_value_kabupaten]) {
        const nama_kabupaten = kabupaten.nama_kabupaten
        if (nama_kabupaten.toLowerCase() === value) {
          const id_kabupaten = kabupaten.id_kabupaten
              
          $("#id_kabupaten").val(id_kabupaten)
          $("#nama_kabupaten").val(nama_kabupaten)
          return
        }
      }
    }

    $("#nama_kabupaten").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_kabupaten").val("")
      $("#id_kecamatan").val("")
      $("#nama_kecamatan").val("")
      kecamatan_list.empty()
      $("#id_kelurahan").val("")
      $("#nama_kelurahan").val("")
      kelurahan_list.empty()
      setTimeout(() => {
        filter_kabupaten_list(value)
      }, list_kabupaten[last_value_kabupaten] === undefined ? on_change_delay : 0)
    })
  }
  
  function init_nama_kecamatan_listener() {
    const fill_kecamatan_list = (value) => {
      kecamatan_list.empty()
      last_value_kecamatan = value
      if (list_kecamatan[value] == undefined) return
      for (const kecamatan of list_kecamatan[value]) {
        const nama_kecamatan = kecamatan.nama_kecamatan
        kecamatan_list.append(`<option value="${nama_kecamatan}"></option>`)
      }
    }

    $("#nama_kecamatan").on("focus keyup", function() {
      const id_kabupaten = $("#id_kabupaten").val()
      if (id_kabupaten === "") return
      const value = `${id_kabupaten}#${$(this).val().toLowerCase()}`
      if (list_kecamatan[value] === undefined) {
        delay_kecamatan = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_kecamatan < fetch_request_delay) return
          await fetch_kecamatan(value)
          fill_kecamatan_list(value)
        }, fetch_request_delay)
      } else {
        fill_kecamatan_list(value)
      }
    })

    const filter_kecamatan_list = (value) => {
      if (list_kecamatan[last_value_kecamatan] === undefined) return
      for (const kecamatan of list_kecamatan[last_value_kecamatan]) {
        const nama_kecamatan = kecamatan.nama_kecamatan
        if (nama_kecamatan.toLowerCase() === value) {
          const id_kecamatan = kecamatan.id_kecamatan
              
          $("#id_kecamatan").val(id_kecamatan)
          $("#nama_kecamatan").val(nama_kecamatan)
          return
        }
      }
    }

    $("#nama_kecamatan").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_kecamatan").val("")
      $("#id_kelurahan").val("")
      $("#nama_kelurahan").val("")
      kelurahan_list.empty()
      setTimeout(() => {
        filter_kecamatan_list(value)
      }, list_kecamatan[last_value_kecamatan] === undefined ? on_change_delay : 0)
    })
  }

  function init_nama_kelurahan_listener() {
    const fill_kelurahan_list = (value) => {
      kelurahan_list.empty()
      last_value_kelurahan = value
      if (list_kelurahan[value] === undefined) return
      for (const kelurahan of list_kelurahan[value]) {
        const nama_kelurahan = kelurahan.nama_kelurahan
        kelurahan_list.append(`<option value="${nama_kelurahan}"></option>`)
      }
    }

    $("#nama_kelurahan").on("focus keyup", function() {
      const id_kecamatan = $("#id_kecamatan").val()
      if (id_kecamatan === "") return
      const value = `${id_kecamatan}#${$(this).val().toLowerCase()}`
      if (list_kelurahan[value] === undefined) {
        delay_kelurahan = new Date().getTime()
        setTimeout(async () => {
          const current_timestamp = new Date().getTime()
          if (current_timestamp - delay_kelurahan < fetch_request_delay) return
          await fetch_kelurahan(value)
          fill_kelurahan_list(value)
        }, fetch_request_delay)
      } else {
        fill_kelurahan_list(value)
      }
    })

    const filter_kelurahan_list = (value) => {
      if (list_kelurahan[last_value_kelurahan] === undefined) return
      for (const kelurahan of list_kelurahan[last_value_kelurahan]) {
        const nama_kelurahan = kelurahan.nama_kelurahan
        if (nama_kelurahan.toLowerCase() === value) {
          const id_kelurahan = kelurahan.id_kelurahan
              
          $("#id_kelurahan").val(id_kelurahan)
          $("#nama_kelurahan").val(nama_kelurahan)
          return
        }
      }
    }

    $("#nama_kelurahan").on("change", function() {
      const value = $(this).val().toLowerCase()
      $("#id_kelurahan").val("")
      setTimeout(() => {
       filter_kelurahan_list(value) 
      }, list_kelurahan[last_value_kelurahan] === undefined ? on_change_delay : 0);
    })
  }

})

// utils function
function toggle_level() {
  switch ($("#id_level").val()) {
    case "1":
      $("#kode_pelanggan").prev().val("RET")
      $("#name_box_agen").removeClass("hidden")
      // $("#agen").prop("required", true)
      break
    case "2":
      $("#kode_pelanggan").prev().val("AGE")
      $("#name_box_agen").addClass("hidden")
      $("#agen").prop("required", false)
      break
  }
}
