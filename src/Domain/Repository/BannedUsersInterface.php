<?php

declare(strict_types=1);

namespace Building\Domain\Repository;

interface BannedUsersInterface
{
    public function isBanned(string $username) : bool;
}
