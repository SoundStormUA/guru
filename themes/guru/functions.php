<?php
/**
 * Guru initiation
 *
 * Initializes Guru's features and includes all necessary files.
 *
 * @package Guru
 */
$language = $_COOKIE['language'];

if (!isset($content_width)) {
    $content_width = 2000;
}

define('templates_directory', get_template_directory_uri() . '/templates/');

add_action('admin_enqueue_scripts', 'enqueue_admin_styles');

if (!function_exists('enqueue_admin_style')) :

    function enqueue_admin_styles()
    {
        global $wp_version;

        $version = wp_get_theme(wp_get_theme()->template)->get('Version');

        $assets = array(
            'css' => '/css/guru.css',
            'js' => '/js/guru-admin.js',
            'ckeditor' => '/js/ckeditor/ckeditor.js',
        );
        wp_enqueue_style('guru-theme', get_template_directory_uri() . $assets['css']);
        wp_enqueue_script('guru-theme', get_template_directory_uri() . $assets['js'], array('jquery'), $version, true);
        wp_enqueue_script('ckeditor', get_template_directory_uri() . $assets['ckeditor'], array('jquery', 'guru-theme'), '', true);
        wp_localize_script('guru-theme', 'WPAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    ;
endif;

add_action('wp_enqueue_scripts', 'guru_scripts_styles');

if (!function_exists('guru_scripts_styles')) :
    function guru_scripts_styles()
    {
        global $wp_version;

        $version = wp_get_theme(wp_get_theme()->template)->get('Version');

        $assets = array(
            'css' => '/css/guru.css',
            'js' => '/js/guru.js',
            'cookie' => '/js/jquery.cookie.js',
        );

        wp_enqueue_style('guru-theme-css', get_template_directory_uri() . $assets['css'], array(), $version);
        wp_enqueue_script('guru-theme', get_template_directory_uri() . $assets['js'], array('jquery'), $version, true);
        wp_enqueue_script('guru-cookie', get_template_directory_uri() . $assets['cookie'], array('jquery'), $version);
        wp_enqueue_style('guru-style', get_stylesheet_uri());
        wp_localize_script('guru-theme', 'WPAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
endif;

add_action('after_setup_theme', 'register_menu');

if (!function_exists(' register_menu ')) :
    function register_menu()
    {
        register_nav_menu('primary', __('Navigation Menu', 'guru'));
    }
endif;

add_action('wp_ajax_nopriv_ajax-submit', 'get_page_content');
add_action('wp_ajax_ajax-submit', 'get_page_content');

function get_page_content()
{
    $name = $_POST['name'];
    $content = get_page_by_title($name, "ARRAY_A", "page");
    $response = renderPage(array('name' => $name));
    echo $response;
    die();
    exit;
}

;

add_action('wp_ajax_nopriv_ajax-get-table', 'get_table');
add_action('wp_ajax_ajax-get-table', 'get_table');

function get_table($tableName)
{
    global $wpdb;

    if ($tableName) {
        $return = 'true';
        $tableName = $wpdb->prefix . $tableName;
    } else {
        $tableName = $wpdb->prefix . $_POST['tableName'];
    }
    $result = array();
    $query = "SELECT * FROM " . $tableName;

    $data_rows = $wpdb->get_results($query, ARRAY_A);
    foreach ($data_rows as $row) {
        $result[] = $row;
    }

    $response = json_encode($result);

    if ($return) {
        return $data_rows;
    }

    header("Content-Type: application/json");
    echo $response;

    die();
    exit;
}

;

add_action('wp_ajax_nopriv_insert-user', 'insert_registered_user');
add_action('wp_ajax_insert-user', 'insert_registered_user');

function insert_registered_user()
{

    global $wpdb;

    $data = array(
        'FIO' => $_POST['contact_full_name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'city' => $_POST['city'],
        'course_id' => $_POST['selectedCourse'],
        'status_id' => 1
    );

    if (isset($_FILES['addingFile'])) {

        $upload_dir = WP_CONTENT_DIR . "/uploads/resume";
        $upload_file_name = 'Resume' . '_' . uniqid() . rand(1000, 9999);
        $upload_file_size = $_FILES['addingFile']['size'];
        $tmp_name = $_FILES['addingFile']['tmp_name'];

        $upload_max_size = 5242880;
        $array_ext = array('docx', 'doc', 'odt');

        $file_ext = pathinfo($_FILES['addingFile']['name'], PATHINFO_EXTENSION);
        $upload_file_name .= '.' . $file_ext;
        $data['resume_file'] = $upload_file_name;

        if (in_array($file_ext, $array_ext) && $upload_file_size < $upload_max_size && is_writable($upload_dir)) {
            move_uploaded_file($tmp_name, "$upload_dir/$upload_file_name");
        } elseif (!file_exists($upload_dir / $upload_file_name)) {
            $errors['filename'] = 'Error file has not been uploaded to the server';
        }
    }

    if (empty($errors)) {
        $wpdb->insert(
            $wpdb->prefix . 'registered_users',
            $data,
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
            )
        );
        exit;
        die;
    }
}

;


add_action('wp_ajax_update-user', 'update_registered_user');
function update_registered_user()
{

    global $wpdb;

    $data = array(
        'FIO' => $_POST['FIO'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'city' => $_POST['city'],
        'course_id' => $_POST['selected_course'],
        'status_id' => $_POST['selected_status']
    );

    $id = $_POST['user_id'];

    $wpdb->update(
        $wpdb->prefix . 'registered_users',
        $data,
        array('ID' => $id),
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d'
        ),
        array('%d')
    );
};

add_action('wp_ajax_delete-user', 'delete_registered_user');
function delete_registered_user()
{

    global $wpdb;

    $id = $_POST['user_id'];

    $wpdb->delete(
        $wpdb->prefix . 'registered_users',
        array('ID' => $id),
        array('%d')
    );
}

;

function renderPage($attr, $content)
{
    ob_start();
    extract($param, EXTR_SKIP);
    get_template_part('coursePage');
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

;

function courses($atts, $content = null)
{
    // Attributes
    extract(shortcode_atts(
            array(
                'animation' => '',
                'name' => '',
            ), $atts)
    );

    $rows = get_table('courses');
    $id = caseCourse($atts['name']);

    foreach($rows as $row){
        $needRow[] = $row;
    }

    $ar = array_slice($needRow, $id - 1, true);

    foreach($ar as $needArray){
        $ar = $needArray;
    }

    $animationName = $atts['animation'];

    switch ($animationName)
    {
        case 'basic':
            $animation = 'basic';
            break;
        case 'js':
            $animation = 'js';
            break;
        case 'android':
            $animation = 'android';
            break;
        case 'ios':
            $animation = 'ios';
            break;
        case 'qa':
            $animation = 'qa';
            break;
    }

    if ($ar['name_' . $GLOBALS['language']] == null){
        $ar['name_' . $GLOBALS['language']] = $ar['name_en'];
    }
    if ($ar['info_' . $GLOBALS['language']] == null){
        $ar['info_' . $GLOBALS['language']] = $ar['name_en'];
    }
    $content = "<div class='course-container course " . $atts['name'] . "'>";
    $content .= '<div class="icons icon-' . $animation . '"></div>';
    $content .= "<header class='course-caption'>";
    $content .= "<span>" . $ar['name_' . $GLOBALS['language']]  . "</span></header>";
    $content .= "<p>" . $ar['info_' . $GLOBALS['language']] . "</p></div>";

    return $content;
};

add_shortcode('add_course', 'courses');

function courseName($atts)
{
    //Atributes
    extract(shortcode_atts(
            array(
              'name' => '',
              'choose' => ''
            ), $atts)
    );

    $rows = get_table('courses');
    $id = caseCourse($atts['name']);

    foreach($rows as $row){
        $needRow[] = $row;
    }

    $ar = array_slice($needRow, $id - 1, true);

    foreach($ar as $needArray){
        $ar = $needArray;
    }

    $name = $ar['name_' . $GLOBALS['language']];
    $info = $ar['info_' . $GLOBALS['language']];
    if ($ar['name_' . $GLOBALS['language']] === null){
        $name = $ar['name_en'];
    }

    if ($ar['info_' . $GLOBALS['language']] === null){
        $info = $ar['info_en'];
    }


    if ($atts['choose'] === 'name'){
        return $name;
    } elseif ($atts['choose'] === 'info' ){
        return $info;
    }
}

add_shortcode('name_course', 'courseName');


function get_data_for_select($table, $field)
{
    $field = $field ? $field : 'name';
    $someTable = get_table($table);

    $ids = array();
    $names = array();

    foreach ($someTable as $row) {
        $ids[] = $row['i'] ? $row['id'] : $row['ID'];
        $names[] = $row[$field];
    }

    return array('ids' => implode(", ", $ids), 'names' => implode(", ", $names));
    die();
    exit;
}

;
function input_shortcode($atts, $content = null)
{
    // Attributes
    extract(shortcode_atts(
            array(
                'id' => 'def',
                'class' => 'def',
                'type' => 'def',
                'name' => 'def',
                'name_i' => 'def',
            ), $atts)
    );

    if ($atts['id']) {
        $id = "id='{$atts['id']}'";
        $id_i = "id='{$atts['id']}_i'";
        $id_p = "id='{$atts['id']}_p'";
    } else {
        $id = "";
        $id_i = "";
        $id_p = "";
    };
    if ($atts['class']) {
        $class = "class='input_div {$atts['class']}'";
    } else {
        $class = "class='input_div'";
    }
    if ($atts['name']) {
        $name = "name='{$atts['name']}'";
    } else {
        $name = "";
    }
    $input = "<div {$id} {$class} {$name}>";

    $input = $input . $input_label . "<input name ='{$atts['name_i']}' {$id_i} type='{$atts['type']}' class='input_field' placeholder='" . do_shortcode($content) . "'></input>";
    $input = $input . "<p {$id_p} class='error not_vissible'></p></div>";

    return $input;
}

add_shortcode('input', 'input_shortcode');

function select_shortcode($atts, $content = null)
{
    extract(shortcode_atts(
            array(
                'id' => 'def',
                'class' => 'def',
                'tooltip' => 'def',
                'values' => 'def',
                'options' => 'def',
                'name' => 'name',
                'name_i' => 'def',
                'not_form' => '',
                'selected' => 1,
            ), $atts)
    );

    if ($atts['tooltip']) {
        $input_label = "<label class='input_header'>" . do_shortcode($content) . "</label><div TITLE='{$atts['tooltip']}' class='tooltip'><i class='mk-icon-question-circle'></i></div>";
    } else {
        $input_label = "<label class='input_header'>" . do_shortcode($content) . "</label>";
    }

    if ($atts['id']) {
        $id = "id='{$atts['id']}'";
        $id_i = "id='{$atts['id']}_i'";
        $id_p = "id='{$atts['id']}_p'";
    } else {
        $id = "";
        $id_i = "";
        $id_p = "";
    };

    if ($atts['name']) {
        $name = "name='{$atts['name']}'";
    } else {
        $name = "";
    };

    if ($atts['name_i']) {
        $name_i = "name='{$atts['name_i']}'";
    } else {
        $name_i = "";
    };

    if ($atts['class']) {
        $class = "class='select_div " . $atts['class'] . "'";
    } else {
        $class = "class='select_div'";
    }
    $input = "<div {$id} {$class} {$name}>";
    if (empty($atts['not_form'])) {
        $input = $input . $input_label;
    }

    if ($atts['options']) {
        $values_array = explode(", ", $atts['values']);
        $options_array = explode(", ", $atts['options']);
        $input = $input . "<select {$name_i} {$id_i}>";

        foreach ($values_array as $value) {
            if ($value == $atts['selected']) {
                $selectedValue = 'selected';
            } else {
                $selectedValue = '';
            }

            $input .= "<option value='" . $value . "' " . $selectedValue . ">" . $options_array[$value] . "</option>";
        }
        $input = $input . "</select>";
        if (empty($atts['not_form'])) {
            $input = $input . "<p {$id_p} class='error not_vissible'></p>";
        }
        $input .= "</div>";
    }

    return $input;
}

add_shortcode('select', 'select_shortcode');

function select_shortcode_ul($atts, $content = null)
{
    extract(shortcode_atts(
            array(
                'id' => 'def',
                'class' => 'def',
                'values' => 'def',
                'options' => 'def',
                'name' => 'name',
                'name_i' => 'def',
                'not_form' => '',
                'selected' => 1,
            ), $atts)
    );

    $input_label = "<div class='selectPlaceholder'>";
    $input_label .= "<input class='select_input hidden' name='{$name_i}'></input>";
    $input_label .= "<span class='selectSpan phSpan'>Choose your course</span></div>";

    if ($atts['id']) {
        $id = "id='{$atts['id']}'";
        $id_i = "id='{$atts['id']}_i'";
        $id_p = "id='{$atts['id']}_p'";
    } else {
        $id = "";
        $id_i = "";
        $id_p = "";
    };

    if ($atts['name']) {
        $name = "name='{$atts['name']}'";
    } else {
        $name = "";
    };

    if ($atts['name_i']) {
        $name_i = "name='{$atts['name_i']}'";
    } else {
        $name_i = "";
    };

    if ($atts['class']) {
        $class = "class='select_div " . $atts['class'] . "'";
    } else {
        $class = "class='select_div'";
    }
    $input = "<div {$id} {$class} {$name}>";
    if (empty($atts['not_form'])) {
        $input = $input . $input_label;
    }

    if ($atts['options']) {
        $values_array = explode(", ", $atts['values']);
        $options_array = explode(", ", $atts['options']);

        $input .= '<div class="selectOptions hidden">';
        $input .= '<ul>';


        foreach ($values_array as $value) {
            $input .= "<li data-value='" . $value . "'><span>" . $options_array[$value - 1] . "</span></li>";
        }
        $input = $input . "</ul></div>";
        if (empty($atts['not_form'])) {
            $input = $input . "<p {$id_p} class='error not_vissible'></p>";
        }
        $input .= "</div>";
    }

    return $input;
}

add_shortcode('select_ul', 'select_shortcode_ul');

function contact_form($atts, $content = null)
{

    $selectList = get_data_for_select('courses', 'name_en');

    if ($GLOBALS['language'] === 'ua') {
        $form = "<div id='registrationFormDiv'>";
        $form .= "<header class='sectionTitle'><span class='title-reg-form'>Реєстрація</span></header>";
        $form .= "<form id='registrationForm' class='contact_form' method='POST' novalidate='novalidate'>";
        $form .= do_shortcode("[input id='contact_full_name' name_i='contact_full_name' required class='marked' type='text']Прізвище, ім’я, по-батькові:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input name_i='email' id='email' required class='marked' type='text']Email:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input id='phone_number' name_i='phone_number' required class='marked' type='text']Контактний телефон:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input id='city' name_i='city' required class='marked' type='text']Місто:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[select_ul id='selectedCourse' name_i='selectedCourse' required values='" . $selectList['ids'] . "'  options='" . $selectList['names'] . "' type='text']Оберіть бажаний курс:[/select_ul]");
        $form .= '<div class="clearboth"></div>';
        $form .= '<div class="buttonsHolder">';
        $form .= '<button name="submit" id="register" class="contact-submit register" data-style="move-up">Надіслати</button>';
        $form .= '<input id="addFile" type="button" class="addFile" data-style="move-up" value="+ резюме" />';
        $form .= '<input type="file" name="addingFile" id="addFileInput" class="hidden"></input></div>';
        $form .= '<input id="hidden_to" type="hidden" value="itschool@thinkmobiles.com" name="contact_to"/>';
        $form .= "</form></div>";
    } elseif ($GLOBALS['language'] === 'en'){
        $form = "<div id='registrationFormDiv'>";
        $form .= "<header class='sectionTitle'><span class='title-reg-form'>Register</span></header>";
        $form .= "<form id='registrationForm' class='contact_form' method='POST' novalidate='novalidate'>";
        $form .= do_shortcode("[input id='contact_full_name' name_i='contact_full_name' required class='marked' type='text']Full Name:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input name_i='email' id='email' required class='marked' type='text']Email:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input id='phone_number' name_i='phone_number' required class='marked' type='text']Phone:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input id='city' name_i='city' required class='marked' type='text']City:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[select_ul id='selectedCourse' name_i='selectedCourse' required values='" . $selectList['ids'] . "'  options='" . $selectList['names'] . "' type='text']Select the desired rate:[/select_ul]");
        $form .= '<div class="clearboth"></div>';
        $form .= '<div class="buttonsHolder">';
        $form .= '<button name="submit" id="register" class="contact-submit register" data-style="move-up">Sign up</button>';
        $form .= '<input id="addFile" type="button" class="addFile" data-style="move-up" value="+ CV" />';
        $form .= '<input type="file" name="addingFile" id="addFileInput" class="hidden"></input></div>';
        $form .= '<input id="hidden_to" type="hidden" value="itschool@thinkmobiles.com" name="contact_to"/>';
        $form .= "</form></div>";
    } elseif ($GLOBALS['language'] === 'ru'){
        $form = "<div id='registrationFormDiv'>";
        $form .= "<header class='sectionTitle'><span class='title-reg-form'>Регистрация</span></header>";
        $form .= "<form id='registrationForm' class='contact_form' method='POST' novalidate='novalidate'>";
        $form .= do_shortcode("[input id='contact_full_name' name_i='contact_full_name' required class='marked' type='text']Фамилия, Имя, Отчество:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input name_i='email' id='email' required class='marked' type='text']Email:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input id='phone_number' name_i='phone_number' required class='marked' type='text']Контактный телефон:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[input id='city' name_i='city' required class='marked' type='text']Город:[/input]");
        $form .= '<div class="clearboth"></div>';
        $form .= do_shortcode("[select_ul id='selectedCourse' name_i='selectedCourse' required values='" . $selectList['ids'] . "'  options='" . $selectList['names'] . "' type='text']Выберите желаемый курс:[/select_ul]");
        $form .= '<div class="clearboth"></div>';
        $form .= '<div class="buttonsHolder">';
        $form .= '<button name="submit" id="register" class="contact-submit register" data-style="move-up">Отправить</button>';
        $form .= '<input id="addFile" type="button" class="addFile" data-style="move-up" value="+ резюме" />';
        $form .= '<input type="file" name="addingFile" id="addFileInput" class="hidden"></input></div>';
        $form .= '<input id="hidden_to" type="hidden" value="itschool@thinkmobiles.com" name="contact_to"/>';
        $form .= "</form></div>";
    }
    return $form;
}

add_shortcode('contact_form', 'contact_form');

function contact_form_course($atts, $content = null)
{

    extract(shortcode_atts(
            array(
                'coursename' => '',
            ), $atts)
    );

    $id = caseCourse($atts{'coursename'});

    switch ($GLOBALS['language'])
    {
        case 'ua':
            $title = 'Реєстрація';
            $fullName = 'Прізвище, ім’я, по-батькові:';
            $phone = 'Контактний телефон:';
            $city = 'Місто:';
            $sign_up = 'Надіслати';
            $cv = '+ резюме';
            break;
        case 'en':
            $title = 'REGISTER:';
            $fullName = 'Full Name:';
            $phone = 'Phone:';
            $city = 'City:';
            $sign_up = 'Sign up';
            $cv = '+ CV';
            break;
        case 'ru':
            $title = 'РЕГИСТРАЦИЯ';
            $fullName = 'Фамилия, Имя, Отчество:';
            $phone = 'Контактный телефон:';
            $city = 'Город:';
            $sign_up = 'Отправить';
            $cv = '+ резюме';
            break;
    }


    $form = "<div id='registrationFormCourse'>";
    $form .= "<header class='sectionTitleCourse'><span> $title </span></header>";
    $form .= "<form id='registrationForm' class='contact_form' method='POST' novalidate='novalidate'>";
    $form .= do_shortcode("[input id='contact_full_name' name_i='contact_full_name' required class='marked' type='text'] $fullName [/input]");
    $form .= '<div class="clearboth"></div>';
    $form .= do_shortcode("[input name_i='email' id='email' required class='marked' type='text']Email:[/input]");
    $form .= '<div class="clearboth"></div>';
    $form .= do_shortcode("[input id='phone_number' name_i='phone_number' required class='marked' type='text'] $phone [/input]");
    $form .= '<div class="clearboth"></div>';
    $form .= do_shortcode("[input id='city' name_i='city' required class='marked' type='text'] $city [/input]");
    $form .= '<div class="clearboth"></div>';
    $form .= '<span class="courseName">' . do_shortcode('[name_course name="' . $atts['coursename'] . '" choose="name"]') . '</span><div class ="selectPlaceholder"><input class="select_input hidden" name="selectedCourse" type="hidden" value = "' . $id . '"></div><p id="selectedCourse_p" class="error not_vissible"></p>';
    $form .= '<div class="clearboth"></div>';
    $form .= '<div class="buttonsHolder">';
    $form .= '<button name="submit" id="register" class="contact-submit register" data-style="move-up">' . $sign_up . '</button>';
    $form .= '<input id="addFile" type="button" class="addFile" data-style="move-up" value="' . $cv . '" />';
    $form .= '<input type="file" name="addingFile" id="addFileInput" class="hidden"></input></div>';
    $form .= '<input id="hidden_to" type="hidden" value="itschool@thinkmobiles.com" name="contact_to"/>';
    $form .= "</form></div>";

    return $form;
}

add_shortcode('course_form','contact_form_course');

function add_equaliser()
{

    return "<li class='equaliser'></li>";

}

add_shortcode('add_equaliser', 'add_equaliser');

/*Adding menu to settings*/

add_action('admin_menu', 'register_my_menu_page');

function register_my_menu_page()
{
    add_menu_page('Manage tables', 'Manage tables', 'manage_options', 'manage_menu', 'my_manage_output');
    add_submenu_page('manage_menu', 'Manage registered users', 'Manage registered users', 'manage_options', 'userpage', 'my_menu_page');
    add_submenu_page('manage_menu', 'Manage themes', 'Manage themes', 'manage_options', 'themespage', 'my_theme_page');
    add_submenu_page('manage_menu', 'Manage literature', 'Manage literature', 'manage_options', 'literapage', 'my_literature_page');
    add_submenu_page('manage_menu', 'Translation string', 'Translation string', 'manage_options', 'translationpage', 'translation_page');
}

function get_users_data($fio, $email, $phone_number, $city, $course_id, $status_id)
{
    global $wpdb;

    $courseWhere = '';

    if ($course_id && $course_id !== 0 && $course_id !== undefined) {
        $courseWhere .= 'course_id=' . $course_id;
    }

    if ($status_id && $status_id !== 0 && $status_id !== undefined && $courseWhere) {
        $courseWhere .= ' and status_id=' . $status_id;
    } else if ($status_id) {
        $courseWhere .= 'status_id=' . $status_id;
    }

    if ($fio && $courseWhere) {
        $courseWhere .= ' and fio like "' . $fio . '%"';
    } else if ($fio) {
        $courseWhere .= 'fio like "' . $fio . '%"';
    }

    if ($email && $courseWhere) {
        $courseWhere .= ' and email like "' . $email . '%"';
    } else if ($email) {
        $courseWhere .= 'email like "' . $email . '%"';
    }

    if ($phone_number && $courseWhere) {
        $courseWhere .= ' and phone_number like "' . $phone_number . '%"';
    } else if ($phone_number) {
        $courseWhere .= 'phone_number like "' . $phone_number . '%"';
    }

    if ($city && $courseWhere) {
        $courseWhere .= ' and city like "' . $city . '%"';
    } else if ($city) {
        $courseWhere .= 'city like "' . $city . '%"';
    }

    if ($courseWhere) {
        $courseWhere = ' WHERE ' . $courseWhere;
    }

    $query = "SELECT reg_users.id as ID, reg_users.FIO as FIO, reg_users.email as email,
    reg_users.phone_number as phone_number, reg_users.city as city, courses.name_en as course_name,
    reg_users.course_id as selected_course, reg_users.status_id as selected_status, status.name as status
    FROM {$wpdb->prefix}registered_users reg_users
    INNER JOIN {$wpdb->prefix}courses courses ON courses.id = reg_users.course_id
    INNER JOIN {$wpdb->prefix}reg_users_status status ON status.id = reg_users.status_id " . $courseWhere . "
    ORDER BY id";
    $usersTable = $wpdb->get_results($query, ARRAY_A);
    return array('data' => $usersTable, 'query' => $courseWhere);
    die();
    exit;
}

function my_menu_page()
{
    global $title;

    $coursesSelectList = get_data_for_select('courses', 'name_en');
    $statusSelectList = get_data_for_select('reg_users_status', 'name');

    $coursesSelectList['ids'] = '0, ' . $coursesSelectList['ids'];
    $coursesSelectList['names'] = 'All, ' . $coursesSelectList['names'];
    $statusSelectList['ids'] = '0, ' . $statusSelectList['ids'];
    $statusSelectList['names'] = 'All, ' . $statusSelectList['names'];

    $page = '<div class="wrap">';
    $page .= '<h1>' . $title . '</h1>';
    $page .= '<section id="usersList">';
    $page .= '<div id="users-table" class="table" cellspacing="0" cellpadding="0">';
    $page .= '<div class="col layer"></div>';
    $page .= '<div class="col checkCol"></div>';
    $page .= '<div class="col numberCol"></div>';
    $page .= '<div class="col fioCol"></div>';
    $page .= '<div class="col emailCol"></div>';
    $page .= '<div class="col phoneCol"></div>';
    $page .= '<div class="col cityCol"></div>';
    $page .= '<div class="col courseCol"></div>';
    $page .= '<div class="col statusCol"></div>';
    $page .= '<div class="col settingsCol"></div>';
    $page .= '<div class="headerContainer">';
    $page .= '<div id="sortRow" class="sort row">';
    $page .= '<div class="layer"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell"><input id="fioInput" name="fio"></input></div>';
    $page .= '<div class="cell"><input id="emailInput" name="email"></input></div>';
    $page .= '<div class="cell"><input id="phoneInput" name="phone_number"></input></div>';
    $page .= '<div class="cell"><input id="cityInput" name="city"></input></div>';
    $page .= '<div class="cell">' . do_shortcode("[select id='courseInput' not_form='true' values='" . $coursesSelectList['ids'] . "'  options='" . $coursesSelectList['names'] . "' type='text'][/select]") . '</div>';
    $page .= '<div class="cell">' . do_shortcode("[select id='statusInput' not_form='true' values='" . $statusSelectList['ids'] . "'  options='" . $statusSelectList['names'] . "' type='text'][/select]") . '</div>';
    $page .= '<div class="cell emailSend fa fa-envelope"></div>';
    $page .= '</div>';
    $page .= '<div class="row header">';
    $page .= '<div class="layer"></div>';
    $page .= '<div class="cell check"><input type="checkbox" id="selectAll"></input></div>';
    $page .= '<div class="cell number">#</div>';
    $page .= '<div class="cell">Full Name</div>';
    $page .= '<div class="cell">Email</div>';
    $page .= '<div class="cell">Phone Number</div>';
    $page .= '<div class="cell">City</div>';
    $page .= '<div class="cell">Course</div>';
    $page .= '<div class="cell">Status</div>';
    $page .= '<div class="cell settings"></div>';
    $page .= '</div>';
    $page .= '</div>';
    $page .= '<div id="tableBody" class="bodyContainer">';
    $page .= renderUsersTable('true');
    $page .= '</div>';
    $page .= '</div>';

    $page .= '</section>';
    $page .= '</div>';

    echo $page;
}

;

add_action('wp_ajax_get-template', 'getTemplate');
add_action('wp_ajax_nopriv_get-template', 'getTemplate');

function getTemplate()
{
    $file = $_POST['file'];
    $template = file_get_contents(templates_directory . $file);

    //$response = array('html' => $template, 'dir' => templates_directory);

    //$response = json_encode( $response );

    //header( "Content-Type: application/json" );

    echo $template;
    die;
    exit;
}

add_action('wp_ajax_render-user', 'renderUsersTable');
add_action('wp_ajax_nopriv_render-user', 'renderUsersTable');

function renderUsersTable($returned)
{
    $coursesSelectList = get_data_for_select('courses', 'name_en');
    $statusSelectList = get_data_for_select('reg_users_status', 'name');
    $usersTable = get_users_data($_POST['fio'], $_POST['email'], $_POST['phone_number'], $_POST['city'], $_POST['course_id'], $_POST['status_id']);

    $resultHtml = '';
    $count = 0;
    foreach ($usersTable['data'] as $row) {
        $count += 1;
        $resultHtml .= '<div class="row">';
        $resultHtml .= '<div class="layer"></div>';
        $resultHtml .= '<div class="cell check"><input name="user_id"  type="checkbox" data-id="' . $row['ID'] . '"></input></div>';
        $resultHtml .= '<div class="cell number">' . $count . '</div>';
        $resultHtml .= '<div class="cell"><input name="FIO" readonly value="' . $row['FIO'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="email" readonly value="' . $row['email'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="phone_number" readonly value="' . $row['phone_number'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="city" readonly value="' . $row['city'] . '"></input></div>';

        $courses_values_array = explode(", ", $coursesSelectList['ids']);
        $courses_options_array = explode(", ", $coursesSelectList['names']);

        $courseSelector = "<select  name='selected_course' id='courseSelect_" . $row['course_id'] . "' readonly disabled> ";
        $selected = $row['selected_course'];

        foreach ($courses_values_array as $value) {
            if ($value == $selected) {
                $selectedValue = 'selected';
            } else {
                $selectedValue = '';
            }
            $courseSelector .= "<option value='" . $value . "' " . $selectedValue . ">" . $courses_options_array[$value - 1] . "</option>";
        }

        $courseSelector .= "</select>";

        $status_values_array = explode(", ", $statusSelectList['ids']);
        $status_options_array = explode(", ", $statusSelectList['names']);

        $statusSelector = "<select  name='selected_status' id='statusSelect_" . $row['course_id'] . "' readonly disabled> ";
        $selected = $row['selected_status'];

        foreach ($status_values_array as $value) {
            if ($value == $selected) {
                $selectedValue = 'selected';
            } else {
                $selectedValue = '';
            }
            $statusSelector .= "<option value='" . $value . "' " . $selectedValue . ">" . $status_options_array[$value - 1] . "</option>";
        }
        $statusSelector .= "</select>";

        $resultHtml .= '<div class="cell">' . $courseSelector . '</div>';
        $resultHtml .= '<div class="cell">' . $statusSelector . '</div>';
        $resultHtml .= '<div class="cell controlDiv fa fa-settings">';
        $resultHtml .= '<div class="settingsIcons">';
        $resultHtml .= '<div class="settingsIcon close fa fa-close"></div>';
        $resultHtml .= '<div class="settingsIcon delete fa fa-trash"></div>';
        $resultHtml .= '<div class="settingsIcon save fa fa-save"></div>';
        $resultHtml .= '<div class="settingsIcon edit fa fa-edit"></div>';
        $resultHtml .= '</div>';
        $resultHtml .= '</div>';
        $resultHtml .= '</div>';
    }

    if ($returned) {
        return $resultHtml;
    }

    echo $resultHtml;
    die();
    exit;
}

;

add_action('wp_ajax_send_message', 'send_message');
add_action('wp_ajax_nopriv_send_message', 'send_message');

function send_message()
{

    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $content = $_POST['content'];

    $to = $email;
    $headers = "From: <{$email}>\r\n";
    $headers .= "Reply-To: {$email}\r\n";

    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $message = "
       <html>
         <head>
            <title>HTML email</title>
         </head>
         <body>
            {$content}
        </body>
      </html>";


    echo $to . ' ' . $subject . ' ' . $message . ' ' . $headers;

    // Call the wp_mail function, display message based on the result.
    if (wp_mail($to, $subject, $message, $headers)) {
        //the message was sent...
        echo true;
    } else {
        //the message was not sent...
        echo false;
    };

    //wp_mail( $to, $subject, $message, $headers );

    die();
}

function drawTestimonials($atts)
{
    extract(shortcode_atts(
            array(
                'name' => '',
                'image' => '',
                'department' => '',
                'text_ua' => '',
                'text_en' => '',
                'text_ru' => ''
            ), $atts)
    );

    if(!$atts['text_' . $GLOBALS['language']]){
        $atts['text_en'];
    };

    $testimonial = '<div class="testimonial hide">';
    $testimonial .= '<div class="testimonial-img">';
    $testimonial .= '<div class="img-background"><img src="' . $atts['image'] . '"></div>';
    $testimonial .= '<div class="circle-img"></div>';
    $testimonial .= '</div>';
    $testimonial .= '<div class="testimonialDescription">';
    $testimonial .= '<span class="tPerson">' . $atts['name'] . '</span>';
    $testimonial .= '<span class="tDepartment">' . $atts['department'] . '</span>';
    $testimonial .= '<span class="tText">' . $atts['text_' . $GLOBALS['language']] . '</span>';
    $testimonial .= '</div>';
    $testimonial .= '</div>';

    return $testimonial;
}

add_shortcode('testimonials', 'drawTestimonials');

function caseCourse($courses) {
        switch ($courses) {
            case 'basic':
                return 1;
                break;
            case 'js':
                return 2;
                break;
            case 'android':
                return 3;
                break;
            case 'ios':
                return 4;
                break;
            case 'qa':
                return 5;
                break;
        }
}

//[content-themes]
function contentThemes($atts) {
	extract( shortcode_atts(array(
					'coursename'=>''), $atts));

	$html = '';
	$html .= '<div id="first-tab-page">';
    $html .= '<div class="about-cours">';
    $html .= '<div class="video-wrap">';
	$html .= '<div class="video">' . '<a class="button">' . 'Вчитись з нами легко' . '</a>' . '</div>';
    $html .= '<p class="vide-desc"></p></div>';
    $html .= '</div>';
    $html .= '<div class="lections">';
    $html .= contentLessons($GLOBALS['language'], $atts['coursename']);
    $html .= '</div>' . '</div>';
    return $html;
}
add_shortcode( 'content-themes', 'contentThemes' );

function contentLessons($language, $name) {
	$rows = get_table('themes');

    $id = caseCourse($name);

	$tmp = array();

	foreach ($rows as $row) {
        if ($row['course_id'] == $id) {
		    $tmp[$row['day']][] = !$row['theme_' . $language] ? $row['theme_en'] : $row['theme_' . $language];
	    }
	}
    ksort($tmp);
    $page = '';
    $page .='<div class="lection-block">';
    $count = 0;
    foreach ($tmp as $day => $rows){
        $count++;
        if ($count % 3 === 0){
            $page .= dayThemes($day, $rows);
            $page .= '</div>';
            $page .= '<div class="lection-block">';
        } else {
            $page .= dayThemes($day, $rows);
        }
    }
   $page .= '</div>';
   return $page;
}

function dayThemes($day, $rows){
        $result = '';
        $result .= '<div class="lection">';
        $result .= '<div class="caption">';
        $result .= '<p class="lection-day">' . $day = strlen($day) > 1 ? '#' . $day . '</p>' : '#0' . $day . '</p>';
        $result .= '<span class="play"></span>';
        $result .= '<a href="" class="lection-name"></a>';
        $result .= '</div>';
        $result .= '<ul>';
            foreach ($rows as $element) {
                $result .= '<li>' . '<p>' . $element . '</p>' . '</li>';
            }
            $result .= '</ul></div>';
    return $result;
}

function get_theme_content($course_id, $day, $theme_en, $theme_ua, $theme_ru)
{
    global $wpdb;

    $themeWhere = '';

    if ($course_id && $course_id !== 0 && $course_id !== undefined) {
        $themeWhere .= 'course_id=' . $course_id;
    }

    if ($day && $themeWhere) {
        $themeWhere .= ' and day like "' . $day . '%"';
    } else if ($day) {
        $themeWhere .= 'day like "' . $day . '%"';
    }

    if ($theme_en && $themeWhere) {
        $themeWhere .= ' and theme_en like "' . $theme_en  . '%"';
    } else if ($theme_en ) {
        $themeWhere .= ' theme_en like "' . $theme_en . '%"';
    }

    if ($theme_ua && $themeWhere) {
        $themeWhere .= ' and theme_ua like "' . $theme_ua  . '%"';
    } else if ($theme_ua ) {
        $themeWhere .= ' theme_ua like "' . $theme_ua . '%"';
    }

    if ($theme_ru && $themeWhere) {
        $themeWhere .= ' and theme_ru like "' . $theme_ru  . '%"';
    } else if ($theme_ru ) {
        $themeWhere .= ' theme_ru like "' . $theme_ru . '%"';
    }
    if ($themeWhere) {
        $themeWhere = ' WHERE ' . $themeWhere;
    }

    $query = "SELECT theme.id as ID, theme.day as day, theme.theme_en as theme_en,theme.theme_ua as theme_ua,
    theme.theme_ru as theme_ru, courses.name_en as course_name, courses.ID as selected_course
    FROM {$wpdb->prefix}themes theme
    INNER JOIN {$wpdb->prefix}courses courses ON courses.id = theme.course_id" . $themeWhere . "
    ORDER BY selected_course, day";
    $themeTable = $wpdb->get_results($query, ARRAY_A);
    return array('data' => $themeTable, 'query' => $themeWhere);
    die();
    exit;
}

function my_theme_page()
{
    global $title;

    $coursesSelectList = get_data_for_select('courses', 'name_en');

    $coursesSelectList['ids'] = '0, ' . $coursesSelectList['ids'];
    $coursesSelectList['names'] = 'All, ' . $coursesSelectList['names'];
    $statusSelectList['ids'] = '0, ' . $statusSelectList['ids'];
    $statusSelectList['names'] = 'All, ' . $statusSelectList['names'];

    $page = '<div class="wrap">';
    $page .= '<h1>' . $title . '</h1>';
    $page .= '<section id="themeList">';
    $page .= '<div id="theme-table" class="table" cellspacing="0" cellpadding="0">';
    $page .= '<div class="col layer"></div>';
    $page .= '<div class="col checkCol"></div>';
    $page .= '<div class="col numberCol"></div>';
    $page .= '<div class="col dayCol"></div>';
    $page .= '<div class="col themeCol"></div>';
    $page .= '<div class="col themeCol"></div>';
    $page .= '<div class="col themeCol"></div>';
    $page .= '<div class="col Col"></div>';
    $page .= '<div class="col settingsCol"></div>';
    $page .= '<div class="headerContainer">';
    $page .= '<div id="sortRow" class="sort row">';
    $page .= '<div class="layer"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell">' . do_shortcode("[select id='courseInput' not_form='true' values='" . $coursesSelectList['ids'] . "'  options='" . $coursesSelectList['names'] . "' type='text'][/select]") . '</div>';
    $page .= '<div class="cell"><input id="dayInput" name="day"></input></div>';
    $page .= '<div class="cell"><input id="themeEnInput" name="theme_en"></input></div>';
    $page .= '<div class="cell"><input id="themeUaInput" name="theme_ua"></input></div>';
    $page .= '<div class="cell"><input id="themeRuInput" name="theme_ru"></input></div>';

    $page .= '<div class="cell addIcon fa fa-plus"></div>';
    $page .= '</div>';
    $page .= '<div class="row header">';
    $page .= '<div class="layer"></div>';
    $page .= '<div class="cell check"><input type="checkbox" id="selectAll"></input></div>';
    $page .= '<div class="cell number">#</div>';
    $page .= '<div class="cell">Course</div>';
    $page .= '<div class="cell">day</div>';
    $page .= '<div class="cell">theme_en</div>';
    $page .= '<div class="cell">theme_ua</div>';
    $page .= '<div class="cell">theme_ru</div>';
    $page .= '<div class="cell settings"></div>';
    $page .= '</div>';
    $page .= '</div>';
    $page .= '<div id="tableBody" class="bodyContainer">';
    $page .= renderThemeTable('true');
    $page .= '</div>';
    $page .= '</div>';
    $page .= '</section>';
    $page .= '</div>';

    echo $page;
}

;

add_action('wp_ajax_render-themes', 'renderThemeTable');
add_action('wp_ajax_nopriv_render-themes', 'renderThemeTable');

function renderThemeTable($returned)
{
    $coursesSelectList = get_data_for_select('courses', 'name_en');
    $themesTable = get_theme_content($_POST['course_id'], $_POST['day'], $_POST['theme_en'], $_POST['theme_ua'], $_POST['theme_ru']);
    $resultHtml = '';
    $count = 0;
    foreach ($themesTable['data'] as $row) {
        $count += 1;
        $resultHtml .= '<div class="row">';
        $resultHtml .= '<div class="layer"></div>';
        $resultHtml .= '<div class="cell check"><input name="theme_id"  type="checkbox" data-id="' . $row['ID'] . '"></input></div>';
        $resultHtml .= '<div class="cell number">' . $count . '</div>';

        $courses_values_array = explode(", ", $coursesSelectList['ids']);
        $courses_options_array = explode(", ", $coursesSelectList['names']);

                $courseSelector = "<select  name='selected_course' id='courseSelect_" . $row['course_id'] . "' readonly disabled> ";
                $selected = $row['selected_course'];

                foreach ($courses_values_array as $value) {
                    if ($value == $selected) {
                        $selectedValue = 'selected';
                    } else {
                        $selectedValue = '';
                    }
                    $courseSelector .= "<option value='" . $value . "' " . $selectedValue . ">" . $courses_options_array[$value - 1] . "</option>";
                }

                $courseSelector .= "</select>";


        $resultHtml .= '<div class="cell">' . $courseSelector . '</div>';

        $resultHtml .= '<div class="cell"><input name="day" readonly value="' . $row['day'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="theme_en" readonly value="' . $row['theme_en'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="theme_ua" readonly value="' . $row['theme_ua'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="theme_ru" readonly value="' . $row['theme_ru'] . '"></input></div>';
        $resultHtml .= '<div class="cell controlDiv fa fa-settings">';
        $resultHtml .= '<div class="settingsIcons">';
        $resultHtml .= '<div class="settingsIcon close fa fa-close"></div>';
        $resultHtml .= '<div class="settingsIcon delete fa fa-delete"></div>';
        $resultHtml .= '<div class="settingsIcon save fa fa-save"></div>';
        $resultHtml .= '<div class="settingsIcon edit fa fa-edit"></div>';
        $resultHtml .= '</div>';
        $resultHtml .= '</div>';
        $resultHtml .= '</div>';
    }

    if ($returned) {
        return $resultHtml;
    }

    echo $resultHtml;
    die();
    exit;
}

add_action('wp_ajax_update-themes', 'updateTheme');
function updateTheme()
{
    global $wpdb;

    $id = $_POST['theme_id'];

    $data = array(
        'course_id' => $_POST['selected_course'],
        'day' => $_POST['day'],
        'theme_en' => $_POST['theme_en'],
        'theme_ua' => $_POST['theme_ua'],
        'theme_ru' => $_POST['theme_ru'],
    );

    $wpdb->update(
        $wpdb->prefix . 'themes',
        $data,
        array('ID' => $id),
        array(
            '%d',
            '%d',
            '%s',
            '%s',
            '%s'
        ),
        array('%d')
    );
}
add_action('wp_ajax_create-themes', 'insertThemeTable');
function insertThemeTable()
{
    global $wpdb;

    $wpdb->insert(
        $wpdb->prefix .'themes',
        array(
            'course_id' => $_POST['selected_course'],
            'day' => $_POST['day'],
            'theme_en' => $_POST['theme_en'],
            'theme_ua' => $_POST['theme_ua'],
            'theme_ru' => $_POST['theme_ru'],
        ),
        array(
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
        ));

    echo $wpdb->insert_id;
    die;
    exit;
}

add_action('wp_ajax_delete-themes', 'delete_themes');
function delete_themes()
{
    global $wpdb;

    $id = $_POST['theme_id'];

    $wpdb->delete(
        $wpdb->prefix . 'themes',
        array('ID' => $id),
        array('%d')
    );
};


//[course-literature]
function courseLiterature($atts) {
	extract( shortcode_atts(array(
					'img' =>'',
					'coursename'=>''), $atts));
	$page = '';
	$page .= '<div id="second-tab-page" style="display:none;">';
	$page .= '<div class="literature">';
    $page .= '<h2>Загальні знання</h2>';
    $page .= contentLiterature($GLOBALS['language'], $atts['coursename']);
    $page .= '</div>' . '</div>';

    return $page;
}
add_shortcode( 'course-literature', 'courseLiterature' );

function contentLiterature($language, $name) {
	$result = '';
	$litTable = get_table('literature');

    $id = caseCourse($name);

	$tmp=array();

	foreach ($litTable as $row) {
        if ($row['course_id'] == $id) {
		    $tmp[!$row['author_' . $language] ? $row['author_en'] : $row['author_' . $language] ][] = !$row['title_' . $language] ? $row['title_en'] : $row['title_' . $language];
	    }
	}
	foreach ($tmp as $author => $title){
        $result = '<div class="source">';
	    foreach($title as $key => $value){
	        $result .= '<span class="title">' . $value . '</span>';
	    }
	    $result .= '<span class="author">' . $author . '</span>';
        $result .= '</div>';
	}
	return $result;
}

function my_literature_page()
{
    global $title;

    $coursesSelectList = get_data_for_select('courses', 'name_en');

    $coursesSelectList['ids'] = '0, ' . $coursesSelectList['ids'];
    $coursesSelectList['names'] = 'All, ' . $coursesSelectList['names'];
    $statusSelectList['ids'] = '0, ' . $statusSelectList['ids'];
    $statusSelectList['names'] = 'All, ' . $statusSelectList['names'];

    $page = '<div class="wrap">';
    $page .= '<h1>' . $title . '</h1>';
    $page .= '<section id="literatureList">';
    $page .= '<div id="literature-table" class="table" cellspacing="0" cellpadding="0">';
    $page .= '<div class="col layer"></div>';
    $page .= '<div class="col checkCol"></div>';
    $page .= '<div class="col numberCol"></div>';
    $page .= '<div class="col courseCol"></div>';
    $page .= '<div class="col titleCol"></div>';
    $page .= '<div class="col titlelCol"></div>';
    $page .= '<div class="col titleCol"></div>';
    $page .= '<div class="col authorCol"></div>';
    $page .= '<div class="col authorCol"></div>';
    $page .= '<div class="col authorCol"></div>';
    $page .= '<div class="col settingsCol"></div>';
    $page .= '<div class="headerContainer">';
    $page .= '<div id="sortRow" class="sort row">';
    $page .= '<div class="layer"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell">' . do_shortcode("[select id='courseInput' not_form='true' values='" . $coursesSelectList['ids'] . "'  options='" . $coursesSelectList['names'] . "' type='text'][/select]") . '</div>';
    $page .= '<div class="cell"><input id="titleEnItput" name="title_en"></input></div>';
    $page .= '<div class="cell"><input id="titleUaInput" name="title_ua"></input></div>';
    $page .= '<div class="cell"><input id="titleRuInput" name="title_ru"></input></div>';
    $page .= '<div class="cell"><input id="authorEnInput" name="author_en"></input></div>';
    $page .= '<div class="cell"><input id="authorUaInput" name="author_ua"></input></div>';
    $page .= '<div class="cell"><input id="authorRuInput" name="author_ru"></input></div>';

    $page .= '<div class="cell addIcon fa fa-plus"></div>';
    $page .= '</div>';
    $page .= '<div class="row header">';
    $page .= '<div class="layer"></div>';
    $page .= '<div class="cell check"><input type="checkbox" id="selectAll"></input></div>';
    $page .= '<div class="cell number">#</div>';
    $page .= '<div class="cell">Course</div>';
    $page .= '<div class="cell">title_en</div>';
    $page .= '<div class="cell">title_ua</div>';
    $page .= '<div class="cell">title_ru</div>';
    $page .= '<div class="cell">author_en</div>';
    $page .= '<div class="cell">author_ua</div>';
    $page .= '<div class="cell">author_ru</div>';
    $page .= '<div class="cell settings"></div>';
    $page .= '</div>';
    $page .= '</div>';
    $page .= '<div id="tableBody" class="bodyContainer">';
    $page .= renderLitTable('true');
    $page .= '</div>';
    $page .= '</div>';
    $page .= '</section>';
    $page .= '</div>';

    echo $page;
}

;

add_action('wp_ajax_render-literature', 'renderLitTable');

function renderLitTable($returned)
{
    $coursesSelectList = get_data_for_select('courses', 'name_en');
    $litTable = get_lit_content($_POST['course_id'], $_POST['title_en'], $_POST['title_ua'], $_POST['title_ru'], $_POST['author_en'], $_POST['author_ua'], $_POST['author_ru']);
    $resultHtml = '';
    $count = 0;
    foreach ($litTable['data'] as $row) {
        $count += 1;
        $resultHtml .= '<div class="row">';
        $resultHtml .= '<div class="layer"></div>';
        $resultHtml .= '<div class="cell check"><input name="lit_id"  type="checkbox" data-id="' . $row['ID'] . '"></input></div>';
        $resultHtml .= '<div class="cell number">' . $count . '</div>';

        $courses_values_array = explode(", ", $coursesSelectList['ids']);
        $courses_options_array = explode(", ", $coursesSelectList['names']);

                $courseSelector = "<select  name='selected_course' id='courseSelect_" . $row['course_id'] . "' readonly disabled> ";
                $selected = $row['selected_course'];

                foreach ($courses_values_array as $value) {
                    if ($value == $selected) {
                        $selectedValue = 'selected';
                    } else {
                        $selectedValue = '';
                    }
                    $courseSelector .= "<option value='" . $value . "' " . $selectedValue . ">" . $courses_options_array[$value - 1] . "</option>";
                }

                $courseSelector .= "</select>";


        $resultHtml .= '<div class="cell">' . $courseSelector . '</div>';

        $resultHtml .= '<div class="cell"><input name="title_en" readonly value="' . $row['title_en'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="title_ua" readonly value="' . $row['title_ua'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="title_ru" readonly value="' . $row['title_ru'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="author_en" readonly value="' . $row['author_en'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="author_ua" readonly value="' . $row['author_ua'] . '"></input></div>';
        $resultHtml .= '<div class="cell"><input name="author_ru" readonly value="' . $row['author_ru'] . '"></input></div>';
        $resultHtml .= '<div class="cell controlDiv fa fa-settings">';
        $resultHtml .= '<div class="settingsIcons">';
        $resultHtml .= '<div class="settingsIcon close fa fa-close"></div>';
        $resultHtml .= '<div class="settingsIcon delete fa fa-delete"></div>';
        $resultHtml .= '<div class="settingsIcon save fa fa-save"></div>';
        $resultHtml .= '<div class="settingsIcon edit fa fa-edit"></div>';
        $resultHtml .= '</div>';
        $resultHtml .= '</div>';
        $resultHtml .= '</div>';
    }

    if ($returned) {
        return $resultHtml;
    }

    echo $resultHtml;
    die();
    exit;
}

;

function get_lit_content($course_id, $title_en, $title_ua, $title_ru, $author_en, $author_ua, $author_ru)
{
    global $wpdb;

    $litWhere = '';

    if ($course_id && $course_id !== 0 && $course_id !== undefined) {
        $litWhere .= 'course_id=' . $course_id;
    }

    if ($title_en && $litWhere) {
        $litWhere .= ' and title_en like "' . $title_en . '%"';
    } else if ($title_en) {
        $litWhere .= 'title_en like "' . $title_en . '%"';
    }

    if ($title_ua && $litWhere) {
        $litWhere .= ' and title_ua like "' . $title_ua  . '%"';
    } else if ($title_ua ) {
        $litWhere .= ' title_ua like "' . $title_ua . '%"';
    }

    if ($title_ru && $litWhere) {
        $litWhere .= ' and title_ru like "' . $title_ru  . '%"';
    } else if ($title_ru ) {
        $litWhere .= ' title_ru like "' . $title_ru . '%"';
    }

    if ($author_en && $litWhere) {
        $litWhere .= ' and author_en like "' . $author_en  . '%"';
    } else if ($author_en ) {
        $litWhere .= ' author_en like "' . $author_en . '%"';
    }

    if ($author_ua && $litWhere) {
        $litWhere .= ' and author_ua like "' . $author_ua  . '%"';
    } else if ($author_ua ) {
        $litWhere .= ' author_ua like "' . $author_ua . '%"';
    }

    if ($author_ru && $litWhere) {
            $litWhere .= ' and author_ru like "' . $author_ru  . '%"';
    } else if ($author_ru ) {
            $litWhere .= ' author_ru like "' . $author_ru . '%"';
    }

    if ($litWhere) {
        $litWhere = ' WHERE ' . $litWhere;
    }

    $query = "SELECT lit.id as ID, lit.title_en as title_en, lit.title_ua as title_ua,lit.title_ru as title_ru,
    lit.author_en as author_en, lit.author_ua as author_ua, lit.author_ru as author_ru,
    courses.name_en as course_name, courses.ID as selected_course
    FROM {$wpdb->prefix}literature lit
    INNER JOIN {$wpdb->prefix}courses courses ON courses.id = lit.course_id" . $litWhere . "
    ORDER BY selected_course";
    $litTable = $wpdb->get_results($query, ARRAY_A);
    return array('data' => $litTable, 'query' => $litWhere);
    die();
    exit;
}

add_action('wp_ajax_update-literature', 'updateLiterature');
function updateLiterature()
{
    global $wpdb;

    $id = $_POST['lit_id'];

    $data = array(
        'course_id' => $_POST['selected_course'],
        'title_en' => $_POST['title_en'],
        'title_ua' => $_POST['title_ua'],
        'title_ru' => $_POST['title_ru'],
        'author_en' => $_POST['author_en'],
        'author_ua' => $_POST['author_ua'],
        'author_ru' => $_POST['author_ru']
    );

    $wpdb->update(
        $wpdb->prefix . 'literature',
        $data,
        array('ID' => $id),
        array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        ),
        array('%d')
    );
}

add_action('wp_ajax_create-literature', 'insertLitTable');
function insertLitTable()
{
    global $wpdb;

    $wpdb->insert(
        $wpdb->prefix .'literature',
        array(
            'course_id' => $_POST['selected_course'],
            'title_en' => $_POST['title_en'],
            'title_ua' => $_POST['title_ua'],
            'title_ru' => $_POST['title_ru'],
            'author_en' => $_POST['author_en'],
            'author_ua' => $_POST['author_ua'],
            'author_ru' => $_POST['author_ru']
        ),
        array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        ));

    echo $wpdb->insert_id;
    die;
    exit;
}

add_action('wp_ajax_delete-literature', 'delete_lit');
function delete_lit()
{
    global $wpdb;

    $id = $_POST['lit_id'];

    $wpdb->delete(
        $wpdb->prefix . 'literature',
        array('ID' => $id),
        array('%d')
    );
};

function htmlShortcodeTab()
{
    $html = '
	<div class="main-tab">
		<ul class="tabs">
			<li id="firstTab" class="active">
				<a href="#first" class="first"><svg  class="ico"x="0px" y="0px" viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve">
					<path d="M47.1,27.7C47.1,27.7,47.1,27.7,47.1,27.7c0-0.1,0-0.2,0-0.3V12.5c0-0.8-0.7-1.5-1.5-1.5H4.4
						c-0.8,0-1.5,0.7-1.5,1.5v24.9c0,0.8,0.7,1.5,1.5,1.5h31.3c0,0,0,0,0,0c0.1,0,0.2,0,0.3,0c0,0,0.1,0,0.1,0c0.1,0,0.1,0,0.2,0
						c0,0,0.1,0,0.1-0.1c0.1,0,0.1,0,0.2-0.1c0.1-0.1,0.2-0.1,0.2-0.2l10-10c0.1-0.1,0.2-0.2,0.2-0.3c0,0,0-0.1,0-0.1
						C47,28,47.1,27.9,47.1,27.7z M5.9,14h38.3v11.9h-8.5c-0.8,0-1.5,0.7-1.5,1.5V36H5.9V14z M37.1,33.8v-4.9H42L37.1,33.8z"/>
					<path d="M10.9,21.1h15.7c0.8,0,1.5-0.7,1.5-1.5s-0.7-1.5-1.5-1.5H10.9c-0.8,0-1.5,0.7-1.5,1.5S10.1,21.1,10.9,21.1z"/>
					<path d="M26.6,23.5H10.9c-0.8,0-1.5,0.7-1.5,1.5s0.7,1.5,1.5,1.5h15.7c0.8,0,1.5-0.7,1.5-1.5S27.4,23.5,26.6,23.5z"/>
				</svg>
					структура курсу</a></li>
			<li id="secondTab">
				<a href="#second" class="second">
					<svg class="ico" x="0px" y="0px" viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve">
						<path  d="M46.8,19c-0.5-4.2-5.4-7.3-11.6-7.3c-4.4,0-8.2,1.6-10.2,4c-2-2.4-5.8-4-10.2-4c-6.2,0-11.2,3.1-11.6,7.3
						c0,0.1,0,0.2,0,0.3v17.2c0,1,0.8,1.8,1.8,1.8c0.6,0,1.1-0.3,1.5-0.8c0.9-1.3,4.1-2.7,8.5-2.7c4.4,0,7.6,1.4,8.5,2.7
						c0.3,0.5,0.9,0.8,1.5,0.8c0.1,0,0.2,0,0.3,0c0.1,0,0.2,0,0.3,0c0.6,0,1.1-0.3,1.5-0.8c0.9-1.3,4.1-2.7,8.5-2.7
						c4.4,0,7.6,1.4,8.5,2.7c0.3,0.5,0.9,0.8,1.5,0.8c0.4,0,0.9-0.2,1.2-0.5c0.4-0.3,0.6-0.8,0.6-1.3V19.3C46.9,19.2,46.8,19.1,46.8,19z
						M14.8,31.8c-3.4,0-6.5,0.7-8.7,2V19.5c0,0,0-0.1,0-0.1c0.2-2.6,4.2-4.8,8.7-4.8s8.5,2.2,8.7,4.8c0,0,0,0.1,0,0.1v14.3
						C21.4,32.6,18.3,31.8,14.8,31.8z M43.9,33.8c-2.1-1.3-5.2-2-8.7-2c-3.4,0-6.5,0.7-8.7,2V19.5c0,0,0-0.1,0-0.1c0.2-2.6,4.2-4.8,8.7-4.8s8.5,2.2,8.7,4.8c0,0,0,0.1,0,0.1V33.8z"/>
					</svg>
					література та ресурси</a>
			</li>
			<li id="thirdTab">
				<a href="#third" class="third">
					<svg class="ico" x="0px" y="0px" viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve">
						<path d="M38.2,39.7H11.8c-0.8,0-1.5-0.7-1.5-1.5V11.8c0-0.8,0.7-1.5,1.5-1.5h26.4c0.8,0,1.5,0.7,1.5,1.5v26.4
							C39.7,39,39,39.7,38.2,39.7z M13.3,36.7h23.4V13.3H13.3V36.7z"/>
						<path d="M24.3,31.3c-0.3,0-0.7-0.1-0.9-0.3l-6-4.9c-0.6-0.5-0.7-1.5-0.2-2.1c0.5-0.6,1.5-0.7,2.1-0.2l4.8,3.9
							l6.8-9.6c0.5-0.7,1.4-0.8,2.1-0.4c0.7,0.5,0.8,1.4,0.4,2.1l-7.8,10.9c-0.2,0.3-0.6,0.6-1,0.6C24.4,31.3,24.3,31.3,24.3,31.3z"/>
					</svg>
				вимоги</a>
			</li>
		</ul>';
    return $html;
}
add_shortcode ('course-tabs', 'htmlShortcodeTab');

add_action('wp_ajax_nopriv_translation-page', 'translationContent');
add_action('wp_ajax_translation-page', 'translationContent');
function translationContent()
{

    $language = $GLOBALS['language'];
    $header = get_table('courses');
    $dictionary = get_table('dictionary');
    $header_array ='';
    $translation_array ='';

    foreach($header as $row){
        if($row['name_' . $language] === null || ''){
            $header_array[$row['hash']][] = $row['name_en'];
        } else {
            $header_array[$row['hash']][] = $row['name_' . $language];
        }
    }

    foreach ($dictionary as $row) {
        if($row['lang_' . $language] === null || '') {
            $translation_array[$row['select']][] = $row['lang_en'];
        } else {
            $translation_array[$row['select']][] = $row['lang_' . $language];
        }
    }

    echo  json_encode($header_array+$translation_array);
    exit;
}

add_action('wp_ajax_nopriv_ajax-page', 'ajaxPage');
add_action('wp_ajax_ajax-page', 'ajaxPage');
function ajaxPage($name)
{
    $name = $_GET['name'];
    $html = do_shortcode("[insert page='" . $name . "' display='content']");
    echo $html;
    exit;
}

function contact_fields()
{
    $facebook = get_post_meta (531, 'facebook', true);
    $vk = get_post_meta (531, 'vk', true);
    $gplus = get_post_meta (531, 'gplus', true);
    $in = get_post_meta (531, 'linkedin', true);
    $skype = get_post_meta (531, 'skype', true);
    $location = get_post_meta (531, 'location', true);
    $contactinfo = get_post_meta (531, 'contactinfo', true);

    $contentFooter = '
    <div class="social-footer">
    <a href="' . $facebook . '" class="social-ico fb"></a>
    <a href="' . $vk . '" class="social-ico vk"></a>
    <a href="' . $gplus  . '" class="social-ico g"></a>
    <a href="' . $in .'" class="social-ico in"></a>
    <a href="' . $skype  . '" class="social-ico skype"></a>
    </div>
    <div class="info">
            <div class="location">' . $location . '</div>
            <div class="contactInfo">' . $contactinfo . '</div>
    </div>';

    return $contentFooter;
}

add_action('wp_ajax_page-dictionary', 'translation_page');
function translation_page()
{
    global $title;

    $page = '<div class="wrap">';
    $page .= '<h1>' . $title . '</h1>';
    $page .= '<section id="langList">';
    $page .= '<div id="lang-table" class="table" cellspacing="0" cellpadding="0">';
    $page .= '<div class="col layer"></div>';
    $page .= '<div class="col checkCol"></div>';
    $page .= '<div class="col select"></div>';
    $page .= '<div class="col langCol"></div>';
    $page .= '<div class="col langCol"></div>';
    $page .= '<div class="col langCol"></div>';
    $page .= '<div class="headerContainer">';
    $page .= '<div id="sortRow" class="sort row">';
    $page .= '<div class="layer"></div>';
    $page .= '<div class="col checkCol"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell"></div>';
    $page .= '<div class="cell"></div>';

    $page .= '<div class="cell addIcon fa fa-plus"></div>';
    $page .= '</div>';
    $page .= '</div>';
    $page .= '<div class="row header">';
    $page .= '<div class="layer"></div>';
    $page .= '<div class="cell check"><input type="checkbox" id="selectAll"></input></div>';
    $page .= '<div class="cell">Select Class</div>';
    $page .= '<div class="cell">lang_ua</div>';
    $page .= '<div class="cell">lang_en</div>';
    $page .= '<div class="cell">lang_ru</div>';
    $page .= '<div class="cell settings"></div>';
    $page .= '</div>';
    $page .= '<div id="tableBody" class="bodyContainer">';
    $page .=  render_dict('true');
    $page .= '</div>';
    $page .= '</div>';
    $page .= '</section>';
    $page .= '</div>';

    echo $page;
}

add_action('wp_ajax_page-dictionary', 'render_dict');
function render_dict($returned)
{
    $dictionary = get_table('dictionary');
    $rows = '';

    foreach ($dictionary as $dic_item) {

        $rows .= '<div class="row">';
        $rows .= '<div class="layer"></div>';
        $rows .= '<div class="cell check"><input name="dic_id"  type="checkbox" data-id="' . $dic_item['id'] . '"></input></div>';
        $rows .= '<div class="cell"><input name="select_class" readonly value="' . $dic_item['select'] . '"></input></div>';
        $rows .= '<div class="cell"><input name="lang_ua" readonly value="' . $dic_item['lang_ua'] . '"></input></div>';
        $rows .= '<div class="cell"><input name="lang_en" readonly value="' . $dic_item['lang_en'] . '"></input></div>';
        $rows .= '<div class="cell"><input name="lang_ru" readonly value="' . $dic_item['lang_ru'] . '"></input></div>';
        $rows .= '<div class="cell controlDiv fa fa-settings">';
        $rows .= '<div class="settingsIcons">';
        $rows .= '<div class="settingsIcon close fa fa-close"></div>';
        $rows .= '<div class="settingsIcon delete fa fa-trash"></div>';
        $rows .= '<div class="settingsIcon save fa fa-save"></div>';
        $rows .= '<div class="settingsIcon edit fa fa-edit"></div>';
        $rows .= '</div>';
        $rows .= '</div>';
        $rows .= '</div>';
    }

    if ($returned) {
        return $rows;
    }

    echo $rows;
    die();
    exit;
}

add_action('wp_ajax_update-translation', 'updateDictionary');
function updateDictionary()
{
    global $wpdb;

    $id = $_POST['dic_id'];

    $data = array(
        'select' => $_POST['select_class'],
        'lang_ua' => $_POST['lang_ua'],
        'lang_en' => $_POST['lang_en'],
        'lang_ru' => $_POST['lang_ru']
    );

    $wpdb->update(
        $wpdb->prefix . 'dictionary',
        $data,
        array('ID' => $id),
        array(
            '%s',
            '%s',
            '%s',
            '%s'
        ),
        array('%d')
    );
}

add_action('wp_ajax_create-translation', 'insertDictionaryTable');
function insertDictionaryTable()
{
    global $wpdb;

    $wpdb->insert(
        $wpdb->prefix .'dictionary',
        array(
            'select' => $_POST['select_class'],
            'lang_ua' => $_POST['lang_ua'],
            'lang_en' => $_POST['lang_en'],
            'lang_ru' => $_POST['lang_ru']
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s'
        ));

    echo $wpdb->insert_id;
    die;
    exit;
}

add_action('wp_ajax_delete-translation', 'delete_dictionary');
function delete_dictionary()
{
    global $wpdb;

    $id = $_POST['dic_id'];

    $wpdb->delete(
        $wpdb->prefix . 'dictionary',
        array('ID' => $id),
        array('%d')
    );
}

function translation_string()
{
    $dictionary = get_table('dictionary');
    $string = array();

    foreach ($dictionary as $dic_item){
        $string[$dic_item['select']][] = $dic_item['lang_' . $GLOBALS['language']];
    }
    return json_encode($string);
}
