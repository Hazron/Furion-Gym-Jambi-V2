<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaketMember;
use Illuminate\Http\Request;

class PaketMemberController extends Controller
{
    public function index(){
        $paketMember = PaketMember::all(); 
        return view('Admin.PaketMember', compact('paketMember'));
    }
}
