<?php namespace App\Http\Requests\Logistics;

use App\Http\Requests\Request;

class FormProductRequest extends Request {

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
			'name'=>'required|unique:products,name'.((empty($data)) ? '' : ','.$data['products']) ,
			//'internal_code'=>'required|unique:products,internal_code'.((empty($data)) ? '' : ','.$data['products']) ,
			//'provider_code'=>'required|unique:products,provider_code'.((empty($data)) ? '' : ','.$data['products']) ,
			//'manufacturer_code'=>'required|unique:products,manufacturer_code'.((empty($data)) ? '' : ','.$data['products']) ,
			'sub_category_id'=>'required|numeric',
			//'brand_id'=>'required|numeric',
			'unit_id'=>'required|numeric'
		];
	}

}
