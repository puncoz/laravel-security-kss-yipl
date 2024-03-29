<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Database\Eloquent\Builder;

class ProjectsController extends Controller
{
    /**
     * @var Builder
     */
    protected $projectModel;

    public function __construct(Project $project)
    {
        $this->projectModel = $project;
    }

    public function index()
    {
        $sortBy = request()->get('sort');
        if ( $sortBy ) {
            $this->projectModel = $this->projectModel->orderByRaw("{$sortBy} asc");
//            $this->projectModel = $this->projectModel->orderBy(request()->input('sort'), 'asc');
        }

        $projects = $this->projectModel->get();

        return view("projects.index", compact('projects'));
    }

    public function show($projectId)
    {
        $project = $this->projectModel->find($projectId);

        return view('projects.show', compact('project'));
    }
}
