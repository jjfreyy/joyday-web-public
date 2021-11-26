$(document).ready(function() {
  $("input.i_login").filter("#username").next().css("bottom", "20px");
  $("input.i_login").on("focus", function() {
    $(this).next().css("bottom", "20px");
  }).on("blur", function() {
    if ($(this).val() === "") $(this).next().css("bottom", "5px");
  });

  $('.tv_nav_icon').on('click', function() {
    var ul = $(this).parent().next();
    if (ul.css('display') === 'none') {
      $(this).css('background', 'url(' +src_base_url+ 'img/minus.ico) center / 50% no-repeat');
      ul.toggle('slow');
    } else {
      $(this).css('background', 'url(' +src_base_url+ 'img/plus.ico) center / 50% no-repeat');
      ul.toggle('slow');
    }
  });

  $('.link_header').on('click', function() {
    var current_index = $(this).parent().index();
    $('#nav_menu').children().each(function(index) {
      if (index !== current_index && $(this).hasClass("submenu_title")) {
        // $(this).children().children().first().css('background', 'url(' +src_base_url+ 'img/arrow-down2.png) center / contain no-repeat');
        $(this).children().filter("ul.submenu").css('display', 'none');
      }
    });
    
    var span = $(this).children().last();
    var submenu = $(this).next();
    if (submenu.css('display') === 'grid') {
      // span.css('background', 'url(' +src_base_url+ 'img/arrow-down2.png) center / contain no-repeat');
      submenu.css('display', 'none');
    } else {
      // span.css('background', 'url(' +src_base_url+ 'img/arrow-up2.png) center / contain no-repeat');
      submenu.css('display', 'grid');
    }
  });

  $(".header_navbar .sub1").each(function(i) {
    var width = $(this).css("width");
    $(this).find(".sub2").each(function(j) {
      $(this).css("left", width);
    });
  });

  $(".header_navbar .sub_list1").on("mouseover", function() {
    var sub2 = $(this).find(".sub2");
    if (sub2.length > 0) {
      sub2.css("display", "grid");
    }
  });

  $(".header_navbar .sub_list1").on("mouseleave", function() {
    var sub2 = $(this).find(".sub2");
    if (sub2.length > 0) {
      sub2.css("display", "none");
    }
  });

  $('.link_aside').on('click', function() {
    span = $(this).children().last();
    submenu = $(this).next();

    if (!submenu.hasClass("submenu")) return;

    if (submenu.css('display') === 'grid') {
      span.css('background', 'url(' +src_base_url+ 'img/arrow-down2.png) center / contain no-repeat');
      submenu.slideUp();
      submenu.removeClass('active');
    } else {
      span.css('background', 'url(' +src_base_url+ 'img/arrow-up2.png) center / contain no-repeat');
      submenu.slideDown(400, function() {
        $(this).css("display", "grid");
      });
      submenu.addClass('active');
    }
  });

  $("body").on("click", ".accordion", function() {
    const container = $(this).parent().children().last()
    const nav_icon = $(this).children().last()
    if (container.css("display") === "none") {
      nav_icon.css("background", "url(" +src_base_url+ "img/arrow-up2.png) center / contain no-repeat");
      container.slideDown();
    } else { 
      nav_icon.css("background", "url(" +src_base_url+ "img/arrow-down2.png) center / contain no-repeat");
      container.slideUp() 
    };
  });

  $('section.main').height($(window).height() - $('.header_navbar').height());

  $(window).on('resize', function() {
    $('div.container, div.sistem1_container').height($(window).height() - $('.header_navbar').height());
    $('section.main').height($(window).height() - $('.header_navbar').height());
    $('#nav_akun_utama').width($(window).width() <= 650 ? '100%' : '50%');
  });
});

function create_paginations(page, total_data, divider, addon = "") {
  var pagination = $("div.pagination")
  var total_page = Math.ceil(total_data / divider) - 1
  // $("div.pagination").css("background-color", "var(--color7)")
  if (total_page < 1) {
    // $("div.pagination").css("background-color", "transparent")
    return
  }

  var pagination_links = "";
  if (page > 0) {
      pagination_links += "<a href='#' class='link_pagination' data-pagination='0" +addon+ "'>Pertama</a>";
  }
  for (var i = 3; i >= 1; i--) {
      if ((page - i) >= 0) {
          pagination_links += "<a href='#' class='link_pagination' data-pagination='" +((page - i) + addon)+ "'>" +(page - i + 1)+ "</a>";
      }
  }
  pagination_links += "<a href='#' class='active link_pagination' data-pagination='" +(page + addon)+ "'>" +(parseInt(page) + 1)+ "</a>";
  for(var i = 1; i <= 3; i++) {
      if ((page + i) <= total_page) {
          pagination_links += "<a href='#' class='link_pagination' data-pagination='" +((page + i) + addon)+ "'>" +(page + i + 1)+ "</a>";
      } else {
          break;
      }
  }
  if (page < total_page) {
      pagination_links += "<a href='#' class='link_pagination' data-pagination='" +(total_page + addon)+ "'>Terakhir</a>";
  }
  pagination.append(pagination_links);
}

function slide_menu_close() {
  $('#slide_menu').css('width', '0');
  $('.link_aside').css('margin-left', '-250px');
}

function slide_menu_open() {
  $('#slide_menu').css('width', '250px');
  $('.link_aside').css('margin-left', '0');
}
