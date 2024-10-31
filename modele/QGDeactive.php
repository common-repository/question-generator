<?php
/*
* deactivate plugin
*/

class QUESTIONGENERATOR_Deactive {
    
    public static function questiongenerator_deactivate(){
        QUESTIONGENERATOR_Config::questiongenerator_inactive_qg();
    }
}
