<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CalculateController extends Controller
{
//    function that returns total sundays
    public function getSunday(Request $request){

        $validator = Validator::make($request->all(),[
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after:start_date'
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()],403);
        }

//        start date
        $start_date = Carbon::createFromFormat('d/m/Y',$request->start_date);
//        end date
        $end_date = Carbon::createFromFormat('d/m/Y',$request->end_date);

//        counts how many years
        $diffYears = $start_date->diffInYears($end_date);

        if($diffYears < 2 || $diffYears > 5){
            return response()->json(['message' => 'Date difference must be greater than two years but less than five' ,'year' => $diffYears],403);
        }

        if($start_date->dayOfWeek === Carbon::SUNDAY){
            return response()->json(['message' => 'Sundays cannot be the start date.'],403);
        }

//      this filters and check the number of Sundays, which is 28 after Sundays.
        $days = $start_date->diffInDaysFiltered(function (Carbon $date){

           $currentDate =  (int)$date->format('d');
            if($date->dayOfWeek === Carbon::SUNDAY) {
                if($currentDate < 28){
                    return $date;
                }
            }

        }, $end_date);

        return response()->json(['total_sundays' => $days],200);
    }
}
