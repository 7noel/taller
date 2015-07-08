<?php

namespace App\Http\Controllers\AutoRepair;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\AutoRepair\ServiceChecklistRepo;
use App\Modules\AutoRepair\CheckitemGroupRepo;

class ServiceChecklistsController extends Controller
{

    protected $repo;
    protected $checkitemRepo;

    public function __construct(ServiceChecklistRepo $repo, CheckitemGroupRepo $checkitemGroupRepo) {
        $this->repo = $repo;
        $this->checkitemGroupRepo = $checkitemGroupRepo;
    }

    public function index()
    {
        $models = $this->repo->index('name', \Request::get('name'));
        return view('partials.index',compact('models'));
    }

    public function create()
    {
        $groups = $this->checkitemGroupRepo->all();
        return view('partials.create', compact('groups'));
    }

    public function store()
    {
        $this->repo->save(\Request::all());
        return \Redirect::route('autorepair.checkitem_groups.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $model = $this->repo->findOrFail($id);
        return view('partials.edit', compact('model'));
    }

    public function update($id)
    {
        $this->repo->save(\Request::all(), $id);
        return \Redirect::route('autorepair.checkitem_groups.index');
    }

    public function destroy($id)
    {
        $model = $this->repo->destroy($id);
        if (\Request::ajax()) { return $model; }
        return redirect()->route('autorepair.checkitem_groups.index');
    }
}
