<?php

class QUESTIONGENERATOR_Active {

    public static function questiongenerator_activate(){
        if (!get_option('qg_validation')) {
            update_option('qg_validation', -1);
        }
    }
}
