<?php namespace App\Http\Requests\Logistics;

use App\Http\Requests\Request;

class FormUnitRequest extends Request {

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
			'name'=>'required|unique:units,name'.((empty($data)) ? '' : ','.$data['units']) ,
			'unit_type_id'=>'required|numeric',
			'symbol'=>'required|unique:units,symbol'.((empty($data)) ? '' : ','.$data['units']) ,
			'value'=>'required|numeric'
		];
	}

}
