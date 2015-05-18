<?php 

namespace App\Modules\Storage;

use App\Modules\Base\BaseRepo;
use App\Modules\Storage\Category;

class CategoryRepo extends BaseRepo{

	public function getModel(){
		return new Category;
	}
}