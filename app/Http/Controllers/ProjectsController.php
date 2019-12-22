<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Database\Eloquent\Builder;

class ProjectsController extends Controller
{
    public function index()
    {
        /** @var Builder $projectModel */
        $projectModel = app(Project::class);
        if ( request()->has('sort') ) {
            $projectModel = $projectModel->orderBy(request()->input('sort'), 'asc');
        }

        $projects = $projectModel->get();

        return view("projects.index", compact('projects'));
    }

    public function show($projectId)
    {
        $project = Project::find($projectId);

        return view('projects.show', compact('project'));
    }
}
