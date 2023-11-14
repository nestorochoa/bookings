<?php

namespace App\Http\Controllers;

use App\Models\BUsers;
use Inertia\Response;
use Inertia\Inertia;

class SchoolManager extends Controller
{
  /**
   * Display the login view.
   */
  public function create(): Response
  {
    $schoolManager = BUsers::where('usr_type', '=', '2')->paginate(20);
    return Inertia::render('SchoolManager/View', [
      'schoolManager' => $schoolManager
    ]);
  }
}