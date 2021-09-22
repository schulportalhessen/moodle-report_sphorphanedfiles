<?php

namespace report_sphorphanedfiles\Files;

use file_storage;
use html_writer;
use stdClass;
use stored_file;

use report_sphorphanedfiles\Security\Security;
use report_sphorphanedfiles\HTML;

/**
 * Class Files
 */
class Files
{
    /**
     * @var file_storage
     */
    private $fileStorage;

    /**
     * Files constructor.
     */
    public function __construct()
    {
        $this->fileStorage = get_file_storage();
    }

    /**
     * @return file_storage
     */
    public function getFileStorage(): file_storage
    {
        return $this->fileStorage;
    }

    /**
     * @return bool|stored_file
     */
    public function getFile(array $fileInfo)
    {
        return $this->getFileStorage()->get_file(
            $fileInfo['contextId'],
            $fileInfo['component'],
            $fileInfo['filearea'],
            $fileInfo['itemId'],
            $fileInfo['filepath'],
            $fileInfo['filename']
        );
    }

    /**
     * @return bool|stored_file
     */
    public function getFileUsingFileInfo(FileInfo $fileInfo)
    {
        return $this->getFile($fileInfo->toArray());
    }

    protected function createPathForFileWithItem(stored_file $storedFile)
    {
        return DIRECTORY_SEPARATOR . $storedFile->get_contextid() . 
               DIRECTORY_SEPARATOR . $storedFile->get_component() . 
               DIRECTORY_SEPARATOR . $storedFile->get_filearea() . $storedFile->get_filepath() . $storedFile->get_itemid() . 
               DIRECTORY_SEPARATOR . $storedFile->get_filename();
    }

    protected function createPathForFile(stored_file $storedFile)
    {
        return DIRECTORY_SEPARATOR . $storedFile->get_contextid() . 
               DIRECTORY_SEPARATOR . $storedFile->get_component() . 
               DIRECTORY_SEPARATOR . $storedFile->get_filearea() . $storedFile->get_filepath() . $storedFile->get_filename();
    }

    /**
     * @param stored_file $storedFile
     * @param stdClass $globalCfg
     * @return string
     */
    public function generateViewFile(stored_file $storedFile, $globalCfg)
    {
        $imagepath = $this->createPathForFile($storedFile);

        $imageUrl = file_encode_url(
            $globalCfg->wwwroot . '/pluginfile.php',
            $imagepath,
            false
        );

        return HTML::createImage($imageUrl);
    }

    /**
     * @param stored_file $storedFile
     * @param stdClass $globalCfg
     * @return string
     */
    public function generateViewFileForWithItemId(stored_file $storedFile, $globalCfg)
    {
        $imagePath = $this->createPathForFileWithItem($storedFile);

        $imageUrl = file_encode_url(
            $globalCfg->wwwroot . '/pluginfile.php',
            $imagePath,
            false
        );

        return HTML::createImage($imageUrl);
    }

    /**
     * @param stored_file $storedFile
     * @param stdClass $globalCfg
     * @return string
     */
    public function generateFallbackView(stored_file $storedFile, $globalCfg)
    {
        $path = $this->createPathForFile($storedFile);

        $pathUrl = file_encode_url(
            $globalCfg->wwwroot . '/pluginfile.php',
            $path,
            false
        );

        return HTML::createLinkInNewTab($pathUrl, $storedFile->get_filename());
    }

    public function deleteFileByUserInCourse(Security $security, FileInfo $fileInfo, $user, $course): bool
    {
        $deleteFile = $this->getFileUsingFileInfo($fileInfo);

        if ($deleteFile) {
            if ($deleteFile->userid === $user->id || $security->allowedToViewDeleteAllFiles($course, $user)) {
                $deleteFile->delete();

                return true;
            }
        }

        return false;
    }
}
