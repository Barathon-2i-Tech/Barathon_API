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
            $table->date('datecreationetablissement');
            $table->string('trancheeffectifsetablissement', 2);
            $table->string('anneeeffectifsetablissement', 4);
            $table->string('activiteprincipaleregistremetiersetablissement', 6);
            $table->timestampTz('datederniertraitementetablissement');
            $table->boolean('etablissementsiege');
            $table->integer('nombreperiodesetablissement');
            $table->string('complementadresseetablissement', 38);
            $table->string('numerovoieetablissement', 4);
            $table->string('indicerepetitionetablissement', 4);
            $table->string('typevoieetablissement', 4);
            $table->string('libellevoieetablissement', 100);
            $table->string('codepostaletablissement', 5);
            $table->string('libellecommuneetablissement', 100);
            $table->string('libellecommuneetrangeretablissement', 100);
            $table->string('distributionspecialeetablissement', 26);
            $table->string('codecommuneetablissement', 5);
            $table->string('codecedexetablissement', 9);
            $table->string('libellecedexetablissement', 100);
            $table->string('codepaysetrangeretablissement', 5);
            $table->string('libellepaysetrangeretablissement', 100);
            $table->string('complementadresse2etablissement', 38);
            $table->string('numerovoie2etablissement', 4);
            $table->string('indicerepetition2etablissement', 4);
            $table->string('typevoie2etablissement', 4);
            $table->string('libellevoie2etablissement', 100);
            $table->string('codepostal2etablissement', 5);
            $table->string('libellecommune2etablissement', 100);
            $table->string('libellecommuneetranger2etablissement', 100);
            $table->string('distributionspeciale2etablissement', 26);
            $table->string('codecommune2etablissement', 5);
            $table->string('codecedex2etablissement', 9);
            $table->string('libellecedex2etablissement', 100);
            $table->string('codepaysetranger2etablissement', 5);
            $table->string('libellepaysetranger2etablissement', 100);
            $table->date('datedebut');
            $table->string('etatadministratifetablissement', 1);
            $table->string('enseigne1etablissement', 50);
            $table->string('enseigne2etablissement', 50);
            $table->string('enseigne3etablissement', 50);
            $table->string('denominationusuelleetablissement', 100);
            $table->string('activiteprincipaleetablissement', 6);
            $table->string('nomenclatureactiviteprincipaleetablissement', 8);
            $table->string('caractereemployeuretablissement', 1);
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
