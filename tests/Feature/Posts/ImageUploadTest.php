<?php

namespace AppTests\Feature\Posts;

use AppTests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ImageUploadTest extends TestCase
{
    use DatabaseTransactions;

    /** @test * */
    public function an_image_can_be_uploaded()
    {
        $this->withoutExceptionHandling();

//        given we have a storage and an image
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpg');


//        when we request the /posts point the image will be saved
        $this->call('POST', '/posts', [], [], ['image' => $image]);
//        then an image is in the storage
        $this->assertFileExists('test.jpg');
    }
}
