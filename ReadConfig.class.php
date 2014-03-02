<?php

/**
 * Read wampserver, php config
 *
 * @author Thierry Daguin
 * @since 2014-03-01
 */
class ReadConfig {

    private $tPhpInfo = array();
    private $wampInstallDir;
    private $tReleases = array();
    private $currentApacheRelease;

    public function __construct($wampInstallDir) {
        $this->setWampInstallDir($wampInstallDir);
        $this->discoverInstalledPhpRelease();
        $this->getPhpInfoInArray();
    }

    private function getPhpInfoInArray() {
        if (empty($this->tPhpInfo)) {
            ob_start();
            phpinfo();
            $this->tPhpInfo = array();
            $info_lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
            $cat = "General";
            foreach ($info_lines as $line) {
                // new cat?
                preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
                if (preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
                    $this->tPhpInfo[trim($cat)][trim($val[1])] = $val[2];
                } elseif (preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
                    $this->tPhpInfo[$cat][$val[1]] = array("local" => $val[2], "master" => $val[3]);
                }
            }
        }
    }

    public function setWampInstallDir($dir) {
        $this->wampInstallDir = $dir . DIRECTORY_SEPARATOR;
        if (!is_file($this->wampInstallDir . 'wampmanager.exe')) {
            throw new Exception('Error : Wamp install dir incorrect : ' . $this->wampInstallDir);
        }
    }

    public function getWampInstallDir() {
        return $this->wampInstallDir;
    }

    public function discoverInstalledPhpRelease() {

        $phpDir = $this->wampInstallDir . 'bin' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR;
        if (is_dir($phpDir)) {
            $handle = opendir($phpDir);
            while ($dir = readdir($handle)) {
                if (is_dir($phpDir . $dir) && strstr($dir, 'php')) {
                    $trimedRelease = ltrim($dir, 'php');
                    $this->tReleases[$trimedRelease] = $dir;
                }
            }
            closedir($handle);
        } else {
            throw new Exception('PHP dir not found : ' . $phpDir);
        }
    }

    public function getInstalledPhpRelease() {
        return $this->tReleases;
    }

    public function getWampserverConfFileName($phpRelease) {
        return $this->getPhpDir($phpRelease) . 'wampserver.conf';
    }

    public function getPhpForApacheIniFileName($phpRelease) {
        return $this->getPhpDir($phpRelease) . 'phpForApache.ini';
    }

    public function phpIniFileExists($phpRelease) {
        if (is_file($this->getPhpIniFileName($phpRelease))) {
            return true;
        } else {
            return false;
        }
    }

    public function getPhpIniFileName($phpRelease) {
        return $this->getPhpDir($phpRelease) . 'php.ini';
    }

    public function getPhpDir($phpRelease) {
        if (isset($this->tReleases[$phpRelease])) {
            return $this->wampInstallDir . 'bin' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . $this->tReleases[$phpRelease] . DIRECTORY_SEPARATOR;
        } else {
            throw new Exception('PHP Release not found ');
        }
    }

    public function getCurrentApacheRelease() {
        return $this->currentApacheRelease;
    }

    public function setCurrentApacheRelease($currentApacheRelease) {
        $this->currentApacheRelease = $currentApacheRelease;
    }

    /**
     * Return the functionnal release (x.y) from a revision release (x.y.z)
     * 
     * @param string $revisionRelease 
     * @return string
     */
    public function getFunctionnalRelease($revisionRelease) {
        $tTemp = explode('.', $revisionRelease);
        return $tTemp[0] . '.' . $tTemp[1];
    }

    public function getCurrentPhpCompiler() {
        $this->getPhpInfoInArray();
        return $this->tPhpInfo['General']['Compiler'];
    }

}
