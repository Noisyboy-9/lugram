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
    public function an_image_can_be_uploaded_and_it_will_be_saved_to_public_disk_inside_images_folder()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpg');


        $this->call('POST', '/posts', [], [], ['image' => $image]);

        $this->assertCount(1, Storage::disk('public')->allDirectories());
        $this->assertCount(1, Storage::disk('public')->allFiles('images'));
    }
}
