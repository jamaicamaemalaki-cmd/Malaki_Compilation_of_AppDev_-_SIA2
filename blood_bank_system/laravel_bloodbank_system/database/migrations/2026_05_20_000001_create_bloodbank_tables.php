<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'donor', 'facility'])->default('donor')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('address')->nullable()->after('phone');
        });

        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('blood_type', 5);
            $table->unsignedTinyInteger('age');
            $table->string('gender', 20);
            $table->decimal('weight', 5, 2)->nullable();
            $table->boolean('declaration_confirmed')->default(false);
            $table->timestamp('declaration_confirmed_at')->nullable();
            $table->text('medical_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('medical_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('facility_category', ['Hospital', 'Rural Health Unit', 'Red Cross']);
            $table->string('facility_name');
            $table->string('license_number')->nullable();
            $table->string('contact_person')->nullable();
            $table->timestamps();
        });

        Schema::create('blood_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_facility_id')->nullable()->constrained()->nullOnDelete();
            $table->string('facility_name')->nullable();
            $table->string('blood_type', 5);
            $table->string('component')->default('Whole Blood');
            $table->unsignedInteger('units_available')->default(0);
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('donation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->cascadeOnDelete();
            $table->enum('facility_category', ['Hospital', 'Rural Health Unit', 'Red Cross']);
            $table->string('facility_name');
            $table->string('blood_type', 5);
            $table->string('component')->default('Whole Blood');
            $table->unsignedInteger('units')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->date('scheduled_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('donor_note')->nullable();
            $table->text('facility_note')->nullable();
            $table->timestamps();
        });

        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->enum('requester_role', ['facility']);
            $table->enum('facility_category', ['Hospital', 'Rural Health Unit', 'Red Cross'])->nullable();
            $table->string('facility_name')->nullable();
            $table->string('blood_type', 5);
            $table->string('component')->default('Whole Blood');
            $table->unsignedInteger('units')->default(1);
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->enum('status', ['pending', 'approved', 'rejected', 'released'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->cascadeOnDelete();
            $table->string('blood_type', 5);
            $table->string('component')->default('Whole Blood');
            $table->unsignedInteger('units')->default(1);
            $table->date('donation_date');
            $table->string('facility_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
        Schema::dropIfExists('blood_requests');
        Schema::dropIfExists('donation_requests');
        Schema::dropIfExists('blood_inventories');
        Schema::dropIfExists('medical_facilities');
        Schema::dropIfExists('donors');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address']);
        });
    }
};
