<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->char('country', 2);
            $table->string('city');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->smallInteger('esg_score')->default(0);  // 0–100
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->jsonb('noga_codes')->default('[]');       // ["C17", "C17.21"]
            $table->jsonb('certifications')->default('[]');   // ["ISO 14001", "ISO 9001"]
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['country', 'esg_score']);
            $table->index('risk_level');
        });

        Schema::create('rfq_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_nr')->unique();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->jsonb('supplier_ids')->default('[]');
            $table->jsonb('noga_codes')->default('[]');
            $table->jsonb('scoring_weights')->default('{}');
            $table->string('location')->nullable();
            $table->integer('search_radius_km')->nullable();
            $table->unsignedBigInteger('annual_volume_chf')->nullable();
            $table->text('description')->nullable();
            $table->longText('rfq_text');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rfq_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rfq_id')->constrained('rfq_documents')->cascadeOnDelete();
            $table->foreignUuid('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_recipients');
        Schema::dropIfExists('rfq_documents');
        Schema::dropIfExists('suppliers');
    }
};
