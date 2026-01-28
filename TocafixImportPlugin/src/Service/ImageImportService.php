<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Content\Media\MediaException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ImageImportService
{
    const TEMP_NAME = 'image-import-from-url';
    const MEDIA_DIR = '/public/media/';
    const MEDIA_FOLDER = 'product';

    private $mediaRepository;
    private $mediaService;
    private $fileSaver;

    /**
     * ImageImport constructor.
     *
     * @param EntityRepository $mediaRepository
     * @param MediaService $mediaService
     * @param FileSaver $fileSaver
     */
    public function __construct(
        EntityRepository $mediaRepository,
        MediaService $mediaService,
        FileSaver $fileSaver
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->mediaService = $mediaService;
        $this->fileSaver = $fileSaver;
    }

    /**
     * Method, that downloads a file from a URL and returns an ID of a newly created media, based on it
     *
     * @param string $imageName
     * @param Context $context
     * @return string|null
     */
    public function addImageToMediaFromPath(string $imageName, string $basePath, Context $context)
    {
        $mediaId = null;

        //process with the cache disabled
        $context->addState(EntityIndexerRegistry::USE_INDEXING_QUEUE);

        $imagePaths = $this->rglob($basePath . '/' . $imageName);
        if (!empty($imagePaths)) {
            $imagePath = $imagePaths[0];
            $fileNameParts = explode('.', $imageName);

            //get the file name and extension
            $fileName = $fileNameParts[0];
            $fileExtension = $fileNameParts[1];

            if ($fileName && $fileExtension) {

                // check if the file already exists in the media folder and get id if so
                $mediaId = $this->getMediaIdByFileName($fileName, $context);

                if ($mediaId) {
                    return $mediaId;
                }

                //copy the file from the URL to the newly created local temporary file
                $filePath = tempnam(sys_get_temp_dir(), self::TEMP_NAME);
                file_put_contents($filePath, file_get_contents($imagePath));

                //create media record from the image
                $mediaId = $this->createMediaFromFile($filePath, $fileName, $fileExtension, $context);
            }
        }

        return $mediaId;
    }

    /**
     * Method, that returns an ID of a newly created media, based on a local file from the Shopware's media directory
     *
     * @param string $fileName
     * @param string $directoryName
     * @param Context $context
     * @return string|null
     */
    public function addImageToMediaFromFile(string $fileName, string $directoryName, Context $context)
    {
        //compose the path to file
        $filePath = trim($directoryName . '/' . $fileName);

        // Check if file exists before proceeding
        if (!file_exists($filePath)) {
            return null;
        }

        //get the file extension
        $fileNameParts = explode('.', $fileName);
        $fileName = "";
        for ($i = 0; $i < (count($fileNameParts) - 1); $i++) {
            $fileName = $fileName . $fileNameParts[$i];
        }
        $fileExtension = isset($fileNameParts[count($fileNameParts) - 1]) ? $fileNameParts[count($fileNameParts) - 1] : 'jpg';

        $mediaId = $this->getMediaIdByFileName($fileName, $context);

        if ($mediaId) {
            return $mediaId;
        }

        //create media record from the image and return its ID
        return $this->createMediaFromFile($filePath, $fileName, $fileExtension, $context);
    }

    /**
     * Method, that returns an ID of a newly created media, based on a local file from the Shopware's media directory
     *
     * @param string $fileName
     * @param Context $context
     * @return string|null
     */
    public function getMediaIdByFileName(string $fileName, Context $context)
    {
        //get the media ID by the file name
        $mediaId = $this->mediaRepository->searchIds(
            (new Criteria())->addFilter(new EqualsFilter('fileName', $fileName)),
            $context
        )->firstId();

        return $mediaId;
    }


    /**
     * Method, that creates a new media record from a local file and returns its ID
     *
     * @param string $filePath
     * @param string $fileName
     * @param string $fileExtension
     * @param Context $context
     * @return string|null
     */
    private function createMediaFromFile(string $filePath, string $fileName, string $fileExtension, Context $context)
    {
        $mediaId = null;

        //get additional info on the file
        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath);

        //create and save new media file to the Shopware's media library
        try {
            $mediaFile = new MediaFile($filePath, $mimeType, $fileExtension, $fileSize);
            $mediaId = $this->mediaService->createMediaInFolder(self::MEDIA_FOLDER, $context, false);
            $this->fileSaver->persistFileToMedia(
                $mediaFile,
                $fileName,
                $mediaId,
                $context
            );
        } catch (MediaException $e) {
            if ($e->getErrorCode() === MediaException::MEDIA_DUPLICATED_FILE_NAME) {
                echo $e->getMessage();
            } else {
                echo $e->getMessage();
            }
        } catch (\Exception $e) {
            echo ($e->getMessage());
        }

        return $mediaId;
    }

    /**
     * Method, that takes care of deleting the newly created media record, if something goes wrong with saving data to it
     *
     * @param string $mediaId
     * @param Context $context
     * @return null
     */
    private function mediaCleanup(string $mediaId, Context $context)
    {
        $this->mediaRepository->delete([['id' => $mediaId]], $context);
        return null;
    }

    private function rglob($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge(
                [],
                ...[$files, $this->rglob($dir . "/" . basename($pattern), $flags)]
            );
        }

        return $files;
    }
}
