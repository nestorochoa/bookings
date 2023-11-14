<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Inertia\Response;
use Inertia\Inertia;

class Dashboard extends Controller
{
  /**
   * Display the login view.
   */
  public function create(): Response
  {
    return Inertia::render('Dashboard');
  }
}