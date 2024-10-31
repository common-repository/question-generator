<?php
/*
* Uninstall Plugin
*/
class QUESTIONGENERATOR_Uninstall {

    public static function questiongenerator_uninstallation(){
        QUESTIONGENERATOR_Config::questiongenerator_uninstall_qg();
    }
}
