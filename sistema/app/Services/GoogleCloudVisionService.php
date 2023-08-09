<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;

class GoogleCloudVisionService
{
    public function getImageText(string $image)
    {


        $client = new ImageAnnotatorClient([
            'credentials' => base_path(env("GOOGLE_JSON_DIR"))
        ]);
        $annotation = $client->annotateImage(
            fopen($image, 'r'),
            [Type::TEXT_DETECTION]
        );
        $texts = $annotation->getTextAnnotations();
        if (isset($texts[0])) {
            return $texts[0]->getDescription();
        }
        return "-";


    }
}
