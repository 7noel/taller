<?php 

namespace App\Modules\Security;

use App\Modules\Base\BaseRepo;
use App\Modules\Security\PermissionGroup;

class PermissionGroupRepo extends BaseRepo{

	public function getModel(){
		return new PermissionGroup;
	}
	
}