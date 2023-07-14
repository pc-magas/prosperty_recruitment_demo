<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Database\QueryException;

use App\Models\Spy;

class SpyController extends BaseController
{

    /**
     * PUT /spy
     *
     * @param Request $request Httpo request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
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

        $agency =  $request->get('agency');

        $spy = new Spy();
        $spy->name = $name;
        $spy->surname = $surname;
        $spy->country_of_operation = $request->get('country_of_operation');

        try {
            $spy->birth_date = $birthDate;
            $spy->agency = $agency;
            $spy->deathDate = $request->get('death_date');

        // Any exception will occur if mutators throw exception
        }catch(\Exception $e){
            return new JsonResponse(['message'=>$e->getMessage()],400);
        }

        try {
            $spy->save();
        } catch(QueryException $e) {
            
            // Database shows this error code if constraint fails
            // constraint will fail if duplicate record exists upon database
            if((int)$e->getCode() == 23000){
                return new JsonResponse(['messages'=>'Spy already exists'],409);
            }

            return new JsonResponse(['messages'=>'Spy Could not Be saved'],500);
        }

        return new JsonResponse(['messages'=>'Spy has sucessfully been saved','spy'=>$spy],201);
    }


    /**
     * Get /spy/random
     *
     * Return 5 random spies
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function randomSpies(Request $request): JsonResponse
    {
        $spies = Spy::limit(5)->inRandomOrder()->get();
        return new JsonResponse($spies);
    }

    /**
     * GET /spies
     *
     * @return JsonResponse
     */
    public function spies(Request $request): JsonResponse
    {
        $page = (int)$request->get('page');
        $limit = (int)$request->get('limit');

        return new JsonResponse(Spy::paginate($limit,['*'],'',$page),200);
    }
}