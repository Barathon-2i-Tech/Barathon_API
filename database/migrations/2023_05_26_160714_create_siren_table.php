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
        Schema::create('siren', function (Blueprint $table) {
            $table->string('siren', 9);
            $table->string('statutDiffusionUniteLegale', 1);
            $table->boolean('unitePurgeeUniteLegale')->nullable();
            $table->date('dateCreationUniteLegale')->nullable();
            $table->string('sigleUniteLegale', 20)->nullable();
            $table->string('sexeUniteLegale', 4)->nullable();
            $table->string('prenom1UniteLegale', 20)->nullable();
            $table->string('prenom2UniteLegale', 20)->nullable();
            $table->string('prenom3UniteLegale', 20)->nullable();
            $table->string('prenom4UniteLegale', 20)->nullable();
            $table->string('prenomUsuelUniteLegale', 20)->nullable();
            $table->string('pseudonymeUniteLegale', 100)->nullable();
            $table->string('identifiantAssociationUniteLegale', 10)->nullable();
            $table->string('trancheEffectifsUniteLegale', 2)->nullable();
            $table->string('anneeEffectifsUniteLegale', 4)->nullable();
            $table->timestampTz('dateDernierTraitementUniteLegale')->nullable();
            $table->integer('nombrePeriodesUniteLegale')->nullable();
            $table->string('categorieEntreprise', 3)->nullable();
            $table->string('anneeCategorieEntreprise', 4)->nullable();
            $table->date('dateDebut')->nullable();
            $table->string('etatAdministratifUniteLegale', 1)->nullable();
            $table->string('nomUniteLegale', 100)->nullable();
            $table->string('nomUsageUniteLegale', 100)->nullable();
            $table->string('denominationUniteLegale', 120)->nullable();
            $table->string('denominationUsuelle1UniteLegale', 70)->nullable();
            $table->string('denominationUsuelle2UniteLegale', 70)->nullable();
            $table->string('denominationUsuelle3UniteLegale', 70)->nullable();
            $table->string('categorieJuridiqueUniteLegale', 4)->nullable();
            $table->string('activitePrincipaleUniteLegale', 6)->nullable();
            $table->string('nomenclatureActivitePrincipaleUniteLegale', 8)->nullable();
            $table->string('nicSiegeUniteLegale', 5)->nullable();
            $table->string('economieSocialeSolidaireUniteLegale', 1)->nullable();
            $table->string('societeMissionUniteLegale', 1)->nullable();
            $table->string('caractereEmployeurUniteLegale', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('siren');
    }
};
