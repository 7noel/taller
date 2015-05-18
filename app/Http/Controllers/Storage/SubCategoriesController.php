<?php namespace App\Http\Controllers\Storage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Storage\CategoryRepo;
use App\Modules\Storage\SubCategoryRepo;

use App\Http\Requests\Logistics\FormSubCategoryRequest;

class SubCategoriesController extends Controller {

	protected $repo;
	protected $categoryRepo;

	public function __construct(SubCategoryRepo $repo, CategoryRepo $categoryRepo) {
		$this->repo = $repo;
		$this->categoryRepo = $categoryRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$categories = $this->categoryRepo->getList();
		return view('partials.create', compact('categories'));
	}

	public function store(FormSubCategoryRequest $request)
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('storage.sub_categories.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$categories = $this->categoryRepo->getList();
		$model = $this->repo->findOrFail($id);
		return view('partials.edit', compact('model', 'categories'));
	}

	public function update($id, FormSubCategoryRequest $request)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('storage.sub_categories.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('storage.sub_categories.index');
	}
	public function ajaxList($category_id)
	{
		$ajax = $this->repo->ajaxList($category_id);
		return \Response::json($ajax);
	}
}
