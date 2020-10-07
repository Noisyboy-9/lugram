<?php

namespace AppTests\Feature\Posts;

use App\Lugram\traits\tests\user\HasUserInteractions;
use AppTests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseMigrations;


class ImageUploadTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /**
     * hit image upload end point with the provided image
     *
     * @param \Illuminate\Http\Testing\File $image
     */
    private function uploadImage($image = null)
    {
        return $this->call('POST', '/posts', [], [], ['image' => $image]);
    }

    /** @test * */
    public function a_guest_can_not_make_a_new_post()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpg');

        $response = $this->uploadImage($image);

        $this->assertEquals(401, $response->getStatusCode());

        $this->assertEquals('Unauthorized.', $response->content());
    }

    /** @test * */
    public function an_authenticated_user_can_uploaded_a_image_and_it_will_be_saved_images_folder_and_its_path_will_be_saved()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpg');
        $this->uploadImage($image);

        $this->assertCount(1, Storage::disk('public')->allDirectories());
        $this->assertCount(1, Storage::disk('public')->allFiles('images'));

        $imageSavePath = Storage::disk('public')->getAdapter()->getPathPrefix() . "images" . "\\" . $image->hashName();
        $imageRealSavePath = realpath($imageSavePath);

        $this->seeInDatabase('posts', [
            'image_path' => $imageRealSavePath,
        ]);
    }

    /** @test * */
    public function an_image_is_required_to_upload_image()
    {
        $response = $this->call('POST', '/posts', [], ['image' => null], []); // bad request call

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertCount(0, Storage::disk('public')->allFiles('images'));
    }

    /** @test * */
    public function an_image_must_have_the_proper_type_to_be_saved()
    {
        Storage::fake('public');
        $badImage = UploadedFile::fake()->create('test . exe');
        $response = $this->uploadImage($badImage);
        $this->assertEquals(422, $response->getStatusCode());
    }
}
