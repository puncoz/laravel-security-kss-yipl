<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * @var User
     */
    protected $userModel;

    public function __construct()
    {
        $this->userModel = app(User::class);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var User $user */
        $user = factory(User::class)->states('admin')->make();

        if ( $this->checkIfAlreadySeeded($user->email) ) {
            return;
        }

        $user->save();
    }

    protected function checkIfAlreadySeeded(string $email): bool
    {
        return $this->userModel->where('email', $email)->exists();
    }
}
