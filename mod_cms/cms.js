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

    $('input.inputUpload , input.inputGallery').on("change", function(e) {
        var fileUpload = $(this);
        var num_file = parseInt(fileUpload.get(0).files.length);
        var limit_file = parseInt(fileUpload.attr('data-limit'));
        var patten_name = $(this).attr('name');
        patten_name = patten_name.replace("pattern_", "");
        patten_name = patten_name.replace("[]", "");
        var current_file = parseInt($('#display_' + patten_name + ' li').length);
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

            $('input[type="file"]').prop('disabled', true);
        }
    });

    $(".displayUpload").sortable({
        stop: function(e, ui) {
            var block_id = $(this).attr('id');
            block_id = block_id.replace("display_", "");

            $.map($(this).find('li'), function(el) {
                var location_name = $(el).attr('data-pos');
                var new_name = 'fileUpload_' + block_id + '[' + $(el).index() + ']';

                $('#display_' + block_id + ' li[data-pos="' + location_name + '"]').find('input').attr('name', new_name);
            });
        }
    });
    $(".displayUpload").disableSelection();

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
    var num_upload = parseInt($('#display_' + action_id + ' li').length);
    var path = data.path;

    $.each(data.success, function(i, obj) {


        var row = '';
        row += '<li data-pos="' + num_upload + '">';
        row += '<button type="button" class="btn bg-red btn-sm pull-right" onclick="RemoveTempFile(' + action_id + ',' + num_upload + ',\'' + obj.file + '\');"><i class="fa fa-trash-o"></i></button>';
        row += '<a class="info-box" target="_blank" href="' + path + obj.file + '">';
        row += '<span class="info-box-icon">';
        row += '<i class="fa ' + getMimes(obj.ext) + '"></i>';
        row += '</span>';
        row += '<div class="info-box-content">';
        row += '<span class="info-box-text">' + obj.name + '</span>';
        row += '<span class="info-box-number">';
        row += '<div class="col-md-6">';
        row += 'Mimes : ' + obj.type;
        row += '</div><div class="col-md-6">';
        row += 'Size : ' + obj.size;
        row += '</div><div class="clearfix"></div></span>';
        row += '</div>';
        row += '</a>';
        row += '<input type="hidden" name="fileUpload_' + action_id + '[' + num_upload++ + ']" value="' + obj.file + '">';
        row += '</li>';

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
    $('input[type="file"]').prop('disabled', false);
    action_id = 0;
}

function returnTempError() {
    clearTempFile();
    swal(warning_text1, warning_text3, "error");
}

function RemoveTempFile(id, pid, name) {
    $.post("mod_cms/cms_ajax.php", { name: name, clear: "clearTemp" })
        .done(function(data) {
            $('#display_' + id + ' li[data-pos="' + pid + '"]').remove();
        });
}

function returnGalleryFile(data) {
    var num_upload = parseInt($('#display_' + action_id + ' li').length);
    var path = data.path;

    $.each(data.success, function(i, obj) {


        var row = '';
        row += '<li data-pos="' + num_upload + '">';
        row += '<span class="mailbox-attachment-icon has-img">';
        row += '<img onclick="$(\'li[data-pos=' + num_upload + ']\').find(\'a.group_view\').click();" src="thumbnail.php?w=200&h=150&p=' + path + obj.file + '" alt="' + obj.name + '">';
        row += '</span>';
        row += '<div class="mailbox-attachment-info">';
        row += '<a href="' + path + obj.file + '" class="mailbox-attachment-name group_view group_view_' + action_id + '"><i class="fa fa-camera"></i>' + obj.name + '</a>';
        row += '<span class="mailbox-attachment-size">';
        row += obj.size;
        row += '<a href="#" onclick="RemoveTempFile(' + action_id + ',' + num_upload + ',\'' + obj.file + '\'); return false;" class="btn btn-default btn-xs pull-right"><i class="fa fa-trash-o"></i></a>';
        row += '</span>';
        row += '</div>';
        row += '<input type="hidden" name="fileUpload_' + action_id + '[' + num_upload++ + ']" value="' + obj.file + '">';
        row += '</li>';

        $('#display_' + action_id).append(row).find('li').fadeIn(500);
    });


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

    $(".group_view_" + action_id).colorbox({ rel: 'group_view_' + action_id, transition: "fade", maxHeight: "80%" });

    clearTempFile();
}