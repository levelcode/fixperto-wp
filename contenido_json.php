<?php /* Template Name: contenido_json */ ?>
<?php
//the_title( '<h3>', '</h3>' );
$postid = get_the_ID();
$post = get_post($postid);
$content = apply_filters('the_content', $post->post_content); 
echo $content;
?>