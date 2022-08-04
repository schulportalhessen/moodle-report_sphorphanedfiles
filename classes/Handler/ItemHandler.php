<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\HTML;

defined('MOODLE_INTERNAL') || die();

abstract class ItemHandler extends Handler
{
    protected $implementationmode = 'item';

    /**
     * @override
     */
    protected function generateViewFile($orphanedFile)
    {
        if ($this->implementationmode == 'item') {
            return $this->apiM->files()->generateViewFileForWithItemId($orphanedFile);
        } else {
            return $this->apiM->files()->generateViewFile($orphanedFile);
        }
    }

    /**
     * @param FileInfo $fileInfo
     * @return string|void html-code tu display as a representation for the filename
     */
    public function getFileName(FileInfo $fileInfo)
    {
        if ('item' === $this->implementationmode) {
            // Content-Modus
            if ($fileInfo->getFileArea() === 'content') {
                $url = $this->apiM->files()->createURLForFileWithItem($this->apiM->files()->getFileUsingPathnamehash($fileInfo->getPathnamehash()));
                return HTML::createLinkInNewTab($url, $fileInfo->getFileName());
            }
        } else {
            // Intro-Modus
            return $this->getManager()->files()->generateFallbackView(
                $this->getManager()->files()->getFileUsingPathnamehash($fileInfo->getPathnamehash())
            );
        }
    }




    /**
     * Set the value of implementationmode
     *
     * @return  self
     */
    public function setImplementationmode($implementationmode)
    {
        $this->implementationmode = $implementationmode;

        return $this;
    }
}
