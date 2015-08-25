/**
 * Created by soundstorm on 05.06.15.
 */
var errors = {};
var courses = [];
var files;
var innerSection = jQuery("#content");

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

jQuery(document).ready(function(){
    var hash_array = [];

    jQuery('#site-navigation a').each(function(ind, elem){
        hash_array.push(jQuery(elem).attr('href'));
    });

    hash_array.splice(hash_array.length-1,1);

    jQuery('#arrow-next').attr('href',hash_array[1]);
    jQuery('#arrow-prev').attr('href',hash_array[hash_array.length-1]);

    function scrollTo(element, time) {
        var offset = -100;

        jQuery('html, body').animate({
            scrollTop: jQuery(element).offset().top + offset
        }, time);
    };

    jQuery(document).on('click', function (event) {
        var curElement = event.target;

        if (checkElement(curElement, 'planLi')) {
            event.preventDefault()
            if (!jQuery('#plan').length) {
                window.location.hash = '#shedule';
            } else {
                scrollTo("#plan", 1000);
            }
        } else if (checkElement(curElement, 'register')) {
            if (!jQuery("#registrationFormDiv").length) {
                scrollTo("#registrationFormCourse", 1000);
            } else {
                scrollTo("#registrationFormDiv", 1000);
            }
        }
    });

    function checkElement(element, elementClass) {
        var parentSize = jQuery(element).parent('.' + elementClass).size();
        var elementClasses = (jQuery(element).attr("class") !== undefined) ? jQuery(element).attr("class").indexOf(elementClass) : -1;

        if (parentSize > 0 || elementClasses > -1) {
            return elementClass;
        }
        return false
    };

    function ajax_page(name) {
            if(window.location.hash === '#shedule') {
                name = 'home';

                var succesShudele = function (html) {
                    jQuery("#content").empty();
                    jQuery('#content').append(html);
                    scrollTo("#plan", 1000);
                    drawAnimatedLines();
                };

                jQuery.ajax({
                    url: WPAjax.ajaxurl,
                    type: 'GET',
                    data: {
                        action: 'course-page',
                        name: name
                    },
                    success: succesShudele
                });
            } else {
                jQuery.ajax({
                    url: WPAjax.ajaxurl,
                    type: 'GET',
                    data: {
                        action: 'course-page',
                        name: name
                    },
                    success: function (html) {
                        jQuery("#content").empty();
                        jQuery('#content').append(html);
                        drawAnimatedLines();
                        innerSection.on('click', '#firstTab',  function (){
                            jQuery('#first-tab-page').show();
                            jQuery('#second-tab-page').hide();
                            jQuery('#third-tab-page').hide();
                        });

                        innerSection.on('click', '#secondTab',  function (){
                            jQuery('#second-tab-page').show();
                            jQuery('#first-tab-page').hide();
                            jQuery('#third-tab-page').hide();
                        });

                        innerSection.on('click', '#thirdTab',  function (){
                            jQuery('#third-tab-page').show();
                            jQuery('#first-tab-page').hide();
                            jQuery('#second-tab-page').hide();
                        });
                    }
                });
            }
            var next_hash = "";
            var prev_hash = "";

            next_hash = prev_hash;
            for(i=0; i< hash_array.length; i++){
                if(hash_array[i] == "#"+name){
                    if(i==hash_array.length-1){
                        next_hash = hash_array[0];
                    } else {
                        next_hash = hash_array[i+1];
                    }
                    if(i==0){
                        prev_hash = hash_array[hash_array.length-1];
                    } else {
                        prev_hash = hash_array[i-1];
                    }

                }
            }
            jQuery('#arrow-next').attr('href',next_hash);
            jQuery('#arrow-prev').attr('href',prev_hash);
        };

        var hashSwitch = function () {
            switch(window.location.hash )
            {
                case '#shedule':
                    ajax_page();
                    break;
                case '#basic':
                    ajax_page(window.location.hash.substr(1));
                    break;
                case '#js':
                    ajax_page(window.location.hash.substr(1));
                    break;
                case '#android':
                    ajax_page(window.location.hash.substr(1));
                    break;
                case '#ios':
                    ajax_page(window.location.hash.substr(1));
                    break;
                case '#qa':
                    ajax_page(window.location.hash.substr(1));
                    break;
                case '#home':
                    window.location.href = '#home';
                    ajax_page(window.location.hash.substr(1));
                    break;
            }
            setHeaders();
        };

    jQuery(hashSwitch);
    jQuery(window).bind('hashchange', hashSwitch);

    var validate = function () {
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
                var rev_email = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

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
    };

    var validateSelect = function(){
        var selectedCourse = jQuery("#selectedCourse_p");

        if (jQuery('.select_input').val() === '') {
            selectrdCourse.removeClass('not_vissible');
            errors['selected'] = 'Оберіть будь ласка потрібний курс!';
            selectedCourse.html(errors['selected']);
        } else {
            selectedCourse.addClass('not_vissible');
            delete errors.selected;
        }
    };

    var validateFile = function (file) {
        var buttonHolder = jQuery('.buttonsHolder');
        var upload_max_size = 5242880;
        var array_ext = ['doc', 'docx', 'odt'];
        var file_ext = file.name.split('.').pop().toLowerCase();
        var div = jQuery('<div id="filename" class="filename">' + file.name + '</div>');

        delete errors.file;

        if (files === undefined){
            errors['file'] = 'Звантажте файл резюме формату - ' + array_ext.join(',');
            buttonHolder.append('<p class="error">' + errors['file'] + '</p>');
        } else if (file === files[0]) {

            if (file.size > upload_max_size) {
                errors['file'] = 'Перевищенно максимальний розмір файла. завантажуйте файл розміром до - ' + upload_max_size/10024 + ' Мб';
                buttonHolder.append('<p class="error">' + errors['file'] + '</p>');
            } else if (jQuery.inArray(file_ext,array_ext)== -1) {
                errors['file'] = 'Розширення файлу непідтримуєтся,завантажте файл формату - ' + array_ext.join(',');
                buttonHolder.append('<p class="error">' + errors['file'] + '</p>');
            } else if (jQuery('#filename').text() === ''){
                div.insertAfter(buttonHolder);
                buttonHolder.find('.error').empty();
            } else if (jQuery('#filename').text() != '') {
                jQuery('#filename').empty();
                div.insertAfter(buttonHolder);
            } else {
                delete errors.file;
            }
        }
    };

    innerSection.unbind().on('focusout', 'input#contact_full_name_i, input#email_i, input#phone_number_i, input#city_i', validate);
    innerSection.on("click", "#selectedCourse", validateSelect);


    innerSection.on('click', '#addFile', function() {
        jQuery('#addFileInput').click();
    });

    innerSection.on('change', '#addFileInput', prepareUpload);

    function prepareUpload(event) {
        delete file;
        files = event.target.files;
        var file = files[0];
        jQuery(validateFile(file));
    }

    innerSection.on('submit', "#registrationForm", function (event) {
       var input =  jQuery("input");
        event.preventDefault();
        event.stopPropagation();

        input.each(validate);
        jQuery(validateSelect);
        jQuery(validateFile);
        if (Object.keys(errors) == 0 && input.val()!= '') {

            var form = document.getElementById("registrationForm");
            var formData = new FormData(form);

            var oReq = new XMLHttpRequest();

            formData.append('action', 'insert-user');

            oReq.open("POST", WPAjax.ajaxurl, true);

            oReq.onreadystatechange = function() {
                if (oReq.readyState == 4 && oReq.status == 200) {
                    jQuery('input', '#registrationForm').each(function() {
                        var type = this.type;

                        if (type === 'text'){
                            this.value = '';
                        } else if (type === 'file') {
                            delete files[0];
                            jQuery(".filename").text('');
                        }
                    });
                }
            };
            oReq.send(formData);
        }
    });

    innerSection.on("click", ".selectPlaceholder", function () {
        displaySelector(this);
    });

    innerSection.on('click', '.selectOptions li', function () {
        var selectDiv = jQuery(this).closest('.select_div');
        var input = selectDiv.find('.select_input');

        input.val(jQuery(this).data('value'));
        input.data('text', jQuery(this).text());
        displaySelector(this);
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

    drawAnimatedLines();

    jQuery(window).resize(function() {
        drawAnimatedLines();
    });

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

    jQuery(window).on('scroll', function () {
        checkAnimation();
    });
});
