<?php

namespace report_sphorphanedfiles\Files;

use file_storage;
use stored_file;
use moodle_url;

use report_sphorphanedfiles\Security\Security;
use report_sphorphanedfiles\HTML;

/**
 * Class Files
 */
class Files
{
    private const DIRECTORY_SEPARATOR = "/";
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
        if ($storedFile->get_filepath() === '/'){
            return self::DIRECTORY_SEPARATOR . $storedFile->get_contextid() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_component() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_filearea() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_itemid() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_filename();
        } else {
            return self::DIRECTORY_SEPARATOR . $storedFile->get_contextid() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_component() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_filearea() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_itemid() . 
            $storedFile->get_filepath() . $storedFile->get_filename();
        }
    }

    protected function createPathForFile(stored_file $storedFile)
    {
        return self::DIRECTORY_SEPARATOR . $storedFile->get_contextid() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_component() .
            self::DIRECTORY_SEPARATOR . $storedFile->get_filearea() . $storedFile->get_filepath() . $storedFile->get_filename();
    }

    protected function createURLForFile(stored_file $storedFile)
    {
        return new moodle_url('/pluginfile.php' . $this->createPathForFile($storedFile));
    }

    public function createURLForFileWithItem(stored_file $storedFile)
    {
        return new moodle_url('/pluginfile.php' . $this->createPathForFileWithItem($storedFile));
    }

    /**
     * @param stored_file $storedFile
     * @return string
     */
    public function generateViewFile(stored_file $storedFile)
    {
        return HTML::createImage($this->createURLForFile($storedFile));
    }

    /**
     * @param stored_file $storedFile
     * @return string
     */
    public function generateViewFileForWithItemId(stored_file $storedFile)
    {
        return HTML::createImage($this->createURLForFileWithItem($storedFile));
    }

    /**
     * @param stored_file $storedFile
     * @return string
     */
    public function generateFallbackView(stored_file $storedFile)
    {
        return HTML::createLinkInNewTab($this->createURLForFile($storedFile), $storedFile->get_filename());
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