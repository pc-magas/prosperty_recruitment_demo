<?php

namespace Tests\Unit\Models;

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

}