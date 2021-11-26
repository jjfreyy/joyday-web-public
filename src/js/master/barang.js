$(document).ready(() => {
    
    let list_barang = {}
    let list_brand = {}
    let list_tipe = {}
    const barang_list = $("#barang_list")
    const brand_list = $("#brand_list")
    const tipe_list = $("#tipe_list")
    let delay_barang = delay_brand = delay_tipe = new Date().getTime()
    let last_value_barang = last_value_brand = last_value_tipe = ""

    init()

    async function init() {
        fetch_barang($("#kode_barang").val().toLowerCase())
        init_kode_barang_listener()
        init_nama_brand_listener()
        init_nama_tipe_listener()
    }
    
    // fetch function
    async function fetch_barang(filter = "") {
        const response = await fetch_request({url: `master/barang/fetch?type=barang&filter=${filter}`})
        list_barang[filter] = response || undefined
    }
    
    async function fetch_brand(filter = "") {
        const response = await fetch_request({url: `master/barang/fetch?type=brand&filter=${filter}`})
        list_brand[filter] = response || undefined
    }

    async function fetch_tipe(filter = "") {
        const response = await fetch_request({url: `master/barang/fetch?type=tipe&filter=${filter}`})
        list_tipe[filter] = response || undefined
    }

    // event function
    function init_kode_barang_listener() {
        const fill_barang_list = (value) => {
            barang_list.empty()
            last_value_barang = value
            if (list_barang[value] === undefined) return
            for (const barang of list_barang[value]) {
                const kode_barang = barang.kode_barang
                // const nama_barang = barang.nama_barang
                if (kode_barang.toLowerCase().includes(value)) {
                    barang_list.append(`<option value="${kode_barang}"></option>`)
                } 
                // else if (nama_barang.toLowerCase().includes(value)) {
                //     barang_list.append(`<option value="${nama_barang}"></option>`)
                // }
            }
        }

        $("#kode_barang").on("focus keyup", function() {
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
        
        $("#kode_barang").on("change", function() {
            const value = $(this).val().toLowerCase()
            $("#id_barang").val("")
            setTimeout(() => {
                if (list_barang[last_value_barang] === undefined) return
                for (const barang of list_barang[last_value_barang]) {
                    const kode_barang = barang.kode_barang
                    // const nama_barang = barang.nama_barang
                    if (kode_barang.toLowerCase() === value) {
                        const id_barang = barang.id_barang
                        const id_brand = barang.id_brand
                        const nama_brand = barang.nama_brand
                        const id_tipe = barang.id_tipe
                        const nama_tipe = barang.nama_tipe
                        const ukuran = barang.ukuran
                        const keterangan = barang.keterangan
                        
                        $("#id_barang").val(id_barang)
                        $("#kode_barang").val(kode_barang.split("-")[1])
                        // $("#nama_barang").val(nama_barang)
                        $("#id_brand").val(id_brand)
                        $("#nama_brand").val(nama_brand)
                        $("#id_tipe").val(id_tipe)
                        $("#nama_tipe").val(nama_tipe)
                        $("#ukuran").val(ukuran)
                        $("#keterangan").val(keterangan)
                        return
                    }
                }
            }, list_barang[last_value_barang] === undefined ? on_change_delay : 0)
        })
    }

    function init_nama_brand_listener() {
        const fill_brand_list = (value) => {
            brand_list.empty()
            last_value_brand = value
            if (list_brand[value] === undefined) return
            for (const brand of list_brand[value]) {
                const nama_brand = brand.nama_brand
                brand_list.append(`<option value="${nama_brand}"></option>`)
            }
        }

        $("#nama_brand").on("focus keyup", function() {
            const value = $(this).val().toLowerCase()
            if (list_brand[value] === undefined) {
                delay_brand = new Date().getTime()
                setTimeout(async () => {
                    const current_timestamp = new Date().getTime()
                    if (current_timestamp - delay_brand < fetch_request_delay) return
                    await fetch_brand(value)
                    fill_brand_list(value)
                }, fetch_request_delay)
            } else {
                fill_brand_list(value)
            }
        })

        $("#nama_brand").on("change", function() {
            const value = $(this).val().toLowerCase()
            $("#id_brand").val("")
            setTimeout(() => {
                if (list_brand[last_value_brand] === undefined) return
                for (const brand of list_brand[last_value_brand]) {
                    const nama_brand = brand.nama_brand
                    if (nama_brand.toLowerCase() === value) {
                        const id_brand = brand.id_brand
                        
                        $("#id_brand").val(id_brand)
                        $("#nama_brand").val(nama_brand)
                        return
                    }
                }
            }, list_brand[last_value_brand] === undefined ? on_change_delay : 0)
        })
    }
    
    function init_nama_tipe_listener() {
        const fill_tipe_list = (value) => {
            tipe_list.empty()
            last_value_tipe = value
            if (list_tipe[value] === undefined) return
            for (const tipe of list_tipe[value]) {
                const nama_tipe = tipe.nama_tipe
                tipe_list.append(`<option value="${nama_tipe}"></option>`)
            }
        }

        $("#nama_tipe").on("focus keyup", function() {
            const value = $(this).val().toLowerCase()
            if (list_tipe[value] === undefined) {
                delay_tipe = new Date().getTime()
                setTimeout(async () => {
                    const current_timestamp = new Date().getTime()
                    if (current_timestamp - delay_tipe < fetch_request_delay) return
                    await fetch_tipe(value)
                    fill_tipe_list(value)
                }, fetch_request_delay)
            } else {
                fill_tipe_list(value)
            }
        })

        $("#nama_tipe").on("change", function() {
            const value = $(this).val().toLowerCase()
            $("#id_tipe").val("")
            setTimeout(() => {
                if (list_tipe[last_value_tipe] === undefined) return
                for (const tipe of list_tipe[last_value_tipe]) {
                    const nama_tipe = tipe.nama_tipe
                    if (nama_tipe.toLowerCase() === value) {
                        const id_tipe = tipe.id_tipe
    
                        $("#id_tipe").val(id_tipe)
                        $("#nama_tipe").val(nama_tipe)
                        return
                    }
                }      
            }, list_tipe[last_value_tipe] === undefined ? on_change_delay : 0)
        })
    }
})
