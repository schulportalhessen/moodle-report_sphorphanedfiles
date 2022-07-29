<?php

namespace report_sphorphanedfiles\Files;

use file_storage;
use stored_file;
use moodle_url;

use report_sphorphanedfiles\Security\Security;
use report_sphorphanedfiles\HTML;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

defined('MOODLE_INTERNAL') || die();

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
        if ($storedFile->get_filepath() === '/') {
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

    /**
     * Delete a file if the user is allowed to delete this file.
     * User is allowed to delete if the userid of the file is the userid of the user,
     * so the user is the owner of the file.
     * Also a users with the capability moodle/course:manageactivitys is allowed to delete ALL files because he is
     * has the role editing teacher. (Nonediting teachers does not have this capability.)
     *
     * @param Security $security
     * @param FileInfo $fileInfo
     * @param $user
     * @param $course
     * @return bool
     */
    public function deleteFileByUserInCourse(Security $security, FileInfo $fileInfo, $user, $course): bool
    {
        $isCourseIdOfFileSameLikeCourseidOfTheCourse = $security->isCourseIdOfFileSameLikeCourseidOfTheCourse($fileInfo, $course);
        if ($isCourseIdOfFileSameLikeCourseidOfTheCourse && $security->allowedToViewDeleteAllFiles($course, $user)) {
            $fileToBeDeleted = $this->getFileUsingFileInfo($fileInfo);
            if (!$fileToBeDeleted) {
                echo "file not found, so data might be manipulated or the file is already deleted or something went wrong";
                return false;
            } else {
                // echo "... delete nur simmuliert"; die(); // development!!
                $fileToBeDeleted->delete();
                return true;
            }
        }
        return false;
    }

}
