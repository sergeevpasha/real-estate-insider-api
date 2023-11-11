<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('passkeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->index()
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name')
                ->comment('Name of the passkey');
            $table->text('credential_id')
                ->comment('Credential Id to determine the passkey');
            $table->text('public_key')
                ->comment('Public key to compare against user private key');
            $table->timestamp('last_used_at')
                ->nullable()
                ->comment('Last time the passkey was used');
            $table->string('last_used_ip')
                ->nullable()
                ->comment('Last IP the passkey was used from');
            $table->text('last_used_user_agent')
                ->nullable()
                ->comment('Last user agent the passkey was used from');
            $table->string('last_used_location')
                ->nullable()
                ->comment('Last location the passkey was used from');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passkeys');
    }
};
