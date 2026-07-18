<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('member_number')->unique();
            $table->string('nik', 16)->unique();
            $table->string('name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->text('address');
            $table->string('district');
            $table->string('regency');
            $table->string('province');
            $table->string('whatsapp', 20);
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->string('photo_path')->nullable();
            $table->date('joined_at');
            $table->date('valid_until');
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->uuid('qr_token')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('saving_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->enum('frequency', ['once', 'monthly', 'flexible']);
            $table->decimal('default_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('savings', function (Blueprint $table): void {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->foreignId('saving_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->date('transaction_date')->index();
            $table->enum('direction', ['deposit', 'withdrawal'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table): void {
            $table->id();
            $table->string('loan_number')->unique();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_rate', 8, 4)->default(0);
            $table->unsignedSmallInteger('term_months');
            $table->decimal('total_interest', 15, 2)->default(0);
            $table->decimal('total_payable', 15, 2)->default(0);
            $table->decimal('remaining_balance', 15, 2)->default(0);
            $table->enum('status', ['submitted', 'approved', 'rejected', 'disbursed', 'paid'])->default('submitted')->index();
            $table->date('applied_at');
            $table->date('approved_at')->nullable();
            $table->date('disbursed_at')->nullable();
            $table->text('purpose');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('installments', function (Blueprint $table): void {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('loan_id')->constrained()->restrictOnDelete();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->unsignedSmallInteger('installment_number');
            $table->date('paid_at')->index();
            $table->decimal('principal_paid', 15, 2);
            $table->decimal('interest_paid', 15, 2)->default(0);
            $table->decimal('penalty', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['loan_id', 'installment_number']);
        });

        Schema::create('card_print_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('printed_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('action', ['print', 'download']);
            $table->timestamp('printed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_print_histories');
        Schema::dropIfExists('installments');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('savings');
        Schema::dropIfExists('saving_types');
        Schema::dropIfExists('members');
    }
};
