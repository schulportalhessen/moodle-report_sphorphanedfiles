<?php

namespace report_sphorphanedfiles\Files;

use report_sphorphanedfiles\HTML;

class FileInfoList
{
    private $items;

    protected const FILEREFERENCEKEY = 'fileIDList';

    public static function isSufficientForConstruction($data): bool
    {
        if (isset($data[self::FILEREFERENCEKEY]))
            return true;

        return false;
    }

    public function __construct($data = [])
    {
        if (!empty($data) && isset($data[self::FILEREFERENCEKEY]))
            foreach ($data[self::FILEREFERENCEKEY] as $item)
                $this->items[] = new FileInfo($item);
    }

    public function getNumberOfItems()
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->getNumberOfItems() < 1;
    }

    public function toHTML()
    {
        return HTML::createList(
            array_map(
                function ($element) {
                    return $element->toString();
                },
                $this->items
            )
        );
    }

    public function delete($user, $courseId, $security, $files)
    {
        foreach ($this->items as $item)
            $files()->deleteFileByUserInCourse(
                $security,
                $item,
                $user,
                $courseId
            );
    }
}
