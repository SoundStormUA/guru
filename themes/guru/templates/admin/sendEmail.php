<?php

$form = '';
$form .= '<div id="sForm" class="sForm sFormPadding">';
    $form .= '<div class="titleContainer">';
        $form .= '<div id=closeForm class="closeForm fa fa-close"></div>';
        $form .= '<div class="title fa fa-envelope"><span>Send new Email</span></div>';
    $form .= '</div>';
    $form .= '<div id="emailContent">';
        $form .= '<input placeholder="from"></input>';
        $form .= '<input id="emailSubject" placeholder="email subject"></input>';
        $form .= '<textarea id="emailText" placeholder="email text"></textarea>';
        $form .= '<a id="sendButton" href="#">Send email</a>';
    $form .= '</div>';
$form .= '</div>';
echo $form;
?>