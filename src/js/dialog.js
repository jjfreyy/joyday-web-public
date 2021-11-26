$(document).ready(function() {
    $("body").on("click", "a.confirm_no, a.input_no", function() {
        $(this).parent().parent().slideUp(400, function() {
            $(this).remove();
        });
    })
    
    $("body").on("click", "a.confirm_yes", function() {
        $(this).parent().parent().slideUp(400, function() {
            $(this).remove();
        });
        $("div.simple_dialog").remove();

        var data = $(this).data("dialog").split("#");
        window[data[0]](data)
    });

    $("body").on("click", "a.input_yes", function() {
        const id = $(this).data("dialog").split("#")[2]
        const value = $(`#${id}`).val()
        $(this).parent().parent().slideUp(400, function() {
            $(this).remove();
        });
        $("div.simple_dialog").remove()

        const data = $(this).data("dialog").split("#")
        const fname = data[0]
        const input_data = [data[1], value]
        window[fname](input_data)
    })

    $("body").on("keydown", "input.dialog_input", function(e) {
        if (e.keyCode !== 13) return
        const input_yes = $("a.input_yes")
        const id = input_yes.data("dialog").split("#")[2]
        const value = $(`#${id}`).val()
        input_yes.parent().parent().slideUp(400, function() {
            $(this).remove();
        });
        $("div.simple_dialog").remove()

        const data = input_yes.data("dialog").split("#")
        const fname = data[0]
        const input_data = [data[1], value]
        window[fname](input_data)
    });

    $("body").on("click", "a.dialog_link_dismiss", function() {
        $(this).parent().slideUp(400, function() {
            $(this).remove();
        });
    })
});

function create_dialog(type, data, style = "") {
    switch (type) {
        case "confirm":
            $("div.confirm_dialog").remove();
            var modal = " \
            <div class='confirm_dialog' style='display:none;" +style+ "'> \
                <span class='dialog_icon " +data[0]+ "_icon'></span> \
                <p>" +data[1]+ "</p> \
                <div class='dialog_button'> \
                    <a href='#' class='confirm_no'>Tidak</a> \
                    <a class='confirm_yes' data-dialog='" +data[2]+ "' href='#'>Ya</a> \
                </div> \
            </div>";
        break;
        case "simple":
            $("div.simple_dialog").remove()
            var modal = " \
            <div class='simple_dialog' style='display:none'> \
                <p class='" +data[0]+ "'>" +data[1]+ "</p> \
                <a href='#' class='dialog_link_dismiss'>OK</a> \
            </div>";
            setTimeout(() => {
                $("div.simple_dialog").slideUp(400, function() {
                    $(this).remove()
                })
            }, 5000);
        break
        case "input":
            $("div.input_dialog").remove()
            var modal = `
                <div class="input_dialog confirm_dialog" style="display:none;${style}">
                    <p>${data[0]}</p>
                    <input type="text" id="${data[1]}" class="dialog_input">
                    <div class="dialog_button">
                        <a href="#" class="input_no">Batal</a>
                        <a href="#" class="input_yes" data-dialog="${data[2]}">Kirim</a>
                    </div>
                </div>`
        break
    }
    return modal;
}

function prepare_close_btn_dialog() {
    $("body").on("click", "span.dialog_close_btn", function() {
        $("div.dialog_background").fadeOut(400, function() {
            $("div.dialog_background").remove();
        });
    });
}

