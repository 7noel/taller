<?php namespace App\Http\Controllers\Security;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Modules\Security\UserRepo;
use App\Modules\Security\RoleRepo;
use App\Http\Requests\Security\FormUserRequest;

use App\Http\Requests\Security\ChangePasswordRequest;

class UsersController extends Controller {

	protected $repo;
	protected $roleRepo;

	public function __construct(UserRepo $repo, RoleRepo $roleRepo) {
		$this->repo = $repo;
		$this->roleRepo = $roleRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$roles = $this->roleRepo->all();
		return view('partials.create', compact('roles'));
	}

	public function store(FormUserRequest $request)
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('guard.users.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$roles = $this->roleRepo->all();
		return view('partials.edit', compact('model', 'roles'));
	}

	public function update($id, FormUserRequest $request)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('guard.users.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('guard.users.index');
	}

	public function changePassword()
	{
		return view('auth.change_password');
	}
	public function updatePassword(ChangePasswordRequest $request)
	{
		$model = $this->repo->findOrFail(\Auth::user()->id);
		$model->fill($request->all());
		$model->save();
		return redirect()->to('/');
	}

}
