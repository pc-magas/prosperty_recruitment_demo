<?php

namespace Tests\Models;

use Illuminate\Support\Facades\DB;

use App\Models\Spy;


class SpyTest extends DatabaseTestCase
{
    public function testInsertNoDuplicate()
    {
        $spy = new Spy();
        $spy->name="Namae";
        $spy->surname="Myoji";
        $spy->birth_date='1980-12-1';
        $spy->agency='FSB';
        $spy->save();

        $spy2 = new Spy();
        $spy2->name="Namae";
        $spy2->surname="Myoji";
        $spy2->birth_date='1980-12-1';
        $spy2->agency='FSB';

        $this->expectException(\Exception::class);
        $spy2->save();

        $foundSpyRecords = Spy::all()->toArray();

        $this->assertCount(1,$foundSpyRecords);

        $foundSpy = $foundSpyRecords[0];

        $this->assertEquals('Namae',$foundSpy['name']);
        $this->assertEquals('Myoji',$foundSpy['surname']);
        $this->assertEquals('1980-12-1',$foundSpy['birth_date']);
        $this->assertEquals('FSB',$foundSpy['agency']);
        $this->assertNull($foundSpy['death_date']);
        $this->assertNull($foundSpy['country_of_operation']);
    }

    public function testInsertNoDuplicateAgencyNull()
    {
        $spy = new Spy();
        $spy->name="Namae";
        $spy->surname="Myoji";
        $spy->birth_date='1980-12-1';
        $spy->agency=null;
        $spy->save();

        $spy2 = new Spy();
        $spy2->name="Namae";
        $spy2->surname="Myoji";
        $spy2->birth_date='1980-12-1';
        $spy2->agency=null;

        $this->expectException(\Exception::class);
        $spy2->save();

        $foundSpyRecords = Spy::all()->toArray();

        $this->assertCount(1,$foundSpyRecords);

        $foundSpy = $foundSpyRecords[0];
        $this->assertEquals('Namae',$foundSpy['name']);
        $this->assertEquals('Myoji',$foundSpy['surname']);
        $this->assertEquals('1980-12-1',$foundSpy['birth_date']);
        $this->assertEquals('NO-AGENCY',$foundSpy['agency']);
        $this->assertNull($foundSpy['death_date']);
        $this->assertNull($foundSpy['country_of_operation']);
    }

    public function testInsertDifferentBirthDates()
    {
        $spy = new Spy();
        $spy->name="Namae";
        $spy->surname="Myoji";
        $spy->birth_date='1980-12-1';
        $spy->agency=null;
        $spy->save();

        $spy2 = new Spy();
        $spy2->name="Namae";
        $spy2->surname="Myoji";
        $spy2->birth_date='1984-12-1';
        $spy2->agency=null;
        $spy2->save();

        $foundSpyRecords = Spy::all()->toArray();

        $this->assertCount(2,$foundSpyRecords);

        $foundSpy = $foundSpyRecords[0];

        $this->assertEquals('Namae',$foundSpy['name']);
        $this->assertEquals('Myoji',$foundSpy['surname']);
        $this->assertEquals('1980-12-01',$foundSpy['birth_date']);
        $this->assertEquals('NO-AGENCY',$foundSpy['agency']);
        $this->assertNull($foundSpy['death_date']);
        $this->assertNull($foundSpy['country_of_operation']);

        $foundSpy = $foundSpyRecords[1];

        $this->assertEquals('Namae',$foundSpy['name']);
        $this->assertEquals('Myoji',$foundSpy['surname']);
        $this->assertEquals('1984-12-01',$foundSpy['birth_date']);
        $this->assertEquals('NO-AGENCY',$foundSpy['agency']);
        $this->assertNull($foundSpy['death_date']);
        $this->assertNull($foundSpy['country_of_operation']);
    }

    /**
     * @covers App\Model\Spy::setAgencyAttribute
     */
    public function testAgencyInvalid()
    {
        $spy = new Spy();

        $this->expectException(\InvalidArgumentException::class);
        $spy->agency = 'lalalala';
    }

    public function testDeathDateLessThanBirthDateFailsUponModel()
    {
        $spy = new Spy();
        $spy->birth_date='1980-12-1';

        $this->expectException(\InvalidArgumentException::class);
        $spy->death_date = '1970-12-1';
    }

    public function testBirthDateGreaterThanBirthDateFailsUponModel()
    {
        $spy = new Spy();
        $spy->death_date = '1970-12-1';
        
        $this->expectException(\InvalidArgumentException::class);
        $spy->birth_date='1980-12-1';
    }

    /**
     * We want to ensure that any query will *NOT* accept any database insertion
     * If death date less than Birth Date.
     *
     * We do place thies piece of code here because It is an overhead to place it elsewhere.
     * 
     * @return void
     */
    public function testDeathDateLessThanBirthDateFails()
    {
        $this->expectException(\Exception::class);
        DB::insert("INSERT INTO spies (name,surname,birth_date,death_date) VALUES ('Namae','Myoji','1980-12-01','1970-12-03')");
    }


    /**
     * We want to ensure thatr any query will accept any database insertion
     * If death date is NULL.
     *
     * We do place thies piece of code here because It is an overhead to place it elsewhere.
     * 
     * @return void
     */
    public function testDeathDateNullRawSql()
    {
        DB::insert("INSERT INTO spies (name,surname,birth_date,death_date) VALUES ('Namae','Myoji','1980-12-01',NULL)");

        $records = Spy::all()->toArray();

        $this->assertCount(1,$records);

        $foundSpy = $records[0];

        $this->assertEquals('Namae',$foundSpy['name']);
        $this->assertEquals('Myoji',$foundSpy['surname']);
        $this->assertEquals('1980-12-01',$foundSpy['birth_date']);
        $this->assertEquals('NO-AGENCY',$foundSpy['agency']);
        $this->assertNull($foundSpy['death_date']);
        $this->assertNull($foundSpy['country_of_operation']);
    }
}