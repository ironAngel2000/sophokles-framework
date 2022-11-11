<?php

namespace Sophokles\Module;;

use Sophokles\Sophokles\Helper\coreFunctions;

final class moduleLibrary
{
    /** @var array $moduleGroups */
    protected static $moduleGroups = [];

    /** @var string $lastGroupName */
    protected static $lastGroupName = '';

    /** @var array $modules */
    protected static $modules = [];

    /** @var int $modIndex */
    protected static $modIndex = 0;


    /**
     * register module to controller.
     *
     * @var abstractModule
     * @var string         $moduleName
     * @var int            $right
     * @var string         $path
     * @var int            $intDivision
     */
    public static function registerModule(string $moduleClass, string $moduleName, int $right, string $path, $intDivision = 0)
    {
        self::$modules[self::$modIndex] = [trim($moduleClass), $right, $path, $moduleName, $intDivision];
        if (count(self::$moduleGroups) && trim(self::$lastGroupName) !== '') {
            self::$moduleGroups[self::$lastGroupName][] = &self::$modules[self::$modIndex];
        }
        self::$modIndex++;
    }

    /**
     * Define groups for registert modules to controller
     *
     * @var string $groupName
     */
    public static function registerModuleGroup(string $groupName)
    {
        self::$moduleGroups[$groupName] = [];
        self::$lastGroupName = $groupName;
    }

    /**
    * Get the Registere Modules
     *
     * @var userUsers $user
     *
     * @return array
    */
    public static function getModuleList(int $intRight, int $intGroup): array
    {
        $ret = [];

        if (count(self::$moduleGroups)) {
            foreach (self::$moduleGroups as $groupName => $arrModules) {

                foreach($arrModules as $modEntry){
                    $setEntry = false;

                    if(coreFunctions::checkBinary($intRight, $modEntry[1])){
                        $setEntry = true;
                    }

                    if((int) $modEntry[4] != 0){
                        $setEntry = false;

                        if(coreFunctions::checkBinary($modEntry[4], $intGroup)){
                            $setEntry = true;
                        }
                    }


                    if($setEntry === true){
                        if(!isset($ret[$groupName])){
                            $ret[$groupName] = [];
                        }

                        $ret[$groupName][] = $modEntry;
                    }

                }
            }
        } else {
            $ret[''] = self::$modules;
        }

        return $ret;
    }

    private static function checkForModule($chkPath,  $returnArrEntry=false)
    {

        $ret = null;

        $arrRet = null;

        if(coreFunctions::left($chkPath,1)!=='/'){
            $chkPath = '/'.$chkPath;
        }
        if(coreFunctions::right($chkPath,1)!=='/'){
            $chkPath = $chkPath.'/';
        }

        foreach (self::$modules as $arrMod){

            if(coreFunctions::left($arrMod[2],1)!=='/'){
                $arrMod[2] = '/'.$arrMod[2];
            }
            if(coreFunctions::right($arrMod[2],1)!=='/'){
                $arrMod[2] = $arrMod[2].'/';
            }

            if(trim($arrMod[2])===trim($chkPath)){
                $arrRet = $arrMod;
                $ret = $arrMod[0];
                break;
            }
        }

        if($returnArrEntry===true){
            $ret = $arrRet;
        }

        return $ret;
    }


    public static function getModuleRight($chkPath) : int
    {
        $module = self::checkForModule($chkPath, false,true);
        if(is_array($module)){
            return (int) $module[1];
        }
        else{
            return 0;
        }
    }


    public static function checkModuleRight($chkPath, int $intRight, int $intGroup):bool
    {
        $ret = false;
        $module = self::checkForModule($chkPath, false,true);
        $aktRight = 0;
        if(is_array($module)){
            $aktRight = (int) $module[1];
        }


        $ret = coreFunctions::checkBinary($intRight, $aktRight);

        if($ret == true){

            if((int) $module[4]!==0){
                $ret = coreFunctions::checkBinary($module[4], $intGroup);
            }
        }

        return $ret;
    }


    /**
    * Get the Module by path
     *
     * @return abstractModule
    */
    public static function getModulebyPath(string $path) : abstractModule
    {
        $ret = self::checkForModule($path);

        $obj = new $ret;

        if($obj instanceof abstractModule){

        }
        else{
            if(trim($path)==='/'){
                $chkPath = trim($path);
            }
            else{
                $chkPath = explode('/',$path)[1];
            }


            $ret = self::checkForModule($chkPath,true);
            $obj = new $ret;

        }

        if(!$obj instanceof abstractModule){
            trigger_error('no registert module found in "'.$path.'"', E_USER_ERROR);
            die();
        }

        return $obj;
    }

}
