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
    }
    ;
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
        ;
        scrollTo("#course-wrapper", 1000);
    }
});

jQuery("#registrationForm").submit(function (event) {
    event.preventDefault();
    event.stopPropagation();

    var form = document.getElementById("registrationForm");
    var formData = new FormData(form);

    var oReq = new XMLHttpRequest();

    formData.append('action', 'insert-user');

    oReq.open("POST", WPAjax.ajaxurl, true);

    oReq.onreadystatechange = function() {
        if (oReq.readyState == 4 && oReq.status == 200) {
            alert(oReq.responseText);
            return;
        };
    };

    oReq.send(formData);
});

function drawAnimatedLines() {
    var containerTop = jQuery(".equaliser");
    var containerBottom = jQuery(".equaliser-bottom")
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
                return input.val();
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

    input.val(jQuery(this).find('span').text());
    displaySelector(this);
});

jQuery(document).ready(function($) {
    drawAnimatedLines();

    $('#addFile').click(function() {
        $('#addFileInput').click();
    });

    $('#addFileInput').on('change', prepareUpload);

    function prepareUpload(event)
    {
        files = event.target.files;
    }
});

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