<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
    protected function generateViewFile($orphanedFile) {
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
    public function getFileName(FileInfo $fileInfo) {
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
    public function setImplementationmode($implementationmode) {
        $this->implementationmode = $implementationmode;

        return $this;
    }
}
