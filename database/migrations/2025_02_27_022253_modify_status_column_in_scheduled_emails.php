<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement("ALTER TABLE scheduled_emails CHANGE COLUMN status status ENUM('pending', 'sent', 'failed', 'permanently_failed') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE scheduled_emails CHANGE COLUMN status status ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending'");
    }
};
