/**
 * Created by soundstorm on 10.06.15.
 */

function eventsLoad(element) {
    jQuery(element).find(".controlDiv").hover(function () {
        jQuery(this).children(".settingsIcons").toggleClass("display");
    });
    jQuery(element).find(".settingsIcon").click(function() {

        if (jQuery(this).hasClass("edit")) {

            var row = jQuery(this).closest(".row");

            var inputs = jQuery(row).find("input:not(:checkbox)");
            var selects = jQuery(row).find("select");

            jQuery(selects).each(function () {
                jQuery(this).attr('data-val', jQuery(this).val());
            });

            jQuery(inputs).each(function () {
                jQuery(this).attr('data-val', jQuery(this).val());
            });

            jQuery(inputs).attr("readonly", false);
            jQuery(selects).attr("readonly", false);
            jQuery(selects).attr("disabled", false);

            jQuery(row).addClass("editRow");

            jQuery("#users-table, #theme-table").find(".row").each(function() {
                if (!jQuery(this).hasClass("editRow")) {
                    var rowEl = jQuery(this);

                    var bottomWidth = jQuery(rowEl).css('width');
                    var bottomHeight = jQuery(rowEl).css('height');
                    var rowPos = jQuery(rowEl).position();

                    jQuery(this).find(".layer").addClass("backLayer");

                    jQuery(this).find(".backLayer").css('width', bottomWidth);
                    jQuery(this).find(".backLayer").css('height', bottomHeight);
                    jQuery(this).find(".backLayer").css('top', rowPos.top);
                }
            });

            jQuery(this).closest(".row").find("input:checkbox").attr("checked", true);
        } else if (jQuery(this).hasClass("close")) {
            setReadOnly(this);
            jQuery("#users-table, #theme-table").find(".layer")
                .removeAttr('style')
                .removeClass("backLayer");

        } else if (jQuery(this).hasClass("delete")) {
            var formData;
            var title = jQuery('h1').text();

            var row = jQuery(this).closest(".row");
            var сheckboxId = jQuery(row).find("input:checkbox");
            var checkId = jQuery(сheckboxId).attr("data-id");

            var functionName = '';
            var checkName = '';
            var successAction = '';

            if ( title.indexOf("themes") > -1 ) {
                functionName = 'delete-themes';
                checkName = 'theme_id';
                successAction = 'render-themes';
            } else {
                functionName = 'delete-user';
                checkName = 'user_id';
                successAction = 'render-user';
            }

            formData = 'action=' + functionName + '&' + checkName + '=' + checkId;

            jQuery.ajax({
                url: WPAjax.ajaxurl,
                type: 'POST',
                data: formData,
                success: function() {
                    jQuery(row).addClass('removed-item')
                        .one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
                            jQuery(this).remove();
                        });
                }
            });

        } else if (jQuery(this).hasClass("save")) {
            var formData;
            var title = jQuery('h1').text();

            var self = this;
            var row = jQuery(this).closest(".row");
            var inputs = jQuery(row).find("input:not(:checkbox)");

            var selects = jQuery(row).find("select");
            var сheckboxId = jQuery(row).find("input:checkbox");

            var dataInputs = jQuery(inputs).serialize();
            var dataSelects = jQuery(selects).serialize();
            var checkId = jQuery(сheckboxId).attr("data-id");

            var functionName = '';
            var checkName = '';
            var successAction = '';

            if ( title.indexOf("themes") > -1 ) {
                functionName = checkId ? 'update-themes' : 'create-themes';
                checkName = 'theme_id';
                successAction = 'render-themes';
            } else {
                functionName = 'update-user';
                checkName = 'user_id';
                successAction = 'render-user';
            }

            formData = 'action=' + functionName + '&' + dataInputs + '&' + dataSelects + '&' + checkName + '=' + checkId;

            if (jQuery(inputs[0]).attr("readonly")) {
                return;
            }

            var inputDataVals = jQuery(inputs).map(function () {
                return jQuery(this).attr("data-val");
            }).get();
            var inputVals = jQuery(inputs).map(function () {
                return this.value;
            }).get();

            var selectDataVals = jQuery(selects).map(function () {
                return jQuery(this).attr("data-val");
            }).get();
            var selectVals = jQuery(selects).map(function () {
                return this.value;
            }).get();

            if ((selectDataVals.join() === selectVals.join()) && (inputDataVals.join() === inputVals.join())) {
                jQuery("#theme-table, #users-table").find(".layer")
                    .removeAttr('style')
                    .removeClass("backLayer");

                jQuery(inputs).attr("readonly", true);
                jQuery(selects)
                    .attr("readonly", true)
                    .attr("disabled", true);
                return;
            }

            jQuery.ajax({
                url: WPAjax.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(result) {
                    //alert(result);
                    if (!checkId && result) {
                        checkId = result;
                    }
                    setReadOnly(self);
                    jQuery.ajax({
                        url: WPAjax.ajaxurl,
                        type: 'POST',
                        data: {action: successAction},
                        success: function (data) {
                            var html = jQuery.parseHTML(data);
                            var resHTML = '';

                            jQuery.each(html , function(i, el){
                                if (jQuery(el).find("input:checkbox").attr("data-id") === checkId) {
                                    resHTML += el.innerHTML;
                                }
                            });
                            jQuery("#users-table, #theme-table").find(".layer")
                                .removeAttr('style')
                                .removeClass("backLayer");
                            jQuery(row).empty();
                            jQuery(row).append(resHTML);
                            jQuery(row).addClass('updated-item')
                                .one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function() {
                                    jQuery(row).removeClass('updated-item')
                                });
                            eventsLoad(row);
                        }
                    });
                }
            });
        }
    });
}

function newRow() {
    var html = '<div class="row addRow"><div class="layer"></div><div class="cell check"><input name="theme_id" type="checkbox"></div><div class="cell number"></div><div class="cell"><select name="selected_course" id="courseSelect_" data-val="1"> <option value="1" selected="">Basic+</option><option value="2">JavaScript</option><option value="3">Android</option><option value="4">iOS</option><option value="5">QA</option></select></div><div class="cell"><input name="day"></div><div class="cell"><input name="theme_en"></div><div class="cell"><input name="theme_ua"></div><div class="cell"><input name="theme_ru"></div><div class="cell controlDiv fa fa-settings"><div class="settingsIcons"><div class="settingsIcon save fa fa-save"></div><div class="settingsIcon delete fa fa-delete"></div></div></div></div>';
    var div = jQuery('<div>' + html + '</div>');
    eventsLoad(div);
    jQuery("#tableBody").append(div.children());
    jQuery("#users-table, #theme-table").find(".row").each(function() {
        if (!jQuery(this).hasClass("addRow")) {
            var rowEl = jQuery(this);

            var bottomWidth = jQuery(rowEl).css('width');
            var bottomHeight = jQuery(rowEl).css('height');
            var rowPos = jQuery(rowEl).position();

            jQuery(this).find(".layer").addClass("backLayer");

            jQuery(this).find(".backLayer").css('width', bottomWidth);
            jQuery(this).find(".backLayer").css('height', bottomHeight);
            jQuery(this).find(".backLayer").css('top', rowPos.top);
        } else {
            jQuery("#tableBody").find(".controlDiv").find(".delete").click(function() {
                jQuery("#users-table, #theme-table").find(".layer")
                    .removeAttr('style')
                    .removeClass("backLayer");
            })
        }
    });
}
jQuery(document).ready(function(){
    eventsLoad('#tableBody');
    jQuery('.addIcon').click(newRow);
});

jQuery("#sortRow").find("select").change(function() {
    getFilteredData(this);
});

jQuery("#sortRow").find("input").keyup(function() {
    getFilteredData(this);
});

jQuery(".emailSend").click(function () {
    jQuery.ajax({
        url: WPAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'get-template',
            file: 'admin/sendEmail.php'
        },
        success: function (html) {
            jQuery("#users-table, #theme-table").prepend("<div class='backLayer'></div>");
            jQuery("#usersList").append(html);
            jQuery("#sForm").addClass("open");
            addEventCloseEmailForm();
            CKEDITOR.replace('emailText');
        }
    });
});

function addEventCloseEmailForm() {
    jQuery("#closeForm").click(function () {
        jQuery("#sForm").remove();
        jQuery("#users-table").find(".backLayer").remove();
    });
    jQuery("#sendButton").click(function() {
        jQuery.ajax({
            url: WPAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'send_message',
                email: 'soundstorm.mail@gmail.com',
                subject: jQuery("#emailSubject").val(),
                content: jQuery("#emailText").val()
            },
            success: function(data) {
                alert(data);
            }
        })
    });
}

function getFilteredData(that) {
    var actionName = '';
    var title = jQuery('h1').text();
    var inputs = jQuery(that).closest("#sortRow").find("input");
    var course_id = jQuery(that).closest("#sortRow").find("#courseInput_i").val();
    var status_id = jQuery(that).closest("#sortRow").find("#statusInput_i").val();

    if ( title.indexOf("themes") > -1 ){
        actionName = "action=render-themes";
    } else {
        actionName = "action=render-user";
    }

    var formData = actionName;

    if (course_id) {
        formData += "&course_id=" + course_id;
    }

    if (status_id) {
        formData += "&status_id=" + status_id;
    }

    if (inputs.serialize()) {
        formData +=  "&" + inputs.serialize();
    }

    jQuery.ajax({
        url: WPAjax.ajaxurl,
        type: 'POST',
        data: formData,
        success: function (data) {
            var html = jQuery.parseHTML(data);

            jQuery("#tableBody").empty();

            jQuery.each(html , function(i, el){
                //setTimeout(function() {

                //jQuery(el).addClass('new-item');
                jQuery("#tableBody").append(el);
                    //.one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
                    //    jQuery(el).removeClass('new-item')
                    //});

                eventsLoad(el);

                //}, i*600);
            });

        }
    });

}

jQuery("#selectAll").click(function () {
    if (this.getAttribute( "checked" )) {
        jQuery(this).attr("checked", false);
        jQuery(this).closest(".table").find("input:checkbox").attr("checked", false);
    } else {
        jQuery(this).closest(".table").find("input:checkbox").attr("checked", true);
    }
});

function setReadOnly(that) {
    var inputs = jQuery(that).closest(".row").find("input:not(:checkbox)");
    var selects = jQuery(that).closest(".row").find("select");

    jQuery(inputs).each(function () {
        if (jQuery(this).attr("readonly")) {
            return;
        }
        jQuery(this).val(jQuery(this).attr("data-val"));
    });

    jQuery(that).closest(".row").find("input:checkbox").attr("checked", false);
    jQuery(inputs).attr("readonly", true);
    jQuery(selects).attr("readonly", true);
    jQuery(selects).attr("disabled", true);
}