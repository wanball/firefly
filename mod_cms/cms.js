var $eventSelect = $(".language_switch .select2");
var action_id = 0;

$(function() {


    jQuery("time.timeago").timeago();

    //active
    $("ol.breadcrumb li").last().addClass('active');

    if ($(".language_switch").length > 0) {
        $eventSelect.select2({
            templateResult: language_switch,
            minimumResultsForSearch: Infinity
        });

        $eventSelect.on("select2:select", function(e) {
            console.log(this.value);
        });
    }

    $('select[name="scheduled"]').on("change", function(e) {
        var staus = this.value;
        if (staus == 'scheduled') {
            $('.scheduled_tab').fadeIn();
        } else {
            $('.scheduled_tab').fadeOut().find('input').val('');
        }
    });

    $('#btn_content_back').on("click", function(e) {
        try {
            var path = $(".breadcrumb > li").eq(-2).find("a").attr("href");
            window.location.href = path;
        } catch (error) {
            window.history.back();
        }
    });
    try {
        $(".currency").inputmask('currency', {
            rightAlign: true,
            prefix: "",
        });

        $(".decimal").inputmask('decimal');

        $(".ip_mask").inputmask('ip');

        $(".external_link").inputmask('url');

        $('.color_picker').colorpicker({
            colorSelectors: {
                'black': '#000000',
                'white': '#ffffff',
                'red': '#FF0000',
                'default': '#777777',
                'primary': '#337ab7',
                'success': '#5cb85c',
                'info': '#5bc0de',
                'warning': '#f0ad4e',
                'danger': '#d9534f'
            }
        });
    } catch (error) {

    }

    try {
        var start = moment().format('DD/MM/YYYY');
        var end = moment().add(6, 'days').format('DD/MM/YYYY');

        //Date range picker
        $('.daterange').daterangepicker({
            autoUpdateInput: false,
            startDate: start,
            endDate: end,
            locale: {
                format: 'DD/MM/YYYY'
            },
            opens: "center"
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        $('.datetimerange').daterangepicker({
            autoUpdateInput: false,
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            startDate: start,
            endDate: end,
            locale: {
                format: 'DD/MM/YYYY'
            },
            opens: "center"
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm') + ' - ' + picker.endDate.format('DD/MM/YYYY HH:mm'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        $('.datetime , .datetimepicker').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            locale: {
                format: 'DD/MM/YYYY'
            },
            opens: "center"
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        if (!mobile) {
            //Date picker
            $('.datepicker').daterangepicker({
                singleDatePicker: true,
                autoUpdateInput: false,
                timePicker: false,
                locale: {
                    format: 'DD/MM/YYYY'
                },
                opens: "center"
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY'));
            }).on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        }
    } catch (error) {

    }

    $('.select_province').on("change", function(e) {
        var id_val = $(this).val();
        var name = $(this).attr('name');
        name = name.replace("pattern_province_", "");

        swapDistrict(name, id_val);
    });

    $('.select_district').on("change", function(e) {
        var id_val = $(this).val();
        var name = $(this).attr('name');
        name = name.replace("pattern_district_", "");

        swapSubDistrict(name, id_val);
    });

    $('input.inputUpload').on("change", function(e) {
        var fileUpload = $(this);
        var num_file = parseInt(fileUpload.get(0).files.length);
        var limit_file = parseInt(fileUpload.attr('data-limit'));
        var patten_name = $(this).attr('name');
        patten_name = patten_name.replace("pattern_", "");
        patten_name = patten_name.replace("[]", "");
        var current_file = parseInt($('#display_' + patten_name + ' div').length);

        var total_file = limit_file - current_file;
        if (num_file > limit_file) {
            fileUpload.val('');
            var res = warning_text2.replace('|:NUM:|', limit_file);
            swal(warning_text1, res, "error");
        } else if (num_file > total_file) {
            fileUpload.val('');
            var res = warning_text2.replace('|:NUM:|', limit_file);
            swal(warning_text1, res, "error");
        } else {
            action_id = patten_name;

            $('#Progress_' + action_id).fadeIn();

            $(this).clone().appendTo("#action_frame");
            $('#action_frame').attr({
                action: "mod_cms/cms_ajax.php"
            }).append('<input type="hidden" value="' + patten_name + '" name="pid" >').submit();
        }
    });

});


function language_switch(language) {

    if (!language.id) { return language.text; }
    var $language = $(
        '<span><img src="' + language.element.lang + '" class="img-flag" /> ' + language.text + '</span>'
    );
    return $language;
};

function swapDistrict(name, id_val) {
    $.post("mod_cms/cms_ajax.php", { id: id_val, type: "District" })
        .done(function(data) {
            var msg;
            var first = true;
            $.each(data, function(i, obj) {
                if (first) {
                    swapSubDistrict(name, obj.DISTRICT_ID);
                    first = false;
                }
                msg += '<option value="' + obj.DISTRICT_ID + '">';
                msg += obj.DISTRICT_DESC
                msg += '</option>';
            });
            $('select[name=pattern_district_' + name + ']').html(msg);
        });
}

function swapSubDistrict(name, id_val) {
    $.post("mod_cms/cms_ajax.php", { id: id_val, type: "SubDistrict" })
        .done(function(data) {
            var msg;
            $.each(data, function(i, obj) {
                msg += '<option value="' + obj.SUB_DISTRICT_ID + '">';
                msg += obj.SUB_DISTRICT_DESC
                msg += '</option>';
            });
            $('select[name=pattern_sub_district_' + name + ']').html(msg);
        });
}

function returnTempFile(data) {

    var bg_color = 'bg-green';
    $.each(data.success, function(i, obj) {
        if (bg_color == 'bg-green') {
            bg_color = 'bg-red';
        } else {
            bg_color = 'bg-green';
        }
        var row = '';
        row += '<a class="info-box" target="_blank" href="../upload/temp/' + obj.file + '">';
        row += '<span class="info-box-icon ' + bg_color + '">';
        row += '<img src="' + getMimes(obj.ext) + '" alt="" />';
        row += '</span>';
        row += '<div class="info-box-content">';
        row += '<span class="info-box-text">' + obj.name + '</span>';
        row += '<span class="info-box-number">';
        row += '<div class="col-md-6">';
        row += 'Mimes : ' + obj.type;
        row += '</div><div class="col-md-6">';
        row += 'Size : ' + obj.size;
        row += '</div></span>';
        row += '</div>';
        row += '</a>';

        $('#display_' + action_id).append(row);
    });
    clearTempFile();

    var num_error = data.errors.length;
    if (num_error > 0) {
        var err_msg = '';
        err_msg += data.errors[0];
        if (num_error > 1) {
            err_msg += ' ' + and_text + ' ';
            err_msg += data.errors[1];
        }


        swal(warning_text1, err_msg, "error");
    }
}

function clearTempFile() {

    $('#action_frame').attr({
        action: "#"
    }).find('input').remove();
    $('#pattern_' + action_id).val('');
    $('#action_iframe').attr({
        src: "#"
    });

    $('#Progress_' + action_id).fadeOut();
    action_id = 0;
}

function returnTempError() {
    clearTempFile();
    swal(warning_text1, warning_text3, "error");
}