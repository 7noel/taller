<?php 

namespace App\Modules\HumanResources;

use App\Modules\Base\BaseRepo;
use App\Modules\HumanResources\Job;

class JobRepo extends BaseRepo{

	public function getModel(){
		return new Job;
	}
}