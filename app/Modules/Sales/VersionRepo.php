<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Version;

class VersionRepo extends BaseRepo{

	public function getModel(){
		return new Version;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Version::$filter($search)->with('modelo')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Version::with('modelo')->orderBy('id', 'DESC')->paginate();
		}
	}
	public function getList($name='name', $id='id')
	{
		$versions = Version::with('modelo')->get();
		$list = [""=>"Seleccionar"];
		foreach ($versions as $key => $version) {
			if ($version->modelo->id == 5) {
				$list[$version->id] = $version->modelo->name." ".$version->name;
			} else {
				$list[$version->id] = $version->modelo->name." / ".$version->name;
			}
		}
		return $list;
	}
}