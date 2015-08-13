/**
 * Created by soundstorm on 05.06.15.
 */
var courses = [];
var files;

function getCourses() {
    jQuery.ajax({
        url: WPAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'ajax-get-table',
            tableName: 'courses'
        },
        success: function (response) {
            response.forEach(function (row) {
                courses.push(row.name);
            });
        }
    });
};

function checkElement(element, elementClass) {
    var parentSize = jQuery(element).parent('.' + elementClass).size();
    var elementClasses = (jQuery(element).attr("class") !== undefined) ? jQuery(element).attr("class").indexOf(elementClass) : -1;

    if (parentSize > 0 || elementClasses > -1) {
        return elementClass;
    }
    return false
};

function checkElementIfCourse(element) {
    var result;
    courses.forEach(function (course) {
        var elementResult = checkElement(element, course)
        if (elementResult) {
            result = elementResult;
        }
    });
    if (result) {
        return result;
    };
    return false;
};

function scrollTo(element, time) {
    var offset = -100;

    jQuery('html, body').animate({
        scrollTop: jQuery(element).offset().top + offset
    }, time);
};

jQuery(document).ready(function () {
    jQuery("#course-wrapper").addClass('hidden');
    getCourses();
});

jQuery(document).click(function (event) {
    var curElement = event.target;

    if (checkElement(curElement, 'coursesLi')) {
        event.preventDefault()
        scrollTo("#courses", 1000);
    } else if (checkElement(curElement, 'planLi')) {
        event.preventDefault()
        scrollTo("#plan", 1000);
    } else if (checkElement(curElement, 'course')) {
        var page_name;
        var checkCourse;

        event.preventDefault();
        jQuery("#course-wrapper").removeClass('hidden');

        checkCourse = checkElementIfCourse(curElement);

        if (checkCourse) {
            page_name = checkCourse;
            jQuery.ajax({
                url: WPAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'ajax-submit',
                    name: page_name
                },
                success: function (html) {
                    jQuery("#course-wrapper").empty();
                    jQuery("#course-wrapper").append(html);
                }
            });
        }
        scrollTo("#course-wrapper", 1000);
    }
});

jQuery(document).ready(function(){
    var errors = {};

    jQuery('input#contact_full_name_i, input#email_i, input#phone_number_i, input#city_i').unbind().blur(function () {
        var id = jQuery(this).attr('id');
        var val = jQuery(this).val();

        switch (id) {
            case 'contact_full_name_i':
                var rev_name = /^[-a-zA-Zа-яА-ЯЁёЇїІі\s]+$/;

                if (val === '') {
                    jQuery("#contact_full_name_p").removeClass('not_vissible');
                    errors['contact_full_name'] = 'Введіть будь ласка прізвище, ім\'я, по-батькові';
                    jQuery(this).next('#contact_full_name_p').html(errors['contact_full_name']);
                } else if (!rev_name.test(val) || val.length < 2) {
                    jQuery("#contact_full_name_p").removeClass('not_vissible');
                    errors['contact_full_name'] = 'Введіть будь ласка корректні данні!';
                    jQuery(this).next('#contact_full_name_p').html(errors['contact_full_name']);
                } else {
                    jQuery("#contact_full_name_p").addClass('not_vissible');
                    delete errors.contact_full_name;
                }
                break;

            case 'email_i':
                var rev_email = /^[-_.a-z0-9]+@[-_.a-z0-9]+\.[a-z]{2,6}$/i;

                if (val === '') {
                    jQuery("#email_p").removeClass('not_vissible');
                    errors['email'] = 'Введіть будь ласка Email';
                    jQuery(this).next('#email_p').html(errors['email'])
                } else if (!rev_email.test(val)) {
                    jQuery("#email_p").removeClass('not_vissible');
                    errors['email'] = 'Введіть будь ласка корректний Email';
                    jQuery(this).next('#email_p').html(errors['email']);
                } else {
                    jQuery("#email_p").addClass('not_vissible');
                    delete errors.email;
                }
                break;

            case 'phone_number_i':
                var rev_phone = /^[-\+0-9()\s]+$/;

                if (val === '') {
                    jQuery("#phone_number_p").removeClass('not_vissible');
                    errors['phone'] = 'Введіть будь ласка контактний телефон';
                    jQuery(this).next('#phone_number_p').html(errors['phone']);
                } else if (!rev_phone.test(val)) {
                    jQuery("#phone_number_p").removeClass('not_vissible');
                    errors['phone'] = 'Введіть будь ласка корректний контактний телефон';
                    jQuery(this).next('#phone_number_p').html(errors['phone']);
                } else {
                    jQuery("#phone_number_p").addClass('not_vissible');
                    delete errors.phone;
                }
                break;

            case 'city_i':

                if (val != '') {
                    jQuery("#city_p").addClass('not_vissible');
                    delete errors.city;
                } else {
                    jQuery("#city_p").removeClass('not_vissible');
                    errors['city'] = 'Введіть будь ласка назву міста';
                    jQuery(this).next('#city_p').html(errors['city']);
                }
                break;
        }
    });

    jQuery('#selectedCourse').unbind().click(function (){

        if (jQuery('.select_input').val() === '') {
            jQuery("#selectedCourse_p").removeClass('not_vissible');
            errors['selected'] = 'Оберіть будь ласка потрібний курс!';
            jQuery('#selectedCourse_p').html(errors['selected']);
        } else {
            jQuery("#selectedCourse_p").addClass('not_vissible');
            delete errors.selected;
        }
    });

    drawAnimatedLines();

    jQuery('#addFile').click(function() {
        jQuery('#addFileInput').click();
    });

    jQuery('#addFileInput').on('change', prepareUpload);

    function prepareUpload(event) {
        files = event.target.files;
        var file = files[0];

        var div = jQuery('<div id="filename" class="falename">' + file.name + '</div>');

        if (jQuery('#filename').text() === ''){
            div.insertAfter(jQuery(this).parent());
        } else {
           var error = 'Можливе завантаження тількі одного файла!';
            jQuery('#filename').append('<p class="error">' + error + '</p>');
        }
    }

    jQuery("#registrationForm").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        delete errors.all;
        if (Object.keys(errors) == 0 && jQuery('input').val()!= '') {

            var form = document.getElementById("registrationForm");
            var formData = new FormData(form);

            var oReq = new XMLHttpRequest();

            formData.append('action', 'insert-user');

            oReq.open("POST", WPAjax.ajaxurl, true);

            oReq.onreadystatechange = function() {
                if (oReq.readyState == 4 && oReq.status == 200) {
                    return alert(oReq.responseText);
                }
            };
            oReq.send(formData);
        } else if (jQuery('input').val() === '') {
            errors['all'] = 'Заповніть будь ласка це поле';
            jQuery('.error').removeClass('not_vissible').html(errors['all']);
        }
    });
});

function drawAnimatedLines() {
    var containerTop = jQuery(".equaliser");
    var containerBottom = jQuery(".equaliser-bottom");
    var headerElementWidth = jQuery("#header").width();
    var widthBettween = 23;
    var circleSize = 7;
    var count = ~~(headerElementWidth / widthBettween);
    var firstLeft = (headerElementWidth - (count * widthBettween)) / 2;

    if (firstLeft <= circleSize) {
        count -= 1;
    }

    containerTop.each(function (index, element) {
        jQuery(element).empty();
        jQuery(element).width((count * 23));
        var left = 0;

        for (var i = count; i >= 1; i--) {
            if (i === count) {
                left += circleSize;
            } else {
                left += widthBettween;
            }
            var divTop = '<div class="bar-top" style="left: ' + left + 'px"></div>';

            jQuery(element).append(divTop);
        }
    });

    containerBottom.each(function (index, element) {
        jQuery(element).empty();
        var left = 0;

        for (var i = ~~count; i >= 1; i--) {
            if (i === ~~count || i === 1) {
                left += firstLeft;
            } else {
                left += 23;
            }
            //var left = 20 * i - firstLeft + 3;
            var divBottom = '<div class="bar-bottom" style="left: ' + left + 'px"></div>';

            jQuery(element).append(divBottom);
        }
    });
};

function displaySelector(content) {
    var selectDiv = jQuery(content).closest('.select_div');
    var selectSpan = selectDiv.find('.selectSpan');
    var input = selectDiv.find('.select_input');
    var selectOptions = selectDiv.find('.selectOptions');

    selectSpan.text(function(i, text) {

        if (text === 'Choose your course') {
            if (input.val()) {
                selectSpan.removeClass('phSpan');
                return input.data('text');
            }
        }

        selectSpan.addClass('phSpan');
        return 'Choose your course';
    });

    if (selectDiv.hasClass('active')) {
        selectDiv.toggleClass('active');
        setTimeout(function() {
            selectOptions.toggleClass('hidden');
        }, 120);
    } else {
        selectOptions.toggleClass('hidden');
        setTimeout(function() {
            selectDiv.toggleClass('active');
        }, 20);
    }
};

jQuery(".selectPlaceholder").click(function () {
    displaySelector(this);
});

jQuery('.selectOptions li').click(function () {
    var selectDiv = jQuery(this).closest('.select_div');
    var input = selectDiv.find('.select_input');

    input.val(jQuery(this).data('value'));
    input.data('text', jQuery(this).text());
    displaySelector(this);
});

//jQuery(document).ready(function($) {


jQuery(window).resize(function() {
    drawAnimatedLines();
});

function isElementInViewport(elem) {
    var $elem = jQuery(elem);

    // Get the scroll position of the page.
    var scrollElem = ((navigator.userAgent.toLowerCase().indexOf('webkit') != -1) ? 'body' : 'html');
    var viewportTop = jQuery(scrollElem).scrollTop();
    var viewportBottom = viewportTop + window.outerHeight;

    // Get the position of the element on the page.
    var elemTop = Math.round($elem.offset().top);
    var elemBottom = elemTop + $elem.height();

    return ((elemBottom + 200 < viewportBottom) && (elemTop < viewportBottom));
}


function checkAnimation() {
    var $elemHolder = jQuery('#testimonialsHolder');
    var $elem = $elemHolder.find('.testimonial');

    $elem.each(function (index, element) {
        var $elem = jQuery(element);

        if (isElementInViewport($elem)) {
            // Start the animation
            if (!$elem.hasClass('show')) {
                $elem.addClass('show');
                $elem.removeClass('hide');
            }
        } else {
            if ($elem.hasClass('show')) {
                $elem.removeClass('show');
                $elem.addClass('hide');
            }
        }
    });

}

jQuery(window).scroll(function () {
    checkAnimation();
});