<?php
/**
 * Guru initiation
 *
 * Initializes Guru's features and includes all necessary files.
 *
 * @package Guru
 */

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
        );

        wp_enqueue_style('guru-theme-css', get_template_directory_uri() . $assets['css'], array(), $version);
        wp_enqueue_script('guru-theme', get_template_directory_uri() . $assets['js'], array('jquery'), $version, true);
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
        'status_id' => 1,
        'FILE' => $_FILES['addingFile']
    );

    echo "<script type='text/javascript'>console.dir($_POST);</script>";

    echo($_POST);

    $wpdb->insert(
        $wpdb->prefix . 'registered_users',
        $data,
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d'
        )
    );
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

    print $data;

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
}

;

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

function get_variables($name, $language)
{
    $caption = just_variable($name . '_' . $language, FALSE);

    if (empty($caption)) {
        $caption = (just_variable($name . '_en', FALSE)) ? just_variable($name . '_en', FALSE) : '';
    };

    $info = just_variable($name . 'Info_' . $language, FALSE) ? just_variable($name . 'Info_' . $language, FALSE) : '';

    $result = array('caption' => $caption, 'info' => $info);

    return $result;
}

;

function courses($atts, $content = null)
{
    // Attributes
    extract(shortcode_atts(
            array(
                'img' => '',
                'name' => '',
                'language' => 'en',
            ), $atts)
    );

    $variables = get_variables($atts['name'], $atts['language']);

    $img = (!empty($atts['img'])) ? ("<img src='" . $atts['img'] . "' />") : '';

    $content = "<div class='course-container course " . $atts['name'] . "'>";
    $content .= "<div>" . $img . "</div>";
    $content .= "<header class='course-caption'>";
    $content .= "<span>" . $variables['caption'] . "</span></header>";
    $content .= "<p>" . $variables['info'] . "</p></div>";

    return $content;
};

add_shortcode('add_course', 'courses');

function get_data_for_select($table)
{
    $coursesTable = get_table($table);

    $ids = array();
    $names = array();

    foreach ($coursesTable as $row) {
        $ids[] = $row['ID'];
        $names[] = $row['name'];
    }

    $names_caption = array();

    if ($table === 'courses') {
        foreach ($names as $name) {
           // $names_caption[] = get_variables($name, 'en')['caption'];
        }
    } else {
        $names_caption = $names;
    }

    return array('ids' => implode(", ", $ids), 'names' => implode(", ", $names_caption));
    die();
    exit;
}

;

function contact_form($atts, $content = null)
{

    $selectList = get_data_for_select('courses');

    $form = "<div id='registrationFormDiv'>";
    $form .= "<header class='sectionTitle'><span>Реєстрація</span></header>";
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
    $form .= '<input id="addFile" type="button" class="addFile" data-style="move-up" value="+ резюме" /></div>';
    $form .= '<input type="file" name="addingFile" id="addFileInput" class="hidden"></input></div>';
    $form .= '<input id="hidden_to" type="hidden" value="itschool@thinkmobiles.com" name="contact_to"/>';

    $form .= "</form></div>";
    return $form;
}

add_shortcode('contact_form', 'contact_form');

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

        $input .= "<div class='selectOptions hidden'>";
        $input .= "<ul>";


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

function add_equaliser()
{

    return "<li class='equaliser'></li>";

}

add_shortcode('add_equaliser', 'add_equaliser');

/*Adding menu to settings*/

add_action('admin_menu', 'register_my_menu_page');

function register_my_menu_page()
{
    add_menu_page('Manage tables', 'Manage tables', 'manage_options', 'my_menu', 'my_menu_output');
    add_submenu_page('my_menu', 'Manage registered users', 'Manage registered users', 'manage_options', 'userpage', 'my_menu_page');
    add_submenu_page('my_menu', 'Manage themes', 'Manage themes', 'manage_options', 'themespage', 'my_theme_page');
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
    reg_users.phone_number as phone_number, reg_users.city as city, courses.name as course_name,
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

    $coursesSelectList = get_data_for_select('courses');
    $statusSelectList = get_data_for_select('reg_users_status');

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
    die();
    exit;
}

add_action('wp_ajax_render-user', 'renderUsersTable');
add_action('wp_ajax_nopriv_render-user', 'renderUsersTable');

function renderUsersTable($returned)
{
    $coursesSelectList = get_data_for_select('courses');
    $statusSelectList = get_data_for_select('reg_users_status');
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

function drawTestimonial($content)
{
    $testimonial = '<div class="testimonial hide">';
    $testimonial .= '<div class="testimonial-img">';
    $testimonial .= '<div class="img-background"></div>';
    $testimonial .= '<div class="circle-img"></div>';
    $testimonial .= '</div>';
    $testimonial .= '<div class="testimonialDescription">';
    $testimonial .= '<span class="tPerson">Софія Найда</span>';
    $testimonial .= '<span class="tDepartment">business analytic</span>';
    $testimonial .= '<span class="tText">Etiam habebis sem dicantur magna mollis euismod. Pellentesque habitant morbi tristique senectus et netus</span>';
    $testimonial .= '</div>';
    $testimonial .= '</div>';
    $result .= '<div class="clear"></div>';

    return $testimonial;
}

function drawTestimonials($atts, $content = null)
{
    extract(shortcode_atts(
            array(
                'count' => 1,
            ), $atts)
    );

    if ($atts['count']) {
        $count = $atts['count'];
    } else {
        $count = 1;
    }

    $result = '<div id="testimonialsHolder">';

    for ($i = 1; $i <= $count; $i++) {
        $result .= drawTestimonial($i);
    }

    $result .= '</div>';
    $result .= '<div class="clear"></div>';

    return $result;
}


add_shortcode('testimonials', 'drawTestimonials');



//[content-lessons]
function contentLessons($atts) {
	extract( shortcode_atts(array(
					'lang'=>'en',
					'coursename'=>''), $atts));
	$result = '';
	$rows = get_table('themes');
    $someCourse = get_table('cources');
    foreach ($someCourse as $course){
        if ($course['name'] == $atts['coursename']) {
        $courseid = $course['id'];
        break;
        }
    }

	$tmp=array();

	foreach ($rows as $row) {
        if ($row['course_id'] == $course['id']) {
		    $tmp[$row['day']][] = $row['theme_' . $atts['lang']];
	    }
	}

	foreach ($tmp as $key => $rows){
	$result .= '<div class="themesPerDay">';
	    $result .= '<span class="day">' . $key .'</span>' . '<div class="themes"><ul>';
	     foreach ($rows as $element) {
	        $result .= '<li>' . $element . '</li>';
	     }
	     $result .= '</ul></div></div>';
	}
	return $result;
}

add_shortcode( 'content-lessons', 'contentLessons' );

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

    $query = "SELECT theme.id as ID, theme.day as DAY, theme.theme_en as THEME_EN,theme.theme_ua as THEME_UA,
    theme.theme_ru as THEME_RU,courses.name as course_name
    FROM {$wpdb->prefix}themes theme
    INNER JOIN {$wpdb->prefix}courses courses ON courses.id = theme.course_id
    ORDER BY id";
    $themeTable = $wpdb->get_results($query, ARRAY_A);
    return array('data' => $themeTable, 'query' => $themeWhere);
    die();
    exit;
}

function my_theme_page()
{
    global $title;

    $coursesSelectList = get_data_for_select('courses');
    $statusSelectList = get_data_for_select('reg_users_status');

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
    $page .= renderThemeTable('true');
    $page .= '</div>';
    $page .= '</div>';

    $page .= '</section>';
    $page .= '</div>';

    echo $page;
}

;

function renderThemeTable($returned)
{
    $coursesSelectList = get_data_for_select('courses');
    //$statusSelectList = get_data_for_select('reg_users_status');
    $themesTable = get_theme_content($_POST['course_id'], $_POST['day'], $_POST['theme_en'], $_POST['theme_ua'], $_POST['theme_ru']);
    var_dump($themesTable);
    $resultHtml = '';
    $count = 0;
    foreach ($themeTable['data'] as $row) {
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
