<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stored_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('aggregate_uuid')->nullable()->index();
            $table->unsignedBigInteger('aggregate_version')->nullable();
            $table->integer('event_version')->default(1);
            $table->string('event_class');
            $table->jsonb('event_properties');
            $table->jsonb('meta_data');
            $table->timestamp('created_at')->index();

            $table->unique(['aggregate_uuid', 'aggregate_version']);
            $table->index('event_class');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stored_events');
    }
};
