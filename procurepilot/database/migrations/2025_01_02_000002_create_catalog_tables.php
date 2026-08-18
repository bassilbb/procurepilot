<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->default('goods'); // goods|services|works
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('reg_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->default('Nigeria');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tax_id')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('certifications')->nullable();
            $table->integer('rating')->default(0);
            $table->string('status')->default('pending'); // pending|approved|suspended|blacklisted
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('supplier_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('path');
            $table->string('mime')->nullable();
            $table->integer('size')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('fiscal_year');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft|submitted|approved|rejected
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_plan_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->string('method')->default('open_competitive'); // open_competitive|restricted|single_source
            $table->string('priority')->default('normal'); // low|normal|high|critical
            $table->date('expected_date')->nullable();
            $table->string('status')->default('planned');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('procurement_plan_items');
        Schema::dropIfExists('procurement_plans');
        Schema::dropIfExists('supplier_documents');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
    }
};
