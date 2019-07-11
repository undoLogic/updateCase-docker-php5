<?php
/**
 * Instructions how to setup
 *
 * Download this file from UpdateCase.com
 *
 * Copy this file to your 'webroot' of your cakephp 2.x project
 *    So now you should see the file app/webroot/updateCase.php
 *
 * BeforeFilter (of App_controller.php)
 * add:
 *
 * require_once(APP.'webroot'.DS.'updateCase.php');$this->updateCase = new UpdateCase;$this->set('updateCase', $this->updateCase);
 * Configure::write("UpdateCase.variant_id", ###);
 *
 * Now change the ### to the variant number,
 * In UpdateCase click on the 'Site' in the breadcrumbs bar
 * And each variant has a number
 * Change the ### to the variant you are using
 *
 *  In each action (each method for a page)
 *  Add:
 *  Configure::write("UpdateCase.slug", 'Home');
 *
 *    Now in the each VIEW put this at the top
 *
 *
 * <?php $updateCase->loadPageBySlug(Configure::read("UpdateCase.slug")); ?>
 * OR you can specify the slug here and not add it to the controller
 *
 * <?php $updateCase->loadPageBySlug('Home'); ?>
 *
 *
 * Then in your view you can call all your methods like this: (see the api for all available)
 *
 * <?= $updateCase->getContentBy('Top','title'); ?>
 *
 *
 */
$token = '4#%&BD^*%EFHU*&^%$DCKHFD!!!ADFRGBNJF';
$pathJsonFile = '../Config/Schema/';




function writeToLog($message, $newLine = true) {

    if (is_array($message)) {
        $message = implode("\n", $message);
    }

    if ($newLine) {
        $message = "\n".date('Ymd-His').' > '.$message;
    } else {
        $message = ' > '.$message;
    }
    file_put_contents('updateCase.log', $message, FILE_APPEND);

    //echo APP.'tmp/logs/'.$type;


}


writeToLog('receiving intro '.json_encode($_GET));


//external communcations
if (isset($_POST['token'])) {
    if ($_POST['token'] != $token) {
        die ('405: NO ACCESS');
    } else {
        if (isset($_GET['test'])) {
            if ($_GET['test'] == 'true') {
                echo 'access granted';
            }
        }

        if (isset($_GET['version'])) {
            if ($_GET['version'] == 3) {

                //echo 'h';
                $decoded = json_decode($_POST['variant']);
                //$uuid = $decoded->Variant->uuid;
                $uuid = $decoded[0]->Variant->uuid;
                $variant_id = $decoded[0]->Variant->id;

                if (empty($variant_id)) $variant_id = 'unknown';

                $pathJsonFile = $pathJsonFile.$variant_id.'/';

                if (!file_exists($pathJsonFile)) {
                    mkdir($pathJsonFile);
                }

                $myfile = fopen($pathJsonFile . $uuid . ".json", "w") or die("Unable to open file!");
                fwrite($myfile, $_POST['variant']);
                fclose($myfile);

                echo 'IMPORTED';
            } else {
                echo 'Command not recognized';
            }
        }


    }
} else {

    function showPasswordForm() {
        $c = '';
        $c .= '<div style="width: 300px;">';
        $c .= '<form action="" method="GET">';
        $c .= '<input name="login"/>';
        $c .= '</form>';
        $c .= '</div>';
        echo $c;
    }

    function requireAccess($loginString) {
        if ($loginString == date('d').'-'.date('m')) {
            //yes
            return true;
        } else {
            echo '<h4 style="color: RED;">Password REQUIRED</h4>';
            showPasswordForm();
            $message = '405: Wrong access credential';
            //$this->writeToLog('UpdateCase: '.$message, true);
            die ($message);
        }
    }

    function setupLocal() {
        //local communcation
        requireAccess($_GET['login']);

        //$command = "rsync --help";


        $base = $_SERVER['SERVER_NAME'].''.$_SERVER['REDIRECT_URL'].'/updateCase.php?login='.date('d').'-'.date('m');

        if (isset($_GET['command'])) {
            switch($_GET['command']) {
                case 'status':
                    echo 'status';
                    break;
                case 'createDEVfromCLIENT':
                    ctrl_deleteDEV();
                    ctrl_copyCLIENTtoDEV();
                    echo '<h4 style="color: GREEN;">Finished setting up DEV (from CLIENT)</h4>';
                    break;
                case 'createDEVfromLIVE':
                    ctrl_deleteDEV();
                    ctrl_copyLIVEtoDEV();
                    echo '<h4 style="color: GREEN;">Finished setting up DEV (from LIVE)</h4>';
                    break;
                case 'prepareCLIENTfromDEV':
                    ctrl_deleteCLIENT();
                    ctrl_copyDEVtoCLIENT();
                    echo '<h4 style="color: GREEN;">Finished setting up DEV (from CLIENT)</h4>';
                    break;
                case 'prepareCLIENTfromLIVE':
                    ctrl_deleteCLIENT();
                    ctrl_copyLIVEtoCLIENT();
                    echo '<h4 style="color: GREEN;">Finished setting up CLIENT (from LIVE)</h4>';
                    break;
                case 'launchCLIENT':
                    //ensure dev/backup exists
                    ctrl_getBACKUPpath();
                    ctrl_getClientPath();
                    if (!file_exists(ctrl_getClientPath().'/app')) {
                        echo '<h4 style="color: RED;">CLIENT DOES NOT EXIST</h4>';
                    } else {
                        ctrl_moveLIVEtoBACKUP();
                        echo ctrl_moveCLIENTtoLIVE();
                        echo '<h4 style="color: GREEN;">CLIENT IS LIVE</h4>';
                    }
                    break;
                case 'launchDEV':
                    //ensure dev/backup exists
                    ctrl_getBACKUPpath();
                    ctrl_getDevPath();

                    if (!file_exists(ctrl_getDevPath().'/app')) {
                        echo '<h4 style="color: RED;">DEV DOES NOT EXIST</h4>';
                    } else {
                        ctrl_moveLIVEtoBACKUP();
                        echo ctrl_moveDEVtoLIVE();
                        echo '<h4 style="color: GREEN;">DEV IS LIVE</h4>';
                    }
                    break;
                case 'restoreRecent':
                    //ensure dev/backup exists
                    if (!ctrl_getRecentBACKUPfolder()) {
                        echo '<h4 style="color: RED;">NO RECENT BACKUPS</h4>';
                    } else {
                        ctrl_getBACKUPpath();
                        ctrl_deleteDEV();
                        ctrl_moveLIVEtoDEV();
                        ctrl_moveBACKUPtoLIVE();

                        echo '<h4 style="color: GREEN;">RECENT BACKUP has been restored</h4>';
                    }
                    break;
                case 'cleanUpBackup':
                    $deleted = cleanUpBackup();
                    if (!$deleted) {
                        echo '<h4 style="color: RED;">NOTHING to clean up</h4>';

                    } else {
                        echo '<h4 style="color: orange;">REMOVED BACKUPS: '.implode(', ',$deleted).'</h4>';
                    }
                    break;

                case 'svn-update':
                    echo 'will update svn';

                    echo '<h4 style="color: GREEN;">AVAILABLE PROJECTS TO CHECKOUT into DEV</h4>';

                    //exit;

                    $path = ctrl_getDevPath();
                    if (!$path) {
                        //die ('FATAL: need dev folder to proceed');
                    } else {

                        //rm -rf dev/*
                        //rm -rf dev/.* //get ride of the warning about . and ..

                    }
                    echo $path;
                    break;


                case 'svnList':
                    //$command = "svn export --force https://wush.net/svn/undologic/2017/undoLogic/trunk/. svn/. --username www-data --password omithosi";

                    echo '<h4 style="color: GREEN;">AVAILABLE PROJECTS TO CHECKOUT into DEV</h4>';


                    foreach ($out as $each) {
                        echo "<a href='http://".$base."&command=svnExport&project=".$each."'>".$each."</a> ";

                    }

                    break;
                case 'svnTest':
                    echo '<h4 style="color: orange;">SVN: You should see the SVN HELP below indicating the SVN is installed on the server</h4>';
                    $command = "svn --help";
                    $third = exec($command, $out, $second);
                    print_r ($third);
                    print_r ($out);
                    print_r ($second);

                    echo '<h4 style="color: orange;">SVN: done test</h4>';

                    break;
                case 'svnExport':
                    echo '<h4 style="color: orange;">Exporting: '.$_GET['project'].'</h4>';

                    ctrl_deleteDEV();
                    $third = exec($command, $out, $second);

                    echo '<h4 style="color: GREEN;">Finished !</h4>';
                    break;
                case 'log':
                    $found = file_get_contents('updateCase.log');
                    $parts = explode("\n", $found);
                    $parts = array_reverse($parts);


                    foreach ($parts as $part) {
                        echo $part."\n<br/>";
                    }

                    exec('rm updateCase.log');

                    break;
                default:
                    //print_r ($_SERVER);
            }
        }





        //show the options

        $links = '';
        $links .= "<br/>";$links .= "<br/>";
        $links .= 'CHOOSE: ';
        $links .= "<a href='http://".$base."'>HOME</a> - ";


        $links .= "<br/>";
        //update SVN

        $links .= '<div style="width: 300px;">';
        $links .= '<form action="?login=30-11" method="GET">';
        $links .= '<input name="command" value="svn-update"/>';

        if (isset($_GET['login'])) {
            $links .= '<input name="login" value="'.$_GET['login'].'"/>';
        }
        $links .= '<input name="svn-username"/>';
        $links .= '<input name="svn-password" type="password"/>';
        $links .= '<input name="APP" placeholder="Which app eg updateCase.com"/>';
        $links .= '<input name="path" placeholder="Path to project OR leave black to see listings"/>';
        $links .= '<input type="submit" />';
        $links .= '</form>';
        $links .= '</div>';




        $links .= "<br/>";
        $links .= 'DEV: ';
        $option = 'createDEVfromCLIENT';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";
        $option = 'createDEVfromLIVE';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";


        $links .= "<br/>";
        $links .= 'DEV: ';
        $option = 'createDEVfromCLIENT';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";
        $option = 'createDEVfromLIVE';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";

        $links .= "<br/>";
        $links .= 'CLIENT: ';
        $option = 'prepareCLIENTfromDEV';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";
        $option = 'prepareCLIENTfromLIVE';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";

        $links .= "<br/>";
        $links .= "<br/>";
        $links .= 'LIVE: ';

        $option = 'launchDEV';
        $links .= "<a href='http://".$base."&command=".$option."' onclick=\"if (confirm('Really launch DEV ?')) { return true; } return false;\">".$option."</a> - ";
        $option = 'launchCLIENT';
        $links .= "<a href='http://".$base."&command=".$option."' onclick=\"if (confirm('Really launch CLIENT ?')) { return true; } return false;\">".$option."</a> - ";
        $option = 'restoreRecent';
        $links .= "<a href='http://".$base."&command=".$option."' onclick=\"if (confirm('Really restore backup ?')) { return true; } return false;\">".$option."</a> - ";

        $links .= "<br/>";
        $links .= "<br/>";
        $links .= 'Maintenance: ';
        $option = 'cleanUpBackup';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";
        $option = 'svnList';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";
        $option = 'svnTest';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";
        $option = 'log';
        $links .= "<a href='http://".$base."&command=".$option."'>".$option."</a> - ";


        $links .= "<br/>";
        $links .= "<br/>";
        $links .= "<br/>";
        $links .= "VIEW: ";
        if (file_exists(ctrl_getDevPath().'/app')) {
            $links .= "<a href='http://".$_SERVER['SERVER_NAME'].'/dev'."' target='_blank'>DEV</a> - ";
        }

        if (file_exists(ctrl_getClientPath().'/app')) {
            $links .= "<a href='http://".$_SERVER['SERVER_NAME'].'/client'."' target='_blank'>CLIENT</a> - ";
        }

        if (file_exists(ctrl_getLIVEPath().'/app')) {
            $links .= "<a href='http://".str_replace('test.','', $_SERVER['SERVER_NAME'])."' target='_blank' onclick=\"if (confirm('This uses the server address and NOT the LIVE domain, issues might result that do not actually exist on the LIVE website')) { return true; } return false;\">Main Server LINK</a>";
        }


        //offer the links of what to do
        echo $links;
        exit;
    }


    function cleanUpBackup()
    {

        $backup_documentRoot = ctrl_getBACKUPpath();

        $excludeFromScan = array(
            '.', '..', '.DS_Store', '.svn', 'Thumbs.db', 'Thumbs.db:encryptable'
        );

        $folders = array_diff(scandir($backup_documentRoot), $excludeFromScan);

        sort($folders);
        //remove the oldest one, and let's keep it
        $secondOldest = reset($folders);
        $secondOldest = next($folders);
        $folders = array_diff($folders, array($secondOldest));

        $deleted = array();

        foreach ($folders as $each) {
            //echo $backup_documentRoot.'/'.$each.' ';

            if (!is_dir($backup_documentRoot.'/'.$each)) {
                continue;
            }

            //skip todays first backup
            if ($each == date('Y-m-d').'_1') {
                continue; //keep our first backup today
            }

            $command = 'rm -rf '.$backup_documentRoot.'/'.$each;
            //echo $command.' ';
            exec($command, $result);

            $deleted[] = $each;
        }

        return $deleted;

    }


    function ctrl_moveLIVEtoDEV() {

        $devPath = ctrl_getDevPath();
        $livePath = ctrl_getLIVEPath();

        $message = '';

        //loop until there is no more, meaning the previous is right
        $command = 'mv ' . $livePath . '/' . '* ' . $devPath;
        //print_r ($command);exit;
        exec($command);
        $message .= $command . ' === ';

        $command = 'mv ' . $livePath . '/' . '.[^.]* ' . $devPath;
        exec($command);
        $message .= $command . ' done ';

        return true;

    }

    function ctrl_moveDEVtoLIVE() {

        $devPath = ctrl_getDevPath();
        $livePath = ctrl_getLIVEPath();

        $message = '';

        //loop until there is no more, meaning the previous is right
        $command = 'mv ' . $devPath . '/' . '* ' . $livePath;
        //print_r ($command);exit;
        exec($command);
        $message .= $command . ' === ';

        $command = 'mv ' . $devPath . '/' . '.[^.]* ' . $livePath;
        exec($command);
        $message .= $command . ' done ';

        return true;

    }

    function ctrl_moveCLIENTtoLIVE() {

        $clientPath = ctrl_getClientPath();
        $livePath = ctrl_getLIVEPath();

        $message = '';

        //loop until there is no more, meaning the previous is right
        $command = 'mv ' . $clientPath . '/' . '* ' . $livePath;
        //print_r ($command);exit;
        exec($command);
        $message .= $command . ' === ';

        $command = 'mv ' . $clientPath . '/' . '.[^.]* ' . $livePath;
        exec($command);
        $message .= $command . ' done ';

        return true;

    }

    function ctrl_moveLIVEtoBACKUP() {

        $backupLocation = ctrl_createBACKUPfolder();

        $livePath = ctrl_getLIVEPath();

        $message = '';
        $command = 'mv ' . $livePath . '/' . '* ' . $backupLocation . '/';
        //echo $command;exit;
        exec($command);
        $message .= $command . ' === ';

        $command = 'mv ' . $livePath . '/' . '.[^.]* ' . $backupLocation . '/';
        exec($command);
        $message .= $command . ' done ';



        return true;

    }

    function ctrl_moveBACKUPtoLIVE() {

        $backupLocation = ctrl_getBACKUPpath().'/'.ctrl_getRecentBACKUPfolder();

        $livePath = ctrl_getLIVEPath();

        $message = '';
        $command = 'mv ' . $backupLocation . '/' . '* ' . $livePath . '/';
        //echo $command;exit;

        exec($command);
        $message .= $command . ' === ';

        $command = 'mv ' . $backupLocation . '/' . '.[^.]* ' . $livePath . '/';
        exec($command);
        $message .= $command . ' done ';


        return true;

    }

//this will only allow to get the folder from TODAY, prevent spoofing
    function ctrl_getRecentBACKUPfolder() {

        //backup location
        $backup_documentRoot = ctrl_getBACKUPpath();
        //if it exists, create an alternate
        $folders = scandir($backup_documentRoot);
        $folders= array_reverse($folders);

        foreach ($folders as $folder) {
            //echo $folder;
            //echo "<br/>";
            //we want the last directory that is NOT empty
            $tmp = explode('_', $folder);
            if ($tmp[0]== date('Y-m-d')) {
                //good it was created today
                if (file_exists($backup_documentRoot.'/'.$folder.'/'.'app')) {
                    //it is a full directory
                    return $folder;
                }
            } else {
                //echo $tmp[0];
                //echo "<br/>";
            }
        }

        //not recent backups
        return false;

    }

    function ctrl_createBACKUPfolder() {


        //backup location
        $backup_documentRoot = ctrl_getBACKUPpath();
        //if it exists, create an alternate

        //echo $backup_documentRoot;
        //exit;
        $link = $backup_documentRoot . '/' . date('Y-m-d');




        //let's assign a number to our dated dir
        $number = 0;
        do {
            $number++;

            if ($number > 100) {
                $message = 'FATAL: woops timeout!';
                $this->writeToLog('UpdateCase: '.$message, true);
                die ($message);
            }
        } while (file_exists($link . '_' . $number));
        //let's create our dir with a number
        $command = 'mkdir ' . $link . '_' . $number;
        exec($command);

        //exit;
        return $link . '_' . $number;
    }

    function ctrl_copyCLIENTtoDEV() {

        $clientPath = ctrl_getClientPath();
        //echo $livePath;exit;
        $devPath = ctrl_getDevPath();

        $command = 'cp -r ' . $clientPath . '/. ' . $devPath;
        //copy the www to testing
        exec($command, $result);

        return true;

    }

    function ctrl_copyLIVEtoCLIENT() {

        $livePath = ctrl_getLIVEPath();
        //echo $livePath;exit;
        $clientPath = ctrl_getClientPath();

        $command = 'cp -r ' . $livePath . '/. ' . $clientPath;
        //echo $command;exit;
        //copy the www to testing
        exec($command, $result);

        return true;

    }

    function ctrl_copyLIVEtoDEV() {

        $livePath = ctrl_getLIVEPath();
        //echo $livePath;exit;
        $devPath = ctrl_getDevPath();

        $command = 'cp -r ' . $livePath . '/. ' . $devPath;
        //echo $command;exit;
        //copy the www to testing
        exec($command, $result);

        return true;

    }

    function ctrl_deleteDEV() {
        $devPath = ctrl_getDevPath();

        $command = 'rm -rf '.$devPath.'/*';
        //echo $command;exit;

        //pr ($command);exit;

        exec($command, $result);

        $command = 'rm -rf '.$devPath.'/.*';
        exec($command, $result);
    }

    function ctrl_deleteCLIENT() {
        $clientPath = ctrl_getClientPath();

        $command = 'rm -rf '.$clientPath.'/*';
        //echo ($command);exit;
        exec($command, $result);

        $command = 'rm -rf '.$clientPath.'/.*';
        exec($command, $result);

        return true;
    }

    function ctrl_getDevPath() {
        //version 3
        $version3 = $_SERVER['DOCUMENT_ROOT'].'/'.'dev';
        //echo $version3;
        if (file_exists($version3)) {
            return $version3;
        } else {
            echo tag_warning('Path does not exist: '.$version3);
            return false;
        }

//    $ctrlSub = Configure::read('ctrlbot.subdomain');
//    if (empty($ctrlSub)) die ('default ctrlbox not defined');
//
//    $version1 = str_replace($ctrlSub, 'test/dev', $_SERVER['DOCUMENT_ROOT']);
//    $version2 = str_replace($ctrlSub, 'dev', $_SERVER['DOCUMENT_ROOT']);
//
//    if (file_exists($version1)) {
//        if ($version1 == $_SERVER['DOCUMENT_ROOT']) {
//            die ('could not get the dev path');
//        }
//        return $version1;
//    } elseif (file_exists($version2)) {
//        if ($version2 == $_SERVER['DOCUMENT_ROOT']) {
//            die ('could not get the dev path');
//        }
//        return $version2;
//    } else {
//        return false;
//    }
    }

    function tag_warning($message = false) {

        echo '<span style="background: red; color: white;">';
        echo $message;
        echo '</span><br/>';

    }

    function ctrl_getLIVEPath() {

        //ensure it does not use test in the path
        $root = $_SERVER['DOCUMENT_ROOT'];
        $root = str_replace('test', 'www', $root);

        if (file_exists($root)) {
            return $root;
        } else {
            die ('LIVE path is not accessible: '.$root);
        }
    }

    function ctrl_getClientPath() {

        $version3 = $_SERVER['DOCUMENT_ROOT'].'/'.'client';
        if (file_exists($version3)) {
            return $version3;
        } else {
            echo tag_warning('Path does not exist: '.$version3);
            return false;
        }


//
//    $ctrlSub = Configure::read('ctrlbot.subdomain');
//    if (empty($ctrlSub)) die ('default ctrlbox not defined');
//
//    $version1 = str_replace($ctrlSub, 'test/client', $_SERVER['DOCUMENT_ROOT']);
//    $version2 = str_replace($ctrlSub, 'client', $_SERVER['DOCUMENT_ROOT']);
//
//    if (file_exists($version1)) {
//
//        if ($version1 == $_SERVER['DOCUMENT_ROOT']) {
//            die ('could not get the dev path');
//        }
//
//        return $version1;
//    } elseif (file_exists($version2)) {
//
//        if ($version2 == $_SERVER['DOCUMENT_ROOT']) {
//            die ('could not get the dev path');
//        }
//
//        return $version2;
//    } else {
//        return false;
//    }
    }

    function ctrl_getBACKUPpath() {

        //ensure it does not use test in the path
        $root = $_SERVER['DOCUMENT_ROOT'];
        $root = str_replace('test', 'backup', $root);

        if (file_exists($root)) {
            return $root;
        } else {
            die ('BACKUP path is not accessible: '.$root);
        }



//    $version1 = str_replace('test', 'backup', $_SERVER['DOCUMENT_ROOT']);
//
//    //pr ($root);exit;
//    if (file_exists($version1)) {
//        if ($version1 == $_SERVER['DOCUMENT_ROOT']) {
//            //die ('could not get the backup path');
//        } else {
//            return $version1;
//        }
//
//    }
//
//
//    $version2 = str_replace('ctrlbot', 'backup', $_SERVER['DOCUMENT_ROOT']);
//    if (file_exists($version2)) {
//
//        if ($version2 == $_SERVER['DOCUMENT_ROOT']) {
//            //die ('could not get the backup path');
//        } else {
//            return $version2;
//        }
//
//
//    }
//
//    //ensure it does not use test in the path
//    $root = $_SERVER['DOCUMENT_ROOT'];
//    $root = str_replace('test', 'www', $root);
//
//    $ctrlSub = Configure::read('ctrlbot.subdomain');
//    if (empty($ctrlSub)) die ('default ctrlbox not defined');
//
//    $version1 = str_replace($ctrlSub, 'backup', $root);
//    if (file_exists($version1)) {
//        if ($version1 == $root) {
//            die ('could not get the backup path');
//        }
//
//        return $version1;
//    } else {
//        return false;
//    }
    }

    function ctrl_copyDEVtoCLIENT() {

        $clientPath = ctrl_getClientPath();
        //echo $livePath;exit;
        $devPath = ctrl_getDevPath();

        $command = 'cp -r ' . $devPath . '/. ' . $clientPath;
        //echo $command; exit;
        //copy the www to testing
        exec($command, $result);

        return true;
    }



    //if this being accessed directly
    if (isset($_SERVER)) {
        if (strpos($_SERVER['SCRIPT_NAME'], 'updateCase.php') !== false) {
            setupLocal();
        }
    }

}




if (class_exists('Object')) {
    // Put class TestClass here

    App::uses('Folder', 'Utility');
    App::uses('File', 'Utility');
    App::uses('HttpSocket', 'Network/Http');

    class UpdateCase extends Object
    {

        var $hostPath = 'http://www.updatecase.com/';


        var $debug = array();
//        //var $hostPath = 'http://localhost/updateCase/back-end/';
//
//        var $helpers = array();
//
//        var $testMode = false;
//        var $debug = false;
//        var $quit = false; //if we are coming back from a function we want to stop and not continue
//
//        var $testTitle = "Title Goes Here";
//        var $testImage = 'http://www.setupcase.com/app/webroot/uploads/1/62/grass_house.jpg';
//
//        var $testContent = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc neque nibh, porttitor ac nisi non, consequat
//interdum magna. Phasellus quis risus lobortis, laoreet tellus vitae, feugiat nibh. Vestibulum vulputate placerat congue. In feugiat ornare eros, at volutpat nibh imperdiet sit amet. Pellentesque vehicula ac erat at pulvinar. Pellentesque tempor consectetur efficitur. Nullam aliquam convallis ligula volutpat tempus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus vehicula, turpis maximus ultricies porta, magna magna facilisis nulla, vel dictum turpis nunc ut ipsum. Fusce ipsum magna, consequat quis varius eget, aliquet vitae dui. Nulla a porta purus. Quisque facilisis ac eros tincidunt ornare. Maecenas interdum cursus diam, in malesuada enim
//                    tempus iaculis. Ut accumsan pretium enim at condimentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pulvinar risus eu metus malesuada tristique. Sed purus ex, molestie eu aliquet sed, vulputate nec massa. Curabitur volutpat dignissim neque. Praesent rutrum rhoncus egestas. Praesent sit amet tempor quam.";
//
//        var $testOriginalContent = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc neque nibh, porttitor ac nisi non, consequat
//interdum magna. Phasellus quis risus lobortis, laoreet tellus vitae, feugiat nibh. Vestibulum vulputate placerat congue. In feugiat ornare eros, at volutpat nibh imperdiet sit amet. Pellentesque vehicula ac erat at pulvinar. Pellentesque tempor consectetur efficitur. Nullam aliquam convallis ligula volutpat tempus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus vehicula, turpis maximus ultricies porta, magna magna facilisis nulla, vel dictum turpis nunc ut ipsum. Fusce ipsum magna, consequat quis varius eget, aliquet vitae dui. Nulla a porta purus. Quisque facilisis ac eros tincidunt ornare. Maecenas interdum cursus diam, in malesuada enim
//                    tempus iaculis. Ut accumsan pretium enim at condimentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pulvinar risus eu metus malesuada tristique. Sed purus ex, molestie eu aliquet sed, vulputate nec massa. Curabitur volutpat dignissim neque. Praesent rutrum rhoncus egestas. Praesent sit amet tempor quam.";
//
//        var $testText = false;
//        var $jsonData = false;
//        var $variantName = false;
//        var $variant = false;
//        var $language = false;
//
//        var $slug = false;
//        var $page = false;
//        var $uuid = '';
//
//        var $locationName = false;
//        var $location = false;
//
//        var $elementName = false;
//        var $element;
//
//        var $locationNames = array();
//
//        var $groupName = false;
//        var $groupNames = array();
//
//        public function enableTestMode() {
//            $this->testMode = true;
//        }
//
//        public function disableTestMode() {
//            $this->testMode = false;
//        }
//
        var $possibleLanguages = array(
            'eng' => 'eng',
            'en-us' => 'eng',
            'en-ca' => 'eng',
            'eng' => 'eng',
            'fr-ca' => 'fre',
            'fre' => 'fre',
            'fra' => 'fre',
            'ALL' => 'ALL'
        );
//
        var $convertToLongLang = array(
            'eng' => 'en-ca',
            'fre' => 'fr-ca'
        );

        var $location;
//
//        var $count = 0;
//
//        public function setTestContent($testText) {
//
//            $this->testMode = Configure::read('SetupCase.enableTestMode');
//
//            $this->resetTest = false; //we are setting, so we don't want to see default text
//            $this->testText = $testText;
//        }
//
//        public function resetTestText() {
//            $this->testText = false;
//        }
//
//

    private function initLang() {
        if (empty($this->language)) {

            $this->language = Configure::read('UpdateCase.language');

            //pr (Configure::read('UpdateCase.language'));
            //exit;
            //pr ($this->language);exit;
            $this->language = $this->possibleLanguages[$this->language];
        }
    }


    private function prepareTranslation($element, $term) {

        //echo 'ddd';
        $translations = $this->getByWithoutLoading('All', 'Translations', $element, 'en-ca');
        //echo 'trans'.$translations.'222';
        //exit;

        if (!empty($translations)) {
            //pr ($this->getByWithoutLoading('All', 'Translations', $element));
            $title = $this->cleanUpStringForQuotedSections($translations);
            //pr ($title);exit;
        } else {
            //die ('no transl');
            //no translation
            return $term;
        }
        $title = str_replace("<br><br/>", "<-->", $title);
        $title = str_replace("<br />", "<-->", $title);
        $title = str_replace("<br/>", "<-->", $title);
        $title = trim($title);
        //echo $title;
        $tmp = explode("<-->", $title);
        //print_r ($tmp);exit;
        foreach ($tmp as $eRow) {
            //print_r ($eRow);
            $tmp_term = explode(">", trim($eRow));
            if (strtolower(trim($tmp_term[0])) == strtolower(trim($term))) {
                return $tmp_term[1];

            }
        }

        return $term;

    }

        public function Translate($term, $element = 'en->fr')
        {

            $this->prepareJsonData();

            $this->initLang();

            if ($this->language == 'eng') {
                //do we have a en->en translations
                $translated = $this->prepareTranslation('en->en', $term);
            } else {
                $translated = $this->prepareTranslation($element, $term);
            }

            return $translated;
        }
//
//
        var $seoLocationName = 'SEO';

//
        public function getMetaTitle()
        {
            //do we have a set slug



            $title = '';
            $slug = Configure::read("UpdateCase.slug");


            if (!empty($slug)) { //we have a page specific
                if ($this->doesSlugExist($slug)) {
                    $title = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading($slug, $this->seoLocationName, 'title'));
                }
            }
            if (empty($title)) {

                if ($this->doesSlugExist('All')) {
                    $title = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading('All', $this->seoLocationName, 'title'));
                }
            }

            return $title;

            //do we have a all page with meta
            //return false;
        }
//
        public function getMetaDescription() {
            $desc = '';

            //do we have a set slug
            $slug = Configure::read("UpdateCase.slug");
            if (!empty($slug)) { //we have a page specific
                if ($this->doesSlugExist($slug)) {
                    $desc = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading($slug, $this->seoLocationName, 'description'));
                }
            }

            if (empty($desc)) {
                if ($this->doesSlugExist('All')) {
                    $desc = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading('All', $this->seoLocationName, 'description'));
                }
            }

            return $desc;

            //do we have a all page with meta
            //return false;
        }

        public function getMetaProperty($name) {
            $desc = '';

            //do we have a set slug
            $slug = Configure::read("UpdateCase.slug");
            if (!empty($slug)) { //we have a page specific
                if ($this->doesSlugExist($slug)) {
                    $desc = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading($slug, $this->seoLocationName, $name));
                }
            }

            if (empty($desc)) {
                if ($this->doesSlugExist('All')) {
                    $desc = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading('All', $this->seoLocationName, $name));
                }
            }

            return $desc;

            //do we have a all page with meta
            //return false;
        }





        public function getSEOblock($lang) {

            $string = '';

            $string .= "<!-- This site is optimized -->";
            $string .= "\n";
            $string .= "<meta name=\"description\" content=\"".$this->getMetaDescription()."\"/>";
            $string .= "\n";
            $string .= "<meta name=\"robots\" content=\"noodp\"/>";
            $string .= "\n";
            $string .= "<link rel=\"canonical\" href=\"".Router::url('/', true)."\" />";
            $string .= "\n";
            $string .= "<meta property=\"og:locale\" content=\"".$this->getMetaOgLocale($lang)."\" />";
            $string .= "\n";
            $string .= "<meta property=\"og:type\" content=\"article\" />";
            $string .= "\n";
            $string .= "<meta property=\"og:title\" content=\"".$this->getMetaTitle()."\" />";
            $string .= "\n";
            $string .= "<meta property=\"og:description\" content=\"".$this->getMetaDescription()."\" />";
            $string .= "\n";
            $string .= "<meta property=\"og:url\" content=\"".Router::url('/', true)."\" />";
            $string .= "\n";
            $string .= "<meta property=\"og:site_name\" content=\"".$this->getMetaProperty('site_name')."\" />";
            $string .= "\n";
            $string .= "<meta property=\"article:publisher\" content=\"".$this->getMetaProperty('publisher')."\" />";
            $string .= "\n";
            $string .= "<meta property=\"article:author\" content=\"".$this->getMetaProperty('author')."\" />";
            $string .= "\n";
            //$string .= "<meta property=\"og:image\" content=\"".$this->getMetaOgImage(Router::url('/', true))."\" />";
            //$string .= "\n";



            //$string .= "";

            return $string;

        }

//
        public function getMetaKeywords() {
            //do we have a set slug
            $keywords = false;
            $slug = Configure::read("UpdateCase.slug");
            if (!empty($slug)) { //we have a page specific
                if ($this->doesSlugExist($slug)) {
                    $keywords = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading($slug, $this->seoLocationName, 'keywords'));

                }
            }
            if (empty($keywords)) {
                if ($this->doesSlugExist('All')) {
                    $keywords = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading('All', $this->seoLocationName, 'keywords'));
                }
            }

            return $keywords;

            //do we have a all page with meta
            //return false;
        }


        public function getMetaOgLocale($lang) {


            $send = array(
                'en' => 'en_CA',
                'fr' => 'fr_CA',
                'eng' => 'en_CA',
                'fre' => 'fr_CA'
            );
            if (isset($send[ $lang ])) {
                return $send[ $lang ];
            }
            return false;
        }

        public function getMetaOgLocaleAlternate($lang) {
            $send = array(
                'eng' => 'fr_CA',
                'en' => 'fr_CA',
                'fre' => 'en_CA',
                'fr' => 'en_CA'
            );
            if (isset($send[ $lang ])) {
                return $send[ $lang ];
            }
            return false;
        }
        public function getMetaOgUrl($webroot, $params) {

            return $webroot.$params->url;

            //pr ($webroot);
            //pr ($params);
            //pr ($webroot. ltrim($params->here, '/'));exit;
        }

        public function getMetaOgSiteName()
        {
            //do we have a set slug

            $title = '';
            $slug = Configure::read("UpdateCase.slug");


            if (!empty($slug)) { //we have a page specific
                if ($this->doesSlugExist($slug)) {
                    $title = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading($slug, $this->seoLocationName, 'og-site_name'));
                }
            }
            if (empty($title)) {

                if ($this->doesSlugExist('All')) {
                    $title = $this->cleanUpStringForQuotedSections($this->getByWithoutLoading('All', $this->seoLocationName, 'og-site_name'));
                }
            }

            return $title;

            //do we have a all page with meta
            //return false;
        }

        public function getMetaOgImage($webroot = false)
        {
            //do we have a set slug

            $imageUrl = '';
            $slug = Configure::read("UpdateCase.slug");


            if (!empty($slug)) { //we have a page specific
                if ($this->doesSlugExist($slug)) {
                    $this->loadPageBySlug($slug);
                    $image = $this->getImage('SEO', 'og-image');
                }
            }
            if (empty($title)) {
                $this->loadPageBySlug('All');

                if ($this->doesSlugExist('All')) {
                    $image = $this->getImage('SEO', 'og-image');
                }
            }

            if ($webroot) {
                $imageUrl = $webroot.$image;
            } else {
                $imageUrl = $image;
            }
            //$title = str_replace("<img src='", '', $title);
           // $title = str_replace("' />", '', $title);
            return $imageUrl;

            //do we have a all page with meta
            //return false;
        }



//
        private function cleanUpStringForQuotedSections($str)
        {
            return str_replace('"', "'", $str);
        }

//
//
//
        /**
         * getting content without loading
         */
        private function getByWithoutLoading($slug, $location_to_check, $element_to_check, $lang = false)
        {



            if ($lang) {

            } else {
                $lang = $this->convertToLongLang[Configure::read("UpdateCase.language")];
            }

            if (empty($lang)) {
                $msg = 'Missing in APP_CONTROLLER: Configure::write("UpdateCase.language", "eng")';
                $this->writeToLog($msg);
                die ($msg);
            }
            //pr ($lang);exit;

            //get the page
            foreach ($this->variant->Page as $page) {
                if ($page->slug == $slug) {

                    foreach ($page->Location as $location) {
                        if ($location->name == $location_to_check) {

                            foreach ($location->Element as $element) {
                                if ($element->name == $element_to_check) {


                                    if ($element->language == $lang) {
                                        if (isset($element->Revision[0])) {
                                            return trim($element->Revision[0]->content_text);
                                        }
                                    }
                                }
                            }
                        }
                    }

                }

            }
        }
//
//
//        public function isEvery($nth, $count) {
//
//            //2
//
//            if ($count == $nth) {
//                return true;
//            }
//
//
//            return false;
//
//
//        }
//
//
//        public function isFirstOfEvery($nth, $count = true) {
//
//            $nth = $nth +1;
//
//            if ($count) $this->count++;
//
//            if ($this->count==1) {
//                return true; //the first time we want to know it is the first time
//            }
//            if ($this->count==$nth) {
//                $this->count=1;
//                return true;
//            }
//
//            return false;
//        }
//

//
//


        //this caused issues on staging where entire class not readable
        //var $pathupdate = APP . 'Config' . DS . 'Schema' . DS;

        var $checkForCore = false;

        public function prepareJsonData()
        {




            $alreadyRun = Configure::read("UpdateCase.firstTime");

            if ($alreadyRun == true) {
               // $this->writeToLog('already ', false);
                return false; //we already ran this
            } else {
                Configure::write("UpdateCase.firstTime", true);

                $this->writeToLog('in Prepare Json', true);
            }

            //we don't want to run this twice on the same page call when debug is 2 as it is very slow

//            if (!$this->checkForCore) {
//                $this->canWeModifyCore();
//            }

            $debugLevel = Configure::read('debug');
            $variant_id = Configure::read("UpdateCase.variant_id");
            if (empty($variant_id)) {
                $message = 'UpdateCase: missing \'Configure::write("UpdateCase.variant_id", #);\'';
                $this->writeToLog($message, true);
                die ($message);
            }

            if (!$debugLevel) { //PROD LIVE -> keep it fast
                $this->writeToLog('PROD ', false);
                $newestUuid = $this->getMostRecentFile($variant_id);
                //$newestUuid = '580d10cf-3d18-421b-a23d-55e42d4fab15';

                if (empty($newestUuid)) { //NO LOCAL FILES. WE NEED A NEW ONE
                    //die ('no');
                    $newest = $this->getFileFromUpdateCase($variant_id, $newestUuid);
                    $uuid = $this->getUuidFromRawJson($newest);
                    $this->createNewFile($newest, $uuid, $variant_id);
                } else {
                    $this->getJsonFromUuid($newestUuid, $variant_id);
                }

            } else { //we are in development mode

                $this->writeToLog('dev mode', false);

                //pr ($variant_id);exit;
                $newestUuid = $this->getMostRecentFile($variant_id);

                //pr ($newestUuid);exit;
                $newestServerData = $this->getFileFromUpdateCase($variant_id, $newestUuid);
                if (empty($newestUuid)) { //no files locally
                    $this->writeToLog('no files locally', false);
                    $uuid = $this->getUuidFromRawJson($newestServerData);
                    //pr ($newestServerData);
                    //pr ($uuid);exit;

                    //pr ($newestServerData);
                    $this->createNewFile($newestServerData, $uuid, $variant_id);
                } else { //we have local files
                    $this->writeToLog('we have local files: '.$newestUuid, false);
                    if (empty($newestServerData)) { //we are up to date
                        $this->getJsonFromUuid($newestUuid, $variant_id);
                    } else { //create a new file
                        $this->writeToLog('create a file', false);
                        $uuid = $this->getUuidFromRawJson($newestServerData);
                        $this->writeToLog('uuid: '.$uuid, false);
                        $this->createNewFile($newestServerData, $uuid, $variant_id);
                    }
                }

                $lastTimeDebugEdited = filemtime(APP . 'Config' . DS . 'core.php');
                $now = strtotime('now');

                $diff = $now - $lastTimeDebugEdited;

                if (isset($_SERVER['HTTP_HOST'])) {
                    if ($_SERVER['HTTP_HOST'] == 'localhost') {

                    } else {
                        if ($diff > 3600) { //it has been 15 minutes since we saved our file
                            $this->setDebugOff();
                        }
                    }
                } else {
                    if ($diff > 3600) { //it has been 15 minutes since we saved our file
                        $this->setDebugOff();
                    }
                }



            }

            $this->page = false;

            $this->variant = reset($this->jsonData);

        }

        private function getJsonFromUuid($uuid, $variant_id = false)
        {

            if ($variant_id) {
                if (file_exists(APP . 'Config' . DS . 'Schema' . DS . $variant_id . DS .$uuid . '.json')) {
                    $fileData = file_get_contents(APP . 'Config' . DS . 'Schema' . DS . $variant_id . DS .$uuid . '.json');
                    $this->jsonData = json_decode($fileData);
                    if (empty($this->jsonData)) {
                        $this->jsonData = json_decode(stripslashes($fileData));
                    }
                } else {

                }
            } else {
                if (file_exists(APP . 'Config' . DS . 'Schema' . DS . $uuid . '.json')) {
                    $fileData = file_get_contents(APP . 'Config' . DS . 'Schema' . DS . $uuid . '.json');
                    $this->jsonData = json_decode($fileData);
                    if (empty($this->jsonData)) {
                        $this->jsonData = json_decode(stripslashes($fileData));
                    }
                } else {

                }
            }



        }

        private function getUuidFromRawJson($rawJsonFile)
        {

            $tmp = json_decode($rawJsonFile);
            return $tmp[0]->Variant->uuid;
        }

        private function createNewFile($rawJsonFile, $uuid, $variant_id = false)
        {
            $this->writeToLog('create new file with uuid:'.$uuid, false);
            $this->jsonData = json_decode($rawJsonFile);
            //pr (APP . 'Config' . DS . 'Schema' . DS . $uuid.'.json');exit;
            if ($variant_id) {
                $path = APP . 'Config' . DS . 'Schema' . DS . $variant_id . DS . $uuid . '.json';
            } else {
                $path = APP . 'Config' . DS . 'Schema' . DS . $uuid . '.json';
            }
            $file = new File($path);
            if (!$file->write($rawJsonFile)) {
                die ('could not create file: '.$path);
            } else {
                return true;
            }
        }


        public function getUUIDforVariantId($variant_id) {
            return $this->getMostRecentFile($variant_id);

        }
        public function getUUID() {
            if (!isset($this->uuid)) {
                return false;
            }
            return $this->uuid;
        }


        public function getMostRecentFile($variant_id = false, $reverse = false)
        {

            $this->pathupdate = APP . 'Config' . DS . 'Schema' . DS;
            //die ('inside');

            //pr ($this->pathupdate);exit;
            if (!$variant_id) {

            } else {
                $this->pathupdate = $this->pathupdate.$variant_id;
            }

            //$files = scandir($this->pathupdate);

            //pr ($this->pathupdate);
           // pr ($files);
           // exit;
            $dir = new Folder($this->pathupdate);
            $files = $dir->find('.*\.json');

            foreach ($files as $key => $file) {
                if (strlen($file) < 20) { //we don't want to use the older manual name of sites
                    unset($files[$key]);
                }
            }
            sort($files);

            //echo 'hi';
            //pr ($files);exit;
            if (empty($files)) {
                return false;
            } else {

                //test for the date (i think this is slower) //also sorts opposite!!
//                usort($files, function($a, $b) {
//                    return filemtime($this->pathupdate.$a) < filemtime($this->pathupdate.$b);
//                });

//                pr ($files);

                sort($files);
                if ($reverse) {
                    $newestFile = reset($files);
                } else {
                    $newestFile = end($files);
                }

                $newestFile = str_replace(".json", '', $newestFile);
                return $newestFile;
            }
        }

        private function getFileFromUpdateCase($variant_id, $newestUuid)
        {
            $HttpSocket = new HttpSocket();

            $pathToUse = $this->hostPath . 'public/variants/checkIn/' . $variant_id . '/' . $newestUuid;

            $this->writeToLog('get file from updateCase: '.$pathToUse, false);

            $response = $HttpSocket->post($pathToUse, array(
                'token' => Configure::read('Token.general'),
            ));

            if (empty($response->body)) {
                $this->writeToLog('we have current file', false);
            } else {
                $this->writeToLog('new file exists', false);
            }


            return $response->body;
        }
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
        public function doesSlugExist($slug)
        {

            //////// copied from loadby
            $this->prepareJsonData();
            $this->page = false;
            $this->variant = reset($this->jsonData);
            //get the page
            foreach ($this->variant->Page as $page) {
                //pr ($slug);
                //pr ($page);
                if ($page->slug == $slug) {
                    $this->slug = $slug;
                    $this->uuid = $page->uuid;
                    //pr($page);exit;
                    $this->page = $page;
                }
            }
            /////////// end of copied by loadby

            //get the page
            if (!empty($this->page)) {
                if ($this->page->slug == $slug) {
                    return true;
                }
            }

            return false;
        }

//



    var $slug;
        public function loadPageBySlug($slug)
        {

            $cache = 'images' . DS . Configure::read("UpdateCase.variant_id") . DS;
            if (!file_exists($cache)) die ('Create image cache: webroot/'.$cache);

            $this->slug = $slug;

            $this->prepareJsonData();

            $this->page = false;

            $this->variant = reset($this->jsonData);

            //
            //get the page
            foreach ($this->variant->Page as $page) {
                //pr ($slug);
                //pr ($page);
                if ($page->slug == $slug) {
                    $this->slug = $slug;
                    $this->uuid = $page->uuid;
                    //pr($page);exit;
                    $this->page = $page;
                }
            }

            //pr ($this->page);exit;

            $end = microtime(true);
            //echo '<!-- ' . ($end - $start) . ' -->';

        }

//
//        private function reset() {
//            $this->location = false;
//            $this->locationName = false;
//            $this->element = false;
//            $this->elementName = false;
//
//            $this->groupName = false;
//
//
//        }
//

//

//
//        public function getPageDate() {
//            //$quit = $this->setup($locationName, $elementName, $groupName);
//            return $this->page->date;
//        }
//

        public function getPageDate($format = 'Y-m-d H:i:s') {
            $date = strtotime($this->page->date);

            $lang = Configure::read('UpdateCase.language');
            if ($lang == 'fre') {

                //french
                setlocale(LC_ALL, 'fr_FR.UTF-8');
                //echo date('D d M, Y');
                //return strftime("%a %d %b %Y", $date);
                return strftime("%B %Y", $date);
                //$shortDate = strftime("%d %b %Y", $date);

            } else {
                return date($format, $date);
            }




        }

        public function getLinkBy($locationName, $elementName, $groupName = false) {
            $link = $this->getContentBy($locationName, $elementName, $groupName);
            return $this->cleanLink($link);
        }


        public function isLocationDateActive($locationName) {

            $datedLocation = false;
            $matchingLocation = false;

            //pr ($locationName);exit;

            foreach ($this->page->Location as $location) {

                $this->debug[] = $location->name;

                //maybe it is a futur set version
                $currDate = $this->getDateNow();

                $locationNameSripped = explode(":::", $location->name);

                //pr ($locationName);
                //pr ($locationNameSripped[0].' '.);
                //we only want the beginning without the description
                if ($locationNameSripped[0] == $locationName) {

                    $start = strtotime($location->date_active);
                    $end = strtotime($location->date_expire);
                    if (($currDate > $start) && ($currDate < $end)) {

                        //die ('hi');
                        $datedLocation = $location;
                    }
                } else {
                    $this->debug[] = 'NOT';
                }

                //it is a normal one
                if ($location->name == $locationName) {
                    $matchingLocation = $location;
                }
            }

            //pr ($this->debug);

            //pr ($datedLocation);

            //die ('done');


            if ($datedLocation) {
                return $datedLocation;
            } elseif ($matchingLocation) {
                return $matchingLocation;
            } else {
                return false;
            }


        }


        public function getContentBy($locationName, $elementName, $groupName = false)
        {
            if ($groupName == 'false') $groupName = false;

            if (empty($this->language)) {
                $this->language = Configure::read('UpdateCase.language');
                $this->language = $this->possibleLanguages[$this->language];
            }

            if (empty($this->page)) return false;

            //find the best location

            $location = $this->isLocationDateActive($locationName);

            //pr ($location);
            //exit;

                //pr ($elementName);
                //pr ($location->name);
                foreach ($location->Element as $element) {
                    //pr ($element);
                    //pr ($element->language);exit;

                    //echo $element->name.' - '.$elementName.' 33';

                    if ($elementName != $element->name) {
                        $this->debug[] = 'Element does not match: '.$elementName .'/'. $element->name;
                        continue;
                    } else {
                        $this->debug[] = 'Element MATCHES: '.$elementName .'/'. $element->name;

                    }

                    if ($groupName) { //we have a group so exclude non-groups

                        $this->debug[] = 'We have a grouped name';


                        if ($element->groupBy != $groupName) {
                            $this->debug[] = 'Group does not match: '.$groupName.'/'.$element->groupBy;

                            continue;
                        } else {
                            $this->debug[] = 'Group MATCHES: '.$groupName.'/'.$element->groupBy;

                        }
                    } else { //no group required
                        $this->debug[] = 'No group required';

                        if (!empty($element->groupBy)) {
                            $this->debug[] = 'Element groupBy is empty';
                            continue;
                        }
                    }


                    //we want a language,
                    //except if not languages we will take the all (if exists)
                    if ($this->language != $this->possibleLanguages[$element->language]) {

                        $this->debug[] = 'langs do not match: our lang: '.$this->language.' element lang: '.$element->language;


                        //if all keep it
                        if ($element->language == 'ALL') {

                            $this->debug[] = 'This is ALL lange';

                            //pr ($element->language);
                            $elementALL = $element;
                        } else {

                            $this->debug[] = 'NOT all   ';


                            continue;
                        }
                    } else {
                        //correct language
                        $this->debug[] = 'Correct lang';


                        $elementToUse = $element;

                        //pr ($elementToUse);
                    }
                    //foreach ($element->Revision as $revision) {}
                }


            $return = false;
            if (isset($elementToUse)) {
                $this->revision = $elementToUse->Revision[0];
                $this->element = $elementToUse;
                $return = $elementToUse->Revision[0]->content_text;
                //return $elementToUse->Revision[0]->content_text;
            } elseif (isset($elementALL)) {
                $this->revision = $elementALL->Revision[0];
                $return = $elementALL->Revision[0]->content_text;
                $this->element = $elementALL;
                //return $elementALL->Revision[0]->content_text;
            } else {

                $this->writeToLog('Does not exist | Slug:' . $this->slug . ' / Location: ' . $locationName . ' / Element: ' . $elementName . ' / Group: ' . $groupName);

                $this->debug[] = 'else';
                //return 'Not implemented yet';
                return false;
            }

            return $return;
            //if it is a picture, we need to bring it locally
            //pr ($this->revision);exit;


//            $quit = $this->setup($locationName, $elementName, $groupName);
//
//            if ($quit) {
//                if ($this->testMode) {
//                    return $this->testModeView($elementName);
//                } else {
//                    return $quit;
//                }
//            }
//
//            if (isset($this->element->Revision[0])) {
//                return $this->element->Revision[0]->content_text;
//            } else {
//
//                if ($this->testMode) {
//                    return $this->testModeView($elementName);
//                }
//                //
//                //
//                //pr($this->element);
//                //exit;
//
//
//                $info = ' Location: ' . $locationName;
//                $info .= ' / Element: ' . $elementName;
//                if ($groupName) $info .= ' / Group: ' . $groupName;
//                return $this->missingMessage('No Revision', $info);
//            }
        }


        var $revision;

//
//
        public function getIdBy($locationName, $elementName, $groupName = false) {

            if ($this->getContentBy($locationName, $elementName, $groupName)) {
                return $this->revision->id;
            } else {
                return false;
            }

        }

        public function getUrlBy($locationName, $elementName, $groupName = false)
        {


            $found = $this->getContentBy($locationName, $elementName, $groupName);
            //pr ($found);exit;

            if (isset($this->element->Revision[0])) {
                //pr ($this->element->Revision[0]->content_text);
//            if (preg_match('/\"([^\"]*)\">(.*)<\/a>/iU', $this->element->Revision[0]->content_text, $m)) {
//                pr ($m);exit;
//                return trim($m[1]);
//            }

                $clean = trim($this->element->Revision[0]->content_text);

                $clean = preg_replace('/https?:\/\/[^\s"<>]+/', '$0', $clean);
                //$clean = str_replace(" ", "", $clean);
                $clean = str_replace("&nbsp;", "", $clean);
                $clean = str_replace("\n", "", $clean);
                $clean = str_replace("\r\n", "", $clean);
                $clean = str_replace("\r", "", $clean);
                //$clean = strip_tags(trim($clean));

                //$clean = trim($this->element->Revision[0]->content_text);
                //$clean = trim(str_replace(array("&nbsp;", " ","\n","<br/>"), '', $clean));

                //return "http://www.setupcase.com/app/webroot/uploads/1/62/woodgears.jpg";
                //return $clean;


                //we have a revision so now let's only return the URL
                if (filter_var($clean, FILTER_VALIDATE_URL) !==
                    false
                ) {
                    return $clean;
                } elseif (preg_match('/\"([^"]+)"/', $clean, $m)) {

                    return trim($m[1]);
                } else {
                    return strip_tags(trim($clean));
                }
            }
        }
//
//        private function cleanUp($str) {
//            $str = trim($str, "\t\n\r");
//            //$str = trim($str, "&nbsp;");
//            //$str = str_replace(array("&nbsp;","\r\n", "\r"), '', $str);
//            return $str;
//        }
//
//        public function getContentNoLinksBy($locationName, $elementName, $groupName = false) {
//
//            $quit = $this->setup($locationName, $elementName, $groupName);
//            if ($quit) {
//                if ($this->testMode) {
//                    return $this->testModeView($elementName);
//                } else {
//                    return $quit;
//                }
//            }
//            if (isset($this->element->Revision[0])) {
//
////////////////////// remove the space between ? > this was causing issues with the comments
//                return preg_replace('#<a.*? >([^>]*)</a>#i', '$1',
//                    $this->element->Revision[0]->content_text
//                );
//            } else {
//
//
//
//                if ($this->testMode) {
//                    return $this->testModeView($elementName);
//                }
//
//
//                $info = ' Location: ' . $locationName;
//                $info .= ' / Element: ' . $elementName;
//                if ($groupName) $info .= ' / Group: ' . $groupName;
//                return $this->missingMessage('No Revision', $info);
//            }
//        }
//



        public function getTags()
        {
            if (!$this->slug) {
                $msg = 'no slug set';
                $this->writeToLog($msg);
                die ($msg);
            }

            $tags = array();
            foreach ($this->page->Tag as $tag) {
                $tags[] = $tag->name;
            }
            return $tags;
        }

    public function getPage() {
            debug($this->page);
    }

        public function doesContain($search, $locationName, $elementName = false, $groupName = false) {

            //pr ($this->page);exit;
            $test = $this->getContentBy($locationName, $elementName, $groupName);

            if (!empty($test)) {
                if (strpos($test, $search) !== false) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }

        }

        public function isNotEmpty($locationName, $elementName = false, $groupName = false) {
            //pr ($this->page);exit;
            $test = $this->getContentBy($locationName, $elementName, $groupName);
            if (!empty($test)) {
                return true;
            } else {
                return false;
            }
        }

        public function isEmpty($locationName, $elementName = false, $groupName = false) {

            //pr ($this->page);exit;

            $test = $this->getContentBy($locationName, $elementName, $groupName);

            if (empty($test)) {
                return true;
            } else {
                return false;
            }

        }

        public function exists($locationName, $elementName = false, $groupName = false) {

            //pr ($this->page);exit;

            if (empty($this->page)) {
                return false;
            }

            foreach ($this->page->Location as $location) {
                //echo $locationName.' -> '.$location->name."<br/>";
                if ($locationName != $location->name) {
                    $this->debug[] = 'Location does not match: ' . $locationName . ' / ' . $location->name;
                    continue;
                } else {
                    $this->debug[] = 'Location MATCHES: ' . $locationName . ' / ' . $location->name;

                    //pr ($location);exit;
                    //the location matches

                    if (!$elementName) {
                        //no element so let's return true
                        return true;
                    } else {

                        //we are looking for an element
                        foreach ($location->Element as $element) {

                            //pr ($location->Element);

                            if ($elementName != $element->name) {
                                //echo 'does not '.$elementName;
                                //exit;
                                continue;
                            } else {

                                //pr ($element);exit;

                                if (!$groupName) {
                                    return true;
                                } else {

                                    if ($element->groupBy == $groupName) {
                                        return true;
                                    }
                                }

                            }

                        }

                    }


                }


                return false;


            }


//            $quit = $this->setup($locationName, $elementName, $groupName);
//            if ($quit) {
//                return false;
//            }
//            if (isset($this->element->Revision[0])) {
//                return true;
//            } else {
//                return false;
//            }
        }

        public function removeTextFrom($remove, $string) {
            return str_replace($remove, '', $string);
        }
//
//        public function removeParagraphs($string) {
//            return str_replace(array('<p>', '</p>'), '', $string);
//        }

//
        private function getDateNow() {
            if (isset($_GET['testDate'])) {

                //pr ($_GET['testDate']);
                //we want to test what happens when our date is in the future
                return strtotime($_GET['testDate']);
            } else {
                return strtotime('now');
            }
        }
//
//
//        private function getCorrectLocation() {
////we need to test all and one might be priority
//            $overrideLocations = array();
//            $shouldTest = false;
//            //get the location
//
//
//            //scan all the locations
//            //if there is an override, then use the smallest one
//            //else return the original
//
//
//
//
//
//            //pr ($this->page->Location);
//            foreach ($this->page->Location as $location) {
//
//                if ($location->name == $this->locationName) {
//                    //original
//                    $this->location = $location;
//                }
//
//                //test to see if we have an override
//                if (strpos($location->name, ':::') !== false) {
//                    //we only want to look at the ones that are override
//
//                    $locationName = explode(":::", $location->name); //strip off ::: after the location name
//                    if ($locationName[0] == $this->locationName) {
//                        //we want the one that if available has
//                        $overrideLocations[] = $location;
//                    }
//
//                }
//
//            }
//
//            if (empty($overrideLocations)) {
//                return false; //no override required
//            } else {
//
//
//                $currDate = $this->getDateNow();
//                $byLength = array();
//                //pr ($possibleLocations);
//
//                // pr ($overrideLocations);exit;
//                //we have to figure out which one we want
//                foreach ($overrideLocations as $key => $possibleLocation) {
//
//                    $start = strtotime($possibleLocation->date_active);
//                    $end = strtotime($possibleLocation->date_expire);
//
//                    if (($currDate > $start) && ($currDate < $end)) {
//
//                        $this_diff = ($end - $start);
//                        //echo $key.'-diff-'.$this_diff;
//                        $byLength[ $this_diff ] = $possibleLocation;
//                    }
//                }
//                sort($byLength);
//
//                if (!empty($byLength)) {
//                    $this->location = reset($byLength);
//                }
//
//            }
//        }
        //////////////////////////////////////////////////////////////////////////////////////////RESPONSIVE

        private function modifyTable($string) {

            $debug = true;
            $newTable = '';

            //debug($string);
            //exit;

            //exit;
            //split up by row
            $rows = $this->splitUpByTag('<tr>', '</tr>', $string);
            //debug ($rows);
            // debug($rows[1]);
            $headings = $this->getBetweenTags('<td','</td>', $rows[1]);

            //debug($headings);exit;
            $newTable = "";
            $newTable .= "<table class=\"responsive\">";
            if ($debug) $newTable .= "\n\t";
            $newTable .= "<tr>";
            foreach ($headings as $heading) {
                if ($debug) $newTable .= "\n\t\t";
                $newTable .= "<th>";
                $newTable .= $heading;
                $newTable .= "</th>";

            }
            if ($debug) $newTable .= "\n\t";
            $newTable .= "</tr>";
            //if ($debug) $newTable .= "\n";

            //debug($headings);
            foreach ($rows as $key => $row) {
                //skip the first 2
                if (in_array(trim($key), array(0,1))) continue;

                $row_parts = explode("</td>", trim($row));

                //remove the empty lines
                if (empty(trim($row_parts[0]))) continue;

                if (strpos(trim($row_parts[0]), '<td') !== false) {} else {
                    continue; //it is the end of the table
                }


                if ($debug) $newTable .= "\n\t";
                $newTable .= "<tr>";

                //debug($row_parts);exit;
                foreach ($row_parts as $k => $row_part) {
                    if (isset($headings[ $k ])) {

                        //debug($tmp);
                        if ($debug) $newTable .= "\n\t\t";

                        $newTable .= str_replace("<td", '<td data-th="'.$headings[ $k ].'"', trim($row_part).'</td>');
                        //if ($debug) $newTable .= "\n";
                    }
                }

                if ($debug) $newTable .= "\n\t";
                $newTable .= "</tr>";

            }

            if ($debug) $newTable .= "\n";
            $newTable .= "</table>";

            return $newTable;
        }

        private function getBetweenTags($firstTag, $secondTag, $string) {
            $array = array();
            //debug($string);
            $tmp = explode($firstTag, $string);

            // debug($tmp);exit;
            foreach ($tmp as $key => $parts) {
                //debug ($parts);
                if ($key == 0) continue; //first time is NOT a table
                $parts_b = explode($secondTag, $parts);

                //remove our edge cases
                $parts_b[0] = str_replace(array("<strong>","</strong>"), "", $parts_b[0]);

                //debug($parts_b);exit;
                $tmpp = explode(">", $parts_b[0]);
                //debug($tmpp);
                //exit;
                //debug($tmpp);
                if (isset($tmpp[1])) {
                    $array[] = $tmpp[1];
                } else {
                    $array[] = $tmpp[0];
                }


                //debug($parts_b);
                //exit;
                //$array[] = $parts_b[0];
                //debug($table);exit;
            }

            //pr ($array);
            return $array;
        }

        private function splitUpByTag($firstTag, $secondTag, $string) {

            $array = array();

            $tmp = explode($firstTag, $string);
            //debug($tmp);exit;

            foreach ($tmp as $key => $parts) {

                //debug ($parts);

                //if ($key == 0) continue; //first time is NOT a table

                $parts_b = explode($secondTag, $parts);

                //debug($table);exit;

                foreach($parts_b as $part_b) {
                    //debug ($part_b);
                    $array[] = $part_b;
                }
                //debug ($table[0]);
            }
            return $array;
        }
//

        public function convertToReponsiveTables($string) {

            $modified = "";

            $parts = $this->splitUpByTag('<table>','</table>', $string);

            foreach ($parts as $part) {

                //if a table let's modify the table
                if (strpos($part, '<tr>') !== false) {

                    $newTable = $this->modifyTable($part);
                    //debug($newTable);
                    $modified .= $newTable;
                } else {
                    //debug($part);
                    $modified .= $part;
                }
            }

            return $modified;
        }


        private function languageBegin() {
            if (empty($this->language)) {

                $this->language = Configure::read('UpdateCase.language');

                //pr (Configure::read('UpdateCase.language'));
                //exit;
                //pr ($this->language);exit;
                $this->language = $this->possibleLanguages[$this->language];
            }

        }


        public function getPageSlugsByTagWithLocationElement($tagName, $sortBy = 'ASC', $location, $element, $group = false, $limit = false, $offset = false, $options = false) {

            $pageNames = array();
            //$this->page = false;

            $this->languageBegin();

            $sort = array();

            //get the page

            foreach ($this->variant->Page as $keyPage => $page) {

                $this->page = $page;

                if (!$this->exists($location, $element, $group)) {
                    continue;
                }

                //let's ensure we have the following location / element
                //pr ($page);exit;

                //pr ($page);
                //pr ($page->Tag);
                if (!empty($page->Tag)) {
                    foreach ($page->Tag as $tag) {

                        if (is_array($tagName)) {
                            if (in_array($tag->name, $tagName)) {
                                //this tag is present
                                $sort[$page->slug] = strtotime($page->date);
                            }
                        } else {
                            if ($tag->name == $tagName) {
                                //this tag is present
                                $sort[$page->slug] = strtotime($page->date);
                            }
                        }


                    }
                }
            }

            if ($sortBy == 'ASC') {
                //sort by the date which is the key
                asort($sort);
            } else {
                arsort($sort);
            }

            foreach ($sort as $slug => $num) {
                $pageNames[$slug] = $slug;
            }

            if ($options) {
                if ($options == 'SHUFFLE') {

                    $keys = array_keys($pageNames);
                    shuffle($keys);
                    foreach($keys as $key) {
                        $new[$key] = $pageNames[$key];
                    }
                    $pageNames = $new;
                }

            }
            //pr ($pageNames);exit;

            $this->total = count($pageNames);

            if (empty($pageNames)) {
                return array();
//            $message = 'Tag not found: ' . $tagName;
//            return $this->missingMessage($message);
                exit;
            }

            if (!$limit) {
                return $pageNames;
            } else {
                $pageNames = array_slice($pageNames, (($offset - 1) * $limit), $limit);
                return $pageNames;
            }

            //pr ($pageNames);
            //exit;

        }

        var $total = 0;

        public function getTotalRecords() {
            return $this->total;
        }


        public function getPageSlugsByTag($tagName, $sortBy = 'ASC') {

            $pageNames = array();
            //$this->page = false;

            //pr ($this->variant);exit;

            $sort = array();
            $available = '';
            //get the page
            foreach ($this->variant->Page as $keyPage => $page) {

                //pr ($page);
                //pr ($page->Tag);
                if (!empty($page->Tag)) {
                    foreach ($page->Tag as $tag) {

                        if (is_array($tagName)) {
                            if (in_array($tag->name, $tagName)) {
                                //this tag is present
                                $sort[$page->slug] = strtotime($page->date);
                            }
                        } else {
                            if ($tag->name == $tagName) {
                                //this tag is present
                                $sort[$page->slug] = strtotime($page->date);
                            }
                        }


                    }
                }
            }

            if ($sortBy == 'ASC') {
                //sort by the date which is the key
                asort($sort);
            } else {
                arsort($sort);
            }

            foreach ($sort as $slug => $num) {
                $pageNames[$slug] = $slug;
            }



            if (empty($pageNames)) {

                return array();

//            $message = 'Tag not found: ' . $tagName;
//            return $this->missingMessage($message);
                exit;
            }

            return $pageNames;
        }


        //////////////////////////////////////////////////////////////////////////////////////////RESPONSIVE END
//
//        public function getPagesBySearch($search) {
//
//            $results = array();
//
//            if (!$this->variantName) {
//                $this->chooseVariantName(); //let's try to auto detect
//            }
//            $available = '';
//            //get the page
//            foreach ($this->variant->Page as $page) {
//
//
//                foreach ($page->Location as $location) {
//
//
//                    foreach ($location->Element as $element) {
//
//                        foreach ($element->Revision as $revision) {
//
//                            if (stripos($revision->content_text,$search) !== false) {
//                                //echo 'true';
//                                $found = array(
//                                    'slug' => $page->slug,
//                                    'location' => $location->name,
//                                    'element' => $element->name,
//                                    'language' => $element->language,
//                                    'text' => strip_tags($revision->content_text)
//                                );
//                                $results[$page->slug] = $page;
//                            }
//
//                            //pr ($revision);exit;
//                        }
//                    }
//                }
//
//            }
//
//            //pr ($results);
//            if (!empty($results)) {
//                return $results;
//            } else {
//                return false;
//            }
//
//        }
//
//        public function getPageSlugsBySearch($search) {
//
//            $results = array();
//
//            if (!$this->variantName) {
//                $this->chooseVariantName(); //let's try to auto detect
//            }
//            $available = '';
//            //get the page
//            foreach ($this->variant->Page as $page) {
//
//
//                foreach ($page->Location as $location) {
//
//
//                    foreach ($location->Element as $element) {
//
//                        foreach ($element->Revision as $revision) {
//
//                            if (strpos($revision->content_text,$search) !== false) {
//                                //echo 'true';
//                                $found = array(
//                                    'slug' => $page->slug,
//                                    'location' => $location->name,
//                                    'element' => $element->name,
//                                    'language' => $element->language,
//                                    'text' => strip_tags($revision->content_text)
//                                );
//                                $results[$page->slug][] = $found;
//                            }
//
//                            //pr ($revision);exit;
//                        }
//                    }
//                }
//
//            }
//
//            //pr ($results);
//            if (!empty($results)) {
//                return $results;
//            } else {
//                return false;
//            }
//
//        }
//
//        public function getElementFromPage($page, $name) {
//            foreach ($page->Location as $location) {
//                foreach ($location->Element as $element) {
//                    if ($element->name == $name) {
//                        //pr ($element);exit;
//                        return $element->Revision[0]->content_text;
//                    }
//                }
//            }
//
//            return false;
//        }
//
        public function getLocationNames($ignore = false) {

            $this->count = 0;

            if (!$this->page) {
                $msg = 'Page not loaded';
                $this->writeToLog($msg);
                die ($msg);

            }

            foreach ($this->page->Location as $location) {
                if ($ignore == $location->name) {
                    //we want to ignore this location
                } else {
                    $this->locationNames[$location->name] = $location->name;
                }
            }

            return $this->locationNames;
        }
//

//
        public function getGroupNamesByLocation($locationName, $sort = 'ASC') {

            if (!$this->page) {
                $message = 'Page not loaded';
                $this->writeToLog($message, true);
                die ($message);
            }


            $location = $this->isLocationDateActive($locationName);



            //get the location

            //pr ($this->page);

            //pr ($locationName);exit;

            $this->groupNames = array();


                    foreach ($location->Element as $element) {
                        if (empty($element->groupBy)) {

                        } else {
                            $this->groupNames[ $element->groupBy ] = $element->groupBy;
                        }
                    }



            if ($sort == 'ASC') {
                ksort($this->groupNames);
            } else {
                krsort($this->groupNames);
            }


            return $this->groupNames;
            //pr ($this->groupNames);
            //pr ($this->location);

//            if (!$this->location) {
//
//                if ($this->testMode) {
//                    return $this->groupNames;
//                } else {
//                    echo('Location with name "' . $this->locationName . '" does NOT exist');
//                    echo "<br/>";
//                    echo $this->possibleLocations();
//                    exit;
//                }
//
//            } else {
//                //we have it
//                $this->groupNames = array();
//
//                foreach ($this->location->Element as $element) {
//
//
//
//                    if (!$this->isThisLanguageSet($element->language)) continue;
//
//                    if ($element->groupBy) {
//                        $this->groupNames[$element->groupBy] = $element->groupBy;
//                    }
//
//                    //pr ($this->groupNames);
//                }
//            }


            if (!empty($this->groupNames)) {

                ksort($this->groupNames);
                return $this->groupNames;
            } else {

                if ($this->testMode) {
                    return $this->groupNames;
                } else {
                    $this->missingMessage('No Groups for location: ' . $locationName);
                }


            }
        }






//
//
//
//        public function getAllTagsAvailable() {
//
//            $allTags = array();
//
//            foreach ($this->variant->Page as $page) {
//
//                foreach ($page->Tag as $tag) {
//
//                    $allTags[ $tag->name ] = $tag->name;
//                }
//
//            }
//            return $allTags;
//
//            // exit;
//
//        }
//
//        public function getTags() {
//            if (!$this->slug) die ('no slug set');
//
//            $tags = array();
//            foreach ($this->page->Tag as $tag) {
//                $tags[] = $tag->name;
//            }
//            return $tags;
//        }
//        public function getSlugName() {
//            if (!$this->slug) die ('no slug set');
//            return $this->page->slug;
//        }
//



        public function writeToLog($message, $newLine = true) {

            if (is_array($message)) {
                $message = implode("\n", $message);
            }

            if ($newLine) {
                $message = "\n".date('Ymd-His').' > '.$message;
            } else {
                $message = ' > '.$message;
            }
            file_put_contents('updateCase.log', $message, FILE_APPEND);

            //echo APP.'tmp/logs/'.$type;


        }




        public function convertString($from, $to, $string) {
            foreach ($from as $kFrom => $vFrom) {
                $string = str_replace($vFrom, $to[$kFrom], $string);
            }
            //return "";
            return $string;
        }



        public function getFile($location, $element, $group = false)
        {

            $cache = 'images' . DS . Configure::read("UpdateCase.variant_id") . DS;

            $id = $this->getIdBy($location, $element, $group);

            if (!$id) {
                $message = 'File cannot load | SLUG: ' . $this->slug . ' / Location: ' . $location . ' / Element ' . $element . ' / Group:' . $group;
                $this->writeToLog($message, true);
                return false;
            }
            //pr ($id);
            //pr ($element);exit;

            //pr ($this->revision);

            //pr ($id);
            if ($this->revision->mime == 'application/pdf') {
                $filename = $id . '.pdf';
            } elseif ($this->revision->mime == 'application/epub+zip') {
                $filename = $id . '.epub';
            } elseif ($this->revision->mime == 'application/mobi+zip') {
                $filename = $id . '.mobi';
            } elseif ($this->revision->mime == 'application/octet-stream') {
                $filename = $id . '.mobi';
            } elseif ($this->revision->mime == 'image/jpeg') {
                $filename = $id.'.jpg';
            } else {

                //echo 'cannot load slug';
                //pr ($this->revision);
                //pr ($id);exit;

                //exit;
                //pr ($this->revision);
                $message = 'File cannot load | SLUG: ' . $this->slug . ' / Location: ' . $location . ' / Element ' . $element . ' / Group:' . $group;
                $this->writeToLog($message, true);
                //echo $message;
                //exit;
                //return $message;
                return false;
            }

            //pr ($filename);
            //does a cached version exist
            $file = new File($cache . $filename);

            // pr ($filename);exit;

            if ($file->exists()) {
                //return the local file


                return $cache . $filename;
            } else {
                //create the file locally

                $dir = new Folder($cache, true, 0775);

                if (file_exists($cache)) {
                    $imageLink = 'http://files.setupcase.com/display/' . $id . '/file.png';
                    $file->write(file_get_contents($imageLink));
                    return $cache . $filename;
                } else {
                    //something went wrong with creating the folder, so let's just return the link from our server
                    $imageLink = 'http://files.setupcase.com/display/' . $id . '/file.png';
                    return $imageLink;
                }
            }
        }


        public function getFileDownloadTag($webroot, $displayText, $location, $element, $group = false)
        {

            $file = $webroot.$this->getFile($location, $element, $group);

            //pr ($this->element);exit;
            $imageSizes = '';
            $width = '';
            $height = '';




                $alt = 'alt="'.$location.'-'.$element.'-'.$group;


            $alt = rtrim($alt,'-');
            $alt .= '"'; //close the hyphen
            $tag = '<a href="'.$file.'" target="blank">'.$displayText.'</a>';


            return $tag;
        }









        public function getImage($location, $element, $group = false, $size = 'medium')
        {

            $cache = 'images' . DS . Configure::read("UpdateCase.variant_id") . DS;

            $id = $this->getIdBy($location, $element, $group);

            if (empty($id)) {
                return false;
            }
            //pr ($this->revision);exit;

            if ($this->revision->mime == 'image/jpeg') {
                $filename = $id . '.jpg';
            } elseif ($this->revision->mime == 'image/png') {
                $filename = $id . '.png';
            } else {
                //pr ($this->revision);
                $message = 'Image cannot load | SLUG: '.$this->slug.' / Location: '.$location. ' / Element '.$element.' / Group:'.$group;
                $this->writeToLog($message, true);
                //echo $message;
                //exit;
                return false;
            }

            //pr ($filename);
            //does a cached version exist
            $file = new File($cache . $filename);


            // pr ($filename);exit;

            if ($file->exists()) {
                //return the local file
                return $cache . $filename;
            } else {
                //create the file locally

                $dir = new Folder($cache, true, 0775);

                if (!file_exists($cache)) {
                    die (" - - - - - - Create folder: ".$cache);
                }

                if (file_exists($cache)) {
                    $imageLink = 'http://files.setupcase.com/ImageId/' . $id . '/' . $size . '/pic.jpg';
                    $file->write(file_get_contents($imageLink));
                    return $cache . $filename;
                } else {
                    //something went wrong with creating the folder, so let's just return the link from our server
                    $imageLink = 'http://files.setupcase.com/ImageId/' . $id . '/' . $size . '/pic.jpg';
                    return $imageLink;
                }
            }
        }



        public function getWebsiteUrl($url) {

            if (strpos($url, 'http://') !== FALSE)
            {
                return $url;
            }
            else
            {
                return 'http://'.$url;
            }

        }
        public function cleanLink($link) {
            //ensure no trailing slash
            $link = rtrim($link,'/');
            return $link;
        }


        public function getImageTag($webroot, $location, $element, $group = false, $size = 'medium') {

            $image = $webroot.$this->getImage($location, $element, $group, $size);

            //pr ($this->element);exit;
            $imageSizes = '';
            $width = '';
            $height = '';

            if ($size == 'medium') {
                $width = $this->element->width;
                $height = $this->element->height;


            } elseif ($size == 'large') {

                $heightLarge = ($this->element->width_large * $this->element->height) / $this->element->width;

                $width = $this->element->width_large;
                $height = $heightLarge;


            } elseif ($size == 'thumb') {
                $heightLarge = ($this->element->width_thumb * $this->element->height) / $this->element->width;

                $width = $this->element->width_thumb;
                $height = $heightLarge;

            }

            $imageSizes .= 'width="'.$width.'" height="'.$height.'"';

            if ($size != 'medium') {
                $alt = 'alt="'.$location.'-'.$element.'-'.$group.'-'.$size;
            } else {
                $alt = 'alt="'.$location.'-'.$element.'-'.$group;
            }

            $alt = rtrim($alt,'-');
            $alt .= '"'; //close the hyphen
            $tag = '<img src="'.$image.'" '.$alt.' '.$imageSizes.'/>';


            return $tag;
        }

        ///////////////////////////////////////////////////////////////////////////// AUTO DEBUG
        function setDebugOff()
        {

            //this will set the debug to
            $path_to_file = APP . 'Config';
            $file_contents = file_get_contents($path_to_file . DS . 'core.php');
            //print_r ($file_contents);exit;

            $file_contents = $this->turnOffDebug($file_contents);
            if ($file_contents) {
                //let's save it
                file_put_contents($path_to_file . DS . 'core.php', $file_contents);
                //$this->Session->setFlash('Debug mode is now OFF');
                //echo 'Debug mode is now OFF';

            } else {
                // echo 'Debug mode already off';
            }

        }

        function setDebugOn()
        {


            //this will set the debug to
            $path_to_file = APP . 'Config';
            $file_contents = file_get_contents($path_to_file . DS . 'core.php');
            //print_r ($file_contents);exit;

            $file_contents = $this->turnOnDebug($file_contents);
            if ($file_contents) {
                //let's save it
                file_put_contents($path_to_file . DS . 'core.php', $file_contents);
                //$this->Session->setFlash('Debug mode is now OFF');
                //echo 'Debug mode is now OFF';

            } else {
                // echo 'Debug mode already off';
            }

        }

//        function setDebugOn() {
//
//            //this will set the debug to
//            $path_to_file = APP.'Config';
//
//            $file_contents = file_get_contents($path_to_file.DS.'core.php');
//
//            $file_contents = $this->Admin->turnOnDebug($file_contents);
//            if ($file_contents) {
//                //let's save it
//                file_put_contents($path_to_file.DS.'core.php', $file_contents);
//                $this->Session->setFlash('Debug mode is now ON');
//                echo 'Debug mode is now OFF';
//            } else {
//
//                //echo 'Debug mode already off';
//            }
//
//            $this->layout = false;
//            $this->render(false);
//
//
//
//        }
//        function developer_setDebug($debug = false) {
//            //this will set the debug to
//            $path_to_file = APP.'config';
//
//            $file_contents = file_get_contents($path_to_file.DS.'core.php');
//
//            if ($debug) {
//                //on
//                $file_contents = $this->Admin->turnOnDebug($file_contents);
//                if ($file_contents) {
//                    //let's save it
//                    file_put_contents($path_to_file.DS.'core.php', $file_contents);
//                    $this->Session->setFlash('Debug mode is now ON');
//                } else {
//                    $this->Session->setFlash('Already ON');
//                }
//            } else {
//                //off
//                $file_contents = $this->Admin->turnOffDebug($file_contents);
//                if ($file_contents) {
//                    //let's save it
//                    file_put_contents($path_to_file.DS.'core.php', $file_contents);
//                    $this->Session->setFlash('Debug mode is now OFF');
//                } else {
//                    $this->Session->setFlash('Already OFF');
//                }
//
//            }
//            $this->redirect($this->referer());
//        }
        private function canWeModifyCore()
        {

            $path_to_file = APP . 'Config';
            $file_contents = file_get_contents($path_to_file . DS . 'core.php');

            $pos = strpos($file_contents, $this->on_message);
            if ($pos === false) {
                //there is no on message, let's check if there is an off
                $pos_off = strpos($file_contents, $this->off_message);
                if ($pos_off === false) {
                    //there is a problem,manual intervention is required
                    $msg = 'Core.php cannot be modified: please fix: ' . $this->off_message;
                    $this->writeToLog($msg);
                    die ($msg);

                } else {

                }
            }
            $this->checkForCore = true;
        }

        //var $on_message = array("Configure::write('debug',2);", "Configure::write('debug',1);");
        var $on_message = "Configure::write('debug',2);";
        var $off_message = "Configure::write('debug',0);";

        function turnOnDebug($contents)
        {

            //if the debug mode is on then return same string
            //if the debug mode if off, replace the contents
            //        $on_message = "Configure::write('debug',2);";
            //        $off_message = "Configure::write('debug',0);";
            $pos = strpos($contents, $this->on_message);
            if ($pos === false) {
                //there is no on message, let's check if there is an off
                $pos_off = strpos($contents, $this->off_message);
                if ($pos_off === false) {
                    //there is a problem,manual intervention is required
                    $msg = 'manual intervention required the debug message needs to be exactly: ' . $this->off_message;
                    $this->writeToLog($msg);
                    die ($msg);

                } else {
                    //it's ok, there is an off, so let's repalce it
                    $contents_modified = str_replace($this->off_message, $this->on_message, $contents);
                    return $contents_modified;
                }
            } else {
                //it's already on
                return false;
            }
        }

        function turnOffDebug($contents)
        {

            //if the debug mode is on then return same string
            //if the debug mode if off, replace the contents

            $pos = strpos($contents, $this->off_message);
            if ($pos === false) {
                //there is no off message, let's check if there is an on
                $pos_off = strpos($contents, $this->on_message);
                if ($pos_off === false) {
                    //there is a problem,manual intervention is required
                    $msg = 'manual intervention required the debug message needs to be exactly: ' . $this->on_message;
                    $this->writeToLog($msg);
                    die ($msg);

                } else {
                    //it's ok, there is an off, so let's repalce it
                    $contents_modified = str_replace($this->on_message, $this->off_message, $contents);
                    return $contents_modified;
                }
            } else {
                //it's already on
                return false;
            }
        }
        //////////////////////////////////////////////////////////////////////// END AUTO DEBUG


//        function beforeRender() {
//
//        }
//        function beforeRenderFile() {
//
//        }
//        function afterRenderFile() {
//
//        }
//        function afterRender() {
//
//        }
//        function beforeLayout() {
//
//        }
//
//        function afterLayout() {
//
//        }


    }
}
