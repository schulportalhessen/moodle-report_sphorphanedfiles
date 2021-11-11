<?php

namespace report_sphorphanedfiles\Handler;

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
