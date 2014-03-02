<?php

/**
 * 
 * @author Thierry Daguin
 * @since 2014-03-01
 */
define('BR', '<br>');
define('WAMP_INSTALL_DIR', realpath('../'));

require_once WAMP_INSTALL_DIR . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'UiComponents.class.php';
require_once WAMP_INSTALL_DIR . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'ReadConfig.class.php';
require_once WAMP_INSTALL_DIR . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'VerifyConfig.class.php';

$oReadConfig = new ReadConfig(WAMP_INSTALL_DIR);
$oReadConfig->setCurrentApacheRelease($apacheVersion);
$oVerifyConfig = new VerifyConfig($oReadConfig);

$morePhpContents = 'Only tested with Wampserver 2 32 bits';

$tVersions = $oReadConfig->getInstalledPhpRelease();

// Iterate on each PHP version
foreach ($tVersions as $phpRelease => $dir) {
    $tmpContent = '';

    $morePhpContents .= BR . UiComponents::getPhpReleaseBox($phpRelease);


    $tVerify = $oVerifyConfig->verify($phpRelease);

    foreach ($tVerify as $question => $tDetail) {
        if ($tDetail['response']['result'] === false) {
            $response = UiComponents::getKoBox('KO -> ' . $tDetail['response']['content']);
        } else {
            $response = UiComponents::getOkBox();
        }
        $tmpContent.= $question . ' => ' . $response . BR;
    }

    $morePhpContents.= UiComponents::getInfoBox($tmpContent);
}

$morePhpContents .= BR . UiComponents::getPhpReleaseBox('Other release you could install');

$tAllowedPhpReleases = $oVerifyConfig->getAllowedPhpReleases();
$tmpContent = 'Here is releases you can find at <a href="'.$oVerifyConfig->getPhpDownloadUrl().'" target="_blank">'.$oVerifyConfig->getPhpDownloadUrl().'</a>'.BR;
$tmpContent .= 'Prefer ts (Thread Safe)'.BR;
//VC9 x86 Non Thread Safe 
foreach ($tAllowedPhpReleases as $release) {
   list($php, $release, $ts, $compiler, $arch) = explode('-', $release);
   $tmpContent .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$release.' '.$compiler.' '.$arch.' '.$ts .BR;
}
$morePhpContents.= UiComponents::getInfoBox($tmpContent) . BR;

// TODO : donner les version de php installables pour la version courant de PHP

