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

namespace report_sphorphanedfiles;

defined('MOODLE_INTERNAL') || die();

class Misc
{
    /**
     * @param stored_file $storedFile
     * @return float
     */
    public static function convertByteInMegabyte(int $byte) {
        if ($byte === 0) {
            return $byte;
        }
        $filesizeInKilobyte = $byte / 1024;
        $filesizeInMegabyte = $filesizeInKilobyte / 1024;
        return number_format($filesizeInMegabyte, 2, ',', '');
    }

    public static function translate($data, $translationFile, $prefix = "") {
        foreach ($data as $item)
            $result[$item] = get_string($prefix . $item, $translationFile);

        return $result;
    }
}
