<?php namespace App\Http\Requests\Logistics;

use App\Http\Requests\Request;

class FormSubCategoryRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$data=Request::route()->parameters();
		return [
			'name'=>'required|unique:sub_categories,name'.((empty($data)) ? '' : ','.$data['sub_categories']) ,
			'category_id'=>'required|numeric'
		];
	}

}
