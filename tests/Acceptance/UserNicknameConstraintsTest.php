<?php

namespace Tests\Acceptance;

use App\Models\User\User;
use App\Repositories\UserRepository;
use Tests\FrameworkTest;

class UserNicknameConstraintsTest extends FrameworkTest
{
    /** @var UserRepository */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
    }

    public function testUpdateUserNicknameMaxLengthConstraint()
    {
        /** @var User $user */
        $user   = $this->userFactory->create();
        $data = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'nickname' => "123456789012345678901234567890nicknametoolong", // nickname too long
        ];
        $result = $this->put("/api/users/$user->id", $data);
        $this->assertNotEquals($result->status(), 200);
    }

    public function testUpdateUserNicknameUniqueConstraint()
    {
        /** @var User $user */
        $user   = $this->userFactory->create();
        
        /** @var User $secondUser */
        $secondUser   = $this->userFactory->create();

        $data = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'nickname' => "nonuniquenickname",
        ];
        $this->put("/api/users/$user->id", $data);

        $data = [
            'id'    => $secondUser->id,
            'name'  => $secondUser->name,
            'email' => $secondUser->email,
            'nickname' => "nonuniquenickname",
        ];
        $result = $this->put("/api/users/$secondUser->id", $data);

        $this->assertNotEquals($result->status(), 200);
    }

    public function testCreateUserNicknameMaxLengthConstraint()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique->safeEmail,
            'password' => "password",
            'nickname' => "123456789012345678901234567890nicknametoolong", // nickname too long
        ];

        $result = $this->post("/api/users", $data);

        $this->assertNotEquals($result->status(), 200);
    }

    public function testCreateUserNicknameUniqueConstraint()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique->safeEmail,
            'password' => "password",
            'nickname' => "nonuniquenickname2", // nickname not unique
        ];

        $this->post("/api/users", $data);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique->safeEmail,
            'password' => "password",
            'nickname' => "nonuniquenickname2", // nickname not unique
        ];

        $result = $this->post("/api/users", $data);

        $this->assertNotEquals($result->status(), 200);
    }
}
