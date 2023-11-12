<?php

declare(strict_types=1);

use App\Enums\ApplicationLanguage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->comment('Registered users (realtors) within the system.');
            $table->id();
            $table->string('avatar')
                ->nullable()
                ->comment('Path to User avatar');
            $table->string('first_name')
                ->nullable()
                ->comment('User first name');
            $table->string('last_name')
                ->nullable()
                ->comment('User last name');
            $table->string('email')
                ->unique()
                ->comment('User email');
            $table->string('system_name')
                ->unique()
                ->comment('System name. Used for login, passkeys etc.');
            $table->timestamp('email_verified_at')
                ->nullable()
                ->comment('User email verification timestamp');
            $table->string('password')
                ->comment('Hashed user password');
            $table->boolean('password_not_set')
                ->default(false)
                ->comment('Password can be randomly generated in case user registered with social apps.');
            $table->rememberToken()
                ->comment('User remember token');
            $table->string('application_language')
                ->default(ApplicationLanguage::ENGLISH)
                ->comment('User application language');
            $table->string('github_id')
                ->nullable()
                ->comment('Connected GitHub ID');
            $table->string('github_nickname')
                ->nullable()
                ->comment('GitHub nickname');
            $table->string('google_id')
                ->nullable()
                ->comment('Connected google ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
