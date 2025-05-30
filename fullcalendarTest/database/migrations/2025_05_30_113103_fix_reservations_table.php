<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        //// Supprimer la colonne en double si elle existe
    if (Schema::hasColumn('reservations', 'activity_type')) {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('activity_type');
        });
    }

    // Ajouter la colonne avec le bon type
    Schema::table('reservations', function (Blueprint $table) {
        $table->enum('activity_type', ['stay', 'conference', 'meeting'])
              ->default('stay')
              ->after('end_date');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('activity_type');
        });
    }
};
