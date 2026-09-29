<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('bitrix_user_id')->nullable()->unique();
            $table->string('last_name')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('position')->nullable();
            $table->string('role')->default('employee');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bitrix_user_id',
                'last_name',
                'department_id',
                'position',
                'role'
            ]);
        });
    }
};