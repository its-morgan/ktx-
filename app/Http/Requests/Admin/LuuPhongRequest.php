<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LuuPhongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenphong' => ['required'],
            'tang' => ['required', 'numeric', 'min:1'],
            'giaphong' => ['required', 'numeric', 'min:0'],
            'soluongtoida' => ['required', 'numeric', 'min:1'],
            'succhuamax' => ['required', 'numeric', 'min:1', 'same:soluongtoida'],
            'mota' => ['nullable'],
            'gioitinh' => ['required', 'in:Nam,Nữ'],
        ];
    }

    public function messages(): array
    {
        return [
            'tenphong.required' => 'Tên phòng không được để trống.',
            'tang.required' => 'Tầng không được để trống.',
            'giaphong.required' => 'Giá phòng không được để trống.',
            'giaphong.numeric' => 'Giá phòng phải là số.',
            'soluongtoida.required' => 'Số lượng tối đa không được để trống.',
            'soluongtoida.numeric' => 'Số lượng tối đa phải là số.',
            'soluongtoida.min' => 'Số lượng tối đa phải lớn hơn hoặc bằng 1.',
            'succhuamax.required' => 'Sức chứa tối đa không được để trống.',
            'succhuamax.same' => 'Sức chứa tối đa phải bằng số lượng tối đa.',
            'gioitinh.required' => 'Giới tính không được để trống.',
        ];
    }
}


