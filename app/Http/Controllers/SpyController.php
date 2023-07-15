<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;

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

        $agency =  $request->get('agency')??'NO-AGENCY';
        $foundSpy = Spy::where('name',$name)->where('surname',$surname)->where('agency',$agency)->where('birth_date',$birthDate)->first();
        if($foundSpy){
            return new JsonResponse(['message'=>'Spy already exists','data'=>$foundSpy],409);
        }

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

            /**
             * Database shows this error code if constraint fails
             * constraint will fail if duplicate record exists upon database 
             * 
             * A constraint will fail upon master-slave replication database
             * due to replication lag.
             * 
             * It is implemented like this in order to futureproof my schema.
             * 
             */
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

    private function sanitizeFilteringInput(?string $string)
    {
        if(empty($string)){
            return 'ASC';
        }

        $string = trim($string);
        $string = strtoupper($string);

        return in_array($string,['ASC','DESC'])?$string:'ASC';
    }

    /**
     * GET /spies
     *
     * @return JsonResponse
     */
    public function spies(Request $request): JsonResponse
    {   
        $inputParamers = array_keys($request->all());

        // Input must be a specific subset of input parameters
        if(!empty($inputParamers) && array_intersect($inputParamers, ['page','limit','name','surname','age','sort_fullname','sort_age']) !== $inputParamers){
            return new JsonResponse(['message'=>'Invalid Arguments'],400);
        }

        $page = (int)$request->get('page')??1;
        $limit = (int)$request->get('limit')??10;

        if($page < 0){
            return new JsonResponse(['message'=>'Page must containe a positive value'],400);
        }

        if($limit < 0){
            return new JsonResponse(['message'=>'Limit must containe a positive value'],400);
        }

        $qb = Spy::query();

        if($request->has('name')){
            $name = $request->get('name');
            $name = trim($name);

            if(empty($name)){
                return new JsonResponse(['message'=>'Name must have a value'],400);
            }

            $qb = $qb->where('name',$name);
        }

        if($request->has('surname')){
            $surname = $request->get('surname');
            $surname = trim($surname);

            if(empty($surname)){
                return new JsonResponse(['message'=>'Surname must have a value'],400);
            }

            $qb = $qb->where('surname',$surname);
        }

        if($request->has('age')){
            $age = $request->get('age');
            $age = trim($age);

            if(!preg_match("/(\d+)(\s*-\s*(\d))?/",$age)){
                return new JsonResponse(['message'=>'Age Input Not valid'],400);
            }

            $range = explode('-',$age);
            $range = array_map('intval',$range);
            sort($range);

            if(count($range) == 2){
                $qb->whereRaw("TIMESTAMPDIFF(YEAR, birth_date,COALESCE(death_date,NOW())) >= :age_start",['age_start'=>$range[0]])
                    ->whereRaw("TIMESTAMPDIFF(YEAR, birth_date,COALESCE(death_date,NOW())) <= :age_end",['age_end'=>$range[1]]);
            } else {
                $qb->whereRaw("TIMESTAMPDIFF(YEAR, birth_date,COALESCE(death_date,NOW())) = :age",['age'=>$range[0]]);
            }
        }

        if($request->has('sort_fullname')){
            $filter_type = $request->get('sort_fullname');
            $filter_type = $this->sanitizeFilteringInput($filter_type);

            $qb->orderBy(DB::raw("CONCAT(name,' ',surname)"), $filter_type);
        }

        if($request->has('sort_age')){
            $filter_type = $request->get('sort_age');
            $filter_type = $this->sanitizeFilteringInput($filter_type);
            $qb->orderBy(DB::raw("TIMESTAMPDIFF(YEAR, birth_date,COALESCE(death_date,NOW()))"), $filter_type);
        }


        return new JsonResponse($qb->paginate($limit,['*'],'',$page),200);
    }

}