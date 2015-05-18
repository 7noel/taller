<?php namespace App\Http\Requests\Security;

use App\Http\Requests\Request;

class FormPermissionRequest extends Request {

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
			'name'=>'required|unique:permissions,name'.((empty($data)) ? '' : ','.$data['permissions']) ,
			'action'=>'required|unique:permissions,action'.((empty($data)) ? '' : ','.$data['permissions'])
		];
	}

}
