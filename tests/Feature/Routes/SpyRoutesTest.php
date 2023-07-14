<?php

namespace Tests\Feature\Routes;

use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;

use Tests\TestCase;

class SpyRoutesTest extends TestCase
{
    use RefreshDatabase;    

    public function testAddMissingMandatoryFieldsName()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        

        $response = $this->put('/spy',[
            'surname'=>'Namae',
            'birth_date'=>'1980-1-1',
            'death_date'=>'1985-1-1',
            'agency' => 'CIA',
            'country_of_operation'=>'GR'
        ]);

        $response->assertStatus(400);
    } 

    public function testAddMissingMandatoryFieldsSurname()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        

        $response = $this->put('/spy',[
            'name'=>'Namae',
            'birth_date'=>'1980-1-1',
            'death_date'=>'1985-1-1',
            'agency' => 'CIA',
            'country_of_operation'=>'GR'
        ]);

        $response->assertStatus(400);
    }

    public function testAddMissingMandatoryFieldsMissingBirthDate()
    {
        
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        

        $response = $this->put('/spy',[
            'name'=>'Namae',
            'surname'=>'Myoji',
            'death_date'=>'1985-1-1',
            'agency' => 'CIA',
            'country_of_operation'=>'GR'
        ]);

        $response->assertStatus(400);
    }

    public function testAddSpyWrongAgency()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->put('/spy',[
            'name'=>'Namae',
            'surname'=>'Myoji',
            'birth_date'=>'1980-1-1',
            'death_date'=>'1985-1-1',
            'agency' => 'IIIII',
            'country_of_operation'=>'GR'
        ]);

        $response->assertStatus(400);
    }

    public function testAddBareMinimumSucess()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        

        $response = $this->put('/spy',[
            'name'=>'Namae',
            'surname'=>'Myoji',
            'birth_date'=>'1980-1-1',
        ]);

        $response->assertStatus(201);
    }

    public function testAddAllItemSucess()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->put('/spy',[
            'name'=>'Namae',
            'surname'=>'Myoji',
            'birth_date'=>'1980-1-1',
            'death_date'=>'1985-1-1',
            'country_of_operation'=>'GR'
        ]);

        $response->assertStatus(201);
    }

    public function testError409UponDUplicateRecord()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $spy = \App\Models\Spy::factory()->create();

        $response = $this->put('/spy',[
            'name'=>$spy->name,
            'surname'=>$spy->surname,
            'birth_date'=>$spy->birth_date,
            'death_date'=>$spy->death_date,
            'country_of_operation'=>$spy->country_of_operation
        ]);

        $response->assertStatus(409);
    }


    public function testGetRandomSpiesInRandomOrder()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

       \App\Models\Spy::factory()->count(100)->create();

        $response1 = $this->get('/spy/random');
        $response1->assertStatus(200);

        $response2 = $this->get('/spy/random');
        $response2->assertStatus(200);

        $this->assertNotEquals($response1->getContent(),$response2->getContent());
    }
}
