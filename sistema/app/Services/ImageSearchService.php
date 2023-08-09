<?php

namespace App\Services;

use App\Models\ImageSearch;

class ImageSearchService
{
    public function process($request): ?ImageSearch
    {
        $path = uploadImage($request);
        return $this->createOrGet($path);
    }

    public function createOrGet(string $image): ?ImageSearch
    {
        $fullDir = storage_path('app/' . $image);
        $imageId = md5(file_get_contents($fullDir));
        $row = ImageSearch::whereChecksum($imageId)->first();
        if (!isset($row['id'])) {
            $service = new \App\Services\GoogleCloudVisionService();
            $text = $service->getImageText($fullDir);
            if (is_null($text)) {
                throw new \Exception(__("Invalid imagem."));
            }
            $fileData = pathinfo($fullDir);
            $imageService = new ImageSearch();
            $imageService->image = $fileData['basename'];
            $imageService->extracted_text = $text;
            $imageService->checksum = $imageId;
            $imageService->saveOrFail();
            $row = $imageService;
        }
        return $row;

    }
}
