<?php

namespace Tests\Feature\Routes;

use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Spy;
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

    public function testError409UponDuplicateRecord()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $spy = Spy::factory()->create([
            'name'=>'Namae',
            'surname'=>'Myoji',
            'birth_date'=>'1980-1-1',
            'death_date'=>'1985-1-1',
            'country_of_operation'=>'GR',
            'agency'=>'KGB'
        ]);

        $response = $this->put('/spy',[
            'name'=>$spy->name,
            'surname'=>$spy->surname,
            'birth_date'=>$spy->birth_date,
            'death_date'=>$spy->death_date,
            'country_of_operation'=>$spy->country_of_operation,
            'agency' => $spy->agency
        ]);

        $response->assertStatus(409);
    }


    public function testGetRandomSpiesInRandomOrder()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

       Spy::factory()->count(100)->create();

        $response1 = $this->get('/spies/random');
        $response1->assertStatus(200);

        $response2 = $this->get('/spies/random');
        $response2->assertStatus(200);

        $this->assertNotEquals($response1->getContent(),$response2->getContent());
    }

    public function testGetSpiesNoFilters()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

       Spy::factory()->count(100)->create();

       $response = $this->get('/spies?page=1&limit=10');


       $response->assertStatus(200);


       $expectedRecords = Spy::paginate(10,['*'],'',1)->items();
       $expectedRecords = json_decode(json_encode($expectedRecords),true);

       $responseContent = $response->getContent();
       $responseContentJsonDecoded = json_decode($responseContent,true);
       $this->assertEquals(1,(int)$responseContentJsonDecoded['current_page']);
       $this->assertEquals(10,(int)$responseContentJsonDecoded['per_page']);

       $this->assertCount(10,$responseContentJsonDecoded['data']);

       $response->assertJson([
        'current_page'=>1,
        'data'=> $expectedRecords
       ]);
    }

    public function testGetSpiesNoFiltersPage2()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

       Spy::factory()->count(100)->create();

       $response = $this->get('/spies?page=2&limit=10');


       $response->assertStatus(200);


       $expectedRecords = Spy::paginate(10,['*'],'',2)->items();
       $expectedRecords = json_decode(json_encode($expectedRecords),true);

       $responseContent = $response->getContent();
       $responseContentJsonDecoded = json_decode($responseContent,true);
       $this->assertEquals(2,(int)$responseContentJsonDecoded['current_page']);
       $this->assertEquals(10,(int)$responseContentJsonDecoded['per_page']);

       $this->assertCount(10,$responseContentJsonDecoded['data']);

       $response->assertJson([
        'current_page'=>2,
        'data'=> $expectedRecords
       ]);
    }

    public function testGetSpiesBothBothNameAndFullname()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

       Spy::factory()->count(100)->create();
       Spy::factory()->create(['name'=>'lalala']);


       $response = $this->get('/spies?page=1&limit=10&name=lalala&fullname=lalala');
       
       $response->assertStatus(400);
       
    }


    public function testGetSpiesBothBothSurNameAndFullname()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

       Spy::factory()->count(100)->create();
       Spy::factory()->create(['name'=>'lalala']);


       $response = $this->get('/spies?page=1&limit=10&surname=lalala&fullname=lalala');
       
       $response->assertStatus(400);
       
    }

    public function testGetSpiesNameAnrSurNameAndFullnameProvided()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

       Spy::factory()->count(100)->create();
       Spy::factory()->create(['name'=>'lalala']);


       $response = $this->get('/spies?page=1&limit=10&surname=lalala&name=lalala&fullname=lalala');
       
       $response->assertStatus(400);
    }

    public function testGetSpiesFilterName()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

       Spy::factory()->count(100)->create();
       Spy::factory()->create(['name'=>'lalala']);

       $response = $this->get('/spies?page=1&limit=10&name=lalala');


       $response->assertStatus(200);


       $expectedRecords = Spy::where('name','lalala')->paginate(10,['*'],'',1)->items();
       $expectedRecords = json_decode(json_encode($expectedRecords),true);

       $responseContent = $response->getContent();
       $responseContentJsonDecoded = json_decode($responseContent,true);
       $this->assertEquals(1,(int)$responseContentJsonDecoded['current_page']);
       $this->assertEquals(10,(int)$responseContentJsonDecoded['per_page']);

       $this->assertCount(1,$responseContentJsonDecoded['data']);

       $response->assertJson([
        'current_page'=>1,
        'data'=> $expectedRecords
       ]);
    }

}

