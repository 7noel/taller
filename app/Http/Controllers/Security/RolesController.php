<?php namespace App\Http\Controllers\Security;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Modules\Security\RoleRepo;
use App\Modules\Security\PermissionGroupRepo;
use App\Modules\Security\PermissionRepo;

class RolesController extends Controller {

	protected $repo;
	protected $permissionRepo;
	protected $permissionGroupRepo;

	public function __construct(RoleRepo $repo, PermissionGroupRepo $permissionGroupRepo, PermissionRepo $permissionRepo) {
		$this->repo = $repo;
		$this->permissionRepo = $permissionRepo;
		$this->permissionGroupRepo = $permissionGroupRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$groups = $this->permissionGroupRepo->all();
		$permissions = $this->permissionRepo->all();
		return view('partials.create',compact('permissions' ,'groups'));
	}

	public function store()
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('guard.roles.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$groups = $this->permissionGroupRepo->all();
		$permissions = $this->permissionRepo->all();
		return view('partials.edit', compact('model' ,'permissions' ,'groups'));
	}

	public function update($id)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('guard.roles.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('guard.roles.index');
	}

}
