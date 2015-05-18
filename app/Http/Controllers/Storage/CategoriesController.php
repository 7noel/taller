<?php namespace App\Http\Controllers\Storage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Storage\CategoryRepo;

use App\Http\Requests\Logistics\FormCategoryRequest;

class CategoriesController extends Controller {

	protected $repo;

	public function __construct(CategoryRepo $repo) {
		$this->repo = $repo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		return view('partials.create');
	}

	public function store(FormCategoryRequest $request)
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('storage.categories.index');
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

	public function update($id, FormCategoryRequest $request)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('storage.categories.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('storage.categories.index');
	}

}
