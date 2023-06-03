<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siret', function (Blueprint $table) {
            $table->string('siren', 9);
            $table->string('nic', 5);
            $table->string('siret', 14);
            $table->string('statutDiffusionEtablissement', 1);
            $table->date('dateCreationEtablissement')->nullable();
            $table->string('trancheEffectifsEtablissement', 2)->nullable();
            $table->string('anneeEffectifsEtablissement', 4)->nullable();
            $table->string('activitePrincipaleRegistreMetiersEtablissement', 6)->nullable();
            $table->timestampTz('dateDernierTraitementEtablissement')->nullable();
            $table->boolean('etablissementSiege')->nullable();
            $table->integer('nombrePeriodesEtablissement')->nullable();
            $table->string('complementAdresseEtablissement', 38)->nullable();
            $table->string('numeroVoieEtablissement', 4)->nullable();
            $table->string('indiceRepetitionEtablissement', 4)->nullable();
            $table->string('typeVoieEtablissement', 4)->nullable();
            $table->string('libelleVoieEtablissement', 100)->nullable();
            $table->string('codePostalEtablissement', 5)->nullable();
            $table->string('libelleCommuneEtablissement', 100)->nullable();
            $table->string('libelleCommuneEtrangerEtablissement', 100)->nullable();
            $table->string('distributionSpecialeEtablissement', 26)->nullable();
            $table->string('codeCommuneEtablissement', 5)->nullable();
            $table->string('codeCedexEtablissement', 9)->nullable();
            $table->string('libelleCedexEtablissement', 100)->nullable();
            $table->string('codePaysEtrangerEtablissement', 5)->nullable();
            $table->string('libellePaysEtrangerEtablissement', 100)->nullable();
            $table->string('complementAdresse2Etablissement', 38)->nullable();
            $table->string('numeroVoie2Etablissement', 4)->nullable();
            $table->string('indiceRepetition2Etablissement', 4)->nullable();
            $table->string('typeVoie2Etablissement', 4)->nullable();
            $table->string('libelleVoie2Etablissement', 100)->nullable();
            $table->string('codePostal2Etablissement', 5)->nullable();
            $table->string('libelleCommune2Etablissement', 100)->nullable();
            $table->string('libelleCommuneEtranger2Etablissement', 100)->nullable();
            $table->string('distributionSpeciale2Etablissement', 26)->nullable();
            $table->string('codeCommune2Etablissement', 5)->nullable();
            $table->string('codeCedex2Etablissement', 9)->nullable();
            $table->string('libelleCedex2Etablissement', 100)->nullable();
            $table->string('codePaysEtranger2Etablissement', 5)->nullable();
            $table->string('libellePaysEtranger2Etablissement', 100)->nullable();
            $table->date('dateDebut')->nullable();
            $table->string('etatAdministratifEtablissement', 1);
            $table->string('enseigne1Etablissement', 50)->nullable();
            $table->string('enseigne2Etablissement', 50)->nullable();
            $table->string('enseigne3Etablissement', 50)->nullable();
            $table->string('denominationUsuelleEtablissement', 100)->nullable();
            $table->string('activitePrincipaleEtablissement', 6)->nullable();
            $table->string('nomenclatureactiviteprincipaleetablissement', 8)->nullable();
            $table->string('caractereEmployeurEtablissement', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siret');
    }
};
