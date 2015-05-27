<?php namespace App\Http\Requests\HumanResources;

use App\Http\Requests\Request;

class FormEmployeeRequest extends Request {

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
		return [
			'name'=>'required',
			'paternal_surname'=>'required',
			'maternal_surname'=>'required',
			'id_type_id'=>'required|numeric',
			'doc'=>'required',
			'gender'=>'',
			'address'=>'required',
			'ubigeo_id'=>'required|numeric',
			'email_personal'=>'email',
			'email_company'=>'email'
		];
	}

}
