<?php

namespace report_sphorphanedfiles\Files;

use InvalidArgumentException;

defined('MOODLE_INTERNAL') || die();

/** 
 * This class provides an OOP-representation of the metadata which is
 * used within the Moodle system for data referencing.
 */
class FileInfo
{
    private const SERIALIZATION_SEPARATOR = "§";

    private $pathnamehash;

    private $contextId;
    private $component;
    private $filearea;
    private $itemId;
    private $filepath;
    private $filename;

    /**
     * Create a FileInfo instance using either a string representation (-> serialization)
     *  OR a dictionary OR another FileInfo instance containing the relevant information.
     *
     *  @param $data The data (string or dictionary) to be used for instance
     *               initialization.
     */
    public function __construct($data = null)
    {
        // The world would be simpler, if method and constructor overloading based on.
        // parameter signatures would be possible in PHP :-).
        if (!is_null($data)) {
            if (is_array($data)) {
                $this->setFromArray($data);
            } else if (is_string($data)) {
                $this->setFromString($data);
            } else if ($data instanceof FileInfo) {
                $this->setFromArray($data->toArray());
            } else {
                throw new InvalidArgumentException();
            }
        }
    }

    //protected const FILEREFERENCEKEY = 'filepath_filename';

    //public function addFileReferenceInformation_weg(array $data): array
    //{
    //    $data[self::FILEREFERENCEKEY] = $this->toString();
    //    return $data;
    //}

    public function getPathnamehash()
    {
        return $this->pathnamehash;
    }

    public function getContextId()
    {
        return $this->contextId;
    }

    public function getComponent()
    {
        return $this->component;
    }

    /**
     * @return
     */
    public function getFileArea(): ?string
    {
        return $this->filearea;
    }

    public function getItemId()
    {
        return $this->itemId;
    }

    public function getFilePath()
    {
        return $this->filepath;
    }

    public function getFileName()
    {
        return $this->filename;
    }

    public function toArray(): array
    {
        return [
            'pathnamehash' => $this->getPathnamehash(),
            'contextId' => $this->getContextId(),
            'component' => $this->getComponent(),
            'filearea'  => $this->getFileArea(),
            'itemId'    => $this->getItemId(),
            'filepath'  => $this->getFilePath(),
            'filename'  => $this->getFileName()
        ];
    }

    public function toString(): string
    {
        return implode(FileInfo::SERIALIZATION_SEPARATOR, $this->toArray());
    }

    public function setFromString($data)
    {
        $infoComponents = explode(FileInfo::SERIALIZATION_SEPARATOR, $data);

        $this->setFromArray([
            'pathnamehash' => $infoComponents[0],
            'contextId' => $infoComponents[1],
            'component' => $infoComponents[2],
            'filearea'  => $infoComponents[3],
            'itemId'    => $infoComponents[4],
            'filepath'  => $infoComponents[5],
            'filename'  => $infoComponents[6]
        ]);
    }

    public function setFromArray($data)
    {
       // if (isset($data[self::FILEREFERENCEKEY])) {
       //     $this->setFromString($data[self::FILEREFERENCEKEY]);
       // } else {
            $this->pathnamehash = $data['pathnamehash'];
            $this->contextId = $data['contextId'];
            $this->component = $data['component'];
            $this->filearea = $data['filearea'];
            $this->itemId   = $data['itemId'];
            $this->filepath = $data['filepath'];
            $this->filename = $data['filename'];
        //}
    }

    public function setFromFileWithContext($file, $contextId): FileInfo
    {
        $this->setFromArray([
            'pathnamehash' => $file->pathnamehash,
            'contextId' => $contextId,
            'component' => $file->component,
            'filearea'  => $file->filearea,
            'itemId'    => $file->itemid,
            'filepath'  => $file->filepath,
            'filename'  => $file->filename
        ]);

        return $this;
    }

    public function setFromFile($file): FileInfo
    {
        $this->setFromFileWithContext($file, $file->contextid);

        return $this;
    }
}
