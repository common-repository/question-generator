<?php

require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
if( !function_exists('get_plugin_data') ){
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

class QUESTIONGENERATOR_API
{
    public static function questiongenerator_getEndpoint(WP_REST_Request $request){

        $endpoints = array(
            'url',                     
            'new_category',            
            'createArticle',                 
            'updateArticle',      
            'delete_post',             
            'upload_image',                 
            'get_all_categories',
            'get_all_post',
            'post_info',
            'version_check',
            'get_all_pages',
            'reset_qg_plugin'
        );

        if (in_array($request['endpoint'], $endpoints)){
            if (null !== $request->get_header('Key')) {
                if (QUESTIONGENERATOR_Restfull::questiongenerator_isApiSecret($request->get_header('Key'))) {
                    return self::{$request['endpoint']}($request);
                }
                return self::api_error('Key unknown');
            }
            return self::api_error('API Key unknown');
        } else {
            return self::api_error('Unknown action');
        }
    }

    public static function version_check(){
        $plugin = get_plugin_data(QUESTIONGENERATOR_PLUGIN_FILE);
        return self::api_success($plugin['Version']);
    }

    public static function reset_qg_plugin(){
        QUESTIONGENERATOR_Config::questiongenerator_inactive_qg();
        return self::api_success('Your Question generator plugin has been deactivated');
    }

    public static function get_all_post(){
        return self::api_success(get_posts(array('numberposts' => -1)));
    }

    public static function get_all_pages(){
        return self::api_success(get_pages(array('post_type'    => 'page',
        'post_status'  => 'publish')));
    }


    public static function post_info(WP_REST_Request $request){
        if (isset($request['positionId'])) {
            $infos = get_post($request['positionId']);
            if ($infos) {
                return self::api_success(array('titre' => $infos->post_title, 'contenu' => $infos->post_content, 'url' => get_permalink($request["positionId"])));
            }
            return self::api_error('your post ID '.$request['positionId'].' is unknown');
        }
        return self::api_error('Missing parameter');
    }

    
    public static function url(WP_REST_Request $request){
        if (isset($request['positionId'])) {
            return self::api_success(get_permalink($request["positionId"]));
        }
        return self::api_error('Missing parameter');
    }

    /*
     */
    public static function new_category(WP_REST_Request $request){
        if (isset($request['category'])) {
            $idCategory = wp_insert_term($request['category'],'category');
           

            if(!is_wp_error($image)){
                return self::api_success($idCategory);
            }
            return self::api_error($idCategory->get_error_message());
        }
        return self::api_error('Missing parameter');
    }

    /*
     */
    public static function createArticle(WP_REST_Request $request){
        if (isset($request["title"], $request["content"], $request["type"], $request["menu_order"], $request['url_image'], $request['alt_image'])) {
            $article_a = array(
                'post_title' => $request["title"],
                'post_content' => $request["content"],
                'post_status' => 'publish',
                'post_author'   => 1,
                'post_category' => array( $request["idCategory"], ),
                'post_type' => $request["type"],
                'menu_order' => $request["menu_order"],
                'post_name' => $request['url']
            );
            if ($request['date'] != 0) {
                $article_a['post_date'] = $request["date"];
            }
            remove_all_filters("content_save_pre"); 
            $id_article = wp_insert_post($article_a);
            if( !is_wp_error($id_article) && $id_article > 0 ) {
                $statut = self::upload_image($request); 
                
                if (isset($statut['code']) && $statut['code'] == 200){
                    $request['url_image'] = $statut['result'];
                    $request["id_post"] = $id_article;
                    $statut = self::set_image_une($request); 
                    if (isset($statut['code']) && $statut['code'] == 200){
                        return self::api_success($id_article);
                    }
                    return self::api_error('Error thumbnail upload: '.$statut['error']);
                }
                return self::api_error('Error upload Image : '.$statut['error']);
            }
            return self::api_error($id_article->get_error_message());
        }
        return self::api_error("Missing parameter");
    }

    /*
     */
    public static function updateArticle(WP_REST_Request $request){
        if (isset($request["title"], $request["content"], $request['positionId'])) {
            $article_a = array(
                'post_title' => $request["title"],
                'post_content' => $request["content"],
                'ID' => $request['positionId']
            );
            remove_all_filters("content_save_pre"); 
            $id_article = wp_update_post($article_a);
            if( !is_wp_error($id_article) && $id_article > 0 ) {
                if (isset($request['url_image'], $request['alt_image'])){
                    self::delete_image($request);
                    $statut = self::upload_image($request); 
                    if (isset($statut['code']) && $statut['code'] == 200){
                        $request['url_image'] = $statut['result'];
                        $request["id_post"] = $id_article;
                        $statut = self::set_image_une($request); 
                        if (isset($statut['code']) && $statut['code'] == 200){
                            return self::api_success($id_article);
                        }
                        return self::api_error('Error thumbnail upload: '.$statut['error']);
                    }
                    return self::api_error('Error upload Image : '.$statut['error']);
                }
                return self::api_success(1);
            }
            return self::api_error($id_article->get_error_message());
        }
        return self::api_error("Missing parameter");
    }

    /*
     * Delete an Article
     */
    public static function delete_post(WP_REST_Request $request){
        if (isset($request['positionId'])) {
            $statut = wp_delete_post($request['positionId'], true);
            if(!is_wp_error($statut)){
                return self::api_success(1);
            }
            return self::api_error($image->get_error_message());
        }
        return self::api_error('Missing parameter');
    }

    /*
     */
    public static function upload_image(WP_REST_Request $request){
        if (isset($request['url_image'], $request['alt_image'])) {
            $image = media_sideload_image($request["url_image"], '0', $request["alt_image"], "src");
            if(!is_wp_error($image)){
                return self::api_success($image);
            }
            return self::api_error($image->get_error_message());
        }
        return self::api_error("Missing parameter");
    }

    /*
     */
    public static function set_image_une(WP_REST_Request $request){
        if (isset($request['url_image'], $request['id_post'])) {
            $id_image = self::pn_get_attachment_id_from_url($request['url_image']);
            set_post_thumbnail($request['id_post'], $id_image);
            if(!is_wp_error($id_image)){
                return self::api_success($id_image);
            }
            return self::api_error($id_image->get_error_message());
        }
        return self::api_error("Missing parameter");
    }

    /*
     */
    public static function delete_image(WP_REST_Request $request){
        if (isset($request["positionId"])){
            $tId = get_post_thumbnail_id($request["positionId"]);
            if($tId){
                if (wp_delete_attachment($tId, true)){
                    return self::api_success(1);
                }
                return self::api_error('Something went wrong');
            }
            return self::api_error($request["positionId"].' : Id inconnu');
        }
    }


    public static function get_all_categories(WP_REST_Request $request){
        
        return self::api_success(get_categories(array(
            'child_of'    => 0,
            'parent'      => '',
            'orderby'     => 'term_id',
            'order'       => 'DESC',
            'hide_empty'  => 0,
            'hierarchical'=> 1,
        )));
    }


    public static function api_success($result, $code = 200){
        return array('code' => $code, 'result' => $result);
    }

    public static function api_error($result, $code = 401){
        return array('code' => $code, 'result' => $result);
    }

    private static function pn_get_attachment_id_from_url( $attachment_url = '' ) {
       global $wpdb;
       $attachment_id = false;
        if ( '' == $attachment_url )
           return;
        $upload_dir_paths = wp_upload_dir();
        if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
            $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
            $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
            $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
        }
        return $attachment_id;
    }
}
