<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

/**
 * Class PageHandler
 * @package report_sphorphanedfiles\Handler
 */
class PageHandler extends Handler
{
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $user,
        $courseId,
        $instance,
        $iconHtml
    ): array {

        $htmlContent = '';
        $modName = $instance->modname;
        $name = $instance->name;

        $dbparams = ['id' => $instance->instance];

        //$htmlContent .= format_module_intro('page', $page, $instance->id, false);
        $htmlContent = $this->getIntro($instance);

        // page is different to other mod
        $page = $this->apiM->database()->getDbM()->get_record('page', $dbparams, '*');
        $htmlContent .= '<h4>Seiteninhalt</h4>' . file_rewrite_pluginfile_urls($page->content, 'pluginfile.php', $contextId, 'mod_page', 'content', $page->revision);

        $userAllowedToDelete = $this->isUserAllowedToViewDeleteAllFilesForCourse($user, $courseId);

        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $modName, $courseId, $htmlContent);

        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);

            $viewOrphanedFiles[] = [
                'modName' => $modName,
                'name' => $name,
                'modurl' => $this->getModuleURLForInstance($instance),
                'instanceId' => $instance->id,
                'contextId' => $contextId,
                'filename' => $this->getFileName(new FileInfo($formDelete)),
                'preview' => $this->getPreviewForFileWithItemId(new FileInfo($formDelete)),
                'formDelete' => $formDelete->toArray(),
                'content' => $htmlContent,
                'userAllowedToDelete' => $userAllowedToDelete,
                'iconHtml' => $iconHtml,
                'filesize' => Misc::convertByteInMegabyte((int)$file->filesize)
            ];
        }

        return $viewOrphanedFiles;
    }
}
