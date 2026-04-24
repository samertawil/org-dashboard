<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class ActivitiesSector extends Model
{
   protected $table = 'activities';

   protected $fillable = [
      'sector_id',
      'sector_name',
      'activites_date',
   ];

   /**
    * Replaces the MySQL view with an Eloquent query.
    * Uses fromSub to allow filtering by aliases in where clauses.
    */
   public function newQuery()
   {
       $subquery = DB::table('activities')
           ->join('statuses', 'activities.sector_id', '=', 'statuses.id')
           ->select([
               'activities.sector_id',
               'statuses.status_name as sector_name',
               DB::raw("DATE_FORMAT(activities.start_date, '%m/%Y') as activites_date"),
               DB::raw("MAX(activities.start_date) as sort_date")
           ])
           ->groupBy('activities.sector_id', 'statuses.status_name', DB::raw("DATE_FORMAT(activities.start_date, '%m/%Y')"))
           ->orderBy('sort_date', 'desc');

       return parent::newQuery()->fromSub($subquery, 'activities_sector_sub');
   }
}
