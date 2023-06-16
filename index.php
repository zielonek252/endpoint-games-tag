<?php 
//Plugin Name: Webixa endpoint sender

function games_post_type_add() {
    register_post_type( 'games',
      array(
        'labels' => array(
          'name' => __( 'Games' ),
          'singular_name' => __( 'Game' )
        ),
        'public' => true,
        'has_archive' => true,
      )
    );
  }
  add_action( 'init', 'games_post_type_add' );
  
  function create_taxonomy_games() {
    register_taxonomy(
      'tags',
      'games',
      array(
        'label' => __( 'Tags' ),
        'rewrite' => array( 'slug' => 'tag' ),
        'hierarchical' => false,
      )
    );
  }
  add_action( 'init', 'create_taxonomy_games' );
  
  function send_data_to_endpoint($post_ID, $post, $update) {
    if ($post->post_type == "games" && $post->post_status == "publish") {
        $tags = wp_get_post_terms($post_ID, 'tags', array("fields" => "names"));
        $tags_string = implode(",", $tags);
        $url = "https://chat.webixa.pl/hooks/648b22e27574ae12c40d683d/QXhPWw27My93Tsk7apXwBh2ucSYjKwmrAbBZESh4ZkroTttC";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $data = '{"text":"Wykorzystany endpoint","attachments":[{"text":"id posta: '.$post_ID.'"},{"text":"tagi: '.$tags_string.'"},{"text":"autor: Grzegorz Czarny"}]}';

        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        //for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
var_dump($resp);
var_dump($post);
var_dump($post_id);
var_dump($tags_string);
    }
}
add_action( 'save_post', 'send_data_to_endpoint', 10, 3 );
