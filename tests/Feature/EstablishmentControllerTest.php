<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Controllers\EstablishmentController;
use App\Models\Establishment;

class EstablishmentControllerTest extends TestCase
{
    use RefreshDatabase;


    public function test_get_establishments_by_owner_id()
    {
        $owner = $this->createOwnerUser();
        
        $structure = [
            "status",
            "message",
            "data" => [
                "*" => [
                "establishment_id",
                "trade_name",
                "siret",
                "address_id",
                "logo",
                "phone",
                "email",
                "website",
                "opening",
                "owner_id",
                "status_id",
                "deleted_at",
                "created_at",
                "updated_at",
                "address",
                "postal_code",
                "city",
                "comment",
                ]
            ]
        ];
    
        $response = $this->actingAs($owner)->get(route('establishment.list', ['owner_id' => $owner->owner_id]));

    $response->assertStatus(200);
    $response->assertJsonCount(2, 'data'); // Remplacez 3 par le nombre d'établissements créés par votre seeder
    $response->assertJsonStructure($structure);
    }



    public function test_store_establishment()
    {
        $owner = $this->createOwnerUser();
        
        $requestData = [
            // Remplacez les valeurs par les données appropriées pour le test
            'trade_name' => 'Nom de l\'établissement',
            'siret' => '12345678901234',
            'logo' => null, // Chemin d'accès du logo ou null si aucun logo
            'phone' => '0123456789',
            'email' => 'exemple@example.com',
            'website' => 'http://www.example.com',
            'opening' => json_encode([
                'Lundi' => 'fermer',
                'Mardi' => '17h00 - 01h00',
                'Mercredi' => '17h00 - 01h00',
                'Jeudi' => '17h00 - 01h00',
                'Vendredi' => '17h00 - 01h00',
                'Samedi' => '17h00 - 01h00',
                'Dimanche' => '17h00 - 01h00',
            ]),
            'address' => 'Adresse de l\'établissement',
            'postal_code' => '12345',
            'city' => 'Ville de l\'établissement',
        ];
        
        $response = $this->actingAs($owner)->post(route('establishment.store', ['owner_id' => $owner->owner_id]), $requestData);
        
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                [
                    'trade_name',
                    'siret',
                    'logo',
                    'phone',
                    'email',
                    'website',
                    'opening',
                    'owner_id',
                    'address_id',
                    'status_id',
                ]
            ]
        ]);
    }

    public function test_update_establishment()
    {
        $owner = $this->createOwnerUser();
        $establishment = Establishment::factory()->create([
            'owner_id' => $owner->owner_id,
        ]);
    
        $requestData = [
            // Remplacez les valeurs par les données appropriées pour le test
            'trade_name' => 'Nouveau nom de l\'établissement',
            'siret' => '12345678901234',
            'logo' => null, // Chemin d'accès du logo ou null si aucun logo
            'phone' => '0123456789',
            'email' => 'exemple@example.com',
            'website' => 'http://www.example.com',
            'opening' => json_encode([
                'Lundi' => 'fermer',
                'Mardi' => '17h00 - 01h00',
                'Mercredi' => '17h00 - 01h00',
                'Jeudi' => '17h00 - 01h00',
                'Vendredi' => '17h00 - 01h00',
                'Samedi' => '17h00 - 01h00',
                'Dimanche' => '17h00 - 01h00',
            ]),
            'address' => 'Nouvelle adresse de l\'établissement',
            'postal_code' => '12345',
            'city' => 'Nouvelle ville de l\'établissement',
        ];
    
        $response = $this->actingAs($owner)->put(route('establishment.update', ['owner_id' => $owner->owner_id, 'establishment_id' => $establishment->establishment_id]), $requestData);
    
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                [
                    'establishment_id',
                    'trade_name',
                    'siret',
                    'address_id',
                    'logo',
                    'phone',
                    'email',
                    'website',
                    'opening',
                    'owner_id',
                    'status_id',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                ],
                [
                    'address_id',
                    'address',
                    'postal_code',
                    'city',
                ],
            ],
        ]);
    }

    public function test_delete_establishment()
    {
        $owner = $this->createOwnerUser();
        $establishment = Establishment::factory()->create([
            'owner_id' => $owner->owner_id,
        ]);
    
        $response = $this->actingAs($owner)->delete(route('establishment.delete', ['owner_id' => $owner->owner_id, 'establishment_id' => $establishment->establishment_id]));
    
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }
    


}

