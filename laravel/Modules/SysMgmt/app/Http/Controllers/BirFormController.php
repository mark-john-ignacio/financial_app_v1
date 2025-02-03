<?php

namespace Modules\SysMgmt\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BirFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sysmgmt::bir-form.index');
    }
}
