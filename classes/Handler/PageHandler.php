<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Class PageHandler
 * @package report_sphorphanedfiles\Handler
 */
class PageHandler extends ItemHandler
{
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $user,
        $courseId,
        $instance,
        $iconHtml
    ): array {
        $htmlContent = $this->getIntro($instance);

        $modName = $instance->modname;
        $name = $instance->name;

        // Sonderfall, weil PAge auch HTML-Content hat
        $page = $this->getManager()->database()->dataFiles()->getPage($instance);
        $htmlContent .= '<h4>Seiteninhalt</h4>'
            . file_rewrite_pluginfile_urls(
                $page->content,
                'pluginfile.php',
                $contextId,
                'mod_page',
                'content',
                $page->revision
            );

        $userAllowedToDeleteThisFile =  $this->apiM->security()->isUserAllowedToDeleteFiles($courseId, $user);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $htmlContent, $modName);

        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);
            // Bad workaround
            $this->setImplementationmode('xxxxxx');
            if ($file->filearea == 'content' ) {
                $this->setImplementationmode('item');
            }

            $viewOrphanedFiles[] = $this->getSkeleton(
                $formDelete,
                $file,
                $instance,
                [
                    'modName' => $modName,
                    'name' => $name." id=".$instance->id,
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
