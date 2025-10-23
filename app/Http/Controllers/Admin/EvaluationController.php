<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;

class EvaluationController extends Controller
{
    public function index(){
        $evals = Evaluation::with('submission.user','submission.assignment')->orderBy('created_at','desc')->paginate(25);
        return view('admin.evaluations', compact('evals'));
    }

    public function show($id){
        $eval = Evaluation::with('submission.user','submission.assignment')->findOrFail($id);
        return view('admin.show_eval', compact('eval'));
    }
}
