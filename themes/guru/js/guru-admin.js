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

            jQuery("#users-table").find(".row").each(function() {
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
            jQuery("#users-table").find(".layer")
                .removeAttr('style')
                .removeClass("backLayer");

        } else if (jQuery(this).hasClass("delete")) {

            var row = jQuery(this).closest(".row");
            var сheckboxId = jQuery(row).find("input:checkbox");
            var checkId = jQuery(сheckboxId).attr("data-id");

            var formData = 'action=delete-user&' + 'user_id=' + checkId;

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

            if ( title.indexOf("themes") > -1 ) {
                functionName = 'update-themes';
                checkName = 'theme_id';
            } else {
                functionName = 'update-user';
                checkName = 'user_id';
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
                jQuery("#users-table").find(".layer")
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
                    alert(result);
                    setReadOnly(self)
                    jQuery.ajax({
                        url: WPAjax.ajaxurl,
                        type: 'POST',
                        data: {action: 'render-user'},
                        success: function (data) {
                            var html = jQuery.parseHTML(data);
                            var resHTML = '';

                            jQuery.each(html , function(i, el){
                                if (jQuery(el).find("input:checkbox").attr("data-id") === checkId) {
                                    resHTML += el.innerHTML;
                                }
                            });
                            jQuery("#users-table").find(".layer")
                                .removeAttr('style')
                                .removeClass("backLayer");
                            jQuery(row).empty();
                            jQuery(row).append(resHTML);
                            jQuery(row).addClass('updated-item')
                                .one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
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

jQuery(document).ready(function(){
    eventsLoad('#tableBody');
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
            jQuery("#users-table").prepend("<div class='backLayer'></div>");
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

    var inputs = jQuery(that).closest("#sortRow").find("input");
    var course_id = jQuery(that).closest("#sortRow").find("#courseInput_i").val();
    var status_id = jQuery(that).closest("#sortRow").find("#statusInput_i").val();

    var formData = "action=render-user";

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
                jQuery("#tableBody").append(el)
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
};