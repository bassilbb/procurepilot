<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('open'); // open|restricted
            $table->string('method')->default('open_competitive'); // open_competitive|restricted|direct
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('currency')->default('NGN');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closing_at')->nullable();
            $table->timestamp('opening_at')->nullable();
            $table->string('evaluation_method')->default('weighted_score'); // lowest_price|weighted_score|quality_cost
            $table->string('status')->default('draft'); // draft|published|closed|under_evaluation|awarded|cancelled
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('award_notice')->nullable();
            $table->timestamps();
        });

        Schema::create('tender_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 15, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('estimated_unit_price', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('weight')->default(0); // percentage
            $table->integer('max_score')->default(100);
            $table->timestamps();
        });

        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency')->default('NGN');
            $table->text('compliance_declaration')->nullable();
            $table->string('status')->default('submitted'); // submitted|evaluated|awarded|rejected|withdrawn
            $table->decimal('technical_score', 10, 2)->nullable();
            $table->decimal('financial_score', 10, 2)->nullable();
            $table->decimal('total_score', 10, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bid_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bid_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 15, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('bid_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bid_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criterion_id')->constrained('evaluation_criteria')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 10, 2)->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('tender_suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bid_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('award_amount', 15, 2)->default(0);
            $table->string('currency')->default('NGN');
            $table->text('justification')->nullable();
            $table->string('status')->default('recommended'); // recommended|approved|declined
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
        Schema::dropIfExists('tender_suppliers');
        Schema::dropIfExists('bid_scores');
        Schema::dropIfExists('bid_items');
        Schema::dropIfExists('bids');
        Schema::dropIfExists('evaluation_criteria');
        Schema::dropIfExists('tender_items');
        Schema::dropIfExists('tenders');
    }
};
