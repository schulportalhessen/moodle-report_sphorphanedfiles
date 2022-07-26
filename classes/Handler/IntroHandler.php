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

use stdClass;
use cm_info;
use dml_exception;

use report_sphorphanedfiles\Files\FileInfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Class IntroHandler
 * @package report_sphorphanedfiles\Handler
 */
class IntroHandler extends Handler
{

    /**
     * @var string
     */
    private $componentName;

    /**
     * @return string
     */
    public function getComponentName(): string {
        return $this->componentName;
    }

    /**
     * @override
     */
    public function canHandle(string $component): bool {
        $handleractivitiescore = get_config('report_sphorphanedfiles', 'handleractivitiescore');
        if (isset($handleractivitiescore) && in_array($component, explode(',', $handleractivitiescore))) {
            return true;
        }
        $handleractivitiesplugin = get_config('report_sphorphanedfiles', 'handleractivitiesplugin');
        if (isset($handleractivitiesplugin) && in_array($component, explode(',', $handleractivitiesplugin))) {
            return true;
        }
        $handlermaterialscore = get_config('report_sphorphanedfiles', 'handlermaterialscore');
        if (isset($handlermaterialscore) && in_array($component, explode(',', $handlermaterialscore))) {
            return true;
        }
        $handlermaterialsplugin = get_config('report_sphorphanedfiles', 'handlermaterialsplugin');
        if (isset($handlermaterialsplugin) && in_array($component, explode(',', $handlermaterialsplugin))) {
            return true;
        }

        return false;
    }

    /**
     * @override
     */
    protected function enumerateFiles($user, $context, $course, $module): array {
        $result = $this->getManager()->database()->dataFiles()->getFilesForComponentIntro($context, $module) ?? [];
        return $this->postFilter($result);
    }

    /**
     * @param array $viewOrphanedFiles
     * @param int $contextId
     * @param stdClass $user
     * @param int $courseId
     * @param cm_info $instance
     * @param cm_info $iconHtml
     * @return array
     * @throws dml_exception
     */
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $user,
        $courseId,
        $instance,
        $iconHtml
    ): array {

        // FIXME: Das ist nicht die optimal passende Stelle für die Instanzvariablen-
        //        zuweisung. Verdeckte Abhängigkeit: getIntro nutzt getComponentName-
        //        Interface.
        $this->componentName = $instance->modname;

        $htmlContent = $this->getIntro($instance);

        $name = $instance->name;
        $userAllowedToDeleteThisFile = $this->apiM->security()->isUserAllowedToDeleteFiles($courseId, $user);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $htmlContent, $this->getComponentName());

        $componentName = $this->getComponentName();
        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);

            $viewOrphanedFiles[] = $this->getSkeleton(
                $formDelete,
                $file,
                $instance,
                [
                    'modName' => $componentName,
                    'name' => $name . " id=" . $instance->id,
                    'instanceId' => $instance->id,
                    'contextId' => $contextId,
                    'content' => $htmlContent,
                    'userAllowedToDeleteThisFile' => $userAllowedToDeleteThisFile,
                    'iconHtml' => $iconHtml,

                    'post_pathnamehash' => $formDelete->getPathnamehash(),
                    'post_contextId' => $formDelete->getContextId(),
                    'post_component' => $formDelete->getComponent(),
                    'post_filearea' => $formDelete->getFileArea(),
                    'post_itemId' => $formDelete->getItemId(),
                    'post_filepath' => $formDelete->getFilePath(),
                    'post_filename' => $formDelete->getFileName()
                ]
            );
        }

        return $viewOrphanedFiles;
    }
}
