<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\HTML;

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
     * @override
     */
    public function getFileName(FileInfo $fileInfo)
    {
        if ('item' === $this->implementationmode) {
            // Content-Modus
            if ($fileInfo->getFileArea() === 'content') {
                $url = $this->apiM->files()->createURLForFileWithItem($this->apiM->files()->getFileUsingFileInfo($fileInfo));
                return HTML::createLinkInNewTab($url, $fileInfo->getFileName());
            }
        } else {
            // Intro-Modus
            return $this->getManager()->files()->generateFallbackView(
                $this->getManager()->files()->getFileUsingFileInfo($fileInfo)
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
