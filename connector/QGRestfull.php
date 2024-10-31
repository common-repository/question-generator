<?php

class QUESTIONGENERATOR_Restfull {

    private $nonce;

    public static function questiongenerator_init(){
        add_action('rest_api_init', array(__CLASS__, 'questiongenerator_set_routes'));
    }

    public static function questiongenerator_qg_new_route(){
        register_rest_route('qg/v1', "/API", array(
            'methods' => 'POST',
            'callback' => array('QUESTIONGENERATOR_API', 'questiongenerator_getEndpoint')
        ));
    }

    public static function questiongenerator_isApiSecret($apiKey){
        if ($apiKey == get_option('api_qg_key') && get_option('qg_validation') == 1) {
            return true;
        }
        return false;
    }

    public static function questiongenerator_set_routes(){
        require_once QUESTIONGENERATOR_PLUGIN_DIR . 'connector/QGAPI.php';
        self::questiongenerator_qg_new_route();
    }
}
