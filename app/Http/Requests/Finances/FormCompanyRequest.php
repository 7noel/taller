<?php namespace App\Http\Requests\Finances;

use App\Http\Requests\Request;

class FormCompanyRequest extends Request {

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
		$type = \Request::only('id_type_id')['id_type_id'];
		switch ($type) {
			case '1':
				$rules = 'digits:11|';
				break;
			case '2':
				$rules = 'digits:8|';
				break;
			default:
				if(is_numeric(\Request::only('doc')['doc'])){ $rules = 'digits_between:6,11|'; }
				else { $rules = 'between:6,11|'; }
				break;
		}
		return [
			'id_type_id'=>'required|numeric',
			'doc' => $rules.'required|unique:companies,doc'.((empty($data)) ? ',NULL' : ','.$data['companies']).',id,id_type_id,' . $type,
			'company_name'=>'required_if:id_type_id,1',
			'name'=>'required_if:id_type_id,2,3,4',
			'paternal_surname'=>'required_if:id_type_id,2,3,4',
			'maternal_surname'=>'required_if:id_type_id,2,3,4',
			'address'=>'required',
			'ubigeo_id'=>'required|numeric',
			'email'=>'email'
		];
	}

}
