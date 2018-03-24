<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableResponseLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('response_logs', function (Blueprint $table) {
            $table->longText('parameters')->change();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('response_logs', function (Blueprint $table) {
            $table->string('parameters', 50)->change();
            $table->dropColumn(['created_at']);
        });
    }
}
