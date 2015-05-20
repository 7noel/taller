<?php namespace App\Http\Controllers\Storage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Storage\UnitRepo;
use App\Modules\Base\UnitTypeRepo;
use App\Modules\Storage\CategoryRepo;
use App\Modules\Storage\SubCategoryRepo;
use App\Modules\Storage\ProductRepo;
use App\Modules\Base\CurrencyRepo;

use App\Http\Requests\Logistics\FormProductRequest;
use App\Modules\Storage\Stock;

class ProductsController extends Controller {

	protected $repo;
	protected $categoryRepo;
	protected $subCategoryRepo;
	protected $unitRepo;
	protected $unitTypeRepo;
	protected $currencyRepo;

	public function __construct(ProductRepo $repo, SubCategoryRepo $subCategoryRepo, CategoryRepo $categoryRepo, UnitRepo $unitRepo, UnitTypeRepo $unitTypeRepo, CurrencyRepo $currencyRepo) {
		$this->repo = $repo;
		$this->categoryRepo = $categoryRepo;
		$this->subCategoryRepo = $subCategoryRepo;
		$this->unitRepo = $unitRepo;
		$this->unitTypeRepo = $unitTypeRepo;
		$this->currencyRepo = $currencyRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$categories = $this->categoryRepo->getList();
		$unit_types = $this->unitTypeRepo->getList();
		$currencies = $this->currencyRepo->getList2();
		return view('partials.create', compact('categories', 'unit_types', 'currencies'));
	}

	public function store(FormProductRequest $request)
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('storage.products.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		//dd($model->unit->unit_type_id);
		$categories = $this->categoryRepo->getList();
		$unit_types = $this->unitTypeRepo->getList();
		$currencies = $this->currencyRepo->getList2();

		$sub_categories = $this->subCategoryRepo->getList2($model->sub_category->category_id);
		$units = $this->unitRepo->getList2($model->unit->unit_type_id);
		return view('partials.edit', compact('model', 'sub_categories', 'units', 'categories', 'unit_types', 'currencies'));
	}

	public function update($id, FormProductRequest $request)
	{
		$a=[1,2,3];
		$b=[2,3];
		//dd(array_diff($b, $a));
		$data = $request->all();
		$data['id']=$id;
		$data = $this->repo->prepareData($data);
		//dd($data);
		$this->repo->save($data,$id);
		return \Redirect::route('storage.products.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('storage.products.index');
	}

}
