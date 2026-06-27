<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->unsignedInteger('response_minutes');    // first-response SLA target
            $table->unsignedInteger('resolution_minutes'); // resolution SLA target
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['organization_id', 'priority']); // one policy per priority per org
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};
