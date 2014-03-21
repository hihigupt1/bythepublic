<?php

include("request_handler.php");

$app = new Phalcon\Mvc\Micro ();

/**
 * All the routes
 * 
 */

/**
 * Get Routes.
 */
$app->get('/api/comments/post/{post_id}', "Handler::get_post_comments");
$app->get('/api/post/{long}/{lat}', "Handler::get_post_by_postion");


/**
 * POST Routes.
 */
$app->post('/api/comment/{post_id}',"Handler::add_new_comment");
$app->post('/api/adduser',"Handler::add_new_user");
$app->post('/api/addpost',"Handler::add_new_post");
$app->post('/api/upload/{name}/{ext}',"Handler::upload_video");



/**
 * Response header.
 */
header('Content-Type: application/json;charset=utf-8;');
$app->handle ();
