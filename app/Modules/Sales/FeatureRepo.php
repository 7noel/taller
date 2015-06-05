<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Feature;

class FeatureRepo extends BaseRepo{

	public function getModel(){
		return new Feature;
	}

	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Feature::$filter($search)->with('feature_group')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Feature::with('feature_group')->orderBy('id', 'DESC')->paginate();
		}
	}

	public function saveMany($features, $k)
	{
		foreach ($features as $key => $data) {
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
				if (isset($data['name'])) {
					Feature::create($data);
				}
			}
		}
		
	}
}