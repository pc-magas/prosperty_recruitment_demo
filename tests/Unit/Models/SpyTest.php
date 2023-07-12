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
    }
}