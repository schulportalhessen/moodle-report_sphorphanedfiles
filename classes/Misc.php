<?php

namespace report_sphorphanedfiles;

class Misc
{
    /**
     * @param stored_file $storedFile
     * @param stdClass $globalCfg
     * @return float
     */
    public static function convertByteInMegabyte(int $byte)
    {
        if ($byte === 0) {
            return $byte;
        }
        $filesizeInKilobyte = $byte / 1024;
        $filesizeInMegabyte = $filesizeInKilobyte / 1024;
        return number_format($filesizeInMegabyte, 2, ',', '');
    }
}
