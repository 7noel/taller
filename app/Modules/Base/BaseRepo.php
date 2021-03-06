<?php 

namespace App\Modules\Base;

abstract class BaseRepo{

	protected $model;

	public function __construct() {
		$this->model = $this->getModel();
	}

	abstract public function getModel();
	
	public function find($id){
		return $this->model->find($id);
	}
	public function findOrFail($id){
		return $this->model->findOrFail($id);
	}
	public function all()
	{
		return $this->model->all();
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return $this->model->$filter($search)->orderBy("$filter", 'ASC')->paginate();
		} else {
			return $this->model->orderBy('id', 'DESC')->paginate();
		}
	}

	public function getList($name='name', $id='id')
	{
		return $list = [""=>'Seleccionar'] + $this->model->lists($name, $id)->toArray();
	}
	public function all_with_deleted()
	{
		return $this->model->withTrashed()->get();
	}
	public function all_only_deleted()
	{
		return $this->model->onlyTrashed()->get();
	}
	public function jsonArray($array,$value,$label)
	{
		foreach ($array as $valor) {
			$data[]=array("value"=>$valor[$value],
				'label'=>$valor[$label],
				'id'=>$valor
			);
		}
		return Response::json($data);
	}
	public function destroy($id)
	{
		$model=$this->model->findOrFail($id);
		$model->delete();
		$message = $model->name. ' fue eliminado';
		if (\Request::ajax()) {
			return response()->json([
				'id'=>$model->id,
				'message'=>$message
			]);
		}
		\Session::flash('message', $message);
		return $model;
	}
	public function save($data, $id=0)
	{
		$data = $this->prepareData($data);
		if ($id>0) {
			$model = $this->model->findOrFail($id);
			$model->fill($data);
		} else {
			$model = $this->model->fill($data);			
		}
		if ($model->save()) {
			return $model;
		} else {
			return false;
		}
	}
	public function prepareData($data)
	{
		return $data;
	}
	public function syncMany($allData,$k1,$k2)
	{
		$new_ids = [];
		foreach ($allData as $key => $data) {
			$new_ids[] = $data["$k2"];
		}
		$old_ids = $this->model->where($k1['key'],$k1['value'])->lists($k2);
		if (!isset($old_ids)) { $old_ids=[]; }

		$toDelete = array_diff($old_ids, $new_ids);
		$toSave = array_diff($new_ids, $old_ids);
		$toEdit = array_intersect($old_ids, $new_ids);
		if (!empty($toDelete)) {
			$this->model->where($k1['key'], $k1['value'])->whereIn($k2,$toDelete)->delete();
		}
		foreach ($allData as $key => $data) {
			if (in_array($data[$k2], $toSave)) {
				$data[$k1['key']] = $k1['value'];
				$this->save($data);
			} else if (in_array($data[$k2], $toEdit)) {
				$model = $this->model->where($k1['key'],$k1['value'])->where($k2, $data["$k2"])->first();
				$model->fill($data);
				$model->save();
			}
		}
		return true;
	}

	public function saveFile($folder = '', $file, $nameOld = '')
	{
		
		$name = $file->getClientOriginalName();
		if ($nameOld == '') {
			$nameOld = $name;
		}
		if (\Storage::exists($folder.'/'.$nameOld)) {
			\Storage::delete($folder.'/'.$nameOld);
		}
		\Storage::disk('local')->put('img/'.$name,  \File::get($file));
	}
	public function prepareDataImage($data, $images)
	{
		foreach ($images as $key => $image) {
			if (isset($data[$image])) {
				$data[$image] = $data[$image]->getClientOriginalName();
			} else if (isset($data['delete_'.$image])) {
				$data[$image] = '';
			}
			else {
				unset($data[$image]);
			}
		}
		return $data;
	}
	public function saveMany($items, $k)
	{
		foreach ($items as $key => $data) {
			$data = $this->prepareData($data);
			$data[$k['key']] = $k['value'];
			if (isset($data['id'])) {
				$model = $this->findOrFail($data['id']);
				if (trim($data['name']) == '') {
					$model->delete();
				} else {
					$model->fill($data);
					$model->save();
				}
			} else {
				if (trim($data['name']) != '') {
					$this->model->create($data);
				}
			}
		}
	}
}