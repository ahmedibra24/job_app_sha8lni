<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;
use facade\Illuminate\Support\Facades\Auth;

class dashboardController extends Controller
{
    public function index(Request $request)
    {
        $query= JobVacancy::query();

        //! search & filter
        if($request->has('search')&&$request->has('filter')){
            $query->where(function ($qu) use($request) {
                $qu->where('title','like','%'.$request->search.'%')
                ->orWhere('location','like','%'.$request->search.'%')
                ->orWhereHas('company',function ($q)use($request) {
                    $q->where('name','like','%'.$request->search.'%');
                });
            })
            ->where('type','like','%'.$request->filter.'%');
        }

        //! search 
        if($request->has('search') && $request->filter == null){
            $query->where('title','like','%'.$request->search.'%')
            ->orWhere('location','like','%'.$request->search.'%')
            ->orWhereHas('company',function ($q)use($request) {
                $q->where('name','like','%'.$request->search.'%');
            });
        } 

        //! filter 
        if($request->has('filter') && $request->search == null){
            $query->where('type','like','%'.$request->filter.'%');
        } 
 
        //! all
        $jobs = $query->latest()->paginate(10);
        
        return view('dashboard',compact('jobs'));
    }
}
