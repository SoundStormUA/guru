/**
 * Created by soundstorm on 05.06.15.
 */
var errors = {};
//var files;
var innerSection = jQuery("#content");
var menuContainer = jQuery("#top-menu-container");
/*var select_language = jQuery('select.language');
var current_language = jQuery.cookie('language');

var setLanguage = function (language){
    switch(language){
        case 'ua':
            jQuery.cookie('language','ua');
            break;
        case 'en':
            jQuery.cookie('language','en');
            break;
        case 'ru':
            jQuery.cookie('language','ru');
            break;
    }
};*/

function isElementInViewport(elem) {
    var $elem = jQuery(elem);

    // Get the scroll position of the page.
    var scrollElem = 'body'; //((navigator.userAgent.toLowerCase().indexOf('webkit') != -1) ? 'body' : 'html');
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

jQuery(window).on("scroll", function () {
    checkAnimation();
});

jQuery(document).ready(function(){

    /*if(!current_language){
        setLanguage('ua')
    }*/

    translation_ajax();
    var hash_array = [];

    jQuery('#site-navigation a').each(function(ind, elem){
        hash_array.push(jQuery(elem).attr('href'));
    });

    hash_array.splice(hash_array.length-1,1);

    jQuery('#arrow-next').attr('href',hash_array[1]);
    jQuery('#arrow-prev').attr('href',hash_array[hash_array.length-1]);

    function checkElement(element, elementClass) {
        var parentSize = jQuery(element).parent('.' + elementClass).size();
        var elementClasses = (jQuery(element).attr("class") !== undefined) ? jQuery(element).attr("class").indexOf(elementClass) : -1;

        if (parentSize > 0 || elementClasses > -1) {
            return elementClass;
        }
        return false
    }

    function scrollTo(element, time) {
        var offset = -100;

        jQuery('html, body').animate({
            scrollTop: jQuery(element).offset().top + offset
        }, time);
    }

    menuContainer.on('click', function (event) {
        var curElement = event.target;

        if (checkElement(curElement, 'planLi')) {
            event.preventDefault();

            if (!jQuery('#plan').length) {
                window.location.hash = '#shedule';
            } else {
                scrollTo("#plan", 1000);
            }
        }
    });

    var scrollRegister = function(event) {
        var curElement = event.target;

        if (checkElement(curElement, 'register')) {
            event.preventDefault();

            if (!jQuery("#registrationFormDiv").length) {
                scrollTo("#registrationFormCourse", 1000);
            } else {
                scrollTo("#registrationFormDiv", 1000);
            }
        }
    };

    /*select_language.val(current_language);
    select_language.on('change',  function (event) {
        event.preventDefault();
        setLanguage(select_language.val());
        ajax_page(window.location.hash ? window.location.hash.substr(1) : 'home');
        translation_ajax();
    });*/

    innerSection.on('click', '#plan', scrollRegister);
    jQuery("#header, #plan" ).on('click', scrollRegister);

    var switchTabs = function () {
        var first = jQuery('#firstTab');
        var second = jQuery('#secondTab');
        var third = jQuery('#thirdTab');
        var firstPage = jQuery('#first-tab-page');
        var secondPage = jQuery('#second-tab-page');
        var thirdPage = jQuery('#third-tab-page');

        innerSection.on('click', '#firstTab', function (event) {
            event.preventDefault();

            first.addClass('active');
            second.removeClass('active');
            third.removeClass('active');
            firstPage.show();
            secondPage.hide();
            thirdPage.hide();
        });
        innerSection.on('click', '#secondTab', function (event) {
            event.preventDefault();

            first.removeClass('active');
            second.addClass('active');
            third.removeClass('active');
            secondPage.show();
            firstPage.hide();
            thirdPage.hide();
        });
        innerSection.on('click', '#thirdTab', function (event) {
            event.preventDefault();

            first.removeClass('active');
            second.removeClass('active');
            third.addClass('active');
            thirdPage.show();
            firstPage.hide();
            secondPage.hide();
        });
    };

    function translation_ajax() {
        jQuery.ajax({
            url: WPAjax.ajaxurl,
            type: 'GET',
            data: {
                action: 'translation-page'
            },
            success: function (object) {
                var headh1 =  jQuery('#slide-course h1');
                var str = jQuery.parseJSON(object);
                //var translationClass = Object.keys(str['string']);

                /*for (var i = 0; i <= translationClass.length; i++){
                    jQuery('.' + translationClass[i]).text(str['string'][translationClass[i]]);
                }*/

                if (location.hash && location.hash != '#home' && location.hash != '#shedule') {
                    var key = location.hash;
                    key = key.substring(1, key.length);
                    headh1.text(str['header'][key]);
                } else {
                    headh1.text('IT School');
                }
            }
        });
    }

    function ajax_page(name) {
            if(window.location.hash === '#shedule') {
                name = 'home';

                var succesShudele = function (html) {
                    innerSection.text('');
                    innerSection.append(html);
                    translation_ajax();
                    scrollTo("#plan", 1000);
                    drawAnimatedLines();
                };

                jQuery.ajax({
                    url: WPAjax.ajaxurl,
                    type: 'GET',
                    data: {
                        action: 'ajax-page',
                        name: name
                    },
                    success: succesShudele
                });
            } else {
                jQuery.ajax({
                    url: WPAjax.ajaxurl,
                    type: 'GET',
                    data: {
                        action: 'ajax-page',
                        name: name
                    },
                    success: function (html) {
                        innerSection.text('');
                        innerSection.append(html);
                        translation_ajax();
                        drawAnimatedLines();
                        switchTabs();
                        innerSection.on('click', '#plan', scrollRegister);
                    }
                });
            }
            var next_hash = "";
            var prev_hash = "";

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
        }

        var hashSwitch = function () {
            var num = jQuery('.number');
            switch(window.location.hash )
            {
                case '#shedule':
                    ajax_page();
                    num.text('1/7');
                    break;
                case '#basic':
                    ajax_page(window.location.hash.substr(1));
                    num.text('2/7');
                    break;
                case '#js':
                    ajax_page(window.location.hash.substr(1));
                    num.text('3/7');
                    break;
                case '#android':
                    ajax_page(window.location.hash.substr(1));
                    num.text('4/7');
                    break;
                case '#ios':
                    ajax_page(window.location.hash.substr(1));
                    num.text('5/7');
                    break;
                case '#qa':
                    ajax_page(window.location.hash.substr(1));
                    num.text('6/7');
                    break;
                case '#unity':
                    ajax_page(window.location.hash.substr(1));
                    num.text('7/7');
                    break;
                case '#home':
                    ajax_page(window.location.hash.substr(1));
                    num.text('1/7');
                    break;
            }
        };

    jQuery(hashSwitch);
    jQuery(window).bind('hashchange', hashSwitch);

    var validate = function () {
        var id = jQuery(this).attr('id');
        var val = jQuery(this).val();

        switch (id) {
            case 'contact_full_name_i':
                var rev_name = /^[-a-zA-Zа-яА-ЯЁёЇїІі\s]+$/;

                if (!val) {
                    jQuery("#contact_full_name_p").removeClass('not_vissible');
                    errors['contact_full_name'] = 'Введіть будь ласка прізвище, ім\'я, по-батькові';
                    jQuery(this).next('#contact_full_name_p').html('<span class="fullName">' + errors['contact_full_name'] + '</span>');
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

                if (!val) {
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

                if (!val) {
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
                var rev_city = /^[a-zA-Zа-яА-ЯЁёЇїІі\s]+$/;

                if (!val) {
                    jQuery("#city_p").removeClass('not_vissible');
                    errors['city'] = 'Введіть будь ласка назву міста';
                    jQuery(this).next('#city_p').html(errors['city']);
                } else if (!rev_city.test(val)){
                    jQuery("#city_p").removeClass('not_vissible');
                    errors['city'] = 'Введіть будь ласка корректно назву міста';
                    jQuery(this).next('#phone_number_p').html(errors['phone']);
                } else {
                    jQuery("#city_p").addClass('not_vissible');
                    delete errors.city;
                }
                break;
        }
    };

    var validateSelect = function(){
        var selectedCourse = jQuery("#selectedCourse_p");

        if (!jQuery('.select_input').val()) {
            selectedCourse.removeClass('not_vissible');
            errors['selected'] = 'Оберіть будь ласка потрібний курс!';
            selectedCourse.html(errors['selected']);
        } else {
            selectedCourse.addClass('not_vissible');
            delete errors.selected;
        }
    };

   /* var validateFile = function (file) {
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
                errors['file'] = 'Перевищенно максимальний розмір файла. завантажуйте файл розміром до - ' + upload_max_size/1000024+ ' Мб';
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
    };*/

    innerSection.unbind().on('focusout', 'input#contact_full_name_i, input#email_i, input#phone_number_i, input#city_i', validate);
    innerSection.on("click", "#selectedCourse", validateSelect);

    [].slice.call( document.querySelectorAll( 'button.contact-submit' ) ).forEach( function( bttn ) {
        new ProgressButton( bttn, {
            callback : function( instance ) {
                var progress = 0,
                    interval = setInterval( function() {
                        progress = Math.min( progress + Math.random() * 0.2, 1 );
                        instance._setProgress( progress );

                        if( progress === 1 ) {
                            instance._stop(1);
                            clearInterval( interval );
                        }
                    }, 100 );
            }
        });
    });

    /*innerSection.on('click', '#addFile', function() {
        jQuery('#addFileInput').click();
    });

    innerSection.on('change', '#addFileInput', prepareUpload);

    function prepareUpload(event) {
        delete file;
        files = event.target.files;
        var file = files[0];
        jQuery(validateFile(file));
    }*/

    innerSection.on('submit', "#registrationForm", function (event) {
        var input =  jQuery("input");
        event.preventDefault();
        event.stopPropagation();

        input.each(validate);
        jQuery(validateSelect);
        //jQuery(validateFile);

        if (Object.keys(errors) == 0 && input.val()!= '') {

            var form = document.getElementById("registrationForm");
            var formData = new FormData(form);

            var oReq = new XMLHttpRequest();

            formData.append('action', 'insert-user');

            oReq.open("POST", WPAjax.ajaxurl, true);

            oReq.onreadystatechange = function() {
                if (oReq.readyState == 4 && oReq.status == 200) {
                    jQuery(".selectSpan").text('Оберіть курс:');
                    jQuery('input', '#registrationForm').each(function() {
                        var type = this.type;

                        if (type === 'text'){
                            this.value = '';
                        } else if (type === 'file') {
                            delete files[0];
                            jQuery(".filename").text('');
                        }

                        button.removeClass("state-error");
                        button.addClass("state-success");
                    });
                }
            };
            oReq.send(formData);
        } else {
            button.removeClass('state-success');
            button.addClass("state-error")
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
        var widthBettween = 42;
        var circleSize = 7;
        var count = ~~(headerElementWidth / widthBettween);
        var firstLeft = (headerElementWidth - (count * widthBettween)) / 2;

        if (firstLeft <= circleSize) {
            count -= 1;
        }

        containerTop.each(function (index, element) {
            jQuery(element).empty();
            jQuery(element).width((count * 42));
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
    }

    drawAnimatedLines();

    jQuery(window).resize(function() {
        drawAnimatedLines();
    });

    function displaySelector(content) {
        var selectDiv = jQuery(content).closest('.select_div');
        var selectSpan = selectDiv.find('.selectSpan');
        var input = selectDiv.find('.select_input');
        var selectOptions = selectDiv.find('.selectOptions');

        selectSpan.text(function() {

                if (input.val()) {
                    selectSpan.removeClass('phSpan');
                    return input.data('text');
                } else {
                selectSpan.addClass('phSpan');
            }
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
    }
});

jQuery(document).ready(function($){
    // browser window scroll (in pixels) after which the "back to top" link is shown
    var offset = 300,
    //browser window scroll (in pixels) after which the "back to top" link opacity is reduced
        offset_opacity = 1200,
    //duration of the top scrolling animation (in ms)
        scroll_top_duration = 700,
    //grab the "back to top" link
        $back_to_top = $('.cd-top');

    //hide or show the "back to top" link
    $(window).scroll(function(){
        ( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
        if( $(this).scrollTop() > offset_opacity ) {
            $back_to_top.addClass('cd-fade-out');
        }
    });

    //smooth scroll to top
    $back_to_top.on('click', function(event){
        event.preventDefault();
        $('body,html').animate({
                scrollTop: 0 ,
            }, scroll_top_duration
        );
    });

});