<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('supplier_documents', function (Blueprint $table) {
            $table->foreignId('requirement_id')->nullable()->after('supplier_id')
                ->constrained('supplier_document_requirements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_documents', function (Blueprint $table) {
            $table->dropForeign(['requirement_id']);
            $table->dropColumn('requirement_id');
        });
        Schema::dropIfExists('supplier_document_requirements');
    }
};
