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
        Schema::connection('pgsql_db_sirene')->create('sirene', function (Blueprint $table) {
            $table->string('siren', 9);
            $table->string('nic', 5);
            $table->string('siret', 14);
            $table->string('statutdiffusionetablissement', 1);
            $table->date('datecreationetablissement')->nullable();
            $table->string('trancheeffectifsetablissement', 2)->nullable();
            $table->string('anneeeffectifsetablissement', 4)->nullable();
            $table->string('activiteprincipaleregistremetiersetablissement', 6)->nullable();
            $table->timestampTz('datederniertraitementetablissement')->nullable();
            $table->boolean('etablissementsiege')->nullable();
            $table->integer('nombreperiodesetablissement')->nullable();
            $table->string('complementadresseetablissement', 38)->nullable();
            $table->string('numerovoieetablissement', 4)->nullable();
            $table->string('indicerepetitionetablissement', 4)->nullable();
            $table->string('typevoieetablissement', 4)->nullable();
            $table->string('libellevoieetablissement', 100)->nullable();
            $table->string('codepostaletablissement', 5)->nullable();
            $table->string('libellecommuneetablissement', 100)->nullable();
            $table->string('libellecommuneetrangeretablissement', 100)->nullable();
            $table->string('distributionspecialeetablissement', 26)->nullable();
            $table->string('codecommuneetablissement', 5)->nullable();
            $table->string('codecedexetablissement', 9)->nullable();
            $table->string('libellecedexetablissement', 100)->nullable();
            $table->string('codepaysetrangeretablissement', 5)->nullable();
            $table->string('libellepaysetrangeretablissement', 100)->nullable();
            $table->string('complementadresse2etablissement', 38)->nullable();
            $table->string('numerovoie2etablissement', 4)->nullable();
            $table->string('indicerepetition2etablissement', 4)->nullable();
            $table->string('typevoie2etablissement', 4)->nullable();
            $table->string('libellevoie2etablissement', 100)->nullable();
            $table->string('codepostal2etablissement', 5)->nullable();
            $table->string('libellecommune2etablissement', 100)->nullable();
            $table->string('libellecommuneetranger2etablissement', 100)->nullable();
            $table->string('distributionspeciale2etablissement', 26)->nullable();
            $table->string('codecommune2etablissement', 5)->nullable();
            $table->string('codecedex2etablissement', 9)->nullable();
            $table->string('libellecedex2etablissement', 100)->nullable();
            $table->string('codepaysetranger2etablissement', 5)->nullable();
            $table->string('libellepaysetranger2etablissement', 100)->nullable();
            $table->date('datedebut')->nullable();
            $table->string('etatadministratifetablissement', 1);
            $table->string('enseigne1etablissement', 50)->nullable();
            $table->string('enseigne2etablissement', 50)->nullable();
            $table->string('enseigne3etablissement', 50)->nullable();
            $table->string('denominationusuelleetablissement', 100)->nullable();
            $table->string('activiteprincipaleetablissement', 6)->nullable();
            $table->string('nomenclatureactiviteprincipaleetablissement', 8)->nullable();
            $table->string('caractereemployeuretablissement', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql_db_sirene')->dropIfExists('sirene');
    }
};
