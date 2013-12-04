<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Repository;


/**
 * Repository controller to handle file uploads and supply
 * data about repository files to frontend.
 *
 * Files can be uploaded from frontend and backend.
 * But files from frontend can be uploaded only to
 * secured folder not accessible from the Internet
 */
class AdminController extends \Ip\Controller{


    /**
     * Move files from temporary folder to repository.
     */
    public function storeNewFiles()
    {

        if (!isset($_POST['files']) || !is_array($_POST['files'])) {
            return new \Ip\Response\Json(array('status' => 'error', 'errorMessage' => 'Missing POST variable'));
        }

        $files = isset($_POST['files']) ? $_POST['files'] : array();

        $temporaryDir = str_replace('/', DIRECTORY_SEPARATOR, rtrim(ipFile('file/tmp/'), '/\\')); // for Windows compatibility

        foreach ($files as $key => $file) {
            if (realpath($file['dir']) != $temporaryDir) {
                throw new \Exception("File is outside TMP dir.");
            }
        }


        $newFiles = array();

        $destination = ipFile('file/repository/');
        foreach ($files as $key => $file) {
            $newName = \Ip\Internal\File\Functions::genUnoccupiedName($file['renameTo'], $destination);
            copy(ipFile($file['file']), $destination.$newName);
            unlink(ipFile($file['file'])); //this is a temporary file
            $browserModel = \Ip\Module\Repository\BrowserModel::instance();
            $newFile = $browserModel->getFile($newName);
            $newFiles[] = $newFile;
        }
        $answer = array(
            'status' => 'success',
            'files' => $newFiles
        );

        return new \Ip\Response\Json($answer);
    }


    public function getAll()
    {

        $seek = isset($_POST['seek']) ? (int) $_POST['seek'] : 0;
        $limit = 10000;
        $filter = isset($_POST['filter']) ? $_POST['filter'] : null;

        $browserModel = BrowserModel::instance();
        $files = $browserModel->getAvailableFiles($seek, $limit, $filter);

        usort ($files , array($this, 'sortFiles') );

        $fileGroups = array();
        foreach($files as $file) {
            $fileGroups[date("Y-m-d", $file['modified'])][] = $file;
        }


        $answer = array(
            'fileGroups' => $fileGroups
        );

        return new \Ip\Response\Json($answer);
    }

    private function sortFiles($a, $b)
    {
        if ($a['modified'] == $b['modified']) {
            return 0;
        }
        return ($a['modified'] > $b['modified']) ? -1 : 1;
    }


}