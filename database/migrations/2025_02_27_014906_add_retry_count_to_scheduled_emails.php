<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('scheduled_emails', function (Blueprint $table) {
            $table->text('error_message')->nullable()->after('status');
            $table->integer('retry_count')->default(0)->after('error_message');
        });
    }

    public function down()
    {
        Schema::table('scheduled_emails', function (Blueprint $table) {
            $table->dropColumn(['error_message', 'retry_count']);
        });
    }
};
