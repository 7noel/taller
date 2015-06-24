<?php namespace App\Http\Requests\Security;

use App\Http\Requests\Request;

class FormUserRequest extends Request {

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
		//dd($data);
		return [
			'name'=>'required',
			'email'=>'required|unique:users,email'.((empty($data)) ? '' : ','.$data['users']),
			'password'=>((empty($data)) ? 'required' : '')
		];
	}

}
