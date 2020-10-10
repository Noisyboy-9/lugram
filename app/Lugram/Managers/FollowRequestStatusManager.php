<?php


namespace App\Lugram\Managers;


class FollowRequestStatusManager
{
    public const ACCEPTED = 1;
    public const AWAITING_FOR_RESPONSE = 2;
    public const DECLINED = 3;
    public const CANCELED = 4;
}
