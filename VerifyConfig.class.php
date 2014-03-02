<?php

/**
 * Verify config
 *
 * @author Thierry Daguin
 * @since 2014-03-01
 */
class VerifyConfig {

    private $oReadConfig;
    private $tAllPhpRelease = array();
    private $phpDownloadUrl = "http://windows.php.net/download/";

    public function __construct(ReadConfig $oReadConfig) {
        $this->oReadConfig = $oReadConfig;
    }

    public function wampserverConfFileExists($phpRelease) {
        if (is_file($this->oReadConfig->getWampserverConfFileName($phpRelease))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Read wampserver.conf
     * @param type $version
     */
    public function currentApacheVersionIsdefinedInWampserverConfFile($phpRelease) {
        if (is_file($this->oReadConfig->getWampserverConfFileName($phpRelease))) {
            require_once $this->oReadConfig->getWampserverConfFileName($phpRelease);
            $tTemp = explode('.', $this->oReadConfig->getCurrentApacheRelease());
            $trimedCurrentApacheVersion = $tTemp[0] . '.' . $tTemp[1];
            if (isset($phpConf['apache'][$trimedCurrentApacheVersion])) {
                return true;
            }
        }
        return false;
    }

    public function phpForApacheIniFileExists($phpRelease) {
        if (is_file($this->oReadConfig->getPhpForApacheIniFileName($phpRelease))) {
            return true;
        } else {
            return false;
        }
    }

    public function phpIniFileExists($phpRelease) {
        if (is_file($this->oReadConfig->getPhpIniFileName($phpRelease))) {
            return true;
        } else {
            return false;
        }
    }

    public function zipExtensionIsNotInPhpIniFile($phpRelease) {
        if ($this->extensionIsNotInPhpIniFile($phpRelease, 'php_zip.dll')) {
            return true;
        }
        return false;
    }

    public function pharExtensionIsNotInPhpIniFile($phpRelease) {
        if ($this->extensionIsNotInPhpIniFile($phpRelease, 'php_phar.dll')) {
            return true;
        }
        return false;
    }

    public function extensionIsNotInPhpIniFile($phpRelease, $extension) {
        if ($this->phpForApacheIniFileExists($phpRelease)) {
            $result = strstr(file_get_contents($this->oReadConfig->getPhpForApacheIniFileName($phpRelease)), $extension);
            if ($result === false) {
                return true;
            }
        }
        return false;
    }

    private function addResponseOk($question) {
        $this->t[$question]['response']['result'] = true;
    }

    private function addResponseKo($question, $response = '') {
        $this->t[$question]['response']['result'] = false;
        $this->t[$question]['response']['content'] = $response;
    }

    public function verify($phpRelease) {
        $this->t = array();

        /*
         * wampserver.conf file is found ?
         */
        $question = $this->oReadConfig->getWampserverConfFileName($phpRelease) . ' is found ?';
        if ($this->wampserverConfFileExists($phpRelease)) {
            $this->addResponseOk($question);
        } else {
            $this->addResponseKo($question, 'Copy this file from another PHP dir');
        }

        /*
         * wampserver.conf file contains current Apache version ?
         */
        $question = $this->oReadConfig->getWampserverConfFileName($phpRelease) . ' contains current Apache version (' . $this->oReadConfig->getFunctionnalRelease($this->oReadConfig->getCurrentApacheRelease()) . ') ?';
        if ($this->currentApacheVersionIsdefinedInWampserverConfFile($phpRelease)) {
            $this->addResponseOk($question);
        } else {
            $this->addResponseKo($question, 'Correct this file to add apache version in array');
        }

        /*
         * phpForApache.ini file is found ?
         */
        $question = $this->oReadConfig->getPhpForApacheIniFileName($phpRelease) . ' is found ?';
        if ($this->phpForApacheIniFileExists($phpRelease)) {
            $this->addResponseOk($question);
        } else {
            $this->addResponseKo($question, 'Copy this file from another PHP ' . $this->oReadConfig->getFunctionnalRelease($phpRelease) . ' dir or from php.ini in this dir');
        }

        /*
         * zip extension 
         */
        $question = $this->oReadConfig->getPhpForApacheIniFileName($phpRelease) . ' does NOT contain zip extension ?';
        if ($this->zipExtensionIsNotInPhpIniFile($phpRelease)) {
            $this->addResponseOk($question);
        } else {
            $this->addResponseKo($question, 'you should delete the line "extension=php_zip.dll" even it is commented to avoid problems in the future');
        }

        /*
         * phar extension 
         */
        $question = $this->oReadConfig->getPhpForApacheIniFileName($phpRelease) . ' does NOT contain phar extension ?';
        if ($this->pharExtensionIsNotInPhpIniFile($phpRelease)) {
            $this->addResponseOk($question);
        } else {
            $this->addResponseKo($question, 'you should delete the line "extension=php_phar.dll" even it is commented to avoid problems in the future');
        }

// TODO : verifier dans le php.ini le chemin des extensions
//extension_dir = "J:/wamp/bin/php/php5.4.3/ext/"


        /*
         * php.ini file is found ?
         */
        $question = $this->oReadConfig->getPhpIniFileName($phpRelease) . ' is found ?';
        if ($this->phpIniFileExists($phpRelease)) {
            $this->addResponseOk($question);
        } else {
            $this->addResponseKo($question, 'Copy this file from another PHP ' . $this->oReadConfig->getFunctionnalRelease($phpRelease) . ' dir or from php.ini-development in this dir');
        }


        return $this->t;
    }

    public function getAllowedPhpReleases() {
        $this->tAllPhpRelease = array();

        // Search all PHP releases
        $page = file_get_contents($this->getPhpDownloadUrl());
        $info_lines = explode("\n", strip_tags($page, "<option>"));
        foreach ($info_lines as $line) {
            if (preg_match("~<option value=\"php~", $line, $val)) {
                //var_dump($val) . BR;
                //echo '<pre>'.$line.'</pre>'.BR;
                if (preg_match('/"([^"]+)"/', $line, $t)) {
                    //php-5.5-ts-VC11-x86
                    list($php, $release, $ts, $compiler, $arch) = explode('-', $t[1]);
                    $this->tAllPhpRelease[$arch][$compiler][$t[1]] = $t[1];
                }
            }
        }
        // Search possible releases. Suppose it has to be compiled as the current one
        $tTemp = explode(' ', $this->oReadConfig->getCurrentPhpCompiler()); // MSVC9 (Visual C++ 2008) 
        $compiler = ltrim($tTemp[0], 'MS');

        return $this->tAllPhpRelease['x86'][$compiler];
    }

    public function getPhpDownloadUrl() {
        return $this->phpDownloadUrl;
    }

}
