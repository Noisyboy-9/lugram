<?php

namespace AppTests\Feature\Follow;

use App\Lugram\Managers\FollowRequestStatusManager;
use App\Lugram\traits\tests\user\HasUserInteractions;
use App\Models\User;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class FollowRequestTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function a_authenticated_user_can_request_for_following_another_user()
    {
        $this->withoutExceptionHandling();

        $jhon = $this->login();
        $jane = $this->createUser();

        $this->post('/requests/' . $jane->id)
            ->shouldReturnJson()
            ->seeJson(['requested' => true])
            ->assertResponseStatus(201);

        $this->seeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::AWAITING_FOR_RESPONSE,
        ]);

        $this->assertCount(1, $jane->requests());
    }

    /** @test * */
    public function a_user_can_not_send_another_follow_request_to_the_same_user_if_it_has_a_past_request_in_awaiting_status()
    {
        $jhon = $this->login(User::factory()->make(['username' => 'jhon']));
        $jane = $this->createUser(['username' => 'jane']);

        $jhon->makeFollowRequest($jane); // it have a past request in awaiting for response mode

        $this->post('/requests/' . $jane->id)
            ->shouldReturnJson()
            ->seeJson(['message' => 'same request have already been sent to user.'])
            ->assertResponseStatus(406);
    }

    /** @test * */
    public function a_user_must_be_authenticated_to_request_following_another_user()
    {
        $jhon = $this->createUser();

        $this->post('/requests/' . $jhon->id)
            ->seeStatusCode(401);

        $this->notSeeInDatabase('follows', [
            'following_id' => $jhon->id,
        ]);

        $this->assertCount(0, $jhon->followings);
    }

    /** @test * */
    public function a_user_must_exist_in_order_for_another_user_to_follow()
    {
        $this->withoutExceptionHandling();
        $jhon = $this->login();

        $this->post('/requests/' . 12)
            ->seeJson(['message' => 'not found'])
            ->assertResponseStatus(404);// bad id : id does not relate to any known user

        $this->notSeeInDatabase('follows', [
            'follower_id' => $jhon->id,
        ]);
    }

    /** @test * */
    public function an_authenticated_user_can_accept_a_follow_request()
    {
        $jhon = $this->createUser();
        $jane = $this->login();

        $jhon->makeFollowRequest($jane);

        $this->put('/requests/' . $jhon->id . '/accept')
            ->shouldReturnJson()
            ->seeJson(['accepted' => true])
            ->assertResponseOk();

        $this->seeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::ACCEPTED,
        ]);

        $this->notSeeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::AWAITING_FOR_RESPONSE,
        ]);

        $this->assertTrue($jane->hasAcceptedRequestOf($jhon));
        $this->assertTrue($jhon->isFollowerOf($jane));
        $this->assertTrue($jane->isFollowingOf($jhon));
    }

    /** @test * */
    public function an_user_must_be_authenticated_to_accept_a_request()
    {
        $jhon = $this->createUser();
        $jane = $this->createUser();

        $jhon->makeFollowRequest($jane);

        $this->put('/requests/' . $jhon->id . '/accept')
            ->assertResponseStatus(401);


        $this->notSeeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::ACCEPTED,
        ]);

        $this->seeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::AWAITING_FOR_RESPONSE,
        ]);
    }

    /** @test * */
    public function a_request_must_first_exist_in_order_to_be_able_to_accept_it()
    {
        $jhon = $this->createUser();
        $jane = $this->login();

        $this->put('/requests/' . $jhon->id . '/accept')
            ->shouldReturnJson()
            ->seeJson(['message' => 'no request found from the user'])
            ->seeStatusCode(406);

    }

    /** @test * */
    public function a_request_must_be_in_awaiting_status_in_order_to_be_able_to_accept_it()
    {
        $jhon = $this->createUser();
        $jane = $this->login();

        $jhon->makeFollowRequest($jane);
        $jane->acceptRequest($jhon);

        $this->put('/requests/' . $jhon->id . '/accept')
            ->shouldReturnJson()
            ->seeJson(['message' => 'no request found from the user'])
            ->seeStatusCode(406);
    }

    /** @test * */
    public function request_of_a_valid_user_id_can_be_accepted()
    {
        $jane = $this->login();

        $this->put('/requests/' . 123 . '/accept')
            ->shouldReturnJson()
            ->seeJson(['message' => 'not found'])
            ->seeStatusCode(404);
    }

    /** @test * */
    public function an_authenticated_user_can_decline_a_follow_request()
    {
        $this->withoutExceptionHandling();
        $jhon = $this->createUser();
        $jane = $this->login();

        $jhon->makeFollowRequest($jane);

        $this->put('/requests/' . $jhon->id . '/decline')
            ->shouldReturnJson()
            ->seeJson(['declined' => true])
            ->assertResponseOk();

        $this->seeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::DECLINED,
        ]);

        $this->notSeeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::AWAITING_FOR_RESPONSE,
        ]);


        $this->assertFalse($jane->hasAcceptedRequestOf($jhon));
        $this->assertFalse($jhon->isFollowerOf($jane));
        $this->assertFalse($jane->isFollowingOf($jhon));
    }

    /** @test * */
    public function a_user_must_be_authenticated_to_decline_a_request()
    {
        $jhon = $this->createUser();
        $jane = $this->createUser();

        $jhon->makeFollowRequest($jane);
        $this->put('/requests/' . $jhon->id . '/decline')
            ->assertResponseStatus(401);

        $this->notSeeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::DECLINED,
        ]);

        $this->seeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::AWAITING_FOR_RESPONSE,
        ]);

        $this->assertTrue($jane->hasAwaitingRequestFrom($jhon));
    }

    /** @test * */
    public function a_request_must_first_exist_in_order_to_be_able_decline_it()
    {
        $jhon = $this->createUser();
        $jane = $this->login();

        $this->put('/requests/' . $jhon->id . '/decline')
            ->seeJson(['message' => 'no request found from the user'])
            ->assertResponseStatus(406);
    }

    /** @test * */
    public function a_request_must_be_in_awaiting_status_in_order_to_be_able_to_decline_it()
    {
        $jhon = $this->createUser();
        $jane = $this->login();

        $jhon->makeFollowRequest($jane);
        $jane->declineRequest($jhon);

        $this->put('/requests/' . $jhon->id . '/decline')
            ->shouldReturnJson()
            ->seeJson(['message' => 'no request found from the user'])
            ->seeStatusCode(406);
    }

    /** @test * */
    public function request_of_a_valid_user_id_can_be_declined()
    {
        $jane = $this->login();

        $this->put('/requests/' . 123 . '/decline')
            ->shouldReturnJson()
            ->seeJson(['message' => 'not found'])
            ->seeStatusCode(404);
    }
}
