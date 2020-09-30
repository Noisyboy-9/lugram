<?php

namespace AppTests\Feature\Posts;

use AppTests\TestCase;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ImageUploadTest extends TestCase
{
    use DatabaseTransactions;

    /** @test * */
    public function an_image_can_be_uploaded_and_a_new_post_been_created_with_it()
    {
        // given we have a storage for saving images and we have a image

        // when

        //then
    }
}
