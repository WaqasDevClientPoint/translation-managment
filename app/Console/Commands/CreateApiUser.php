<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateApiUser extends Command
{
    protected $signature = 'api:create-user {email?} {password?}';
    protected $description = 'Create a new API user';

    public function handle(): void
    {
        $email = $this->argument('email') ?? $this->ask('What is the user email?');
        $password = $this->argument('password') ?? $this->secret('What is the user password?');

        try {
            $user = User::create([
                'name' => explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make($password)
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            $this->info('User created successfully!');
            $this->info('API Token: ' . $token);
            
            // Save token to a file for easy access
            $tokenFile = storage_path('app/api-token.txt');
            file_put_contents($tokenFile, $token);
            $this->info('Token saved to: ' . $tokenFile);

        } catch (\Exception $e) {
            $this->error('Failed to create user: ' . $e->getMessage());
        }
    }
} 