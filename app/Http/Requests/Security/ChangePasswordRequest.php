<?php namespace App\Http\Requests\Security;
 
use Illuminate\Foundation\Http\FormRequest;
 
class ChangePasswordRequest extends FormRequest
{
 
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
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:6'
        ];
    }
 
    public function messages()
    {
        return ['current_password'=>'La contraseña actual no coincide con la del Usuario'];
    }
 
    /**
     * Get the sanitized input for the request.
     *
     * @return array
     */
    public function sanitize()
    {
        return $this->only('clave');
    }
 
}