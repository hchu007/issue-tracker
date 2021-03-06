<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Project::class);

        $developers = User::role('developer')->get();

        return view('projects.create', compact('developers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'min:3'],
            'description' => ['required', 'min:3'],
            'duedate' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/projects/create')
                ->withErrors($validator)
                ->withInput();
        }

        $project = new Project(request(['title', 'description', 'duedate']));
        $project->manager_id = auth()->id();
        $project->save();

        $project->developers()->sync(request('developers'));

        return redirect('projects');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $project_id
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $manager = $project->manager;
        $developers = $project->developers;

        return view('projects.show', compact('project', 'manager', 'developers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $project_id
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $developers = User::role('developer')->get();

        return view('projects.edit', compact('project', 'developers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $project_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'min:3'],
            'description' => ['required', 'min:3'],
            'developers' => 'exists:users,id',
            'type' => 'required',
            'status' => 'required',
            'priority' => 'required',
            'duedate' => ['required', 'after_or_equal:created_at'],
        ]);

        if ($validator->fails()) {
            return redirect('/projects/{$project->id}/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $project->update(request(['title', 'description', 'type', 'status', 'priority', 'duedate']));

        $project->developers()->sync(request('developers'));

        return redirect("/projects/" . $project->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $project_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        Project::destroy($project->id);

        return redirect('projects');
    }
}
