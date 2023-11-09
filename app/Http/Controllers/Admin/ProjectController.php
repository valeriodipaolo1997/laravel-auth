<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::orderByDesc('id')->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.projects.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $val_data = $request->validated();

        $val_data['slug'] = Str::slug($request->title, '-');
        //dd($val_data);

        if ($request->has('thumb')) {
            $file_path = Storage::put('projects_images', $request->thumb);
            $val_data['thumb'] = $file_path;
        }

        Project::create($val_data);
        return to_route('admin.projects.index')->with('message', 'Project created successfully');
    }
    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {

        if ($project) {
            return view('admin.projects.show', compact('project'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $val_data = $request->validated();

        if ($request->has('thumb') && $project->thumb) {

            Storage::delete($project->thumb);

            $newImageFile = $request->thumb;
            $file_path = Storage::put('projects_images', $newImageFile);
            $val_data['thumb'] = $file_path;
        }

        $project->update($val_data);

        return to_route('admin.projects.index')->with('message', 'Welldone! project updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if (!is_null($project->exif_thumbnail)) {
            Storage::delete($project->thumb);
        }
        $project->delete();

        return to_route('admin.projects.index')->with('message', 'Welldone! Project deleted successfully');
    }

    public function trash_projects()
    {
        return view('admin.projects.trash', ['trash_project' => Project::onlyTrashed()->get()]);
    }
}
