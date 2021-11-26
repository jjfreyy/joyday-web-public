$(document).ready(function() {
  init()

  function init() {
    init_date_filter()

    $("#tanggal1, #tanggal2, #filter").on("keypress", function(e) {
      if (e.keyCode === 13) print_laporan()
    });

    $(".search_icon").on("click", function() {
        print_laporan()
    });
  }

  function print_laporan() {
    const date = get_date_filter()
    const tipe = $("#tipe").val()
    const filter = $("#filter").val()
    const url = `${base_url}laporan/barang_masuk/print?d1=${date.date1}&d2=${date.date2}&t=${tipe}&f=${filter}`
    window.open(url, "_blank")
  }
});