<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Spy;

class SpyController extends BaseController
{

    public function add(Request $request)
    {

        $name = $request->get('name');
        $name = trim($name)??null;

        $surname = $request->get('surname');
        $surname = trim($surname)??null;
        
        $birthDate = $request->get('birth_date');
        $birthDate = trim($birthDate)??"";

        if(empty($name) || empty($surname) || empty($birthDate)){
            return new JsonResponse(['messages'=>'either name, surname or birthdate cannot be empty'],400);
        }

        $spy = new Spy();
        $spy->name = $name;
        $spy->surname = $name;
        $spy->country_of_operation = $request->get('country_of_operation');

        try {
            $spy->birth_date = $birthDate;
            $spy->agency = $request->get('agency');
            $spy->deathDate = $request->get('death_date');
        // Any exception will occur if mutators throw exception
        }catch(\Exception $e){
            return new JsonResponse(['message'=>$e->getMessage()],400);
        }

        try {
            $spy->save();
        } catch(\Exception $e) {
            dd($e);
            return new JsonResponse(['messages'=>'Spy Could Bot Be saved'],500);
        }

        return new JsonResponse(['messages'=>'Spy has sucessfully been saved','spy'=>$spy],201);
    }   
}