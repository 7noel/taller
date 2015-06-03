<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Version;

class VersionRepo extends BaseRepo{

	public function getModel(){
		return new Version;
	}
}