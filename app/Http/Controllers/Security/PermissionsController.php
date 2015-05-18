<?php namespace App\Http\Controllers\Security;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Modules\Security\PermissionRepo;
use App\Modules\Security\PermissionGroupRepo;
use App\Http\Requests\Security\FormPermissionRequest;

class PermissionsController extends Controller {

	protected $repo;
	protected $groupRepo;

	public function __construct(PermissionRepo $repo, PermissionGroupRepo $groupRepo) {
		$this->repo = $repo;
		$this->groupRepo = $groupRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$groups = $this->groupRepo->getList();
		return view('partials.create', compact('groups'));
	}

	public function store(FormPermissionRequest $request)
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('guard.permissions.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$groups = $this->groupRepo->getList();
		return view('partials.edit', compact('model', 'groups'));
	}

	public function update($id, FormPermissionRequest $request)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('guard.permissions.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('guard.permissions.index');
	}

}
