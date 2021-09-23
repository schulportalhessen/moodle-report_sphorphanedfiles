<?php

namespace report_sphorphanedfiles\Handler;

abstract class ItemHandler extends Handler
{
    /**
     * @override
     */
    protected function generateViewFile($orphanedFile)
    {
        return $this->apiM->files()->generateViewFileForWithItemId($orphanedFile);
    }
}
