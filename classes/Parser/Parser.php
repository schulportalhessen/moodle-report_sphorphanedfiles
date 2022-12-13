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

namespace report_sphorphanedfiles\Parser;

defined('MOODLE_INTERNAL') || die();

//
// Preparations for later PHP 8 transition.
//
// We are currently using PHP 7.x which is not the latest PHP version.
// Functionality that is built-in in PHP 8 and might be useful in our
// modules is provided.
//
// The following code is safe to execute in PHP 8 environments as a check is
// performed before a „substitute“ is provided.
//
//

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle) {
        return empty($needle) || strpos($haystack, $needle) !== false;
    }
}

/**
 * Class Parser
 */
class Parser
{
    /**
     * @param string $htmlContent
     * @return array|null
     */
    public function extractFileNamesFromString(string $htmlContent): ?array {
        // search for all images
        preg_match_all("/<img\s.*?src=(?:'|\")([^'\">]+)(?:'|\")/", $htmlContent, $matchesImg);

        $files = [];
        foreach ($matchesImg[1] ?? [] as $usedFile) {
            $files[] = urldecode($usedFile);
        }

        // search for all links
        preg_match_all("/<a\s.*?href=(?:'|\")([^'\">]+)(?:'|\")/", $htmlContent, $matchesHref);
        foreach ($matchesHref[1] ?? [] as $usedFile) {
            $files[] = urldecode($usedFile);
        }

        // search all links in text
        $urlstart = '(?:http(s)?://|(?<!://)(www\.))';
        $domainsegment = '(?:[\pLl0-9][\pLl0-9-]*[\pLl0-9]|[\pLl0-9])';
        $numericip = '(?:(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
        $port = '(?::\d*)';
        $pathchar = '(?:[\pL0-9\.!$&\'\(\)*+,;=_~:@-]|%[a-f0-9]{2})';
        $path = "(?:/$pathchar*)*";
        $querystring = '(?:\?(?:[\pL0-9\.!$&\'\(\)*+,;=_~:@/?-]|%[a-fA-F0-9]{2})*)';
        $fragment = '(?:\#(?:[\pL0-9\.!$&\'\(\)*+,;=_~:@/?-]|%[a-fA-F0-9]{2})*)';

        // Lookbehind assertions.
        // Is not HTML attribute or CSS URL property. Unfortunately legit text like "url(http://...)" will not be a link.
        $lookbehindend = "(?<![]),.;])";

        $regex = "$urlstart((?:$domainsegment\.)+$domainsegment|$numericip)" .
            "($port?$path$querystring?$fragment?)$lookbehindend";

        preg_match_all('#' . $regex . '#ui', $htmlContent, $matchesUrl);
        foreach ($matchesUrl[0] ?? [] as $usedFile) {
            $files[] = urldecode($usedFile);
        }

        return $files;
    }

    /**
     * @param string $htmlContent
     * @param array $allFiles
     * @param int $contextId
     * @return array
     */
    public function extractOrphanedFilesFromString(string $htmlContent, array $allFiles, $contextId = ""): array {
        $filesInContent = $this->extractFileNamesFromString($htmlContent);

        foreach ($allFiles ?? [] as $index => $file) {
            foreach ($filesInContent ?? [] as $contentFile) {
                if (
                    str_contains($contentFile, $file->filename) &&
                    str_contains($contentFile, (string)$contextId)
                ) {
                    // This is NOT an orphaned file -- it is used in the content -- so
                    // it is removed from the list.
                    //
                    // TODO: Check if the file is an alias --> SQL query?
                    unset($allFiles[$index]);
                }
            }
        }

        return $allFiles;
    }
}
