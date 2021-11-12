<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

/**
 * Class SectionSummaryHandler 
 */
class SectionSummaryHandler extends ItemHandler
{
    /**
     * @override
     */
    protected function enumerateFiles($user, $context, $course, $fileItemIdSectionInfo): array
    {
        if ($this->isUserAllowedToViewDeleteAllFilesForCourse($user, $course)) {
            $result = $this->apiM->database()->dataFiles()->getFilesForSectionSummary($fileItemIdSectionInfo, $context) ?? [];
        } else {
            $result = $this->apiM->database()->dataFiles()->getFilesOfUserForSectionSummary($user->id, $context, $fileItemIdSectionInfo) ?? [];
        }

        return $this->postFilter($result);
    }

    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $sectionInfo,
        $user,
        $courseId,
        $iconHtml
    ): array {
        $sectionHtml = $sectionInfo->summary;
        $fileItemIdSectionInfo = $sectionInfo->id;

        $userAllowedToDelete = $this->isUserAllowedToViewDeleteAllFilesForCourse($user, $courseId);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $sectionHtml, $fileItemIdSectionInfo);
        echo "<h3> Anzahl verwaister Dateien: </h3>";

        $modName = 'Sectionsummary';
        echo "$modName: " .  count($orphanedFiles) . '<br />';
        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFile($file);

            $viewOrphanedFiles[] = $formDelete->addFileReferenceInformation([
                'modName' => 'course',
                'name' => "$modName",
                'instanceId' => 'todo',
                'contextId' => $contextId,
                'filename' => $this->getFileName(new FileInfo($formDelete)),
                'preview' => $this->getPreviewForFile(new FileInfo($formDelete)),
                'content' => $sectionHtml,
                'userAllowedToDelete' => $userAllowedToDelete,
                'filesize' => Misc::convertByteInMegabyte((int)$file->filesize)
            ]);
        }

        return $viewOrphanedFiles;
    }
}
