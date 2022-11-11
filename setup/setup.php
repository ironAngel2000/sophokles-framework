<?php
session_start();

echo __DIR__;

die();


$aktURI = str_replace('setup.php','',$_SERVER['SCRIPT_NAME']);

$aktFolder = str_replace('setup.php','',$_SERVER['SCRIPT_FILENAME']);

$twigPathTemplate = [];
$twigPathTemplate[] = $aktFolder.'twig/templates/';
$twigPathTemplate[] = $aktFolder.'twig/includes/';

$aktBASE = explode('/vendor/', $_SERVER['SCRIPT_FILENAME'])[0];
$aktBASE .= '/vendor/';


require_once $aktBASE.'autoload.php';


$twigLoader = new  \Twig\Loader\FilesystemLoader($twigPathTemplate);

$twigOpt = [];
$twigOpt['debug'] = true;


$twigEnvironment = new \Twig\Environment($twigLoader, $twigOpt);


$cssFile = $aktURI.'css/setup.css';

$twigEnvironment->addGlobal('aktURL',$_SERVER['SCRIPT_NAME']);
$twigEnvironment->addGlobal('cssFile',$cssFile);

$setupStatus = 0;

if(isset($_POST['nxtstep'])){
    $setupStatus = (int) $_POST['nxtstep'];
}

if(isset($_POST['btsubmit'])){
    if(trim($_POST['btsubmit'])==='<< back'){
        $setupStatus -= 2;
    }
}


$twigEnvironment->addGlobal('setupStatus',$setupStatus);

if(isset($_POST['dbHost'])){
    if(trim($_POST['dbHost'])===''){
        $_SESSION['dbHost'] = 'localhost';
    }
    else{
        $_SESSION['dbHost'] = trim($_POST['dbHost']);
    }
}

if(isset($_POST['dbPort'])){
    if(trim($_POST['dbPort'])===''){
        $_SESSION['dbPort'] = '3306';
    }
    else{
        $_SESSION['dbPort'] = trim($_POST['dbPort']);
    }
}

if(isset($_POST['dbUser'])){
    $_SESSION['dbUser'] = trim($_POST['dbUser']);
}

if(isset($_POST['dbPassword'])){
    $_SESSION['dbPassword'] = trim($_POST['dbPassword']);
}

if(isset($_POST['dbDatabase'])){
    $_SESSION['dbDatabase'] = trim($_POST['dbDatabase']);
}

if(isset($_POST['dbSocket'])){
    $_SESSION['dbSocket'] = trim($_POST['dbSocket']);
}

$twigEnvironment->addGlobal('dbData',$_SESSION);


if($setupStatus===3){
    $dirRoot = explode('/vendor/', $_SERVER['SCRIPT_FILENAME'])[0];

    $arrDirs = [];
    $arrDirs[] = 'local';
    $arrDirs[] = 'local/traits';
    $arrDirs[] = 'system';
    $arrDirs[] = 'system/config';
    $arrDirs[] = 'system/traits';

    $arrFiles = [];
    $arrFiles['index.php'] ='index.php';
    $arrFiles['local/traits/localController.php'] =  'localController.php';
    $arrFiles['system/autoloader.php'] =  'autoloader.php';
    $arrFiles['system/config/db.php'] =  'db.php';
    $arrFiles['system/config/sysconfig.php'] =  'sysconfig.php';
    $arrFiles['system/traits/systemController.php'] =  'systemController.php';


    //$this->setPort(3306);
    //$this->setSocket(3306);

    foreach($arrDirs as $checkDir){

        $dirName = $dirRoot.'/'.$checkDir;

        if(!is_dir($dirName)){
            mkdir($dirName,'0775',true);
        }
    }


    foreach($arrFiles as $target=>$srcName){
        $targetFile = $dirRoot.'/'.$target;
        $srcFile = $aktFolder.'src/'.$srcName;

        if(!is_file($targetFile) && is_file($srcFile)){

            switch($srcName){
                case 'db.php':
                    $find = fopen($srcFile,'r');
                    $fileTxt = fread($find,filesize($srcFile));
                    fclose($find);

                    $fileTxt = str_replace('[DBHOST]',$_SESSION['dbHost'],$fileTxt);
                    $fileTxt = str_replace('[DBDATABASE]',$_SESSION['dbDatabase'],$fileTxt);
                    $fileTxt = str_replace('[DBUSER]',$_SESSION['dbUser'],$fileTxt);
                    $fileTxt = str_replace('[DBPW]',$_SESSION['dbPassword'],$fileTxt);

                    if(isset($_SESSION['dbSocket']) && trim($_SESSION['dbSocket'])!==''){
                        $fileTxt = str_replace('[DBPORT]'."\r\n",'',$fileTxt);
                        $socket = '$this->setSocket(\''.$_SESSION['dbSocket'].'\');';
                        $fileTxt = str_replace('[DBSOCKET]',$socket,$fileTxt);
                    }
                    else{
                        $fileTxt = str_replace('[DBSOCKET]'."\r\n",'',$fileTxt);
                        $port = '$this->setPort('.(int) $_SESSION['dbPort'].');';
                        $fileTxt = str_replace('[DBPORT]',$port,$fileTxt);
                    }

                    $find = fopen($targetFile,'w+');
                    fwrite($find,$fileTxt);
                    fclose($find);

                    break;
                case 'sysconfig.php':
                    $find = fopen($srcFile,'r');
                    $fileTxt = fread($find,filesize($srcFile));
                    fclose($find);

                    $pwhash = password_hash(uniqid('autogen',true).uniqid('salt',true),PASSWORD_BCRYPT);
                    $fileTxt = str_replace('[PWHASH]',$pwhash,$fileTxt);
                    $find = fopen($targetFile,'w+');
                    fwrite($find,$fileTxt);
                    fclose($find);
                    break;
                default:
                    copy($srcFile,$targetFile);
                    break;
            }

        }


    }
}



$twigTemplate = $twigEnvironment->load('setup.html');
echo $twigTemplate->render();

