<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Class LabelHandler 
 */
class LabelHandler extends Handler
{
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $user,
        $courseId,
        $instance,
        $iconHtml
    ): array {
        $htmlContent = $instance->content;
        
        $modName = $instance->modname;

        $userAllowedToDelete = $this->isUserAllowedToViewDeleteAllFilesForCourse($user, $courseId);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $htmlContent, $modName);
        // echo "$modName: " .  count($orphanedFiles) . '<br />';
        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);

            $viewOrphanedFiles[] = $formDelete->addFileReferenceInformation([
                'modName' => $modName,
                'name' => get_string('pluginname', 'mod_label') . ' id=' . $instance->id,
                'instanceId' => $instance->id,
                'contextId' => $contextId,
                'filename' => $this->getFileName(new FileInfo($formDelete)),
                'preview' => $this->getPreviewForFile(new FileInfo($formDelete)),
                'content' => $htmlContent,
                'userAllowedToDelete' => $userAllowedToDelete,
                'filesize' => Misc::convertByteInMegabyte((int)$file->filesize)
            ]);
        }

        return $viewOrphanedFiles;
    }
}
