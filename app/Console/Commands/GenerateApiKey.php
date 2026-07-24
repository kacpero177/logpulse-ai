<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateApiKey extends Command
{
    protected $signature = 'logpulse:generate-key {email}';
    protected $description = 'Generuje nowy API Key dla podanego użytkownika';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Developer', 'password' => bcrypt('secret123')]
        );

        $token = $user->createToken('api-key')->plainTextToken;

        $this->info("Klucz API wygenerowany pomyślnie dla: {$email}");
        $this->warn("Twój API Key: {$token}");
        $this->comment("Zapisz go, nie zostanie pokazany ponownie!");
    }
}