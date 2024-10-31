<?php

class QUESTIONGENERATOR_Config {

    public static function questiongenerator_init(){
        if (is_admin()) {
            add_action('admin_menu', array(__CLASS__, 'questiongenerator_admin_menu'));  
            add_action('admin_enqueue_scripts', array(__CLASS__, 'questiongenerator_admin_scripts'));                                        
            add_filter("plugin_action_links_".QUESTIONGENERATOR_PLUGIN_NAME, array(__CLASS__, 'questiongenerator_my_plugin_settings_link' ));
        }
    }

    public static function questiongenerator_admin_scripts() {
        wp_enqueue_style('qg_css', QUESTIONGENERATOR_PLUGIN_URL .'css/settings.css', array());
    }

    public static function questiongenerator_admin_menu(){
        add_menu_page(
            'QuestionGenerator',
            'QuestionGenerator',
            'manage_options',
            'questiongenerator-config',
            array(__CLASS__, 'questiong_config'),
            'data:image/svg+xml;base64,' . base64_encode(file_get_contents(QUESTIONGENERATOR_PLUGIN_DIR . '/images/qg_logo.svg'))
        );
    }

    public static function questiong_config() {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = sanitize_text_field($_SERVER['REQUEST_METHOD']);
            if (strtoupper($method) === 'POST') {
                $code_erreur = 0;
                $apiKey = sanitize_key($_POST['api_qg_key']);
                if (null !== $apiKey) {
                    update_option('api_qg_key', $apiKey);
                }
                if($code_erreur = self::questiongenerator_verifyAccount()){
                    self::questiongenerator_inactive_qg();
                }
            }
        }
        
        require QUESTIONGENERATOR_PLUGIN_DIR . 'front/config.php';
    }

    public static function questiongenerator_my_plugin_settings_link($links) {
        $settings_link = '<a href="admin.php?page=questiongenerator-config">'.__('Settings').'</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    // Check User Identities
    private static function questiongenerator_verifyAccount(){
        $response = self::questiongenerator__post(array(
            'verify' => true,
            'nonce' => wp_create_nonce('qg_verify')
        ));

        $response = json_decode($response['body']);
        if ($response->statusCode == '200'){
            update_option('qg_validation', 1);
            return 0;
        } else {
            return 1;
        }
    }

    public static function questiongenerator_inactive_qg(){
        if (get_option('qg_validation') == 1) {
            update_option('qg_validation', 4);
        
        }
    }

    public static function questiongenerator_uninstall_qg(){
            delete_option('qg_validation');
            delete_option('api_qg_key');
    }


    private static function questiongenerator__post($body){
        if(wp_verify_nonce( $body['nonce'], 'qg_verify' )){
            $plugin = get_plugin_data(QUESTIONGENERATOR_PLUGIN_FILE);
            $body = array_merge($body, array(
                'domain' => self::questiongenerator_trimToRoot(getenv('HTTP_HOST')),
                'version' => $plugin['Version']
            ));
            $body = wp_json_encode( $body );
            return wp_remote_post('https://www.question-generator.com/api/verify', array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'   => [
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . get_option('api_qg_key')
                ],
                'body'        => $body,
                'cookies'     => array()
            ));
        }
        
    }

    private static function questiongenerator_trimToRoot($url){
            $root = trim($url);
            $root = parse_url($root, PHP_URL_HOST);
            $root = str_replace("www.", "", $root);
           

            if($root == "" && strpos($url, ".") != 0){
                 $root = "http://".$url;
                 $root = parse_url($root, PHP_URL_HOST);
                 $root = str_replace("www.", "", $root);
            }
            return $root;
    }
}
