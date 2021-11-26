const fetch_request_delay = 500
const on_change_delay = fetch_request_delay * 2
const display_per_page = 15

function convert_currency_tonumber(currency) {
  currency = currency.replace(/[.]/g, '');
  currency = currency.replace(',', '.');
  return currency;
}

function convert_number_tocurrency(number) {
  if (number === '-') {
    return number;
  }

  var is_minus = number < 0;
  if (is_minus) number *= -1;
  
  var number_arr = number.toString().split('.');
  var remainder = number_arr[0].length % 3;
  var currency = number_arr[0].substr(0, remainder);
  var thousand = number_arr[0].substr(remainder).match(/\d{3}/gi);
  
  if (thousand != null && thousand.length > 0) {
    separator = remainder > 0 ? '.' : '';
    currency += separator + thousand.join('.');
  }
  
  if (number_arr[1] === "") {
    currency += ",";
  } else if (!isNaN(number_arr[1]) && number_arr[1] !== "00") {
    currency += "," +number_arr[1].substr(0, 2);
  }
  
  currency = is_minus ? '-' + currency : currency;

  return currency;
}

async function fetch_request({ url, method = "get", data }) {
  try {
    const response = await fetch(`${base_url}${url}`, {
      method: method,
      headers: { "X-Requested-With": "XMLHttpRequest", "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
    if (response.status === 500) throw("Not valid response")
    return await response.json()
  } catch (e) {
    console.log(e)
    return false
  }
}

function format_date({ date: val, date_separator = "-", include_time = false }) {
  if (val === "-" || is_empty(val)) return "-";
  if (val.toString().split(" ").length == 2) {
    val = val.toString().split(" ").join("T");
  }
  
  var val = new Date(Date.parse(val));
  var fdate = ("00" +val.getDate()).substr(-2)+date_separator+("00" +(val.getMonth() + 1)).substr(-2)+date_separator+val.getFullYear();
  if (include_time) fdate += ", " +val.toLocaleTimeString();
  return fdate;
}

function get_date_filter() {
  var date1 = "";
  var date2 = "";
  if ($("#tanggal_check").prop("checked")) {
      date1 = $("#tanggal1").val();
      date2 = $("#tanggal2").val();
  } else if ($("#bulan_check").prop("checked")) {
      var date = new Date($("#bulan").val().split("-")[0], $("#bulan").val().split("-")[1], 0);
      date1 = $("#bulan").val()+ "-01";
      date2 = $("#bulan").val()+ "-" +("0"+date.getDate()).substr(-2);
  }
  return {date1: date1, date2: date2};
}

function get_responsive_style(element, selector, data) {
  switch (element) {
    case "table":
      var table_width = 0;
      var cols = Array();
      $(selector+ " col").each(function(i) {
        table_width += parseInt($(this).prop("width").replace("px", ""));
        cols.push($(selector+ " th").eq(i).html());
      });
      var cols_length = cols.length;

      var style = " \
      <style media='only screen and (max-width:" +(table_width)+ "px)'> \
        " +selector+ " { table-layout: auto; } \
        " +selector+ " thead { display: none; } \
        " +selector+ " tr { \
          display: grid; \
          grid-template-columns: " +(data.template_columns == undefined ? "1fr" : data.template_columns)+ "; \
          grid-auto-rows: minmax(30px, max-content); \
          border-top: 1px solid var(--table-row-border-color); \
        } \
        " +selector+ " tr td:first-child { display:none } \
        " +selector+ " tr td::before { \
          position: absolute; \
          top: 0; \
          left: 0; \
          padding-left: 5px; \
          font-weight: bold; \
          text-decoration:underline; \
        } \
        " +selector+ " tr td { \
          position: relative; \
          padding-left: " +data.padding_left+ "px; \
          height: fit-content; \
          min-height: 30px; \
        } \
        " +selector+ " tr td select { \
          width: 300px; \
        }";

        for(var i = 0; i < cols_length; i++) {
          style += selector+ " tr td:nth-child(" +(i+1)+ ")::before{content:'" +cols[i]+ "'}";
        }

        style += " \
        @media only screen and (max-width: 495px) { \
          " +selector+ " tr { grid-template-columns: 1fr; } \
          " +selector+ " tr td select { width: 200px; } \
        } \
        </style> \
        " +(data.style == undefined ? "" : data.style);
        break;
    case "custom":
        var style = "<style>" +data.style+ "</style>";
        break;
  }

  $("body").append(style);
}

function if_empty_then({ value, type = "string", allow_zero = true, assign = "-" }) {
  if (is_empty(value)) return assign;
  if (!allow_zero && value == 0) return assign;

  switch (type) {
    case "string": return value;
    case "date": return format_date({date: value});
    case "number": return convert_number_tocurrency(value);
  }
  return value; 
}

function is_empty(value) {
  if (value === null || value === undefined || value === "") return true;
  return false;
}

function is_nan(number) {
  return number === "" || isNaN(number);
}

function init_date_filter() {
  $("input[name='date_search_method']").on("change", function() {
    if ($(this).prop("id") === "tanggal_check") {
        $("#tanggal1, #tanggal2").prop("disabled", false);
        $("#bulan").prop("disabled", true);
    } else if ($(this).prop("id") === "bulan_check") {
        $("#tanggal1, #tanggal2").prop("disabled", true);
        $("#bulan").prop("disabled", false);
    }
  });
}

function prevent_default_on_enter() {
  for (const id_element of arguments) {
    $(`#${id_element}`).on("keydown", function(e) {
      if (e.keyCode === 13) e.preventDefault()
    })
  }
}
